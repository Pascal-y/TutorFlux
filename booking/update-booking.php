<?php
@include '../config.php';
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header('Location: ../login/login.php');
    exit();
}

if (!isset($_GET['id'])) {
    header('Location: ../student/student-dashboard.php?section=section2#'); // Redirect if no booking ID is provided
    exit();
}

$booking_id = $_GET['id'];
$student_id = $_SESSION['student_id']; // Assuming student_id is stored in the session

// Fetch booking details
$query = "SELECT * FROM booking WHERE booking_Id = ? AND student_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('ii', $booking_id, $student_id);
$stmt->execute();
$booking_result = $stmt->get_result();

if ($booking_result->num_rows > 0) {
    $booking = $booking_result->fetch_assoc();
} else {
    header('Location: ../student/student-dashboard.php?section=section2#'); // Redirect if booking not found
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle form submission to update booking
    $tutor_id = $_POST['tutor'];
    $course_id = $_POST['course'];
    $level = $_POST['level'];
    $duration = $_POST['duration'];
    $mode = $_POST['mode'];
    $selected_days = isset($_POST['tutoring_days']) ? implode(',', $_POST['tutoring_days']) : '';
    $selected_timeslot = isset($_POST['timeslot']) ? implode(',', $_POST['timeslot']) : '';

    $update_query = "UPDATE booking SET tutor_id = ?, course_id = ?, educational_level = ?, duration = ?, mode = ? WHERE booking_Id = ?";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param('iisssi', $tutor_id, $course_id, $level, $duration, $mode, $booking_id);

    if ($update_stmt->execute()) {
        header('Location: ../student/student-dashboard.php?section=section2#'); // Redirect after successful update
        exit();
    } else {
        echo "Error updating booking.";
    }
}

// Fetch tutors and courses for form options
$tutors_query = "SELECT * FROM tutor";
$tutors_result = $conn->query($tutors_query);

$courses_query = "SELECT * FROM courses";
$courses_result = $conn->query($courses_query);

// Fetch tutor timeslots and days from the database, if $tutor_id is set
$tutoring_days = [];
$timeslots = [];
if (isset($booking['tutor_id'])) {
    $tutor_schedule_query = "SELECT tutoring_days, timeslots FROM tutor WHERE tutor_Id = ?";
    $stmt = $conn->prepare($tutor_schedule_query);
    $stmt->bind_param("i", $booking['tutor_id']);
    $stmt->execute();
    $tutor_schedule_result = $stmt->get_result();

    if ($tutor_schedule_result->num_rows > 0) {
        $tutor_schedule = $tutor_schedule_result->fetch_assoc();
        // Check if values are not null before exploding
        if (!is_null($tutor_schedule['tutoring_days'])) {
            $tutoring_days = explode(",", $tutor_schedule['tutoring_days']);
        }
        if (!is_null($tutor_schedule['timeslots'])) {
            $timeslots = explode(",", $tutor_schedule['timeslots']);
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../assets/logo.png" type="image/x-icon" />
    <title>Update Booking</title>
    <link rel="stylesheet" href="../login/login.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .popup {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 400px;
            max-width: 100%;
        }

        h1 {
            font-size: 24px;
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        input[type="text"],
        select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button[type="submit"] {
            background-color: #28a745;
            color: #fff;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
        }

        button[type="submit"]:hover {
            background-color: #F98435;
        }

        button.close {
            background-color: black;
            color: white;
            width: 34px;
            height: 42px;
            font-size: 28px;
            font-weight: 900;
            padding: 1px;
            border: none;
            border-radius: 5px;
            margin-left: auto;
            display: block;
            cursor: pointer;
        }

        button.close:hover {
            background-color: #F98435;
        }

        .toggle-btn {
            background-color: #e8e5e5;
            color: black;
            border: none;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
            width: 200%;
            font-size: 16px;
            margin-bottom: 8px;
        }
        
        .dropdown-content{
            padding: 8px;
            width: 200%;
        }
    </style>
</head>

<body>
    <div class="popup" id="popupForm">
        <button type="button" onclick="cancelBooking()" style="width: 30px; height:30px; border-radius: 50%; color: white; margin-left: 320px; font-size:20px; font-weight:700; background-color:black;">X</button>
        <form action="" method="POST">
            <h1>Update Booking</h1>

            <label for="tutor">Tutor Name</label>
            <select id="tutor" name="tutor" required>
                <?php while ($tutor = $tutors_result->fetch_assoc()) : ?>
                    <option value="<?php echo $tutor['tutor_Id']; ?>" <?php echo $tutor['tutor_Id'] == $booking['tutor_id'] ? 'selected' : ''; ?>>
                        <?php echo $tutor['Fname']; ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <label for="course">Course</label>
            <select id="course" name="course" required>
                <?php while ($course = $courses_result->fetch_assoc()) : ?>
                    <option value="<?php echo $course['course_Id']; ?>" <?php echo $course['course_Id'] == $booking['course_Id'] ? 'selected' : ''; ?>>
                        <?php echo $course['course_name']; ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <label for="level">Educational Level</label>
            <select id="level" name="level" required>
                <option value="Primary" <?php echo $booking['educational_level'] == 'Primary' ? 'selected' : ''; ?>>Primary</option>
                <option value="Secondary" <?php echo $booking['educational_level'] == 'Secondary' ? 'selected' : ''; ?>>Secondary</option>
                <option value="University" <?php echo $booking['educational_level'] == 'University' ? 'selected' : ''; ?>>University</option>
            </select>

            <!-- Timeslot Selection -->
            <div class="dropdown">
                <button type="button" onclick="toggleDropdown('timeslot-dropdown')" class="toggle-btn">Select Timeslot</button>
                <div id="timeslot-dropdown" class="dropdown-content">
                    <?php foreach ($timeslots as $slot) : ?>
                        <label><input type="checkbox" name="timeslot[]" value="<?php echo $slot; ?>"> <?php echo htmlspecialchars($slot); ?></label><br>
                    <?php endforeach; ?>
                </div>
            </div>
<br>
            <!-- Tutoring Days Selection -->
            <div class="dropdown">
                <button type="button" onclick="toggleDropdown('days-dropdown')" class="toggle-btn">Select Days</button>
                <div id="days-dropdown" class="dropdown-content">
                    <label><input type="checkbox" name="tutoring_days[]" value="Monday" <?php echo in_array('Monday', $tutoring_days) ? 'checked' : ''; ?>> Monday</label><br>
                    <label><input type="checkbox" name="tutoring_days[]" value="Tuesday" <?php echo in_array('Tuesday', $tutoring_days) ? 'checked' : ''; ?>> Tuesday</label><br>
                    <label><input type="checkbox" name="tutoring_days[]" value="Wednesday" <?php echo in_array('Wednesday', $tutoring_days) ? 'checked' : ''; ?>> Wednesday</label><br>
                    <label><input type="checkbox" name="tutoring_days[]" value="Thursday" <?php echo in_array('Thursday', $tutoring_days) ? 'checked' : ''; ?>> Thursday</label><br>
                    <label><input type="checkbox" name="tutoring_days[]" value="Friday" <?php echo in_array('Friday', $tutoring_days) ? 'checked' : ''; ?>> Friday</label><br>
                    <label><input type="checkbox" name="tutoring_days[]" value="Saturday" <?php echo in_array('Saturday', $tutoring_days) ? 'checked' : ''; ?>> Saturday</label><br>
                    <label><input type="checkbox" name="tutoring_days[]" value="Sunday" <?php echo in_array('Sunday', $tutoring_days) ? 'checked' : ''; ?>> Sunday</label>
                </div>
            </div>

            <label for="duration">Duration of Tutoring:</label>
            <input type="text" id="duration" name="duration" value="<?php echo htmlspecialchars($booking['duration']); ?>" required>

            <label for="mode">Mode of Tutoring:</label>
            <select id="mode" name="mode" required>
                <option value="in-person" <?php echo $booking['mode'] == 'in-person' ? 'selected' : ''; ?>>In-person</option>
                <option value="online" <?php echo $booking['mode'] == 'online' ? 'selected' : ''; ?>>Online</option>
            </select>

            <button type="submit">Update Booking</button>
        </form>
    </div>
    <script>
        function toggleDropdown(id) {
            var dropdown = document.getElementById(id);
            dropdown.classList.toggle('show');
        }

        function cancelBooking() {
            window.location.href = '../student/student-dashboard.php?section=section2#';
        }
        window.onclick = function(event) {
            if (!event.target.matches('.toggle-btn')) {
                var dropdowns = document.getElementsByClassName('dropdown-content');
                for (var i = 0; i < dropdowns.length; i++) {
                    var openDropdown = dropdowns[i];
                    if (openDropdown.classList.contains('show')) {
                        openDropdown.classList.remove('show');
                    }
                }
            }
        }
    </script>
</body>

</html>