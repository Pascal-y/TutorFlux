<?php
@include '../config.php';
session_start();

// Check if the user is logged in and if the role is 'student'
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header('Location: ../login/login.php');
    exit();
}

// Get student information from session
$student_username = $_SESSION['username'];

// Fetch student details from the database
$student_query = "SELECT * FROM student WHERE Fname = '$student_username'";
$student_result = mysqli_query($conn, $student_query);

if (mysqli_num_rows($student_result) > 0) {
    $student_row = mysqli_fetch_assoc($student_result);
    $student_id = $student_row['student_Id'];
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

// Only run the query if $student_id is set
if (isset($student_id)) {
    // Prepare the SQL query to retrieve bookings where the student's session ID matches the booking student ID
    $sql = "SELECT tutor.profile_pic, tutor.Fname AS tutor_name, tutor.email AS tutor_email, tutor.contact AS tutor_contact, 
                   tutor.experience AS tutor_experience, courses.course_name, booking.educational_level, 
                   booking.duration, booking.mode, booking.status, booking.booking_time, booking.booking_Id, booking.tutoring_days, booking.timeslot
            FROM booking
            JOIN tutor ON tutor.tutor_Id = booking.tutor_id
            JOIN courses ON booking.course_Id = courses.course_Id
            WHERE booking.student_id = '$student_id'";

    // Execute the query
    $result = $conn->query($sql);
} else {
    $result = false; // No student ID means no bookings can be fetched
}

mysqli_close($conn);

// Handle different sections
$section = isset($_GET['section']) ? $_GET['section'] : 'dashboard';

// Create the image URL based on the file name stored in the database
$imageUrl = "../uploads/" . htmlspecialchars($profile_picture);
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../assets/logo.png" type="image/x-icon" />
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="dashboard.css">
    <link rel="stylesheet" href="../navbar/navbar.css">
    <link rel="stylesheet" href="../assignment/style.css">
</head>

<body style="background-color: white;">
    <div class="wrapper">
        <header>
            <nav class="navbar">
                <img src="../assets/logo.png" style="height: 60px;" alt="">
                <div class="logo">TutorFlux</div>
                <div class="menu-toggle" id="mobile-menu">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </nav>
        </header>

        <div class="main-section">
            <aside class="sidebar">
                <div class="profile">
                    <img src="<?php echo $imageUrl; ?>" alt="Student Profile" class="profile-img" style="height: 60px; width:60px; border-radius: 50%; border: 2px solid white;">
                    <h5 class="profile-name"><?php echo htmlspecialchars($student_name); ?></h5>
                </div>
                <h1 class="sidebar-title" style="margin: 20px 5px 10px 5px; font-size: 26px; color: #ff8c00;"><i class="fas fa-tachometer-alt"></i>Dashboard</h1>

                <ul>
                    <li class="nav-item"><a href="#" style="text-decoration: none;" onclick="showSection('section1')"><i class="fas fa-user-cog"></i>Profile</a></li>
                    <li class="nav-item"><a href="#" style="text-decoration: none;" onclick="showSection('section2')"><i class="fas fa-calendar-alt"></i> Bookings</a></li>
                    <li class="nav-item"><a href="#" style="text-decoration: none;" onclick="showSection('section3')"><i class="fas fa-book"></i> Courses</a></li>
                    <li class="nav-item"><a href="#" style="text-decoration: none;" onclick="showSection('section4')"><i class="fas fa-tasks"></i> Assignments</a></li>
                    <li class="nav-item"><a href="#" style="text-decoration: none;" onclick="openChats()"><i class="fas fa-envelope"></i> Messages</a></li>
                    <li class="nav-item">
                        <a href="../payment/payment.php" style="text-decoration: none;">
                            <i class="fas fa-credit-card"></i> Pay
                        </a>
                    </li>

                    <li class="nav-item"><a href="#" style="text-decoration: none;" onclick="showSection('section5')"><i class="fas fa-bell"></i> Notifications</a></li>
                    <li class="nav-item"><a class="nav-link" href="../home/home.php"><i class="fas fa-home"> Home</i></a></li>
                    <li class="nav-item"><a href="../logout.php" style="text-decoration: none;"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                    <!-- Add more links as needed -->

                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                </ul>
            </aside>

            <div class="content">
                <div id="section1" class="section active">
                    <div class="profile-container">
                        <img src="<?php echo $imageUrl; ?>" alt="" class="profile-pic" id="profilePic">
                        <p><strong>Name:</strong> <span id="fullName"><?php echo htmlspecialchars($student_name); ?></span></p>
                        <p><strong>Email:</strong> <span id="email"><?php echo htmlspecialchars($student_email); ?></span></p>
                        <p><strong>Address:</strong> <span id="address" style="margin-left: 38px;"><?php echo htmlspecialchars($student_address); ?></span></p>
                        <p><strong>Contact:</strong> <span id="contact" style="margin-left: 42px;"></span><?php echo htmlspecialchars($student_contact); ?></p>
                        <p><strong>Age:</strong> <span id="age" style="margin-left: 72px;"><?php echo htmlspecialchars($student_age); ?> Years</span></p>
                        <p><strong>Created on:</strong> <span id="createdDate" style="margin-left: 22px;"><?php echo htmlspecialchars($student_created); ?></span></p>
                        <a href='update-profile.php?student_Id=<?php echo $student_id; ?>' class="edit-btn"><i class="fas fa-edit"></i></a>
                    </div>

                </div>


                <div id="section2" class="section">
                    <?php if ($result && $result->num_rows > 0): ?>
                        <div class="booking-container">
                            <div class="booking-info">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Picture</th>
                                            <th>Tutor Name</th>
                                            <th>Course Name</th>
                                            <th>Educational Level</th>
                                            <th>Duration</th>
                                            <th>Mode</th>
                                            <th>Tutoring Days</th>
                                            <th>Time Slots</th>
                                            <th>Status</th>
                                            <th>Booking Time</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($booking = $result->fetch_assoc()): ?>
                                            <tr>
                                                <td><img src="../uploads/<?php echo htmlspecialchars($booking['profile_pic']); ?>" alt="Profile Picture" class="tutor_pic"></td>
                                                <td><?php echo htmlspecialchars($booking['tutor_name']); ?></td>
                                                <td><?php echo htmlspecialchars($booking['course_name']); ?></td>
                                                <td><?php echo htmlspecialchars($booking['educational_level']); ?></td>
                                                <td><?php echo htmlspecialchars($booking['duration']); ?></td>
                                                <td><?php echo htmlspecialchars($booking['mode']); ?></td>
                                                <td><?php echo htmlspecialchars($booking['tutoring_days']); ?></td>
                                                <td><?php echo htmlspecialchars($booking['timeslot']); ?></td>
                                                <td style="<?php
                                                            if ($booking['status'] == 0) {
                                                                echo 'background-color: #4CAF50; color: white;'; // Green background for Pending
                                                            } elseif ($booking['status'] == 1) {
                                                                echo 'background-color: #ff9800; color: white;'; // Orange background for Approved
                                                            } elseif ($booking['status'] == 2) {
                                                                echo 'background-color: #f44336; color: white;'; // Red background for Rejected
                                                            } elseif ($booking['status'] == 3) {
                                                                echo 'background-color: #A9A9A9; color: white;'; // Red background for Rejected
                                                            }
                                                            ?> padding: 10px; text-align: center;">
                                                    <?php
                                                    echo $booking['status'] == 0 ? 'Pending' : ($booking['status'] == 1 ? 'Approved' : ($booking['status'] == 2 ? 'Rejected' : 'Cancelled'));
                                                    ?></td>
                                                <td><?php echo htmlspecialchars($booking['booking_time']); ?></td>
                                                <td>
                                                    <?php if ($booking['status'] == 0 || $booking['status'] == 1): ?>
                                                        <button class="update-btns" onclick="updateBooking(<?php echo $booking['booking_Id']; ?>)"><i class="fas fa-edit"></i></button>
                                                        <button class="cancel-btns" onclick="cancelBooking(<?php echo $booking['booking_Id']; ?>)"><i class="fas fa-times"></i></button>
                                                    <?php elseif ($booking['status'] == 3 || $booking['status'] == 2): ?>
                                                        <button class="delete-btns" onclick="deleteBooking(<?php echo $booking['booking_Id']; ?>)"><i class="fas fa-trash"></i></button>
                                                    <?php else: ?>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php else: ?>
                        <p>No bookings found.</p>
                    <?php endif; ?>
                </div>



                <div id="section3" class="section">
                    <h1>Courses</h1>
                    <p>This section contains Course options.</p>
                    <p>No courses available yet.</p>
                </div>






                <div id="section4" class="section">
                    <div class="container text-center">
                        <h2 class="my-4">Assignment Management</h2>
                    </div>

                    <div class="container">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Description</th>
                                    <th>Assignment File</th>
                                    <th>Course</th>
                                    <th>Tutor</th>
                                    <th>Assigned Date</th>
                                    <th>Due Date</th>
                                    <th>Status</th>
                                    <th>Grade</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                include '../config.php';

                                // Get student information from session
                                $student_username = $_SESSION['username'];

                                // Fetch student details from the database
                                $student_query = "SELECT * FROM student WHERE Fname = '$student_username'";
                                $student_result = mysqli_query($conn, $student_query);

                                if (mysqli_num_rows($student_result) > 0) {
                                    $student_row = mysqli_fetch_assoc($student_result);
                                    $student_Id = $student_row['student_Id'];
                                }

                                // Define the upload directory for assignments
                                $uploadDir = '../assignment/';

                                // Corrected SQL query to display assignments for a particular student
                                $sql = "SELECT DISTINCT assignments.assignmentID, assignments.title, assignments.description, assignments.assignment_file, 
                       courses.course_name, tutor.Fname AS tutor_name, assign.assigned_date, assign.due_date, 
                       submissions.grade, submissions.status
                FROM assignments
                JOIN courses ON courses.course_Id = assignments.course_Id
                JOIN tutor ON tutor.tutor_Id = assignments.tutor_Id
                JOIN assign ON assign.assignmentID = assignments.assignmentID
                LEFT JOIN submissions ON submissions.assignmentID = assignments.assignmentID AND submissions.submittedBy = '$student_Id'
                WHERE assign.student_Id = '$student_Id'";

                                $result = $conn->query($sql);

                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        // Determine the status text based on the submission status
                                        $status_text = 'Submission Pending';
                                        if ($row['status'] == 1) {
                                            $status_text = 'Submitted';
                                        } elseif ($row['status'] == 2) {
                                            $status_text = 'Graded';
                                        }

                                        // Build the full path for the download link
                                        $filePath = $uploadDir . $row['assignment_file'];
                                        $fileName = basename($row['assignment_file']); // Extract only the file name

                                        echo "<tr>
                        <td>" . $row['title'] . "</td>
                        <td>" . $row['description'] . "</td>
                        <td>
                            " . $fileName . "
                            <a href='" . $filePath . "' download class='btn btn-primary' style='margin-left: 10px;'>
                                <i class='fa fa-download'></i>
                            </a>
                        </td>
                        <td>" . $row['course_name'] . "</td>
                        <td>" . $row['tutor_name'] . "</td>
                        <td>" . $row['assigned_date'] . "</td>
                        <td>" . $row['due_date'] . "</td>
                        <td>" . $status_text . "</td>
                        <td>" . $row['grade'] . "</td>
                        <td>
                            <a href='../assignment/submit_assignment.php?assignmentID=" . $row['assignmentID'] . "' class='btn btn-primary' style='background-color: orange; color: white; border-radius: 50%;'>
                                <i class='fa fa-upload'></i>
                            </a>
                        </td>
                      </tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='10'>No assignments found</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>


                    </div>
                </div>
            </div>
        </div>

        <footer>
            <p>&copy; 2024 TutorFlux. All rights reserved.</p><br>
            <p>Designed and Developed by: YENNYUYGHA PASCAL</p>
        </footer>
    </div>

    <script>
        document.getElementById('mobile-menu').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('active');
        });

        function showSection(sectionId) {
            const sections = document.querySelectorAll('.section');
            sections.forEach(section => section.classList.remove('active'));

            document.getElementById(sectionId).classList.add('active');
        }

        function updateBooking(bookingId) {
            if (confirm('Are you sure you want to update this booking?')) {
                window.location.href = '../booking/update-booking.php?id=' + bookingId;
            }
        }

        function deleteBooking(bookingId) {
            if (confirm('Are you sure you want to delete this booking?')) {
                // Redirect to the delete-booking.php page with the booking ID
                window.location.href = '../booking/delete-booking.php?id=' + bookingId;
            }
        }

        function cancelBooking(bookingId) {
            if (confirm('Are you sure you want to cancel this booking?')) {
                // Use AJAX to cancel the booking
                var xhr = new XMLHttpRequest();
                xhr.open('GET', '../booking/cancel-booking.php?id=' + bookingId, true);
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        alert('Booking cancelled successfully.');
                        // Reload the page to reflect the changes
                        location.reload();
                    } else {
                        alert('Error cancelling booking.');
                    }
                };
                xhr.send();
            }
        }

        function openChats() {
            // Redirect to the chat  page with the tutor's ID in the query string
            window.location.href = '../communication/chat.php';
        }
    </script>
</body>

</html>