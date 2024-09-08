<?php
include '../config.php';

if (isset($_GET['assignmentID'])) {
    $assignmentID = $_GET['assignmentID'];

    // Fetch the current assignment data
    $sql = "SELECT * FROM assignments WHERE assignmentID = '$assignmentID'";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $assignment = $result->fetch_assoc();
    } else {
        echo "Assignment not found.";
        exit();
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $title = $_POST['title'];
        $description = $_POST['description'];
        $course_Id = $_POST['course'];

        // Handle file upload
        $uploadDir = 'uploads/assignments/';
        $fileName = basename($_FILES['assignment_file']['name']);
        $uploadFilePath = $uploadDir . $fileName;

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        if (move_uploaded_file($_FILES['assignment_file']['tmp_name'], $uploadFilePath)) {
            // Update the assignment in the database
            $sql = "UPDATE assignments SET title = ?, description = ?, assignment_file = ?, course_Id = ? WHERE assignmentID = ?";

            $stmt = $conn->prepare($sql);
            if ($stmt) {
                $stmt->bind_param('sssii', $title, $description, $uploadFilePath, $course_Id, $assignmentID);
                if ($stmt->execute()) {
                    echo "Assignment updated successfully!";
                    header("Location: ../tutors/tutor-dashboard.php?section=5");
                } else {
                    echo "Error: " . $stmt->error;
                }
                $stmt->close();
            } else {
                echo "Error: " . $conn->error;
            }
        } else {
            echo "File upload failed.";
        }

        $conn->close();
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../assets/logo.png" type="image/x-icon"/>
    <title>Edit Assignment</title>
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
        <h2 class="my-4">Edit Assignment</h2>
        <form action="" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" class="form-control" id="title" name="title" value="<?php echo $assignment['title']; ?>" required>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea class="form-control" id="description" name="description" rows="4"><?php echo $assignment['description']; ?></textarea>
            </div>
            <div class="form-group">
                <label for="course">Course</label><br>
                <select id="course" name="course" required class="form-control">
                    <?php
                    $courses_query = "SELECT * FROM courses";
                    $courses_result = $conn->query($courses_query);
                    while ($row = $courses_result->fetch_assoc()) {
                        $selected = ($row['course_Id'] == $assignment['course_Id']) ? 'selected' : '';
                        echo "<option value='" . $row['course_Id'] . "' $selected>" . $row['course_name'] . "</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="file">Assignment File</label>
                <input type="file" class="form-control-2" id="file" name="assignment_file">
            </div>
            <input type="submit" class="btn-submit">
        </form>
    </div>
</body>
</html>
