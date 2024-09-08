<?php
include '../config.php';

$message = "";  // Initialize message variable

if (isset($_GET['assignmentID'])) {
    $assignmentID = $_GET['assignmentID'];

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $studentID = $_POST['studentID'];
        $due_date = $_POST['due_date'] ?? '';

        // Check if the assignment is already assigned to the student
        $check_sql = "SELECT * FROM assign WHERE student_Id = '$studentID' AND assignmentID = '$assignmentID'";
        $check_result = $conn->query($check_sql);

        if ($check_result->num_rows > 0) {
            $message = "Assignment already assigned to this student!";
        } else {
            // Insert the assignment if not already assigned
            $sql = "INSERT INTO assign (student_Id, assignmentID, due_date) VALUES ('$studentID', '$assignmentID', '$due_date')";

            if ($conn->query($sql) === TRUE) {
                $message = "Assignment successfully assigned to the student!";
            } else {
                $message = "Error: " . $conn->error;
            }
        }
    }

    $students_sql = "SELECT * FROM student";
    $students_result = $conn->query($students_sql);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Assignment</title>
    <link rel="shortcut icon" href="../assets/logo.png" type="image/x-icon"/>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .container {
            max-width: 490px;
            margin: 150px auto;
            padding: 20px;
            background: #fff;
            border-radius: 5px;
            box-shadow: 0px 4px 10px 1px rgba(0, 0, 0, 0.4);
        }
        h2 {
            margin-bottom: 20px;
        }
        .message {
            display: block;
            margin-bottom: 20px;
            font-size: 16px;
            color: #dc3545;
        }
        .message.success {
            color: #28a745;
        }
        .btn-submit {
            color: white;
            background-color: #006400;
            width: 100%;
            font-size: 24px;
            font-weight: 700;
            border: none;
            border-radius: 4px;
        }
        .btn-submit:hover {
            background-color: #ff8c00;
        }
    </style>
</head>
<body>
    <div class="container">
    <button type="button" onclick="cancel()" style="width: 34px; height:34px; margin-left: 420px; font-size:20px; font-weight:700; background-color:black; padding: 0px; color:white; border-radius:4px;">X</button>
        <h2>Assign Home Work to Student</h2>
        <span class="message <?php echo $message === 'Assignment successfully assigned to the student!' ? 'success' : ''; ?>">
            <?php echo $message; ?>
        </span>
        <form action="" method="post">
            <div class="form-group">
                <label for="studentID">Select Student:</label>
                <select name="studentID" id="studentID" class="form-control" required>
                    <?php
                    if ($students_result->num_rows > 0) {
                        while ($student = $students_result->fetch_assoc()) {
                            echo "<option value='" . $student['student_Id'] . "'>" . $student['Fname'] . "</option>";
                        }
                    } else {
                        echo "<option>No students found</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="due_date">Date Due:</label>
                <input type="date" id="due_date" name="due_date" class="form-control">
            </div>
            <input type="submit" class="btn-submit">
        </form>
    </div>
</body>

<script>
        function cancel() {
            window.location.href = '../tutors/tutor-dashboard.php';
        }
    </script>
</html>
