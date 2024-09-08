<?php
@include '../config.php';
session_start();

// Check if the user is logged in and if the role is 'student'
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header('Location: ../login/login.php');
    exit();
}

// Get student information from session
$student_username = $_SESSION['username'];

// Fetch student details from the database
$student_query = "SELECT * FROM student WHERE Fname = '$student_username'";
$student_result = mysqli_query($conn, $student_query);

if (mysqli_num_rows($student_result) > 0) {
    $student_row = mysqli_fetch_assoc($student_result);
    $student_id = $student_row['student_Id'];
    $student_name = $student_row['Fname'];
    $profile_picture = $student_row['profile_pic'];
    $student_email = $student_row['email'];
    $student_address = $student_row['address'];
    $student_contact = $student_row['contact'];
    $student_age = $student_row['age'];
    $student_pass = $student_row['pass'];
    $student_created = $student_row['created_date'];
} else {
    $student_name = 'Unknown';
    $profile_picture = '../assets/avatar.png'; // Default image if no profile picture is set
}


mysqli_close($conn);

// Handle different sections
$section = isset($_GET['section']) ? $_GET['section'] : 'dashboard';

// Create the image URL based on the file name stored in the database
$imageUrl = "../uploads/" . htmlspecialchars($profile_picture);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../assets/logo.png" type="image/x-icon"/>
    <link rel="stylesheet" href="../navbar/navbar.css">
    <title>Courses</title>
    <style>
        .course-container{
            margin: 80px 2000px;
            align-items: center;

        }
        h6{
                padding: 200px 700px;
            }
    </style>
</head>
<body>
    <?php require"../navbar/navbar.php"; ?>
    <h6>No courses Created yet</h6>
    <div class="course-container">

    </div>
    


    <?php require"../footer/footer.php"; ?>
</body>
</html>