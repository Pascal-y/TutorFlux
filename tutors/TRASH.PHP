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


// Handle different sections
$section = isset($_GET['section']) ? $_GET['section'] : 'dashboard';

// Create the image URL based on the file name stored in the database
$imageUrl = "../uploads/" . htmlspecialchars($profile_picture);

// Handle the search query
$query = isset($_GET['search']) ? $_GET['search'] : '';
$query = $conn->real_escape_string($query);

// Fetch tutors based on the search query or all tutors if no search query is provided
$sql = "SELECT tutor.*, COALESCE(AVG(review.rating), 0) AS average_rating 
        FROM tutor 
        LEFT JOIN review ON review.tutor_Id = tutor.tutor_Id 
        WHERE tutor.status = 0";

// Apply search filters if a query is provided
if ($query) {
    $sql .= " AND (tutor.Fname LIKE '%$query%' 
                OR tutor.address LIKE '%$query%' 
                OR tutor.experience LIKE '%$query%' 
                OR tutor.tutoring_days LIKE '%$query%' 
                OR tutor.tutoring_levels LIKE '%$query%' 
                OR tutor.tutoring_courses LIKE '%$query%')";
}

// Group by tutor_Id to ensure correct aggregation
$sql .= " GROUP BY tutor.tutor_Id";

$result = $conn->query($sql);
$tutors = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $tutors[] = $row;
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
    <title>TutorFlux</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="tutor.css">
    <link rel="stylesheet" href="style.css">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .navbar-brand {
            margin-top: -20px;
        }

        .navbar-brand {
            margin-top: -20px;
        }

        body,
        html {
            height: 100%;
            margin: 0;
        }

        .wrapper {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .content {
            flex: 1;
        }

        .footer {
            background: #f8f9fa;
            text-align: center;

        }
    </style>
</head>

<body>
    <div class="wrapper">
        <!-- Navigation -->
        <nav class="navbar navbar-expand-lg navbar-light bg-[#]">
            <a class="navbar-brand" href="#" style="font-size: 30px;font-weight:bold; margin-top:-20px;">
                <img src="../assets/logo.png" style="height: 60px; margin-top: -4px; margin-right: 4px;" alt="">TutorFlux
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto" style="margin-top: 8px; font-size: 20px;">
                    <li class="nav-item"><a class="nav-link" href="../home/home.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="../tutors/tutors.php">Tutors</a></li>
                    <li class="nav-item"><a class="nav-link" href="../courses/courses.php">Courses</a></li>
                    <li class="nav-item"><a class="nav-link" href="../fluxai/fluxai.php">Fluxai</a></li>
                    <li class="nav-item"><a class="nav-link" href="../about/about.php">About</a></li>
                    <li class="nav-item"><a class="nav-link" href="../logout.php">Logout</a></li>
                    <li class="nav-item"><a class="nav-link" href="../student/student-dashboard.php">
                            <img src="<?php echo $imageUrl; ?>" style="height: 50px; width: 50px; margin-top:-8px; border-radius:50%; border: 2px solid white;" style="height: 50px; width: 50px; margin-top:-8px;" />
                        </a></li>
                </ul>
            </div>
        </nav>

        <!-- Main Content -->
        <div class="content">
            <div class="body">
                <div class="search-container">
                    <form action="" method="GET">
                        <input type="text" name="search" class="form-control search-input" placeholder="Search..." value="<?php echo htmlspecialchars($query); ?>">
                        <button type="submit" class="search-button">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>

                <!-- HTML to Display Tutor Cards -->
                <div class="tutor-container">
                    <?php if (!empty($tutors)): ?>
                        <?php foreach ($tutors as $tutor): ?>
                            <?php
                            // Create the image URL based on the file name stored in the database
                            $imageUrl = "../uploads/" . htmlspecialchars($tutor['profile_pic']);
                            ?>
                            <div class="tutor-card">
                                <div class="tutor-profile">
                                    <img src="<?php echo $imageUrl; ?>" alt="Tutor Profile">
                                </div>
                                <div class="tutor-info">
                                    <h2><?php echo htmlspecialchars($tutor['Fname']); ?></h2>
                                    <p>Contact: <?php echo htmlspecialchars($tutor['contact']); ?></p>
                                    <p>Location: <?php echo htmlspecialchars($tutor['address']); ?></p>
                                    <p>Experience: <?php echo htmlspecialchars($tutor['experience']); ?> Years</p>
                                    <p>Courses: <?php echo htmlspecialchars($tutor['tutoring_courses']); ?></p>
                                    <p>Availability: <?php echo htmlspecialchars($tutor['availability']); ?></p>
                                    <p>Tutoring Days: <?php echo htmlspecialchars($tutor['tutoring_days']); ?></p>
                                </div>
                                <div class="tutor-rating">Ratings: <?php echo str_repeat('★', floor($tutor['average_rating'])); ?><?php echo str_repeat('☆', 5 - floor($tutor['average_rating'])); ?></div>
                                <div class="tutor-actions">
                                    <button onclick="messageTutor(<?php echo $tutor['tutor_Id']; ?>)"><i class="fas fa-envelope"></i></button>
                                    <button class="book" onclick="bookTutor(<?php echo $tutor['tutor_Id']; ?>)"><i class="fas fa-calendar-alt"></i></button>
                                    <button class="rate-review" onclick="rateAndReview(<?php echo $tutor['tutor_Id']; ?>)"><i class="fas fa-star"></i></button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="no-results" style="margin: 240px auto">Tutor not found within that range.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Footer -->
            <footer class="footer">
                <?php require "../footer/footer.php"; ?>
            </footer>
        </div>

        <script>
            function messageTutor(tutorId) {
                window.location.href = "../communication/chat.php?tutor_Id=" + tutorId;
            }

            // Function to redirect to bookTutor page
            function bookTutor(tutorId) {
                // Redirect to the booking page with the tutorId as a query parameter
                window.location.href = "../booking/booking.php?tutor_Id=" + tutorId;
            }

            function rateAndReview(tutorId) {
                window.location.href = "../reviews/review.php?tutor_Id=" + tutorId;
            }
        </script>

        <!-- FontAwesome for the search icon -->
        <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
        <!-- Bootstrap JS and dependencies -->
        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

        <!-- Footer -->
        <?php require_once("../footer/footer.php"); ?>
</body>

</html>