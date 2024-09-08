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
    <title>Fluxai Smart Assistant Tutor</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="fluxai.css">
    <link rel="stylesheet" href="../navbar/navbar.css">

    
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
</head>

<body >
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-[#]" style="margin: -14px;">
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
                <li class="nav-item"><a class="nav-link" href="../student/student-dashboard.php"><img src="<?php echo $imageUrl; ?>" style="height: 50px; width: 50px; margin-top:-8px; border-radius:50%; border: 2px solid white;" style="height: 50px; width: 50px; margin-top:-8px;" /> </a></li>
            </ul>
        </div>
    </nav>


    <div class="wrapper">
        <div class="title">Fluxai Smart Tutor</div>
        <div class="form">
            <div class="bot-inbox inbox">
                <div class="icons">
                    <img src="../assets/boticon.png" alt="" style="height: 40px; width: 40px;" />
                </div>
                <div class="msg-header">
                    <p>Welcome to Fluxai! <br> Your Smart Assistance Tutor <br>
                        I'm here to guide you through your learning journey.
                </div>
            </div>
        </div>
        <div class="typing-field">
            <div class="input-data">
                <input id="data" type="text" placeholder="Type something here.." required>
                <button id="send-btn">Send</button>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            $("#send-btn").on("click", function() {
                $value = $("#data").val();
                $msg = '<div class="user-inbox inbox"><div class="msg-header"><p>' + $value + '</p></div><img src="<?php echo $imageUrl; ?>" alt="" style="height: 40px; width: 40px; margin-left: 10px;"/></div>';
                $(".form").append($msg);
                $("#data").val('');

                // start ajax code
                $.ajax({
                    url: 'message.php',
                    type: 'POST',
                    data: 'text=' + $value,
                    success: function(result) {
                        $replay = '<div class="bot-inbox inbox"><div class="icons"><img src="../assets/boticon.png" alt="" style="height: 40px; width: 40px;"/></div><div class="msg-header"><p>' + result + '</p></div></div>';
                        $(".form").append($replay);
                        // when chat goes down the scroll bar automatically comes to the bottom
                        $(".form").scrollTop($(".form")[0].scrollHeight);
                    }
                });
            });
        });
    </script>
</body>
<?php require "../footer/footer.php" ?>
</html>