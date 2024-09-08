<?php
// Include the configuration file
@include '../config.php';

// Start the session
session_start();

// Check if the user is logged in and is a tutor
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'tutor') {
    header('Location: ../login/login.php');
    exit();
}

// Retrieve the tutor ID from the URL query parameter
if (isset($_GET['tutor_Id'])) {
    $tutor_id = $_GET['tutor_Id'];
} else {
    header('Location: tutor-dashboard.php');
    exit();
}

// Fetch tutor details from the database
$query = "SELECT * FROM tutor WHERE tutor_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $tutor_id);
$stmt->execute();
$tutor_result = $stmt->get_result();

// Check if the tutor exists
if ($tutor_result->num_rows > 0) {
    $tutor = $tutor_result->fetch_assoc();
} else {
    header('Location: tutor-dashboard.php'); // Redirect if tutor not found
    exit();
}

// Handle form submission to update tutor profile
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $contact = $_POST['contact'];
    $experience = $_POST['experience'];
    $tutoring_days = $_POST['tutoring_days'];
    $tutoring_levels = $_POST['tutoring_levels'];
    $tutoring_courses = $_POST['tutoring_courses'];
    $availability = $_POST['availability'];
    $timeslots = isset($_POST['timeslots']) ? implode(',', $_POST['timeslots']) : '';// New timeslots field
    $hourly_pay = $_POST['hourly_rate']; // New hourly_pay field

    // Handle profile picture upload
    if (!empty($_FILES['profile_pic']['name'])) {
        $profile_pic = time() . '_' . basename($_FILES['profile_pic']['name']);
        $target_dir = "../uploads/";
        $target_file = $target_dir . $profile_pic;

        if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $target_file)) {
            // Optionally delete the old profile picture here
        } else {
            $profile_pic = $tutor['profile_pic']; // Use existing profile picture if upload fails
        }
    } else {
        $profile_pic = $tutor['profile_pic']; // Use existing profile picture if no new one is uploaded
    }

    // Handle CV upload
    if (!empty($_FILES['cv']['name'])) {
        $cv = time() . '_' . basename($_FILES['cv']['name']);
        $target_file = $target_dir . $cv;

        if (move_uploaded_file($_FILES['cv']['tmp_name'], $target_file)) {
            // Optionally delete the old CV here
        } else {
            $cv = $tutor['cv']; // Use existing CV if upload fails
        }
    } else {
        $cv = $tutor['cv']; // Use existing CV if no new one is uploaded
    }

    // Prepare the update query
    $update_query = "UPDATE tutor SET Fname = ?, email = ?, address = ?, contact = ?, experience = ?, tutoring_days = ?, tutoring_levels = ?, tutoring_courses = ?, availability = ?, timeslots = ?, hourly_rate = ?, profile_pic = ?, cv = ? WHERE tutor_id = ?";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param('ssssssssssdssi', $name, $email, $address, $contact, $experience, $tutoring_days, $tutoring_levels, $tutoring_courses, $availability, $timeslots, $hourly_pay, $profile_pic, $cv, $tutor_id);

    // Execute the update query
    if ($update_stmt->execute()) {
        header('Location: tutor-dashboard.php'); // Redirect after successful update
        exit();
    } else {
        echo "Error updating profile.";
    }

    // Close the statement
    $update_stmt->close();
}

// Close the database connection
$conn->close();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../assets/logo.png" type="image/x-icon" />
    <title>Edit Profile</title>
    <link rel="stylesheet" href="../login/login.css">
    <style>
        body {
            margin: 20px;
        }

        .popup form {
            max-width: 500px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f7f7f7;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.4);
            display: grid;
            grid-template-columns: 1fr;
            gap: 10px;
        }

        .submit-btn {
            background-color: #006400;
            width: 40%;
            padding: 10px;
            color: white;
            margin: 10px auto;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            grid-column: span 2;
        }

        /* Two columns on larger screens */
        @media (min-width: 768px) {
            .popup form {
                grid-template-columns: 1fr 1fr;
            }
        }

        .popup h2 {
            text-align: center;
            margin-bottom: 10px;
            grid-column: span 2;
        }

        .popup label {
            display: block;
            font-weight: bold;
            margin-bottom: 8px;
        }

        .popup input,
        .popup select {
            width: 100%;
            padding: 6px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .popup button:hover {
            background-color: #ff8c00;
        }

        /* Full width for certain elements */
        .popup form h2,
        .popup form button {
            grid-column: span 2;
        }

        /* Make cancel button float to the right of the title */
        h2 button {
            width: 30px;
            height: 30px;
            font-size: 20px;
            font-weight: 700;
            background-color: black;
            padding: 0;
            color: white;
        }

        h2 button:hover {
            background-color: #ff8c00;
        }
    </style>
</head>

<body>
    <div class="popup" id="popupForm">
        <form action="" method="POST" enctype="multipart/form-data">
            <button type="button" onclick="cancel()" style="width: 34px; height:34px; margin-left: 420px; font-size:20px; font-weight:700; background-color:black; padding: 0px; color:white; border-radius:4px;">X</button>
            <h2>Edit Profile</h2>

            <label for="name">Name:</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($tutor['Fname']); ?>" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($tutor['email']); ?>" required>

            <label for="address">Address:</label>
            <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($tutor['address']); ?>" required>

            <label for="contact">Contact:</label>
            <input type="text" id="contact" name="contact" value="<?php echo htmlspecialchars($tutor['contact']); ?>" required>

            <label for="experience">Experience:</label>
            <input type="text" id="experience" name="experience" value="<?php echo htmlspecialchars($tutor['experience']); ?>" required>

            <label for="tutoring_days">Tutoring Days:</label>
            <input type="text" id="tutoring_days" name="tutoring_days" value="<?php echo htmlspecialchars($tutor['tutoring_days']); ?>" required>

            <label for="tutoring_levels">Levels:</label>
            <input type="text" id="tutoring_levels" name="tutoring_levels" value="<?php echo htmlspecialchars($tutor['tutoring_levels']); ?>" required>

            <label for="tutoring_courses">Courses:</label>
            <input type="text" id="tutoring_courses" name="tutoring_courses" value="<?php echo htmlspecialchars($tutor['tutoring_courses']); ?>" required>

            <label for="availability">Availability:</label>
            <input type="text" id="availability" name="availability" value="<?php echo htmlspecialchars($tutor['availability']); ?>" required>

            <label for="timeslots">Timeslots:</label>
            <!-- Timeslots -->
            <div class="dropdown-container">
                    <div class="dropdown">
                        <button type="button" onclick="toggleDropdown(this)">Available Timeslots</button>
                        <div class="dropdown-content">
                            <label><input type="checkbox" value="8:00 AM - 10:00 AM" name="timeslots[]"> 8:00 AM - 10:00 AM</label>
                            <label><input type="checkbox" value="10:00 AM - 12:00 PM" name="timeslots[]"> 10:00 AM - 12:00 PM</label>
                            <label><input type="checkbox" value="12:00 PM - 2:00 PM" name="timeslots[]"> 12:00 PM - 2:00 PM</label>
                            <label><input type="checkbox" value="2:00 PM - 4:00 PM" name="timeslots[]"> 2:00 PM - 4:00 PM</label>
                            <label><input type="checkbox" value="4:00 PM - 6:00 PM" name="timeslots[]"> 4:00 PM - 6:00 PM</label>
                            <label><input type="checkbox" value="6:00 PM - 8:00 PM" name="timeslots[]"> 6:00 PM - 8:00 PM</label>
                        </div>
                    </div>
                </div>
                
            <label for="hourly_pay">Hourly Pay:</label>
            <input type="number" id="hourly_pay" name="hourly_rate" value="<?php echo htmlspecialchars($tutor['hourly_rate']); ?>" required step="0.01">

            <label for="profile_pic">Profile Picture:</label>
            <input type="file" id="profile_pic" name="profile_pic" accept="image/*">

            <label for="cv">CV:</label>
            <input type="file" id="cv" name="cv" accept=".pdf,.doc,.docx">

            <button type="submit" class="submit-btn">Update Profile</button>
        </form>

    </div>

    <script>
        function cancel() {
            window.location.href = 'tutor-dashboard.php';
        }
    </script>
</body>

</html>