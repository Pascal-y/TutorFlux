<?php
@include '../config.php';
session_start();

// Check if the user is logged in and if the role is 'student'
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header('Location: ../login/login.php');
    exit();
}

// Check if the booking ID is passed in the URL
if (isset($_GET['id'])) {
    $booking_id = $_GET['id'];

    // Delete the booking from the booking table
    $delete_query = "DELETE FROM booking WHERE booking_Id = '$booking_id'";

    if (mysqli_query($conn, $delete_query)) {
        echo "Booking deleted successfully.";
    } else {
        echo "Error deleting booking: " . mysqli_error($conn);
    }

    // Close the database connection
    mysqli_close($conn);

    // Redirect back to the dashboard
    header('Location: ../student/student-dashboard.php?section=section2#');
    exit();
} else {
    echo "Invalid request.";
}
?>
