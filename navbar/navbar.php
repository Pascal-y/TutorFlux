<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../assets/logo.png" type="image/x-icon"/>
    <link rel="stylesheet" href="navbar.css">
    <title></title>
</head>

<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-[#]">
        <a class="navbar-brand" href="#" style="font-size: 30px;font-weight:bold; margin-top:0px;">
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
                <li class="nav-item"><a class="nav-link" href="../student/student-dashboard.php"><img src="<?php echo $imageUrl; ?>"  style="height: 50px; width: 50px; margin-top:-8px; border-radius:50%; border: 2px solid white;" /> </a></li>
            </ul>
        </div>
    </nav>
</body>

<script>
    // Toggler script to expand the navbar and add margin-bottom
    document.querySelector('.navbar-toggler').addEventListener('click', function() {
        document.querySelector('nav').classList.toggle('expanded');
    });
</script>

</html>
