<?php
@include '../config.php';

// Initialize the $error array
$error = [];

if (isset($_POST['submit'])) {
    // Escape user inputs to prevent SQL injection
    $Fname = mysqli_real_escape_string($conn, $_POST['Fname']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $contact = mysqli_real_escape_string($conn, $_POST['contact']);
    $experience = (int)$_POST['experience']; // Ensure experience is an integer

    // Handling file uploads
    $profile_pic = isset($_FILES['profile_pic']['name']) ? $_FILES['profile_pic']['name'] : null;
    $cv = isset($_FILES['cv']['name']) ? $_FILES['cv']['name'] : null;

    // Password handling
    $pass = mysqli_real_escape_string($conn, $_POST['password']);
    $cpass = mysqli_real_escape_string($conn, $_POST['cpassword']);

    // Handling multiple checkbox values
    $tutoring_days = isset($_POST['tutoring_days']) ? implode(',', $_POST['tutoring_days']) : '';
    $tutoring_levels = isset($_POST['tutoring_levels']) ? implode(',', $_POST['tutoring_levels']) : '';
    $tutoring_courses = isset($_POST['tutoring_courses']) ? implode(',', $_POST['tutoring_courses']) : '';
    $availability = isset($_POST['availability']) ? implode(',', $_POST['availability']) : '';

    // Handling timeslots and hourly rate
    $timeslots = isset($_POST['timeslots']) ? implode(',', $_POST['timeslots']) : '';
    $hourly_rate = (float)$_POST['hourly_rate']; // Ensure hourly rate is a float

    // Validate hourly rate (optional, e.g., checking if it’s a positive number)
    if ($hourly_rate <= 0) {
        $error[] = 'Hourly rate must be a positive number.';
    }

    // Define allowed file types
    $allowedTypes = array("jpg", "png", "jpeg", "gif");
    $allowedCv = array("pdf", "doc", "docx", "rtf");

    // Get file extensions and convert to lowercase
    $profile_ext = $profile_pic ? strtolower(pathinfo($profile_pic, PATHINFO_EXTENSION)) : null;
    $cv_ext = $cv ? strtolower(pathinfo($cv, PATHINFO_EXTENSION)) : null;

    $profile_temp = isset($_FILES['profile_pic']['tmp_name']) ? $_FILES['profile_pic']['tmp_name'] : null;
    $cv_temp = isset($_FILES['cv']['tmp_name']) ? $_FILES['cv']['tmp_name'] : null;

    $profile_targetPath = "../uploads/" . $profile_pic;
    $cv_targetPath = "../uploads/" . $cv;

    // Check if passwords match
    if ($pass !== $cpass) {
        $error[] = 'Passwords do not match!';
    } elseif (strlen($pass) < 8) {
        $error[] = 'Password must be at least 8 characters long.';
    } else {
        // Hash the password before storing it
        $hashed_pass = password_hash($pass, PASSWORD_BCRYPT);
    }

    // Check if the email is already registered
    $select = "SELECT * FROM tutor WHERE email = '$email'";
    $result = mysqli_query($conn, $select);

    if ($result === false) {
        // Query execution failed
        $error[] = mysqli_error($conn);
    } elseif (mysqli_num_rows($result) > 0) {
        $error[] = 'Tutor with this email already exists!';
    }

    // Validate and upload profile picture and CV
    if (empty($error)) {
        if (in_array($profile_ext, $allowedTypes) && in_array($cv_ext, $allowedCv)) {
            if (move_uploaded_file($profile_temp, $profile_targetPath) && move_uploaded_file($cv_temp, $cv_targetPath)) {
                // Insert new tutor data into the database including timeslots and hourly rate
                $insert = "INSERT INTO tutor (Fname, email, address, contact, experience, profile_pic, cv, tutoring_days, tutoring_levels, tutoring_courses, availability, timeslots, hourly_rate, pass) 
                    VALUES ('$Fname', '$email', '$address', '$contact', $experience, '$profile_pic', '$cv', '$tutoring_days', '$tutoring_levels', '$tutoring_courses', '$availability', '$timeslots', $hourly_rate, '$hashed_pass')";

                if (mysqli_query($conn, $insert)) {
                    echo "Registration Successful";
                    header('Location: ./login.php');
                    exit;
                } else {
                    $error[] = 'Failed to register tutor: ' . mysqli_error($conn);
                }
            } else {
                $error[] = 'Failed to upload files.';
            }
        } else {
            $error[] = 'Invalid file types. Only JPG, PNG, JPEG, GIF are allowed for profile picture, and PDF, DOC, DOCX, RTF for CV.';
        }
    }
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../assets/logo.png" type="image/x-icon" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="./tutor.css">
    <title>Tutor Registration</title>

</head>

<body>
    <div class="container">
        <div class="signup form">
            <header>Tutor Registration</header>
            <form action="" method="post" enctype="multipart/form-data">
                <?php
                if (isset($error)) {
                    foreach ($error as $error) {
                        echo '<span class="error-msg" style="color: red;">' . $error . '</span>';
                    };
                };
                ?>
                <input type="text" name="Fname" required placeholder="Full Name" class="form-control">
                <input type="email" name="email" required placeholder="Email" class="form-control">
                <input type="text" name="address" required placeholder="Location" class="form-control">
                <input type="text" name="contact" required placeholder="Contact" class="form-control">
                <input type="number" name="experience" required placeholder="Level of experience" class="form-control">

                <label class="file-upload" for="profile_pic">
                    <img class="upload-icon" src="../assets/icons8-upload-48.png" />Profile Picture
                    <input type="file" name="profile_pic" required id="profile_pic" class="form-control" accept="image/*">
                </label>
                <label class="file-upload" for="Cv" style="margin-left: 160px;">
                    <img class="upload-icon" src="../assets/icons8-upload-48.png" /> CV
                    <input type="file" name="cv" id="Cv" class="form-control">
                </label>

                <!-- Tutoring Courses -->
                <div class="dropdown-container">
                    <div class="dropdown">
                        <button type="button" onclick="toggleDropdown(this)">Tutoring Courses</button>
                        <div class="dropdown-content">
                            <?php
                            // Fetch courses from the database
                            $sql = "SELECT course_Id, course_name FROM courses";
                            $result = $conn->query($sql);

                            if ($result->num_rows > 0) {
                                // Output data of each row
                                while ($row = $result->fetch_assoc()) {
                                    echo '<label><input type="checkbox" name="tutoring_courses[]" value="' . $row['course_name'] . '"> ' . $row['course_name'] . '</label>';
                                }
                            } else {
                                echo "No courses available.";
                            }

                            // Close the connection
                            $conn->close();
                            ?>
                        </div>
                    </div>
                </div>

                <!-- Tutoring Days -->
                <div class="dropdown-container">
                    <div class="dropdown">
                        <button type="button" onclick="toggleDropdown(this)">Tutoring Days</button>
                        <div class="dropdown-content">
                            <label><input type="checkbox" value="Monday" name="tutoring_days[]"> Monday</label>
                            <label><input type="checkbox" value="Tuesday" name="tutoring_days[]"> Tuesday</label>
                            <label><input type="checkbox" value="Wednesday" name="tutoring_days[]"> Wednesday</label>
                            <label><input type="checkbox" value="Thursday" name="tutoring_days[]"> Thursday</label>
                            <label><input type="checkbox" value="Friday" name="tutoring_days[]"> Friday</label>
                            <label><input type="checkbox" value="Saturday" name="tutoring_days[]"> Saturday</label>
                        </div>
                    </div>
                </div>

                <!-- Tutoring Levels -->
                <div class="dropdown-container">
                    <div class="dropdown">
                        <button type="button" onclick="toggleDropdown(this)">Tutoring Levels</button>
                        <div class="dropdown-content">
                            <label><input type="checkbox" value="primary" name="tutoring_levels[]"> Primary</label>
                            <label><input type="checkbox" value="secondary" name="tutoring_levels[]"> Secondary</label>
                            <label><input type="checkbox" value="university" name="tutoring_levels[]"> University</label>
                        </div>
                    </div>
                </div>

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

                <!-- Tutoring Levels -->
                <div class="dropdown-container">
                    <div class="dropdown">
                        <button type="button" onclick="toggleDropdown(this)">Availability</button>
                        <div class="dropdown-content">
                            <label><input type="checkbox" value="In Person" name="availability[]">In Person</label>
                            <label><input type="checkbox" value="Online" name="availability[]"> Online</label>
                        </div>
                    </div>
                </div>

                <!-- Hourly Rate -->
                <input type="number" name="hourly_rate" required placeholder="Hourly Rate in XAF" class="form-control">

                <input type="password" name="password" required placeholder="Password" class="form-control">
                <input type="password" name="cpassword" required placeholder="Confirm Password" class="form-control">
                <input type="submit" name="submit" class="get-started-btn" value="Sign Up">
            </form>
            <span class="login">Already have an account? <a href="./login.php" style="font-size: 16px;color: #006400;text-decoration: none;">Login</a></span>
        </div>
    </div>

    <script>
        function toggleDropdown(button) {
            const dropdownContainer = button.closest('.dropdown-container');
            dropdownContainer.classList.toggle('open');
        }

        // Close dropdown when clicking outside
        window.onclick = function(event) {
            if (!event.target.matches('.dropdown button')) {
                const dropdowns = document.querySelectorAll('.dropdown-container');
                dropdowns.forEach(function(dropdown) {
                    dropdown.classList.remove('open');
                });
            }
        }
    </script>

    <!-- Link to Bootstrap JS for better functionality (optional) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>