<?php
// Include the configuration file
@include '../config.php';

// Start the session
session_start();

// Check if the user is logged in and is a student
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header('Location: ../login/login.php');
    exit();
}

// Retrieve the student ID from the URL query parameter
if (isset($_GET['student_Id'])) {
    $student_id = $_GET['student_Id'];
} else {
    // If no student ID is found in the URL, redirect to the student dashboard
    header('Location: student-dashboard.php');
    exit();
}

// Fetch student details from the database
$query = "SELECT * FROM student WHERE student_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $student_id);
$stmt->execute();
$student_result = $stmt->get_result();

// Check if the student exists
if ($student_result->num_rows > 0) {
    $student = $student_result->fetch_assoc();
} else {
    header('Location: student-dashboard.php'); // Redirect if student not found
    exit();
}

// Handle form submission to update student profile
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $contact = $_POST['contact'];
    $age = $_POST['age'];

    $update_query = "UPDATE student SET Fname = ?, email = ?, address = ?, contact = ?, age = ? WHERE student_id = ?";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param('ssssii', $name, $email, $address, $contact, $age, $student_id);

    if ($update_stmt->execute()) {
        header('Location: student-dashboard.php'); // Redirect after successful update
        exit();
    } else {
        echo "Error updating profile.";
    }
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../assets/logo.png" type="image/x-icon"/>
    <title>Edit Profile</title>
    <link rel="stylesheet" href="../login/login.css">
    <style>
        body {
            margin: 40px;
        }

        .popup form {
            max-width: 400px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f7f7f7;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.4);
        }

        .popup h1 {
            text-align: center;
            margin-bottom: 10px;
        }

        .popup label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        .popup input,
        .popup select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .popup button {
            width: 100%;
            padding: 10px;
            background-color: #006400;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }

        .popup button:hover {
            background-color: #ff8c00;
        }
    </style>
</head>

<body>
    <div class="popup" id="popupForm">
        <form action="" method="POST">
            <button type="button" onclick="cancel()" style="width: 34px; height:34px; margin-left: 330px; font-size:20px; font-weight:700; background-color:black; padding: 0px;">X</button>
            <h1>Edit Profile</h1>

            <label for="name">Name:</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($student['Fname']); ?>" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($student['email']); ?>" required>

            <label for="address">Address:</label>
            <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($student['address']); ?>" required>

            <label for="contact">Contact:</label>
            <input type="text" id="contact" name="contact" value="<?php echo htmlspecialchars($student['contact']); ?>" required>

            <label for="age">Age:</label>
            <input type="number" id="age" name="age" value="<?php echo htmlspecialchars($student['age']); ?>" required>

            <button type="submit">Update Profile</button>
        </form>
    </div>
</body>

<script>
    function cancel() {
        window.location.href = 'student-dashboard.php';
    }
</script>

</html>
