<?php
@include '../config.php';
session_start();

// Check if the user is logged in and if the role is 'tutor'
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'tutor') {
    header('Location: ../login/login.php');
    exit();
}

// Get the booking ID from the query string
if (isset($_GET['booking_id'])) {
    $bookingId = $_GET['booking_id'];

    // Update the booking status to 'Approved'
    $updateQuery = "UPDATE booking SET status = 1 WHERE booking_Id = '$bookingId'";
    if (mysqli_query($conn, $updateQuery)) {
        // Redirect back to the dashboard with a success message
        header('Location: ../tutors/tutor-dashboard.php?section=2&message=Booking approved successfully');
    } else {
        // Redirect back to the dashboard with an error message
        header('Location: ../tutors/tutor-dashboard.php?section=2&message=Failed to approve booking');
    }
} else {
    // Redirect back to the dashboard if no booking ID is provided
    header('Location: ../tutors/tutor-dashboard.php?section=2');
}

mysqli_close($conn);
?>
