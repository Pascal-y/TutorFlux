<?php
@include '../config.php';

// Initialize the $error array
$error = [];

if (isset($_POST['submit'])) {
    // Escape user inputs to prevent SQL injection
    $Fname = mysqli_real_escape_string($conn, $_POST['Fname']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $contact = mysqli_real_escape_string($conn, $_POST['contact']);
    $age = (int)$_POST['age']; // Make sure age is an integer
    $profile_pic = $_FILES['profile_pic']['name'];
    $pass = mysqli_real_escape_string($conn, $_POST['password']);
    $cpass = mysqli_real_escape_string($conn, $_POST['cpassword']);

    // Validate the profile picture file
    $ext = pathinfo($profile_pic, PATHINFO_EXTENSION);
    $allowedTypes = array("jpg", "png", "jpeg", "gif", "jfif");
    $temp = $_FILES['profile_pic']['tmp_name']; // Corrected key name
    $targetPath = "../uploads/" . $profile_pic;

    // Check if passwords match
    if ($pass !== $cpass) {
        $error[] = 'Passwords do not match!';
    }

    // Ensure password is at least 8 characters long and is not too simple
    elseif (strlen($pass) < 8) {
        $error[] = 'Password must be at least 8 characters long.';
    } else {
        // Hash the password before storing it
        $hashed_password = password_hash($pass, PASSWORD_DEFAULT);
    }

    // Check if the email is already registered
    $select = "SELECT * FROM student WHERE email = '$email'";
    $result = mysqli_query($conn, $select);

    if ($result === false) {
        // Query execution failed
        $error[] = mysqli_error($conn);
    } elseif (mysqli_num_rows($result) > 0) {
        $error[] = 'Student with this email already exists!';
    }

    // Move the uploaded profile picture to the server
    if (empty($error)) {
        if (in_array($ext, $allowedTypes)) {
            if (move_uploaded_file($temp, $targetPath)) {
                // Insert new user data into the database
                $insert = "INSERT INTO student (Fname, email, address, contact, age, profile_pic, pass) 
                        VALUES ('$Fname', '$email', '$address', '$contact', $age, '$profile_pic', '$hashed_password')";
                if (mysqli_query($conn, $insert)) {
                    echo "Registration Successful";
                    header('Location: ./login.php');
                    exit; // Terminate script execution after redirection
                } else {
                    $error[] = 'Failed to register user: ' . mysqli_error($conn);
                }
            } else {
                $error[] = 'Failed to upload profile picture.';
            }
        } else {
            $error[] = 'Invalid Image Type';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../assets/logo.png" type="image/x-icon"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Student Registration</title>
    <link rel="stylesheet" href="signup.css">
</head>

<body>
    <div class="container">
        <div class="signup form">
            <header>Student Registration</header>
            <form action="" method="post" enctype="multipart/form-data">
                <?php
                if (!empty($error)) {
                    foreach ($error as $err) {
                        echo '<span class="error-msg" style="color: red;">' . $err . '</span><br>';
                    }
                }
                ?>
                <input type="text" name="Fname" required placeholder="Full Name" class="form-control">
                <input type="email" name="email" required placeholder="Email" class="form-control">
                <input type="text" name="address" required placeholder="Location" class="form-control">
                <input type="text" name="contact" required placeholder="Contact" class="form-control">
                <input type="number" name="age" required placeholder="Age" class="form-control">
                <label class="file-upload" for="profile_pic" style="font-size: 20px; margin-top:-20px;padding: 20px 0px;">
                    <img class="upload-icon" src="../assets/icons8-upload-48.png" style="margin-bottom: -15px;" />Profile Picture
                    <input type="file" name="profile_pic" required id="profile_pic" class="form-control">
                </label>
                <input type="password" name="password" required placeholder="Password" class="form-control">
                <input type="password" name="cpassword" required placeholder="Confirm Password" class="form-control">
                <input type="submit" name="submit" class="get-started-btn" value="Sign Up">
            </form>
            <span class="login">Already have an account? <a href="./login.php" for="check">Login</a></span>
        </div>
    </div>
</body>

</html>
