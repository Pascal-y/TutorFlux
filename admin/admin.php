<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Responsive Admin Dashboard</title>
    <link rel="stylesheet" href="dashboard.css">
</head>

<body>
    <div class="wrapper">
        <header>
            <nav class="navbar">
                <div class="logo">Admin Dashboard</div>
                <div class="menu-toggle" id="mobile-menu">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </nav>
        </header>

        <div class="main-section">
            <aside class="sidebar">
                <ul>
                    <li><a href="#" onclick="showSection('section1')">Dashboard</a></li>
                    <li><a href="#" onclick="showSection('section2')">Users</a></li>
                    <li><a href="#" onclick="showSection('section3')">Settings</a></li>
                    <li><a href="#" onclick="showSection('section4')">Reports</a></li>
                </ul>
            </aside>

            <div class="content">
                <div id="section1" class="section active">
                    <h1>Dashboard Overview</h1>
                    <p>This is the dashboard overview.</p>
                </div>
                <div id="section2" class="section">
                    <h1>User Management</h1>
                    <p>This section contains user management functionalities.</p>
                </div>
                <div id="section3" class="section">
                    <h1>Settings</h1>
                    <p>This section contains settings options.</p>
                </div>
                <div id="section4" class="section">
                    <h1>Reports</h1>
                    <p>This section contains report data.</p>
                </div>
            </div>
        </div>

        <footer>
            <p>Admin Dashboard © 2024</p>
        </footer>
    </div>

    <script>
        document.getElementById('mobile-menu').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('active');
        });

        function showSection(sectionId) {
            const sections = document.querySelectorAll('.section');
            sections.forEach(section => section.classList.remove('active'));

            document.getElementById(sectionId).classList.add('active');
        }
    </script>
</body>

</html>