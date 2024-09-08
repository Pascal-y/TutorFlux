<?php
@include '../config.php';

session_start();

if (isset($_POST['submit'])) {
    $username = mysqli_real_escape_string($conn, $_POST['name']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Check in Admin table
    $admin_query = "SELECT * FROM Admin WHERE username = '$username'";
    $admin_result = mysqli_query($conn, $admin_query);

    // Check in Student table
    $student_query = "SELECT * FROM student WHERE Fname = '$username' OR email = '$username'";
    $student_result = mysqli_query($conn, $student_query);

    // Check in Tutor table
    $tutor_query = "SELECT * FROM tutor WHERE Fname = '$username' OR email = '$username'";
    $tutor_result = mysqli_query($conn, $tutor_query);

    if ($admin_result === false || $student_result === false || $tutor_result === false) {
        $error[] = mysqli_error($conn);
    } else {
        if (mysqli_num_rows($admin_result) > 0) {
            $admin_row = mysqli_fetch_assoc($admin_result);
            if (password_verify($password, $admin_row['password'])) {
                $_SESSION['role'] = 'admin';
                $_SESSION['username'] = $admin_row['username'];
                header('location:../../../../http://localhost/phpmyadmin/index.php?route=/sql&pos=0&db=tutorflux_db&table=chats');
                exit();
            }
        }

        if (mysqli_num_rows($student_result) > 0) {
            $student_row = mysqli_fetch_assoc($student_result);
            if (password_verify($password, $student_row['pass'])) {
                $_SESSION['role'] = 'student';
                $_SESSION['username'] = $student_row['Fname'];
                header('location:../home/home.php');
                exit();
            }
        }

        if (mysqli_num_rows($tutor_result) > 0) {
            $tutor_row = mysqli_fetch_assoc($tutor_result);
            if (password_verify($password, $tutor_row['pass'])) {
                $_SESSION['role'] = 'tutor';
                $_SESSION['username'] = $tutor_row['Fname'];
                header('location:../tutors/tutor-dashboard.php');
                exit();
            }
        }

        // If credentials don't match any table
        $error[] = 'Invalid username or password!';
    }
}
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../assets/logo.png" type="image/x-icon"/>
    <title>Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="login.css">
</head>
<body style="background-color: white;">
<div class="container">
    <div class="wrapper" style="border-bottom:4px solid #ff8c00; border-radius: 8px;">
        <div class="title" style="background-color: #ff8c00;">
            <img src="../assets/logo.png" alt="logo" class="logo"><br>
            <span class="title-text">Login</span>
        </div>

        <form action="" method="post">
            <?php
            if (isset($error)) {
                foreach ($error as $error) {
                    echo '<span class="error-msg" style="color: red;">' . $error . '</span>';
                }
            }
            ?>
            <div class="row">
                <i class="fas fa-user"></i>
                <input type="text" name="name" placeholder="Email or User Name" required>
            </div>
            <div class="row">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" placeholder="Password" required>
            </div>
            <div class="pass"><a href="./forgot-password.php">Forgot password?</a></div>
            <div class="row button">
                <input type="submit" name="submit" value="Login">
            </div>
            <div class="signup-link">Don't have an Account yet?
                <li class="nav-item dropdown">
                    <a href="#" class="nav-link">Sign Up</a>
                    <div class="dropdown-content">
                        <a href="../login/student.php">Student</a>
                        <a href="../login/tutor.php">Tutor</a>
                    </div>
                </li>
            </div>
        </form>
    </div>
</div>
</body>
</html>
