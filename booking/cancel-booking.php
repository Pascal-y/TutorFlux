<?php
@include '../config.php';
session_start();

// Check if the user is logged in and if the role is 'student'
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header('Location: ../login/login.php');
    exit();
}

// Get the booking ID from the request
if (isset($_GET['id'])) {
    $bookingId = intval($_GET['id']);

    // Update the booking status to 3 (Cancelled)
    $updateQuery = "UPDATE booking SET status = 3 WHERE booking_Id = $bookingId";
    if (mysqli_query($conn, $updateQuery)) {
        echo "Booking cancelled successfully.";
    } else {
        echo "Error cancelling booking: " . mysqli_error($conn);
    }

    mysqli_close($conn);
}
?>
