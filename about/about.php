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
    <link rel="shortcut icon" href="../assets/logo.png" type="image/x-icon"/>
    <title>About TutorFlux</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #F2F5F9;
            color: #333;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 30px;
        }

        h1 {
            color: #143D59;
            font-size: 2.5rem;
            text-align: center;
            margin-bottom: 20px;
        }

        p {
            font-size: 1.1rem;
            line-height: 1.6;
            margin-bottom: 20px;
        }

        .welcome-section {
            background-color: #143D59;
            color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }

        .mission-section {
            padding: 20px;
            background-color: #E6EEF3;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        a{
            font-size: 20px;
        }
        img{
            border-radius: 4px;
            height: 223px;
        }
        nav{
            background-color: #ff8c00;
            height: 80px;
        }
        nav.expanded {
            margin-bottom: 300px;

            ul{
                margin-top: 20px;
            }
            nav{
                padding-top: 16px;
            }
        }


        /* Responsive Design */
        @media (max-width: 768px) {
            h1 {
                font-size: 2rem;
            }

            p {
                font-size: 1rem;
            }

            .container {
                padding: 10px;
            }
            .navbar-toggler {
                display: block;
                background-color: white;
                border: none;
                font-size: 24px;
                cursor: pointer;
                position: relative;
            }
        }
        
        
    </style>
</head>

<body>
    <!-- Navigation -->
<nav class="navbar navbar-expand-lg navbar-light bg-[#]">
        <a class="navbar-brand" href="#" style="font-size: 30px;font-weight:bold; margin-top:0px; "><img src="../assets/logo.png" style="height: 60px; margin-top: -4px; margin-right: 4px;" alt="">TutorFlux</a>
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
                <li class="nav-item"><a class="nav-link" href="../student/student-dashboard.php"><img src="<?php echo $imageUrl; ?>"  style="height: 50px; width: 50px; margin-top:-8px; border-radius:50%; border: 2px solid white;" style="height: 50px; width: 50px; margin-top:-8px;" /> </a></li>
            </ul>
        </div>
    </nav>


    <div class="container">
        <h1>About TutorFlux</h1>

        <div class="welcome-section">
            <p>Welcome to <strong>TutorFlux</strong>, your gateway to a personalized and enriching learning experience! At TutorFlux, we believe in the power of education to transform lives, and our platform is designed to make that transformation seamless, accessible, and tailored just for you. Whether you are a student seeking to excel in your studies or a parent looking to provide your child with the best educational support, TutorFlux connects you with expert tutors who are ready to guide you on your learning journey.</p>
            <p>Our primary objective is to bridge the gap between learners and expert educators by offering a user-friendly platform that caters to diverse learning needs. TutorFlux is not just about finding a tutor; it's about finding the <em>right</em> tutor. We match you with educators who understand your learning style, academic goals, and personal preferences, ensuring that every tutoring session is productive and inspiring. With TutorFlux, you have access to a wide range of subjects and skills, allowing you to explore new interests, strengthen your knowledge, and achieve your academic objectives.</p>
            <p>At TutorFlux, we are committed to fostering a community of learners and educators who are passionate about education. Our platform promotes a culture of continuous learning and growth, where students and tutors alike can thrive. By focusing on personalized learning, flexibility, and quality, TutorFlux empowers you to take control of your educational journey, making learning not just a task, but an exciting and rewarding experience. Welcome to a world of endless learning possibilities with TutorFlux!</p>
        </div>

        <div class="mission-section">
            <h2>Our Mission</h2>
            <p>At TutorFlux, our mission is to make quality education accessible to everyone, everywhere. We aim to create a global community where students can connect with passionate educators who are dedicated to helping them succeed. By leveraging technology and data-driven insights, TutorFlux provides a personalized learning experience that adapts to the unique needs of each learner.</p>
            <p>We believe that education should be flexible, affordable, and tailored to individual learning styles. Whether you're looking to master a new subject, prepare for exams, or develop a new skill, TutorFlux is here to support you every step of the way. Our platform is designed to be intuitive and easy to use, allowing you to focus on what matters most: learning and growth.</p>
        </div>
    </div>

    <?php require"../footer/footer.php"; ?>
</body>


    <!-- Include Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        // Toggler script to expand the navbar and add margin-bottom
        document.querySelector('.navbar-toggler').addEventListener('click', function() {
            document.querySelector('nav').classList.toggle('expanded');
        });
    </script>
</body>

</html>
