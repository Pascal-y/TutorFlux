<?php
@include '../config.php';
session_start();

// Check if the user is logged in and if the role is 'student'
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header('Location: ../login/login.php');
    exit();
}

// Get student information from session
$student_id = $_SESSION['student_id']; // Correct session variable for student ID
$student_name = $_SESSION['username'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = $_SESSION['student_id']; // Ensure correct session variable is used
    $tutor_id = $_POST['tutor_id'];
    $message = mysqli_real_escape_string($conn, $_POST['message']);

    // Insert message into the database
    $insert_query = "
        INSERT INTO chats (message, sender, receiver) 
        VALUES ('$message', '$student_id', '$tutor_id')";

    if (mysqli_query($conn, $insert_query)) {
        echo 'success';
    } else {
        echo 'error';
    }

    mysqli_close($conn);
}

?>