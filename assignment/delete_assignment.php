<?php
include '../config.php';

if (isset($_GET['assignmentID'])) {
    $assignmentID = $_GET['assignmentID'];

    $sql = "DELETE FROM assignments WHERE assignmentID = '$assignmentID'";

    if ($conn->query($sql) === TRUE) {
        echo "Assignment deleted successfully!";
    } else {
        echo "Error deleting assignment: " . $conn->error;
    }

    $conn->close();
    header("Location: ../tutors/tutor-dashboard.php?section=5");
    exit();
}
?>
