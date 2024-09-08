<?php
include '../config.php';

session_start();

// Check if the user is logged in and if the role is 'student'
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header('Location: ../login/login.php');
    exit();
}

// Get student information from session
$student_username = $_SESSION['username'];

// Fetch student details from the database
$student_query = "SELECT student_Id FROM student WHERE Fname = ?";
$stmt = $conn->prepare($student_query);
$stmt->bind_param("s", $student_username);
$stmt->execute();
$student_result = $stmt->get_result();

if ($student_result->num_rows > 0) {
    $student_row = $student_result->fetch_assoc();
    $student_id = $student_row['student_Id'];
} else {
    echo "Student not found.";
    exit();
}

// Check if assignmentID is present in GET request
if (isset($_GET['assignmentID'])) {
    $assignmentID = $_GET['assignmentID'];
} else {
    echo "Assignment ID not found.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $submissionFile = $_FILES['submissionFile']['name'];

    // Define the submission directory
    $submissionDir = 'uploads/submissions/';

    // Check if the directory exists, if not, create it
    if (!is_dir($submissionDir)) {
        mkdir($submissionDir, 0777, true);
    }

    // Move the uploaded file to the designated folder
    $targetFilePath = $submissionDir . basename($submissionFile);
    if (move_uploaded_file($_FILES['submissionFile']['tmp_name'], $targetFilePath)) {
        // Insert the submission details into the database
        $sql = "INSERT INTO submissions (assignmentID, submittedBy, submissionFile, status)
                VALUES (?, ?, ?, 1)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iis", $assignmentID, $student_id, $submissionFile);

        if ($stmt->execute()) {
            echo "Assignment submitted successfully!";
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();

        // Redirect to student dashboard
        header("Location: ../student/student-dashboard.php");
        exit();
    } else {
        echo "Error uploading the file.";
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../assets/logo.png" type="image/x-icon"/>
    <title>Submit Assignment</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            margin: 190px auto;
        }
        .form-control {
            border: none;
        }
        .container {
            max-width: 600px;
        }
        .btn-submit {
            border-radius: 4px;
            background-color: #006400;
            color: white;
            margin: 10px;
        }
        @media (max-width: 768px) {
            .container {
                width: auto;
                margin: auto 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="my-4">Submit Assignment</h2>
        <form action="submit_assignment.php?assignmentID=<?php echo htmlspecialchars($assignmentID); ?>" method="post" enctype="multipart/form-data">
            <input type="hidden" name="assignmentID" value="<?php echo htmlspecialchars($assignmentID); ?>">
            <div class="form-group">
                <input type="file" class="form-control" id="submissionFile" name="submissionFile" required>
            </div>
            <button type="submit" class="btn btn-submit">Submit</button>
        </form>
    </div>
</body>
</html>
