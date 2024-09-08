<?php 
include '../config.php'; 

$submissionID = $_GET['submissionID'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $submissionID = $_POST['submissionID'];
    $grade = floatval($_POST['grade']); // Sanitize and cast the grade to float

    // Prepare the SQL statement with placeholders to prevent SQL injection
    $sql = "UPDATE submissions SET grade = ? WHERE submissionID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("di", $grade, $submissionID);

    if ($stmt->execute()) {
        // Redirect to the tutor dashboard or another relevant page
        header("Location: ../tutors/tutor-dashboard.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../assets/logo.png" type="image/x-icon"/>
    <title>Grade Assignment</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h2 class="my-4">Grade Assignment</h2>
        <form action="grade_assignment.php" method="post">
            <div class="form-group">
                <label for="grade">Enter Grade (as a float):</label>
                <input type="text" class="form-control" id="grade" name="grade" required>
            </div>
            <!-- Hidden input field to store submissionID -->
            <input type="hidden" name="submissionID" value="<?php echo htmlspecialchars($_GET['submissionID']); ?>">
            <button type="submit" class="btn btn-primary">Submit Grade</button>
        </form>
    </div>
</body>
</html>
