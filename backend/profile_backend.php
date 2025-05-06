<?php
session_start();
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['freelancer_id'])) {
    header("Location: ../loginfreelancer.php");
    exit();
}

// Fetch freelancer data
$freelancer_id = $_SESSION['freelancer_id'];
$sql = "SELECT * FROM freelancers WHERE id = $freelancer_id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $freelancer = $result->fetch_assoc();
} else {
    die("Freelancer not found");
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update personal information
    if (isset($_POST['update_personal'])) {
        $full_name = $conn->real_escape_string($_POST['full_name']);
        $username = $conn->real_escape_string($_POST['username']);
        $email = $conn->real_escape_string($_POST['email']);
        $phone_number = $conn->real_escape_string($_POST['phone_number']);
        $id_number = $conn->real_escape_string($_POST['id_number']);
        $birth_date = $conn->real_escape_string($_POST['birth_date']);
        $gender = $conn->real_escape_string($_POST['gender']);
        
        // Handle profile picture upload
        $profile_picture = $freelancer['profile_picture'];
        if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = 'uploads/profile_pictures/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $file_ext = pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION);
            $filename = 'profile_' . $freelancer_id . '_' . time() . '.' . $file_ext;
            $destination =  $upload_dir . $filename;
            
            if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $destination)) {
                // Delete old picture if it's not the default
                if ($profile_picture !== 'image/default-profile.jpg') {
                    @unlink($profile_picture);
                }
                $profile_picture = $destination;
            }
        }
        
        $update_sql = "UPDATE freelancers SET 
                      full_name = '$full_name',
                      username = '$username',
                      email = '$email',
                      phone_number = '$phone_number',
                      id_number = '$id_number',
                      birth_date = '$birth_date',
                      gender = '$gender',
                      profile_picture = 'backend/$profile_picture'
                      WHERE id = $freelancer_id";
        
        if ($conn->query($update_sql)) {
            $_SESSION['success'] = "Personal information updated successfully";
        } else {
            $_SESSION['error'] = "Error updating personal information: " . $conn->error;
        }
    }
    
    // Update professional information
    if (isset($_POST['update_professional'])) {
        $job_title = $conn->real_escape_string($_POST['job_title']);
        $experience_level = $conn->real_escape_string($_POST['experience_level']);
        $job_category = $conn->real_escape_string($_POST['job_category']);
        $skills = $conn->real_escape_string($_POST['skills']);
        $expected_salary = (float)$_POST['expected_salary'];
        $english_level = $conn->real_escape_string($_POST['english_level']);
        $availability = isset($_POST['availability']) ? 1 : 0;
        $bio = $conn->real_escape_string($_POST['bio']);
        
        $update_sql = "UPDATE freelancers SET 
                      job_title = '$job_title',
                      experience_level = '$experience_level',
                      job_category = '$job_category',
                      skills = '$skills',
                      expected_salary = $expected_salary,
                      english_level = '$english_level',
                      availability = $availability,
                      bio = '$bio'
                      WHERE id = $freelancer_id";
        
        if ($conn->query($update_sql)) {
            $_SESSION['success'] = "Professional information updated successfully";
        } else {
            $_SESSION['error'] = "Error updating professional information: " . $conn->error;
        }
    }
    
    // Update address information
    if (isset($_POST['update_address'])) {
        $region = $conn->real_escape_string($_POST['region']);
        $address = $conn->real_escape_string($_POST['address']);
        $postal_code = $conn->real_escape_string($_POST['postal_code']);
        $bank_account = $conn->real_escape_string($_POST['bank_account']);
        
        $update_sql = "UPDATE freelancers SET 
                      region = '$region',
                      address = '$address',
                      postal_code = '$postal_code',
                      bank_account = '$bank_account'
                      WHERE id = $freelancer_id";
        
        if ($conn->query($update_sql)) {
            $_SESSION['success'] = "Address information updated successfully";
        } else {
            $_SESSION['error'] = "Error updating address information: " . $conn->error;
        }
    }
    
    // Update documents and job category (legacy - can be removed if not used)
    if (isset($_POST['update_documents'])) {
        $document_type = $conn->real_escape_string($_POST['document_type']);
        $job_category = $conn->real_escape_string($_POST['job_category']);
        
        $update_sql = "UPDATE freelancers SET 
                      document_type = '$document_type',
                      job_category = '$job_category'
                      WHERE id = $freelancer_id";
        
        if ($conn->query($update_sql)) {
            $_SESSION['success'] = "Documents and job category updated successfully";
        } else {
            $_SESSION['error'] = "Error updating documents: " . $conn->error;
        }
    }
    
    // Redirect to avoid form resubmission
    header("Location: ../ProfileFreelancer.php");
    exit();
}

$conn->close();
?>