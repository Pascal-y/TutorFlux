<?php
@include '../config.php';
session_start();

// Check if the user is logged in and if the role is 'student'
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header('Location: ../login/login.php');
    exit();
}

// Get student information from session
$student_id = $_SESSION['student_id'];
$student_name = $_SESSION['username'];

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['tutor_id'])) {
    $tutor_id = $_GET['tutor_id'];

    $chat_query = "
        SELECT * 
        FROM chats 
        WHERE (sender = '$student_id' AND receiver = '$tutor_id') 
        OR (sender = '$tutor_id' AND receiver = '$student_id')
        ORDER BY sent_time ASC";

    $chat_result = mysqli_query($conn, $chat_query);

    if ($chat_result && mysqli_num_rows($chat_result) > 0) {
        while ($chat_row = mysqli_fetch_assoc($chat_result)) {
            $message_class = ($chat_row['sender'] == $student_id) ? 'sent' : 'received';
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
