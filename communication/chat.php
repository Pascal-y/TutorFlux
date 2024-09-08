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

// Fetch tutors the student has chats with
$tutors_query = "
    SELECT DISTINCT t.tutor_Id, t.Fname, t.profile_pic 
    FROM tutor t 
    JOIN chats c ON (c.sender = t.tutor_Id OR c.receiver = t.tutor_Id)
    WHERE (c.sender = '$student_id' OR c.receiver = '$student_id')";

$tutors_result = mysqli_query($conn, $tutors_query);

// Get tutor ID from the URL
$tutor_id = isset($_GET['tutor_Id']) ? $_GET['tutor_Id'] : null;

// Fetch chat messages between the student and the selected tutor
if ($tutor_id) {
    $chat_query = "
        SELECT * 
        FROM chats 
        WHERE (sender = '$student_id' AND receiver = '$tutor_id') 
        OR (sender = '$tutor_id' AND receiver = '$student_id')
        ORDER BY sent_time ASC";

    $chat_result = mysqli_query($conn, $chat_query);
} else {
    $chat_result = false; // No tutor selected
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
            <!-- Sidebar for tutors -->
            <div class="col-md-3 col-sm-4 col-12 sidebar">
                <div class="profile-list">
                    <h5>Chats <a href="../tutors/tutors.php" style="font-size: 12px; text-decoration:none; margin-left: 220px;">Back</a></h5>
                    <ul class="list-group">
                        <?php while ($tutor_row = mysqli_fetch_assoc($tutors_result)) : ?>
                            <?php
                            $tutor_image_url = "../uploads/" . htmlspecialchars($tutor_row['profile_pic']);
                            $tutor_name = htmlspecialchars($tutor_row['Fname']);
                            ?>
                            <li class="list-group-item profile-item" data-tutor-id="<?php echo $tutor_row['tutor_Id']; ?>">
                                <img src="<?php echo $tutor_image_url; ?>" alt="Profile" class="profile-pic">
                                <span class="user-name"><?php echo $tutor_name; ?></span>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                </div>
            </div>

            <!-- Chat area -->
            <div class="col-md-9 col-sm-8 col-12 chat-area">
                <div class="chat-header">
                    <img src="../assets/avatar.png" alt="Tutor Profile" class="profile-pic-header">
                    <span class="chat-user-name">
                        <?php if ($tutor_id): ?>
                            Chat with Tutor
                        <?php else: ?>
                            No Tutor Selected
                        <?php endif; ?>
                    </span>
                </div>
                <div class="chat-messages" id="chatMessages">
                    <?php if ($tutor_id): ?>
                        <?php if ($chat_result && mysqli_num_rows($chat_result) > 0) : ?>
                            <?php while ($chat_row = mysqli_fetch_assoc($chat_result)) : ?>
                                <div class="chat-message <?php echo $chat_row['sender'] == $student_id ? 'sent' : 'received'; ?>">
                                    <div class="message-content"><?php echo htmlspecialchars($chat_row['message']); ?></div>
                                    <div class="message-time"><?php echo $chat_row['sent_time']; ?></div>
                                </div>
                            <?php endwhile; ?>
                        <?php else : ?>
                            <p>No Chats yet.</p>
                        <?php endif; ?>
                    <?php else: ?>
                        <p>Please select a tutor to view chats.</p>
                    <?php endif; ?>
                </div>
                <div class="chat-input">
                    <input type="text" class="form-control" placeholder="Type your message..." id="messageInput" <?php echo !$tutor_id ? 'disabled' : ''; ?>>
                    <button class="btn btn-primary send-btn" onclick="sendMessage()" <?php echo !$tutor_id ? 'disabled' : ''; ?>>Send</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script>
        function sendMessage() {
            const messageInput = document.getElementById('messageInput');
            const message = messageInput.value.trim();
            const tutorId = new URLSearchParams(window.location.search).get('tutor_Id');

            if (message && tutorId) {
                $.ajax({
                    url: 'send_message.php',
                    type: 'POST',
                    data: {
                        message: message,
                        tutor_id: tutorId
                    },
                    success: function(response) {
                        if (response === 'success') {
                            const chatMessages = document.getElementById('chatMessages');

                            const messageElement = document.createElement('div');
                            messageElement.className = 'chat-message sent';

                            const messageContent = document.createElement('div');
                            messageContent.className = 'message-content';
                            messageContent.innerText = message;

                            messageElement.appendChild(messageContent);
                            chatMessages.appendChild(messageElement);

                            messageInput.value = '';
                            chatMessages.scrollTop = chatMessages.scrollHeight;
                        } else {
                            console.log('Failed to send message.');
                        }
                    }
                });
            }
        }

        $(document).ready(function() {
            $('.profile-item').on('click', function() {
                const tutorId = $(this).data('tutor-id');
                const tutorName = $(this).find('.user-name').text();
                const tutorImage = $(this).find('img').attr('src');

                $('.chat-user-name').text(`Chat with ${tutorName}`);
                $('.profile-pic-header').attr('src', tutorImage);

                const newUrl = `?tutor_Id=${tutorId}`;
                history.pushState(null, '', newUrl);

                fetchChatMessages(tutorId);
            });

            function fetchChatMessages(tutorId) {
                $.ajax({
                    url: 'fetch_messages.php',
                    type: 'GET',
                    data: {
                        tutor_id: tutorId
                    },
                    success: function(response) {
                        $('#chatMessages').html(response);
                        $('#chatMessages').scrollTop($('#chatMessages')[0].scrollHeight);
                    }
                });
            }
        });
    </script> <!-- Custom scripts -->
</body>

</html>
