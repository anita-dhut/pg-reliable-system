<?php
session_start();
include("../connection.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';

// 1️⃣ Email check (AJAX)
if (isset($_POST['action']) && $_POST['action'] === 'check_email') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $check = mysqli_query($conn, "SELECT id FROM owner_registration WHERE email='$email' LIMIT 1");
    echo json_encode(['exists' => mysqli_num_rows($check) > 0]);
    exit;
}

// 2️⃣ Registration & OTP sending
if (isset($_POST['register'])) {
    $name    = mysqli_real_escape_string($conn, $_POST['name']);
    $email   = mysqli_real_escape_string($conn, $_POST['email']);
    $mobile  = mysqli_real_escape_string($conn, $_POST['mobile']);
    $password= mysqli_real_escape_string($conn, $_POST['password']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $role    = $_POST['role'] ?? 'owner';

    // Double-check email existence
    $checkEmailQuery = "SELECT id FROM owner_registration WHERE email='$email' LIMIT 1";
    $checkResult = mysqli_query($conn, $checkEmailQuery);
    if (mysqli_num_rows($checkResult) > 0) {
        $_SESSION['error'] = "Email already exists. Please use a different email.";
        header("Location: ./onwer_registration.php");
        exit;
    }

    // Generate OTP
    $otp = rand(10000, 99999);
    $_SESSION['owner_otp']   = $otp;
    $_SESSION['owner_email'] = $email;

    $_SESSION['temp_owner'] = [
        'name'     => $name,
        'email'    => $email,
        'mobile'   => $mobile,
        'password' => $password,
        'address'  => $address,
        'role'     => $role
    ];

    // Send OTP via email
    $mail = new PHPMailer(true);
    try {
        $mail->SMTPDebug  = 0;
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'ankushdhupe2022@gmail.com';
        $mail->Password   = 'ampjxclunkvhplsc'; // App password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;

        $mail->setFrom('ankushdhupe2022@gmail.com', 'PG Reliable');
        $mail->addAddress($email, $name);

        $mail->isHTML(true);
        $mail->Subject = 'OTP Verification - PG Reliable';
        $mail->Body = "
            <h2>Hello $name,</h2>
            <p>Your OTP for account verification is:</p>
            <h3 style='color:#2d89ef;'>$otp</h3>
        ";
        $mail->AltBody = "Hello $name, Your OTP is: $otp";

        $mail->send();

        // Set success message and redirect to OTP verification page
        echo "<script>alert('Registration successful! OTP sent to your email.');
                window.location.href = '../otp_verification.php';
                </script>";
        exit;
    } catch (Exception $e) {
        // Set error message and redirect back to registration page
        echo "  <script>alert('Registration failed, please try again..');
                    window.location.href = '../onwer_registration.php';
                </script>";
        exit;
    }
}

// Add Template
if(isset($_POST['saveTemplate'])) {
    $bio = mysqli_real_escape_string($conn, $_POST['bio']);
    $buildingName = mysqli_real_escape_string($conn, $_POST['buildingName']);
    $ownerName = mysqli_real_escape_string($conn, $_POST['ownerName']);
    $ownerEmail = mysqli_real_escape_string($conn, $_POST['ownerEmail']);
    $roomType = mysqli_real_escape_string($conn, $_POST['roomType']);
    // print_r($ownerEmail);
    
    // Handle file upload
    $buildingImageName = "";
    if (isset($_FILES['buildingImage']) && $_FILES['buildingImage']['error'] == 0) {
        $targetDir = "../assets/";
        
        // Create assets directory if it doesn't exist
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        
        // Generate unique filename to avoid conflicts
        $fileExtension = pathinfo($_FILES["buildingImage"]["name"], PATHINFO_EXTENSION);
        $buildingImageName = uniqid('building_', true) . '.' . $fileExtension;
        $targetFile = $targetDir . $buildingImageName;
        
        // Move uploaded file
        if (!move_uploaded_file($_FILES["buildingImage"]["tmp_name"], $targetFile)) {
            echo "<script>
                alert('Failed to upload building image!');
                window.location.href = './onwer_dashboard.php';
            </script>";
            exit();
        }
    }
    
    // Insert query - only save name, building_name, building_photo, bio, room_type, and email
    $query = "INSERT INTO onwer_room_template (name, bulding_name, bulding_photo, bio, room_type, email) 
              VALUES ('$ownerName', '$buildingName', '$buildingImageName', '$bio', '$roomType', '$ownerEmail')";
    
    $run = mysqli_query($conn, $query);
    
    if ($run) {
        echo "<script>
            alert('Template saved successfully!');
            window.location.href = './onwer_dashboard.php';
        </script>";
    } else {
        echo "<script>
            alert('Template save failed! Error: " . mysqli_error($conn) . "');
            window.location.href = './onwer_dashboard.php';
        </script>";
    }
}

// Update Template
if (isset($_POST['updateTemplate'])) {
    $templateId = mysqli_real_escape_string($conn, $_POST['templateId']);
    $ownerName = mysqli_real_escape_string($conn, $_POST['ownerName']);
    $buildingName = mysqli_real_escape_string($conn, $_POST['buildingName']);
    $bio = mysqli_real_escape_string($conn, $_POST['bio']);
    $ownerEmail = mysqli_real_escape_string($conn, $_POST['user_email']);
    $roomType = mysqli_real_escape_string($conn, $_POST['roomType']);
    
    // Check if new building image is uploaded
    if (isset($_FILES['newbuildingImage']) && $_FILES['newbuildingImage']['error'] == 0) {
        $targetDir = "../assets/";
        
        // Create assets directory if it doesn't exist
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        
        // Generate unique filename
        $fileExtension = pathinfo($_FILES["newbuildingImage"]["name"], PATHINFO_EXTENSION);
        $buildingImageName = uniqid('building_', true) . '.' . $fileExtension;
        $targetFile = $targetDir . $buildingImageName;
        
        // Move uploaded file
        if (move_uploaded_file($_FILES["newbuildingImage"]["tmp_name"], $targetFile)) {
            // Delete old image if exists
            $oldImageQuery = "SELECT bulding_photo FROM onwer_room_template WHERE id='$templateId' AND email='$ownerEmail'";
            $oldImageRes = mysqli_query($conn, $oldImageQuery);
            if ($oldImageRes && mysqli_num_rows($oldImageRes) > 0) {
                $oldImageData = mysqli_fetch_assoc($oldImageRes);
                $oldImagePath = "../assets/" . $oldImageData['bulding_photo'];
                if (file_exists($oldImagePath) && !empty($oldImageData['bulding_photo'])) {
                    unlink($oldImagePath);
                }
            }
            
            // Update with new building photo
            $query = "UPDATE onwer_room_template 
                      SET name='$ownerName', bulding_name='$buildingName', bulding_photo='$buildingImageName', bio='$bio', room_type='$roomType', email='$ownerEmail' 
                      WHERE id='$templateId' AND email='$ownerEmail'";
        } else {
            echo "<script>
                alert('Failed to upload new building image!');
                window.location.href = './onwer_dashboard.php';
            </script>";
            exit();
        }
    } else {
        // Update without changing building photo
        $query = "UPDATE onwer_room_template 
                  SET name='$ownerName', bulding_name='$buildingName', bio='$bio', room_type='$roomType', email='$ownerEmail' 
                  WHERE id='$templateId' AND email='$ownerEmail'";
    }
    
    $run = mysqli_query($conn, $query);
    
    if ($run) {
        echo "<script>
            alert('Template updated successfully!');
            window.location.href = './onwer_dashboard.php';
        </script>";
    } else {
        echo "<script>
            alert('Template update failed! Error: " . mysqli_error($conn) . "');
            window.location.href = './onwer_dashboard.php';
        </script>";
    }
}


// Ensure owner is logged in
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'owner' || !isset($_SESSION['user_email'])) {
    echo "<script>alert('Please log in as owner first.'); window.location.href='../index.php';</script>";
    exit();
}

$ownerEmail = mysqli_real_escape_string($conn, $_SESSION['user_email']);

// Handle Add Room Images
if (isset($_POST['addRoomImages'])) {

    if (!isset($_FILES['roomImages'])) {
        echo "<script>alert('No images selected!'); window.location.href='./your_rooms.php';</script>";
        exit();
    }

    $targetDir = "../assets/";
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    $uploadedFiles = [];

    // Ensure multiple files are properly handled
    foreach ($_FILES['roomImages']['tmp_name'] as $key => $tmpName) {
        if ($_FILES['roomImages']['error'][$key] === 0) {
            $fileExtension = pathinfo($_FILES['roomImages']['name'][$key], PATHINFO_EXTENSION);
            $newFileName = uniqid('room_', true) . '.' . $fileExtension;

            if (move_uploaded_file($tmpName, $targetDir . $newFileName)) {
                $uploadedFiles[] = $newFileName;
            }
        }
    }

    if (count($uploadedFiles) > 0) {
        // Fetch existing rooms_photo
        $fetchQuery = "SELECT rooms_photo FROM onwer_room_template WHERE email = '$ownerEmail' LIMIT 1";
        $fetchRes = mysqli_query($conn, $fetchQuery);

        if ($fetchRes && mysqli_num_rows($fetchRes) > 0) {
            $data = mysqli_fetch_assoc($fetchRes);

            $existingPhotos = !empty($data['rooms_photo']) ? $data['rooms_photo'] . ',' : '';
            $allPhotos = $existingPhotos . implode(',', $uploadedFiles);

            $updateQuery = "UPDATE onwer_room_template SET rooms_photo = '$allPhotos' WHERE email = '$ownerEmail'";
            mysqli_query($conn, $updateQuery);

            echo "<script>alert('Images uploaded successfully!'); window.location.href='./your_rooms.php';</script>";
        } else {
            echo "<script>alert('Template not found! Please create your template first.'); window.location.href='./onwer_dashboard.php';</script>";
        }
    } else {
        echo "<script>alert('No images uploaded! Please select at least one image.'); window.location.href='./your_rooms.php';</script>";
    }
}

// Handle Remove Room Images

if (isset($_POST['removeRoomImages']) && !empty($_POST['photosToDelete'])) {

    $photosToDelete = explode(',', $_POST['photosToDelete']);

    // Fetch existing rooms_photo
    $fetchQuery = "SELECT rooms_photo FROM onwer_room_template WHERE email = '$ownerEmail' LIMIT 1";
    $fetchRes = mysqli_query($conn, $fetchQuery);

    if ($fetchRes && mysqli_num_rows($fetchRes) > 0) {
        $data = mysqli_fetch_assoc($fetchRes);
        $existingPhotos = explode(',', $data['rooms_photo']);

        // Remove selected photos from the array
        $remainingPhotos = array_diff($existingPhotos, $photosToDelete);

        // Delete files from server
        foreach ($photosToDelete as $photo) {
            $filePath = "../assets/" . trim($photo);
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        // Update database
        $updatedPhotosStr = implode(',', $remainingPhotos);
        $updateQuery = "UPDATE onwer_room_template SET rooms_photo = '$updatedPhotosStr' WHERE email = '$ownerEmail'";
        mysqli_query($conn, $updateQuery);

        echo "<script>alert('Selected images deleted successfully!'); window.location.href='./your_rooms.php';</script>";
    } else {
        echo "<script>alert('No room images found for deletion.'); window.location.href='./your_rooms.php';</script>";
    }
}

// Handle Accept Request
if (isset($_POST['acceptRequest'])) {
    $requestId = intval($_POST['requestId']);
    
    // Fetch request details first
    $fetchQuery = "SELECT name, email, bulding_name, onwer_name FROM room_request WHERE id = $requestId";
    $fetchResult = mysqli_query($conn, $fetchQuery);
    $requestData = mysqli_fetch_assoc($fetchResult);
    
    $updateQuery = "UPDATE room_request SET status = 'accepted' WHERE id = $requestId";
    if (mysqli_query($conn, $updateQuery)) {
        // Send acceptance email
        $mail = new PHPMailer(true);
        try {
            $mail->SMTPDebug  = 0;
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'ankushdhupe2022@gmail.com';
            $mail->Password   = 'ampjxclunkvhplsc';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = 465;
            
            $mail->setFrom('ankushdhupe2022@gmail.com', 'PG Reliable');
            $mail->addAddress($requestData['email'], $requestData['name']);
            
            $mail->isHTML(true);
            $mail->Subject = 'Room Request Accepted - PG Reliable';
            $mail->Body = "
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                    <h2 style='color: #27ae60;'>Good News, {$requestData['name']}!</h2>
                    <p>Your room viewing request has been <strong style='color: #27ae60;'>ACCEPTED</strong>.</p>
                    
                    <div style='background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;'>
                        <h3 style='margin-top: 0;'>Request Details:</h3>
                        <p><strong>Property:</strong> {$requestData['bulding_name']}</p>
                        <p><strong>Owner:</strong> {$requestData['onwer_name']}</p>
                        <p><strong>Status:</strong> <span style='color: #27ae60;'>Accepted</span></p>
                    </div>
                    
                    <p>The property owner has approved your request. You may proceed with viewing the property at your earliest convenience.</p>
                    
                    <p style='color: #7f8c8d; font-size: 12px; margin-top: 30px;'>
                        This is an automated message from PG Reliable. Please do not reply to this email.
                    </p>
                </div>
            ";
            $mail->AltBody = "Hello {$requestData['name']}, Your room request for {$requestData['bulding_name']} has been ACCEPTED.";
            
            $mail->send();
            echo "<script>alert('Request accepted successfully! Notification email sent to the user.'); window.location.href='current_request.php';</script>";
        } catch (Exception $e) {
            echo "<script>alert('Request accepted successfully, but notification email could not be sent.'); window.location.href='current_request.php';</script>";
        }
    } else {
        echo "<script>alert('Failed to accept request. Please try again.');</script>";
    }
}

// Handle Reject Request
if (isset($_POST['rejectRequest'])) {
    $requestId = intval($_POST['requestId']);
    
    // Fetch request details first
    $fetchQuery = "SELECT name, email, bulding_name, onwer_name FROM room_request WHERE id = $requestId";
    $fetchResult = mysqli_query($conn, $fetchQuery);
    $requestData = mysqli_fetch_assoc($fetchResult);
    
    $updateQuery = "UPDATE room_request SET status = 'rejected' WHERE id = $requestId";
    if (mysqli_query($conn, $updateQuery)) {
        // Send rejection email
        $mail = new PHPMailer(true);
        try {
            $mail->SMTPDebug  = 0;
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'ankushdhupe2022@gmail.com';
            $mail->Password   = 'ampjxclunkvhplsc';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = 465;
            
            $mail->setFrom('ankushdhupe2022@gmail.com', 'PG Reliable');
            $mail->addAddress($requestData['email'], $requestData['name']);
            
            $mail->isHTML(true);
            $mail->Subject = 'Room Request Update - PG Reliable';
            $mail->Body = "
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                    <h2 style='color: #e74c3c;'>Request Status Update</h2>
                    <p>Dear {$requestData['name']},</p>
                    <p>We regret to inform you that your room viewing request has been <strong style='color: #e74c3c;'>declined</strong>.</p>
                    
                    <div style='background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;'>
                        <h3 style='margin-top: 0;'>Request Details:</h3>
                        <p><strong>Property:</strong> {$requestData['bulding_name']}</p>
                        <p><strong>Owner:</strong> {$requestData['onwer_name']}</p>
                        <p><strong>Status:</strong> <span style='color: #e74c3c;'>Declined</span></p>
                    </div>
                    
                    <p>We encourage you to explore other available properties on our platform that may suit your requirements.</p>
                    
                    <p>Thank you for using PG Reliable.</p>
                    
                    <p style='color: #7f8c8d; font-size: 12px; margin-top: 30px;'>
                        This is an automated message from PG Reliable. Please do not reply to this email.
                    </p>
                </div>
            ";
            $mail->AltBody = "Hello {$requestData['name']}, Your room request for {$requestData['bulding_name']} has been declined.";
            
            $mail->send();
            echo "<script>alert('Request declined. Notification email sent to the user.'); window.location.href='current_request.php';</script>";
        } catch (Exception $e) {
            echo "<script>alert('Request declined, but notification email could not be sent.'); window.location.href='current_request.php';</script>";
        }
    } else {
        echo "<script>alert('Failed to decline request. Please try again.');</script>";
    }
}







































?>
