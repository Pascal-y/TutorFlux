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
    <title>TutorFlux</title>
    <!-- Bootstrap CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            font-weight: 500;
            animation: zoomOut 1s ease-out forwards;
        }
        
        .features-section {
            padding: 20px 0;
        }
        .testimonial-section {
            background-color: #f8f9fa;
            padding: 50px 0;
        }
        .footer {
            background-color: #343a40;
            color: white;
            padding: 20px 0;
        }
        img{
            border-radius: 4px;
            height: 223px;
        }
        
        nav{
            background-color: #ff8c00;
            height: 80px;
        }
        p{
            margin: 0px 80px;
            justify-content: center;
            font-size: 20px;
        }

        body {
    font-family: 'Arial', sans-serif;
    font-size: 24px;
    font-weight: 500;
}

.hero-section {
    padding: 40px 20px;
    text-align: center;
    background-color: #143D59;
    color: #ffffff;
    border-radius: 8px;
    margin: 50px auto;
    max-width: 1110px;
    box-sizing: border-box;
    
}

.hero-section h1 {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 20px;
}

.hero-section p {
    font-size: 1.25rem;
    line-height: 1.6;
    margin-bottom: 15px;
    
}

/* Zoom Out Animation */
@keyframes zoomOut {
            from {
                transform: scale(3); /* Start 3 times larger */
            }
            to {
                transform: scale(1); /* End at normal size */
            }
        }
        
    @keyframes shake {
            0%, 100% {
                transform: translateX(0);
            }
            25% {
                transform: translateX(-5px);
            }
            50% {
                transform: translateX(5px);
            }
            75% {
                transform: translateX(-5px);
            }
        }

/* Responsive Design */
@media (max-width: 768px) {
    .hero-section {
        padding: 30px 15px;
        margin: 20px 40px;
    }

    .hero-section h1 {
        font-size: 2rem;
    }

    .hero-section p {
        font-size: 1rem;
        margin: 10px;
    }

    .features-section img {
        height: auto;
        width: 100%;
    }
    p{
        font-size: 14px;
    }
}

    
    </style>
</head>
<?php require_once("../navbar/navbar.php"); ?>
<body>
    

    <!-- Hero Section -->
    <div class="hero-section">
        <h1 style="font-size: 35px; font-weight:700; padding:3px; ">Welcome to TutorFlux</h1>
        <p>Your Centralized Tutor Booking Platform To Streamline Learning Processes</p>
        <p>Your gateway to a personalized and enriching learning experience! At TutorFlux, we believe in the power of education to transform lives, and our platform is designed to make that transformation seamless, accessible, and tailored just for you.</p>
        <p>Whether you are a student seeking to excel in your studies or a parent looking to provide your child with the best educational support, TutorFlux connects you with expert tutors who are ready to guide you on your learning journey.</p>
    </div>

    <!-- Features Section -->
    <div class="container features-section">
        <div class="row">
            <div class="col-md-4 text-center">
                <img src="../assets/expert_tutor.jfif" alt="Feature 1" class="img-fluid" style="height: 253px; width: 417px;">
                <h3>Expert Tutors</h3>
                <p>Connect with top-tier tutors to elevate your learning experience.</p>
            </div>
            <div class="col-md-4 text-center">
                <img src="../assets/flexible_learning.webp" alt="Feature 2" class="img-fluid" style="height: 253px; width: 417px;">
                <h3>Flexible Learning</h3>
                <p>Learn at your own pace with flexible scheduling options.</p>
            </div>
            <div class="col-md-4 text-center">
                <img src="../assets/interactive.jpg" alt="Feature 3" class="img-fluid" style="height: 253px; width: 417px;">
                <h3>Interactive Courses</h3>
                <p>Engage with comprehensive courses designed to spark curiosity.</p>
            </div>
        </div>
    </div>

    

    <!-- Testimonial Section -->
    <div class="testimonial-section">
        <div class="container">
            <h2 class="text-center">What Our Users Say</h2>
            <div class="row">
                <div class="col-md-4">
                    <blockquote class="blockquote">
                        <p class="mb-0">"TutorFlux has transformed my learning experience. The tutors are incredibly knowledgeable."</p>
                        <footer class="blockquote-footer">Daniel Tracy</footer>
                    </blockquote>
                </div>
                <div class="col-md-4">
                    <blockquote class="blockquote">
                        <p class="mb-0">"The flexibility of TutorFlux allows me to learn on my schedule, making education accessible."</p>
                        <footer class="blockquote-footer">Shiyntum Clovis</footer>
                    </blockquote>
                </div>
                <div class="col-md-4">
                    <blockquote class="blockquote">
                        <p class="mb-0">"I love the interactive courses; they make learning so much fun and engaging."</p>
                        <footer class="blockquote-footer">Dzelanyuy Kean</footer>
                    </blockquote>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php require_once("../footer/footer.php"); ?>

    <script>
        // Function to redirect to home page
        function redirectToLogin() {
            window.location.href = '../login/login.php'; // Replace with your actual home page link
        }
    </script>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
