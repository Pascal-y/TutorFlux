<?php
require "../config.php";
// Start session
session_start();

// Check if the user is logged in and if the role is 'student'
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header('Location: ../login/login.php');
    exit();
}

// Get student information from session
$student_username = $_SESSION['username'];

// Fetch student details from the database
$student_query = "SELECT * FROM student WHERE Fname = ?";
$stmt = $conn->prepare($student_query);
$stmt->bind_param("s", $student_username);
$stmt->execute();
$student_result = $stmt->get_result();

if ($student_result->num_rows > 0) {
    $student_row = $student_result->fetch_assoc();
    $student_id = $student_row['student_Id'];
    $_SESSION['student_id'] = $student_id;
    $student_name = $student_row['Fname'];
    $profile_picture = $student_row['profile_pic'];
    $student_email = $student_row['email'];
    $student_address = $student_row['address'];
    $student_contact = $student_row['contact'];
    $student_age = $student_row['age'];
    $student_pass = $student_row['pass'];
    $student_created = $student_row['created_date'];
} else {
    $student_name = 'Unknown';
    $profile_picture = '../assets/avatar.png'; // Default image if no profile picture is set
}

// Fetch courses from the database
$courses_query = "SELECT * FROM courses";
$courses_result = $conn->query($courses_query);

// Check if tutor_id is set in the URL
if (isset($_GET['tutor_Id'])) {
    $tutor_id = $_GET['tutor_Id'];
} else {
    $error[] = "No tutor selected! Please select a tutor.";
}

// Fetch tutor timeslots and days from the database
$tutor_schedule_query = "SELECT tutoring_days, timeslots FROM tutor WHERE tutor_Id = ?";
$stmt = $conn->prepare($tutor_schedule_query);
$stmt->bind_param("i", $tutor_id);
$stmt->execute();
$tutor_schedule_result = $stmt->get_result();
$tutor_schedule = $tutor_schedule_result->fetch_assoc();

// Available days and times for the tutor
$tutoring_days = explode(",", $tutor_schedule['tutoring_days']);
$timeslots = explode(",", $tutor_schedule['timeslots']);

// Initialize error array
$error = [];

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && empty($error)) {
    $course_id = $_POST['course'];
    $educational_level = $_POST['level'];
    $duration = $_POST['duration'];
    $mode = $_POST['mode'];
    $selected_days = isset($_POST['tutoring_days']) ? implode(',', $_POST['tutoring_days']) : '';
    $selected_timeslot = isset($_POST['timeslot']) ? implode(',', $_POST['timeslot']) : '';

    // Get student ID from session
    $student_id = $_SESSION['student_id'];

    // Check if the selected timeslot and day are already booked
    $check_booking_query = "SELECT COUNT(*) FROM booking WHERE tutor_id = ? AND timeslot = ? AND tutoring_days = ?";
    $stmt = $conn->prepare($check_booking_query);
    $stmt->bind_param("iss", $tutor_id, $selected_timeslot, $selected_days);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    if ($count > 0) {
        $error[] = "The selected timeslot and day are not available. Please choose another time.";
    } else {
        // Insert booking data into the booking table
        $insert_booking_query = "INSERT INTO booking (student_id, tutor_id, course_id, educational_level, duration, mode, timeslot, tutoring_days, status)
                                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0)";
        $stmt = $conn->prepare($insert_booking_query);
        $stmt->bind_param("iiisssss", $student_id, $tutor_id, $course_id, $educational_level, $duration, $mode, $selected_timeslot, $selected_days);
        $stmt->execute();
        $stmt->close();

        // Redirect to your Home
        header("Location: ../tutors/tutors.php");
        exit();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../assets/logo.png" type="image/x-icon"/>
    <link rel="stylesheet" href="booking.css">
    <title>Book Tutor</title>
</head>
<body>
    <form action="" method="POST">
        <button type="button" onclick="cancelBooking()" style="width: 34px; height:42px; margin-left: 450px; font-size:28px; font-weight:900; padding:1px; background-color:black;" class="cancel">X</button>
        <h1>Book a Tutor</h1>
        <?php
        if (!empty($error)) {
            foreach ($error as $err) {
                echo '<span class="error-msg" style="color: red;">' . $err . '</span><br>';
            }
        }
        ?>
        <label for="course">Course</label>
        <select id="course" name="course" required>
            <?php while ($row = $courses_result->fetch_assoc()) : ?>
                <option value="<?php echo $row['course_Id']; ?>"><?php echo $row['course_name']; ?></option>
            <?php endwhile; ?>
        </select>

        <label for="level">Educational Level</label>
        <select id="level" name="level" required>
            <option value="Primary">Primary</option>
            <option value="Secondary">Secondary</option>
            <option value="University">University</option>
        </select>

        <label for="duration">Duration of Tutoring:</label>
        <input type="text" id="duration" name="duration" required>

        <label for="mode">Mode of Tutoring:</label>
        <select id="mode" name="mode" required>
            <option value="in-person">In-person</option>
            <option value="online">Online</option>
        </select>

        <!-- Timeslot Selection -->
        <label for="timeslot">Available Timeslots</label>
        <div class="dropdown">
            <button type="button" onclick="toggleDropdown('timeslot-dropdown')" class="toggle-btn">Select Timeslot</button>
            <div id="timeslot-dropdown" class="dropdown-content">
                <?php foreach ($timeslots as $slot) : ?>
                    <label><input type="checkbox" name="timeslot[]" value="<?php echo $slot; ?>"> <?php echo $slot; ?></label><br>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Days Selection -->
        <label for="tutoring_days">Available Days</label>
        <div class="dropdown">
            <button type="button" onclick="toggleDropdown('days-dropdown')" class="toggle-btn">Select Days</button>
            <div id="days-dropdown" class="dropdown-content">
                <?php foreach ($tutoring_days as $day) : ?>
                    <label><input type="checkbox" name="tutoring_days[]" value="<?php echo $day; ?>"> <?php echo $day; ?></label><br>
                <?php endforeach; ?>
            </div>
        </div>

        <button type="submit" class="submit-btn">Submit Booking</button>
    </form>

    <script>
        function cancelBooking() {
            window.location.href = '../tutors/tutors.php';
        }

        function toggleDropdown(dropdownId) {
            var dropdown = document.getElementById(dropdownId);
            dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
        }
    </script>
</body>
</html>
