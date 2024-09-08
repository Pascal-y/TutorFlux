<?php
// Include database connection file
include('../config.php');

// Function to hash password
function hashPassword($password)
{
    return password_hash($password, PASSWORD_DEFAULT);
}

// Initialize error and success messages
$error = "";
$success = "";

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $new_password = trim($_POST["new_password"]);
    $confirm_password = trim($_POST["confirm_password"]);

    // Check if passwords match
    if ($new_password != $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        // Hash the new password
        $hashed_password = hashPassword($new_password);

        // Check if the user exists in the Tutor table
        $query = "SELECT * FROM Tutor WHERE Fname = ? AND email = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss", $name, $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Update the password in the Tutor table
            $updateQuery = "UPDATE Tutor SET pass = ? WHERE Fname = ? AND email = ?";
            $updateStmt = $conn->prepare($updateQuery);
            $updateStmt->bind_param("sss", $hashed_password, $name, $email);

            if ($updateStmt->execute()) {
                $success = "Password has been updated successfully.";
            } else {
                $error = "Error updating password. Please try again.";
            }
        } else {
            // Check if the user exists in the Student table
            $query = "SELECT * FROM Student WHERE Fname = ? AND email = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ss", $name, $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                // Update the password in the Student table
                $updateQuery = "UPDATE Student SET pass = ? WHERE Fname = ? AND email = ?";
                $updateStmt = $conn->prepare($updateQuery);
                $updateStmt->bind_param("sss", $hashed_password, $name, $email);

                if ($updateStmt->execute()) {
                    $success = "Password has been updated successfully.";
                } else {
                    $error = "Error updating password. Please try again.";
                }
            } else {
                $error = "User not found.";
            }
        }

        $stmt->close();
    }
}
?>


<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../assets/logo.png" type="image/x-icon"/>
    <title>Reset Password</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="login.css">
</head>

<body style="background-color: white; margin-top: -40px;">
    <div class="container">
        <div class="wrapper" style="border-bottom:4px solid #ff8c00; border-radius: 8px;">
            <div class="title" style="background-color: #ff8c00;">
                <img src="../assets/logo.png" alt="logo" class="logo"><br>
                <span style="margin: 10px 20px 10px -80px;">Reset Password</span>
            </div>

            <form action="" method="post">
                <div class="pass">
                    <?php if ($error) {
                        echo "<p style='color:red;'>$error</p>";
                    } ?>
                    <?php if ($success) {
                        echo "<p style='color:green;'>$success</p>";
                    } ?>
                </div>
                <div class="row">
                    <i class="fas fa-user"></i>
                    <input type="text" name="name" placeholder="Enter your name" required>
                </div>
                <div class="row">
                    <i class="fas fa-envelope"></i>
                    <input type="email" name="email" placeholder="Enter your email" required>
                </div>
                <div class="row">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="new_password" placeholder="Enter new password" required>
                </div>
                <div class="row">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="confirm_password" placeholder="Confirm new password" required>
                </div>
                <div class="button row">
                    <input type="submit" value="Reset Password">
                </div>
                <div class="pass" style="margin-top: 20px; margin-bottom: -8px;">
                    <a href="./login.php" style="text-decoration: none;">Go back to Login</a>
                </div>
            </form>

        </div>
    </div>
</body>

</html>