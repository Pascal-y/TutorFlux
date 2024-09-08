<?php
@include '../config.php';
session_start();

// Check if the user is logged in and if the role is 'tutor'
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'tutor') {
    header('Location: ../login/login.php');
    exit();
}


// Get tutor information from session
$tutor_username = $_SESSION['username'];


// Fetch tutor details from the database
$tutor_query = "SELECT * FROM tutor WHERE Fname = '$tutor_username'";
$tutor_result = mysqli_query($conn, $tutor_query);

if (mysqli_num_rows($tutor_result) > 0) {
    $tutor_row = mysqli_fetch_assoc($tutor_result);
    $tutor_id = $tutor_row['tutor_Id'];
    $tutor_name = $tutor_row['Fname'];
    $profile_picture = $tutor_row['profile_pic'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'], $_POST['student_id'])) {
    $message = mysqli_real_escape_string($conn, $_POST['message']);
    $student_id = $_POST['student_id'];

    // Insert the chat message into the database
    $insert_query = "INSERT INTO chats (sender, receiver, message, sent_time) VALUES ('$tutor_id', '$student_id', '$message', NOW())";
    $insert_result = mysqli_query($conn, $insert_query);

    if ($insert_result) {
        echo 'success';
    } else {
        echo 'error';
    }

    mysqli_close($conn);
}
?>
