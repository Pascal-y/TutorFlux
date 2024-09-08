<?php
@include '../config.php';
session_start();

// Check if the user is logged in and if the role is 'tutor'
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'tutor') {
    header('Location: ../login/login.php');
    exit();
}


// Get tutor information from session
$tutor_username = $_SESSION['username'];


// Fetch tutor details from the database
$tutor_query = "SELECT * FROM tutor WHERE Fname = '$tutor_username'";
$tutor_result = mysqli_query($conn, $tutor_query);

if (mysqli_num_rows($tutor_result) > 0) {
    $tutor_row = mysqli_fetch_assoc($tutor_result);
    $tutor_id = $tutor_row['tutor_Id'];
    $tutor_name = $tutor_row['Fname'];
    $profile_picture = $tutor_row['profile_pic'];
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['student_id'])) {
    $student_id = $_GET['student_id'];

    $chat_query = "
        SELECT * 
        FROM chats 
        WHERE (sender = '$tutor_id' AND receiver = '$student_id') 
        OR (sender = '$student_id' AND receiver = '$tutor_id')
        ORDER BY sent_time ASC";

    $chat_result = mysqli_query($conn, $chat_query);

    if ($chat_result && mysqli_num_rows($chat_result) > 0) {
        while ($chat_row = mysqli_fetch_assoc($chat_result)) {
            $message_class = ($chat_row['sender'] == $tutor_id) ? 'sent' : 'received';
            echo "
            <div class='chat-message $message_class'>
                <div class='message-content'>" . htmlspecialchars($chat_row['message']) . "</div>
                <div class='message-time' style='font-size: 6px;'>" . $chat_row['sent_time'] . "</div>
            </div>";
        }
    } else {
        echo '<p>No messages yet.</p>';
    }

    mysqli_close($conn);
}
?>
