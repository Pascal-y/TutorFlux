<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="./assets/logo.png" type="image/x-icon"/>
    <title>TutorFlux</title>
    <style>
        /* Body and Container */
        body, html {
            height: 100%;
            margin: 0;
            font-family: 'Poppins', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #ff8c00; 
            color: black;
            text-align: center;
        }
        .container {
            display: flex;
            flex-direction: column;
            align-items: center;
            max-width: 90%;
            width: 100%;
        }

        /* App Icon */
        .app-icon {
            width: 300px;
            height: 300px;
            border-radius: 50%;
            margin-bottom: 20px;
            background: url('./assets/logo.png') no-repeat center center/cover; /* Replace with your actual app icon */
            animation: zoomOut 1.5s ease-out forwards, shake 0.5s ease-in-out 1.5s infinite;
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

        /* Shake Animation */
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
            .app-name {
                font-size: 28px;
            }
            .app-description {
                font-size: 16px;
                padding: 0 15px;
            }
            .get-started-btn {
                font-size: 16px;
                padding: 12px 25px;
            }
        }

        @media (max-width: 480px) {
            .app-icon {
                width: 150px;
                height: 150px;
            }
            .app-name {
                font-size: 24px;
            }
            .app-description {
                font-size: 14px;
                padding: 0 10px;
            }
            .get-started-btn {
                font-size: 14px;
                padding: 10px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="app-icon"></div>
    </div>

    <script>
        // Function to redirect to login page
        function redirectToLogin() {
            window.location.href = './login/login.php'; // Replace with your actual login page link
        }

        // Auto redirect after 2.5 seconds
        setTimeout(redirectToLogin, 3500);
    </script>
</body>
</html>
