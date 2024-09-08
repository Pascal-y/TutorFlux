<?php
include '../config.php'; // Include database connection

// Start the session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in and if the role is 'tutor'
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'tutor') {
    header('Location: ../login/login.php');
    exit();
}

// Get tutor information from session
$tutor_username = $_SESSION['username'];

// Fetch tutor details from the database
$tutor_query = "SELECT * FROM tutor WHERE Fname = ?";
$stmt = $conn->prepare($tutor_query);
$stmt->bind_param('s', $tutor_username);
$stmt->execute();
$tutor_result = $stmt->get_result();

if ($tutor_result->num_rows > 0) {
    $tutor_row = $tutor_result->fetch_assoc();
    $tutor_Id = $tutor_row['tutor_Id'];
    $tutor_name = $tutor_row['Fname'];
} else {
    // Handle case where tutor is not found
    echo "Tutor not found.";
    exit();
}


// Fetch courses from the database
$courses_query = "SELECT * FROM courses";
$courses_result = $conn->query($courses_query);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $title = $_POST['title'];
    $description = $_POST['description'];
    $course_Id = $_POST['course'];

    // Handle file upload
    $uploadDir = 'uploads/assignments/'; // Directory where files will be stored
    $fileName = basename($_FILES['assignment_file']['name']); // Get the file name
    $uploadFilePath = $uploadDir . $fileName; // Full file path

    // Check if the directory exists, if not, create it
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true); // Creates the directory with the specified permissions
    }

    // Check if the file was uploaded successfully
    if (move_uploaded_file($_FILES['assignment_file']['tmp_name'], $uploadFilePath)) {
        // Prepare SQL statement to insert data into the assignments table
        $sql = "INSERT INTO assignments (title, description, assignment_file, course_Id, tutor_Id)
            VALUES (?, ?, ?, ?, ?)";

        // Prepare the statement
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            // Bind parameters
            $stmt->bind_param('sssii', $title, $description, $uploadFilePath, $course_Id, $tutor_Id);

            // Execute the statement
            if ($stmt->execute()) {
                echo "New assignment created successfully!";
                header("../tutors/tutor-dashboard.php?section=5#");
            } else {
                echo "Error: " . $stmt->error;
            }

            // Close the statement
            $stmt->close();
        } else {
            echo "Error: " . $conn->error;
        }
    } else {
        echo "File upload failed.";
    }


    // Close the database connection
    $conn->close();

    // Redirect to the assignments view page
    header("Location: ../tutors/tutor-dashboard.php?section=5");
    exit(); // Ensure no further code is executed after the redirect
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../assets/logo.png" type="image/x-icon"/>
    <title>Create Assignment</title>
    <style>
        .create-container {
            max-width: 490px;
            margin: 120px auto;
            padding: 20px 20px;
            width: 100%;
            background: #fff;
            border-radius: 5px;
            box-shadow: 0px 4px 10px 1px rgba(0, 0, 0, 0.4);
            align-items: center;
        }

        h2 {
            margin-bottom: 40px;
        }

        .btn-submit {
            color: white;
            background-color: #006400;
            width: 100%;
            font-size: 24px;
            font-weight: 700;
            border: none;
            border-radius: 4px;
            height: 40px;
            margin-top: 20px;
        }

        .btn-submit:hover {
            background-color: #ff8c00;
        }

        .form-control {
            width: 100%;
            height: 36px;
            border-radius: 4px;
            border: 1px solid gray;
            padding: 0px 6px;
        }

        .form-control-2 {
            width: 100%;
            height: 30px;
            border-radius: 4px;
            border: none;
        }

        .form-group{
            margin-top: 10px;
        }

        .form-control:hover {
            border: 1px solid #28A745;
            box-shadow: 0px 4px 10px 1px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <div class="create-container">
        <h2 class="my-4">Create Assignment</h2>
        <form action="../assignment/process_assignment.php" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" class="form-control" id="title" name="title" required>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea class="form-control" id="description" name="description" rows="4"></textarea>
            </div>
            
            <div class="form-group">
                <label for="course">Course</label><br>
                <select id="course" name="course" required class="form-control">
                    <?php while ($row = $courses_result->fetch_assoc()) : ?>
                        <option value="<?php echo $row['course_Id']; ?>"><?php echo $row['course_name']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="file">Assignment File</label>
                <input type="file" class="form-control-2" id="file" name="assignment_file" required>
            </div>
            <input type="submit" class="btn-submit">
        </form>
    </div>
</body>
</html>
