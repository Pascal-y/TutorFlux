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
    $tutor_Id = $tutor_row['tutor_Id'];
    $tutor_name = $tutor_row['Fname'];
    $profile_picture = $tutor_row['profile_pic'];
}

// Fetch students the tutor has chats with
$students_query = "
    SELECT DISTINCT s.student_Id, s.Fname, s.profile_pic 
    FROM student s 
    JOIN chats c ON (c.sender = s.student_Id OR c.receiver = s.student_Id)
    WHERE (c.sender = '$tutor_Id' OR c.receiver = '$tutor_Id')";

$students_result = mysqli_query($conn, $students_query);

// Get student ID from the URL, default to null if not set
$student_id = isset($_GET['student_Id']) ? $_GET['student_Id'] : null;

// Fetch chat messages between the tutor and the selected student
if ($student_id) {
    $chat_query = "
        SELECT * 
        FROM chats 
        WHERE (sender = '$tutor_Id' AND receiver = '$student_id') 
        OR (sender = '$student_id' AND receiver = '$tutor_Id')
        ORDER BY sent_time ASC";

    $chat_result = mysqli_query($conn, $chat_query);
} else {
    $chat_result = false; // No student selected means no chats to display
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../assets/logo.png" type="image/x-icon"/>
    <title>Chats</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles.css"> <!-- Custom styles -->
    
</head>
<body>
    <div class="container-fluid chat-container">
        <div class="row">
            <!-- Sidebar for students -->
            <div class="col-md-3 col-sm-4 col-12 sidebars">
                <div class="profile-list">
                    <h5>Chats <a href="../tutors/tutor-dashboard.php" style="font-size: 12px; text-decoration:none; margin-left: 220px;">Home</a></h5>
                    <ul class="list-group">
                        <?php if (mysqli_num_rows($students_result) > 0): ?>
                            <?php while ($student_row = mysqli_fetch_assoc($students_result)) : ?>
                                <li class="list-group-item profile-item" data-student-id="<?php echo $student_row['student_Id']; ?>">
                                    <img src="../uploads/<?php echo htmlspecialchars($student_row['profile_pic']); ?>" alt="Profile" class="profile-pic">
                                    <span class="user-name"><?php echo htmlspecialchars($student_row['Fname']); ?></span>
                                </li>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <li class="list-group-item">No students to display</li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>

            <!-- Chat area -->
            <div class="col-md-9 col-sm-8 col-12 chat-area">
                <div class="chat-header">
                    <img src="../assets/avatar.png" alt="Student Profile" class="profile-pic-header">
                    <span class="chat-user-name"><?php echo $student_id ? "Chat with Student" : "No chats open"; ?></span>
                </div>
                <div class="chat-messages" id="chatMessages">
                    <?php if ($chat_result && mysqli_num_rows($chat_result) > 0) : ?>
                        <?php while ($chat_row = mysqli_fetch_assoc($chat_result)) : ?>
                            <div class="chat-message <?php echo $chat_row['sender'] == $tutor_Id ? 'sent' : 'received'; ?>">
                                <div class="message-content"><?php echo htmlspecialchars($chat_row['message']); ?></div>
                                <div class="message-time"><?php echo $chat_row['sent_time']; ?></div>
                            </div>
                        <?php endwhile; ?>
                    <?php else : ?>
                        <p><?php echo $student_id ? "No messages yet." : "No chats open."; ?></p>
                    <?php endif; ?>
                </div>
                <?php if ($student_id): ?>
                <div class="chat-input">
                    <input type="text" class="form-control" placeholder="Type your message..." id="messageInput">
                    <button class="btn btn-primary send-btns" onclick="sendMessage()" style="margin-left: 12px; background-color: #143D59; ::hover{background-color:#FF8C00;}">Send</button>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script>
        // Send message via AJAX
        function sendMessage() {
            const messageInput = document.getElementById('messageInput');
            const message = messageInput.value.trim();
            const studentId = new URLSearchParams(window.location.search).get('student_Id');

            if (message && studentId) {
                $.ajax({
                    url: 'send_message.php',
                    type: 'POST',
                    data: {
                        message: message,
                        student_id: studentId
                    },
                    success: function(response) {
                        if (response === 'success') {
                            const chatMessages = document.getElementById('chatMessages');

                            // Create the message element
                            const messageElement = document.createElement('div');
                            messageElement.className = 'chat-message sent';

                            const messageContent = document.createElement('div');
                            messageContent.className = 'message-content';
                            messageContent.innerText = message;

                            messageElement.appendChild(messageContent);
                            chatMessages.appendChild(messageElement);

                            // Clear the input
                            messageInput.value = '';

                            // Scroll to the bottom
                            chatMessages.scrollTop = chatMessages.scrollHeight;
                        } else {
                            console.log('Failed to send message.');
                        }
                    }
                });
            }
        }

        $(document).ready(function() {
            // Handle profile click to switch chats
            $('.profile-item').on('click', function() {
                const studentId = $(this).data('student-id');
                const studentName = $(this).find('.user-name').text();
                const studentImage = $(this).find('img').attr('src');

                // Update chat header
                $('.chat-user-name').text(studentName);
                $('.profile-pic-header').attr('src', studentImage);

                // Update URL with the selected student ID
                const newUrl = `?student_Id=${studentId}`;
                history.pushState(null, '', newUrl);

                // Fetch and display the chat messages for the selected student
                fetchChatMessages(studentId);
            });

            function fetchChatMessages(studentId) {
                $.ajax({
                    url: 'fetch_messages.php',
                    type: 'GET',
                    data: {
                        student_id: studentId
                    },
                    success: function(response) {
                        $('#chatMessages').html(response);
                        $('#chatMessages').scrollTop($('#chatMessages')[0].scrollHeight);
                    }
                });
            }
        });
    </script>
</body>
</html>
