<?php
@include '../config.php';
session_start();

// Check if the user is logged in and if the role is 'student'
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'tutor') {
    header('Location: ../login/login.php');
    exit();
}

// Fetch courses from the database
$courses_query = "SELECT * FROM courses";
$courses_result = $conn->query($courses_query);

// Get tutor information from session
$tutor_username = $_SESSION['username'];

// Fetch tutor details from the database
$tutor_query = "SELECT * FROM tutor WHERE Fname = '$tutor_username'";
$tutor_result = mysqli_query($conn, $tutor_query);

if (mysqli_num_rows($tutor_result) > 0) {
    $tutor_row = mysqli_fetch_assoc($tutor_result);
    $tutor_Id = $tutor_row['tutor_Id'];
    $tutor_name = $tutor_row['Fname'];
    $profile_picture = $tutor_row['profile_pic'];
    $tutor_email = $tutor_row['email'];
    $tutor_address = $tutor_row['address'];
    $tutor_contact = $tutor_row['contact'];
    $tutor_experience = $tutor_row['experience'];
    $tutoring_days    = $tutor_row['tutoring_days'];
    $tutor_levels = $tutor_row['tutoring_levels'];
    $tutor_courses = $tutor_row['tutoring_courses'];
    $tutor_cv = $tutor_row['cv'];
    $tutor_status = $tutor_row['status'];
    $tutor_availability = $tutor_row['availability'];
    $tutor_ratings = $tutor_row['ratings'];
    $tutor_pass = $tutor_row['pass'];
    $tutor_created = $tutor_row['created_at'];
    $rate = $tutor_row['hourly_rate'];
    $timeslot = $tutor_row['timeslots'];
} else {
    $tutor_name = 'Unknown';
    $profile_picture = '../assets/avatar.png'; // Default image if no profile picture is set
}


// Prepare the SQL query to retrieve bookings where the student's session ID matches the booking student ID
$sql = "SELECT student.profile_pic, student.Fname AS student_name, student.email AS student_email,
student.address AS student_location, student.age AS student_age, student.contact AS student_contact, 
booking.educational_level AS educational_level, courses.course_name, booking.educational_level, 
booking.duration, booking.mode, booking.status, booking.booking_time, booking.booking_Id, booking.tutoring_days, booking.timeslot
        FROM booking
        JOIN courses ON booking.course_Id = courses.course_Id
        JOIN student ON booking.student_Id = student.student_id
        WHERE booking.tutor_id = '$tutor_Id'";

// Execute the query
$result = $conn->query($sql);

mysqli_close($conn);

// Handle different sections
$section = isset($_GET['section']) ? $_GET['section'] : 'dashboard';


// Create the image URL based on the file name stored in the database
$imageUrl = "../uploads/" . htmlspecialchars($profile_picture);
$cvUrl = "../uploads/" . htmlspecialchars($tutor_cv);
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tutor Dashboard</title>
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
                    <img src="<?php echo $imageUrl; ?>" alt="Tutor Profile" class="profile-img" style="height: 60px; width:60px; border-radius: 50%; border: 2px solid white;">
                    <h5 class="profile-name"><?php echo htmlspecialchars($tutor_name); ?></h5>
                </div>
                <h1 class="sidebar-title" style="margin: 20px 5px 10px 5px; font-size: 26px; color: #ff8c00;"><i class="fas fa-tachometer-alt"></i>Dashboard</h1>

                <ul>
                    <li class="nav-item"><a href="#" style="text-decoration: none;" onclick="showSection('section1')"><i class="fas fa-user-cog"></i>Profile</a></li>
                    <li class="nav-item"><a href="#" style="text-decoration: none;" onclick="showSection('section2')"><i class="fas fa-calendar-alt"></i> Bookings</a></li>
                    <li class="nav-item"><a href="#" style="text-decoration: none;" onclick="showSection('section3')"><i class="fas fa-book"></i> Courses</a></li>
                    <li class="nav-item"><a href="#" style="text-decoration: none;" onclick="showSection('section4')"><i class="fas fa-tasks"></i> Assignments</a></li>
                    <li class="nav-item"><a href="#" style="text-decoration: none;" onclick="openChats()"><i class="fas fa-envelope"></i> Messages</a></li>
                    <li class="nav-item"><a href="#" style="text-decoration: none;" onclick="showSection('section8')"><i class="fas fa-bell"></i> Notifications</a></li>
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
                        <div class="left">
                            <img src="<?php echo $imageUrl; ?>" alt="" class="profile-pic" id="profilePic" style="border: 4px solid white;">
                            <p><strong>CV:</strong> <span id="cv" style="margin-left: 4px;"><?php echo htmlspecialchars($tutor_cv); ?>

                                    <a href="<?php echo $cvUrl;
                                                ?>" class="fa fa-eye" target="_blank" style="color: #ff8c00; text-decoration:none; margin: 20px"></a>
                                </span></p>
                        </div>
                        <div class="right">
                            <p><strong>Name:</strong> <span id="fullName"><?php echo htmlspecialchars($tutor_name); ?></span></p>
                            <p><strong>Email:</strong> <span id="email"><?php echo htmlspecialchars($tutor_email); ?></span></p>
                            <p><strong>Address:</strong> <span id="address"><?php echo htmlspecialchars($tutor_address); ?></span></p>
                            <p><strong>Contact:</strong> <span id="contact"><?php echo htmlspecialchars($tutor_contact); ?></span></p>
                            <p><strong>Experience :</strong> <span id="experience"><?php echo htmlspecialchars($tutor_experience); ?> Years</span></p>
                            <p><strong>Tutoring Days:</strong> <span id="days"><?php echo htmlspecialchars($tutoring_days); ?></span></p>
                            <p><strong>Levels:</strong> <span id="levels"><?php echo htmlspecialchars($tutor_levels); ?></span></p>
                            <p><strong>Courses:</strong> <span id="courses"><?php echo htmlspecialchars($tutor_courses); ?></span></p>
                            <p><strong>Hourly Rate:</strong> <span id="rate"><?php echo htmlspecialchars($rate); ?></span></p>
                            <p><strong>Time Slot:</strong> <span id="timeslots"><?php echo htmlspecialchars($timeslot); ?></span></p>
                            <p><strong>Status:</strong> <span id="status">
                                    <?php echo $tutor_status == 0 ? 'Pending' : 'Approved'; ?>

                                    <p><strong>Availability:</strong> <span id="availability"><?php echo htmlspecialchars($tutor_availability); ?></span></p>
                                    <p><strong>Registration Date:</strong> <span id="createdDate"><?php echo htmlspecialchars($tutor_created); ?></span></p>
                        </div>
                        <a href='update-profile.php?tutor_Id=<?php echo $tutor_Id; ?>' class="edit-btn"><i class="fas fa-edit"></i></a>
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
                                            <th>Student Name</th>
                                            <th>Location</th>
                                            <th>Educational Level</th>
                                            <th>Course</th>
                                            <th>Duration</th>
                                            <th>Mode</th>
                                            <th>Tutoring Days</th>
                                            <th>Timeslot</th>
                                            <th>Status</th>
                                            <th>Booking Time</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($booking = $result->fetch_assoc()): ?>
                                            <tr>
                                                <td><img src="../uploads/<?php echo htmlspecialchars($booking['profile_pic']); ?>" alt="Profile Picture" class="tutor_pic"></td>
                                                <td><?php echo htmlspecialchars($booking['student_name']); ?></td>
                                                <td><?php echo htmlspecialchars($booking['student_location']); ?></td>
                                                <td><?php echo htmlspecialchars($booking['educational_level']); ?></td>
                                                <td><?php echo htmlspecialchars($booking['course_name']); ?></td>
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
                                                        <button class="approve-btns" onclick="approveBooking(<?php echo $booking['booking_Id']; ?>)">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                        <button class="reject-btns" onclick="rejectBooking(<?php echo $booking['booking_Id']; ?>)">
                                                            <i class="fas fa-times"></i>
                                                        </button>

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
                </div>

                <div id="section4" class="section">
                    <div class="container text-center">
                        <h2 class="my-4">Assignment Management</h2>
                        <a class="btn btn-secondary btn-lg" onclick="createAssignment()">Create Assignment</a>
                        <a class="btn btn-secondary btn-lg" onclick="showSection('section5')">View Submissions</a>
                    </div>

                    <div class="container">
                        <h2 class="my-4">Assignments</h2>
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Description</th>
                                    <th>Assignment file</th>
                                    <th>Created On</th>
                                    <th>Course</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                include '../config.php';

                                // Assuming $tutor_username is defined somewhere earlier in your script
                                $tutor_query = "SELECT tutor_Id FROM tutor WHERE Fname = '$tutor_username'";
                                $tutor_result = $conn->query($tutor_query);

                                if ($tutor_result->num_rows > 0) {
                                    $tutor_row = $tutor_result->fetch_assoc();
                                    $tutor_Id = $tutor_row['tutor_Id'];

                                    $sql = "SELECT assignments.*, courses.course_name FROM assignments
                        JOIN courses ON courses.course_Id = assignments.course_Id
                        WHERE assignments.tutor_Id = '$tutor_Id'";

                                    $result = $conn->query($sql);

                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            echo "<tr>
                                <td>" . $row['title'] . "</td>
                                <td>" . $row['description'] . "</td>
                                <td>" . $row['assignment_file'] . "</td>
                                <td>" . $row['created_date'] . "</td>
                                <td>" . $row['course_name'] . "</td>
                                <td>
                                    <a href='../assignment/edit_assignment.php?assignmentID=" . $row['assignmentID'] . "' class='btn btn-primary fa fa-edit' style='background-color: orange; color: white; border-radius: 50%;'></a>
                                    <a href='../assignment/assign_assignment.php?assignmentID=" . $row['assignmentID'] . "' class='btn btn-primary fa fa-user-plus'></a>
                                    <a href='../assignment/delete_assignment.php?assignmentID=" . $row['assignmentID'] . "' class='btn btn-primary fa fa-trash' style='background-color: red;'></a>
                                </td>
                              </tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='6'>No assignments found</td></tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='6'>Tutor not found</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>


                <div id="section5" class="section">
                    <?php
                    include '../config.php';


                    // Check if the user is logged in and if the role is 'tutor'
                    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'tutor') {
                        header('Location: ../login/login.php');
                        exit();
                    }

                    // Get tutor information from session
                    $tutor_username = $_SESSION['username'];

                    // Fetch tutor details from the database
                    $tutor_query = "SELECT tutor_Id FROM tutor WHERE Fname = ?";
                    $stmt_tutor = $conn->prepare($tutor_query);
                    $stmt_tutor->bind_param("s", $tutor_username);
                    $stmt_tutor->execute();
                    $tutor_result = $stmt_tutor->get_result();

                    if ($tutor_result->num_rows > 0) {
                        $tutor_row = $tutor_result->fetch_assoc();
                        $tutor_Id = $tutor_row['tutor_Id'];
                    }

                    // Prepare the SQL statement to fetch submissions without redundancy
                    $query = "
SELECT DISTINCT 
    s.submissionID,
    st.profile_pic,
    st.Fname AS student_name,
    c.course_name,
    s.submissionFile,
    s.submissionDate,
    s.grade
FROM 
    submissions s
JOIN 
    assign a ON s.assignmentID = a.assignmentID
JOIN 
    student st ON s.submittedBy = st.student_Id
JOIN 
    assignments asgn ON s.assignmentID = asgn.assignmentID
JOIN 
    courses c ON asgn.course_Id = c.course_Id
WHERE 
    asgn.tutor_Id = ?
    AND s.grade IS NULL"; // This checks if the grade is NULL, meaning not yet graded


                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("i", $tutor_Id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    ?>
                    <div class="container">
                        <h2 class="my-4">Assignment Submissions</h2>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Profile Picture</th>
                                    <th>Student Name</th>
                                    <th>Course Name</th>
                                    <th>Submission File</th>
                                    <th>Submission Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $result->fetch_assoc()) { ?>
                                    <tr>
                                        <td><img src="../uploads/<?php echo htmlspecialchars($row['profile_pic']); ?>" alt="Profile Picture" class="profile-pic" style="height: 30px; width:30px; border-radius:50%;"></td>
                                        <td><?php echo htmlspecialchars($row['student_name']); ?></td>
                                        <td><?php echo htmlspecialchars($row['course_name']); ?></td>
                                        <td>
                                            <?php echo htmlspecialchars(basename($row['submissionFile'])); ?>
                                            <a href="../assignment/uploads/submissions/<?php echo htmlspecialchars($row['submissionFile']); ?>" download class="btn-download">
                                                <i class="fa fa-download" style="color: white; padding: 5px; border-radius: 8px;"></i>
                                            </a>
                                        </td>
                                        <td><?php echo htmlspecialchars($row['submissionDate']); ?></td>
                                        <td>
                                            <form action="grade_submission.php" method="get">
                                                <input type="hidden" name="submissionID" value="<?php echo htmlspecialchars($row['submissionID']); ?>">

                                                <a href='../assignment/grade_assignment.php?submissionID=<?php echo htmlspecialchars($row['submissionID']); ?>' class="btn btn-grade" style="background-color: #006400;
                                                                                                                            border-radius: 8px;
                                                                                                                            height: 30px;
                                                                                                                            width: 60px;
                                                                                                                            color: white;
                                                                                                                            border: none;
                                                                                                                            justify-content: center;
                                                                                                                            cursor: pointer;
                                                                                                                        ">Grade</a>
                                            </form>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>

                    <?php
                    $stmt->close();
                    $conn->close();
                    ?>

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
            var sections = document.getElementsByClassName('section');
            for (var i = 0; i < sections.length; i++) {
                sections[i].classList.remove('active');
            }
            document.getElementById(sectionId).classList.add('active');
        }

        function approveBooking(bookingId) {
            if (confirm('Are you sure you want to approve this booking?')) {
                // Redirect to a PHP script to approve the booking
                window.location.href = '../booking/approve_booking.php?booking_id=' + bookingId;
            }
        }

        function rejectBooking(bookingId) {
            if (confirm('Are you sure you want to reject this booking?')) {
                // Redirect to a PHP script to reject the booking
                window.location.href = '../booking/reject_booking.php?booking_id=' + bookingId;
            }
        }

        function createAssignment() {
            // Redirect to the update profile page with the tutor's ID in the query string
            window.location.href = '../assignment/process_assignment.php';
        }

        function openChats() {
            // Redirect to the chat  page with the tutor's ID in the query string
            window.location.href = '../tutor_communication/chat.php';
        }
    </script>
</body>

</html>