<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    $_SESSION = array();
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_unset();
    session_destroy();
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Manage school operations, portals, grading, scheduling, and attendance with AWS's premium School Management System.">
    <title>AWS - School Management System</title>
    <!-- Stylesheets -->
    <link rel="stylesheet" href="style.css">
    <!-- FontAwesome for Premium Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- SweetAlert2 for Premium Popups -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- JS Logic -->
    <script src="app.js?v=<?php echo time(); ?>" defer></script>
</head>
<body>

    <!-- Header Navigation -->
    <header id="header">
        <div class="container navbar">
            <a href="#" class="logo" id="logo">
                <img src="images/logo.png" alt="AWS Logo" style="height: 56px; width: auto; border-radius: 6px; -webkit-text-fill-color: initial; margin-right: 0.25rem; vertical-align: middle;"> AWS
            </a>
            <ul class="nav-links" id="nav-links">
                <li><a href="#home">Home</a></li>
                <li><a href="#portals">Portals</a></li>
                <li><a href="#features">Features</a></li>
                <li><a href="#stats">Stats</a></li>
            </ul>
            <div class="nav-actions">
                <button class="theme-toggle" id="theme-toggle" aria-label="Toggle dark/light theme">
                    <i class="fa-solid fa-sun" id="theme-icon"></i>
                </button>
                <a href="login.php" class="btn btn-secondary" id="login-btn"><i class="fa-solid fa-right-to-bracket"></i> Login</a>
                <a href="#portals" class="btn btn-primary" id="get-started-btn">Portal Hub</a>
                <button class="menu-btn" id="menu-btn" aria-label="Toggle navigation menu">
                    <i class="fa-solid fa-bars"></i>
                </button>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero" id="home">
        <div class="container hero-grid">
            <div class="hero-content">
                <h1>Next-Gen <span>School Management</span> Ecosystem</h1>
                <p>Welcome to AWS's smart educational management platform. An all-in-one portal designed to connect administrators, teachers, students, and parents seamlessly in a unified digital workspace.</p>
                <div class="hero-buttons">
                    <a href="#portals" class="btn btn-primary" id="enter-portals-btn">
                        Enter Portals <i class="fa-solid fa-arrow-right"></i>
                    </a>
                    <a href="#features" class="btn btn-secondary" id="learn-more-btn">Learn More</a>
                </div>
            </div>
            <div class="hero-visual">
                <div class="hero-card-glow"></div>
                <!-- Interactive HTML Mockup Dashboard -->
                <div class="hero-image floating">
                    <div style="padding: 1.5rem; background: rgba(0,0,0,0.2); border-bottom: 1px solid var(--card-border); display: flex; align-items: center; justify-content: space-between;">
                        <div style="display: flex; gap: 0.5rem;">
                            <span style="width: 12px; height: 12px; border-radius: 50%; background: #ff5f56; display: inline-block;"></span>
                            <span style="width: 12px; height: 12px; border-radius: 50%; background: #ffbd2e; display: inline-block;"></span>
                            <span style="width: 12px; height: 12px; border-radius: 50%; background: #27c93f; display: inline-block;"></span>
                        </div>
                        <span style="font-size: 0.8rem; opacity: 0.6; font-weight: 600;">Apex Dashboard Preview</span>
                        <i class="fa-solid fa-shield-halved" style="opacity: 0.6;"></i>
                    </div>
                    <div style="padding: 2rem; display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div style="background: var(--input-bg); padding: 1rem; border-radius: 12px; border: 1px solid var(--card-border);">
                            <span style="font-size: 0.8rem; color: var(--text-muted);">Total Active Students</span>
                            <h4 style="font-size: 1.8rem; font-weight: 800; margin-top: 0.25rem;">2,548</h4>
                            <span style="font-size: 0.75rem; color: #27c93f;"><i class="fa-solid fa-arrow-up"></i> +12% this term</span>
                        </div>
                        <div style="background: var(--input-bg); padding: 1rem; border-radius: 12px; border: 1px solid var(--card-border);">
                            <span style="font-size: 0.8rem; color: var(--text-muted);">Average GPA Score</span>
                            <h4 style="font-size: 1.8rem; font-weight: 800; margin-top: 0.25rem;">3.84</h4>
                            <span style="font-size: 0.75rem; color: var(--primary);"><i class="fa-solid fa-chart-line"></i> Top 5% District</span>
                        </div>
                        <div style="grid-column: span 2; background: var(--input-bg); padding: 1rem; border-radius: 12px; border: 1px solid var(--card-border); margin-top: 0.5rem;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                                <span style="font-size: 0.8rem; font-weight: 700;">Weekly Attendance Rate</span>
                                <span style="font-size: 0.75rem; font-weight: 800; color: var(--secondary);">98.6%</span>
                            </div>
                            <div style="height: 8px; width: 100%; background: rgba(255,255,255,0.05); border-radius: 4px; overflow: hidden;">
                                <div style="width: 98.6%; height: 100%; background: linear-gradient(90deg, var(--primary), var(--secondary)); border-radius: 4px;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Portal Selection Hub -->
    <section class="portals" id="portals">
        <div class="container">
            <div class="section-title">
                <h2>Portal <span>Access Hub</span></h2>
                <p>Select your user profile category to access your secure portal dashboard workspace.</p>
            </div>
            
            <div class="portals-grid">
                <!-- Admin Card -->
                <div class="portal-card" data-portal="Administrator" id="portal-admin">
                    <div class="portal-icon">
                        <i class="fa-solid fa-user-gear"></i>
                    </div>
                    <h3>Administrator</h3>
                    <p>Manage courses, teachers, staff, billing, systems configurations, and district metrics.</p>
                    <span class="portal-arrow"><i class="fa-solid fa-arrow-right"></i></span>
                </div>

                <!-- Teacher Card -->
                <div class="portal-card" data-portal="Teacher" id="portal-teacher">
                    <div class="portal-icon">
                        <i class="fa-solid fa-chalkboard-user"></i>
                    </div>
                    <h3>Teacher Portal</h3>
                    <p>Track attendance, manage assignments, input gradebooks, and message student groups.</p>
                    <span class="portal-arrow"><i class="fa-solid fa-arrow-right"></i></span>
                </div>

                <!-- Student Card -->
                <div class="portal-card" data-portal="Student" id="portal-student">
                    <div class="portal-icon">
                        <i class="fa-solid fa-user-graduate"></i>
                    </div>
                    <h3>Student Portal</h3>
                    <p>Check schedules, view assignment feedback, submit homework, and download transcripts.</p>
                    <span class="portal-arrow"><i class="fa-solid fa-arrow-right"></i></span>
                </div>

                <!-- Parent Card -->
                <div class="portal-card" data-portal="Parent" id="portal-parent">
                    <div class="portal-icon">
                        <i class="fa-solid fa-users"></i>
                    </div>
                    <h3>Parent Portal</h3>
                    <p>View student progress updates, monitor report cards, pay tuition fees, and contact teachers.</p>
                    <span class="portal-arrow"><i class="fa-solid fa-arrow-right"></i></span>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features" id="features">
        <div class="container">
            <div class="section-title">
                <h2>Comprehensive <span>Features</span></h2>
                <p>Discover the high-end utility systems powering the modern classroom administrative processes.</p>
            </div>

            <div class="features-grid">
                <!-- Feature 1 -->
                <div class="feature-card">
                    <div class="feature-icon-wrapper">
                        <i class="fa-solid fa-chart-pie"></i>
                    </div>
                    <div class="feature-info">
                        <h3>Academic Analytics</h3>
                        <p>Generate detailed progress charts, track class averages, and optimize students' academic outcomes with data insights.</p>
                    </div>
                </div>

                <!-- Feature 2 -->
                <div class="feature-card">
                    <div class="feature-icon-wrapper">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                    </div>
                    <div class="feature-info">
                        <h3>Smart Attendance</h3>
                        <p>Track daily registers, view historic absences, and automatically send system notifications to parents.</p>
                    </div>
                </div>

                <!-- Feature 3 -->
                <div class="feature-card">
                    <div class="feature-icon-wrapper">
                        <i class="fa-solid fa-credit-card"></i>
                    </div>
                    <div class="feature-info">
                        <h3>Fee Management</h3>
                        <p>Secure online payment processing, digital invoices, and flexible tuition billing structures for parents.</p>
                    </div>
                </div>

                <!-- Feature 4 -->
                <div class="feature-card">
                    <div class="feature-icon-wrapper">
                        <i class="fa-solid fa-book-bookmark"></i>
                    </div>
                    <div class="feature-info">
                        <h3>E-Learning Hub</h3>
                        <p>Access online study documents, participate in digital quizzes, and stream recorded curriculum lectures.</p>
                    </div>
                </div>

                <!-- Feature 5 -->
                <div class="feature-card">
                    <div class="feature-icon-wrapper">
                        <i class="fa-solid fa-envelope-open-text"></i>
                    </div>
                    <div class="feature-info">
                        <h3>Direct Communication</h3>
                        <p>Interactive chats, urgent announcement broadcasts, and custom report alerts sent right to your device.</p>
                    </div>
                </div>

                <!-- Feature 6 -->
                <div class="feature-card">
                    <div class="feature-icon-wrapper">
                        <i class="fa-solid fa-calendar-days"></i>
                    </div>
                    <div class="feature-info">
                        <h3>Schedule Optimizer</h3>
                        <p>Create visual class timetables, manage exam schedulers, and prevent double-booking conflicts automatically.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Statistics Counters -->
    <section class="stats" id="stats">
        <div class="container stats-grid">
            <div class="stat-item">
                <h3 class="counter" data-target="2500">0</h3>
                <p>Students Enrolled</p>
            </div>
            <div class="stat-item">
                <h3 class="counter" data-target="120">0</h3>
                <p>Expert Instructors</p>
            </div>
            <div class="stat-item">
                <h3 class="counter" data-target="85">0</h3>
                <p>Active Classrooms</p>
            </div>
            <div class="stat-item">
                <h3 class="counter" data-target="99">0</h3>
                <p>Success Rate %</p>
            </div>
        </div>
    </section>

    <!-- Login Modal -->
    <div class="modal-overlay" id="login-modal-overlay">
        <div class="modal-container" id="login-modal-container">
            <button class="modal-close" id="modal-close" aria-label="Close modal">&times;</button>
            <div class="modal-header">
                <h2><span id="portal-title-prefix">Student</span> Portal Access</h2>
                <p>Please enter your credentials below to log in.</p>
            </div>
            <!-- Mock Login Form -->
            <form id="portal-login-form">
                <div id="login-error" style="color: var(--accent); font-size: 0.85rem; margin-bottom: 1rem; display: none; text-align: center; font-weight: 600;"></div>
                <div class="form-group">
                    <label class="form-label" for="username" id="username-label">Username / ID</label>
                    <div class="input-wrapper">
                        <i class="fa-solid fa-user input-icon"></i>
                        <input type="text" id="username" class="form-control" placeholder="Enter your identifier" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <div class="input-wrapper">
                        <i class="fa-solid fa-lock input-icon"></i>
                        <input type="password" id="password" class="form-control" placeholder="Enter your secure password" required>
                    </div>
                </div>

                <div class="form-options">
                    <label class="remember-me">
                        <input type="checkbox" id="remember-me"> Remember me
                    </label>
                    <a href="#" class="forgot-password" id="forgot-password-link" onclick="openForgotModalDirect(event)">Forgot Password?</a>
                </div>

                <button type="submit" class="btn btn-primary submit-btn" id="login-submit-btn">
                    Authenticate Securely <i class="fa-solid fa-shield-check"></i>
                </button>
            </form>
        </div>
    </div>

    <!-- Forgot Password Modal -->
    <div class="modal-overlay" id="forgot-modal-overlay">
        <div class="modal-container" id="forgot-modal-container">
            <button class="modal-close" id="forgot-modal-close" aria-label="Close modal" onclick="closeForgotModalDirect(event)">&times;</button>
            <div class="modal-header">
                <h2 id="forgot-modal-title">Reset Password</h2>
                <p id="forgot-modal-desc">Please enter your Username or ID to recover your password.</p>
            </div>
            <form id="forgot-password-form">
                <!-- Step 1: Username / ID Input -->
                <div id="forgot-step-1">
                    <div class="form-group">
                        <label class="form-label" for="forgot-username">Username / ID</label>
                        <div class="input-wrapper">
                            <i class="fa-solid fa-user input-icon"></i>
                            <input type="text" id="forgot-username" class="form-control" placeholder="Enter your Username / ID" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary submit-btn" id="forgot-submit-btn">
                        Forgot Password <i class="fa-solid fa-key"></i>
                    </button>
                </div>

                <!-- Step 2: Set New Password (Opened after verification) -->
                <div id="forgot-step-2" style="display: none;">
                    <div class="form-group">
                        <label class="form-label" for="forgot-new-password">New Password</label>
                        <div class="input-wrapper">
                            <i class="fa-solid fa-lock input-icon"></i>
                            <input type="password" id="forgot-new-password" class="form-control" placeholder="Enter new password">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="forgot-confirm-password">Confirm New Password</label>
                        <div class="input-wrapper">
                            <i class="fa-solid fa-circle-check input-icon"></i>
                            <input type="password" id="forgot-confirm-password" class="form-control" placeholder="Confirm new password">
                        </div>
                    </div>

                    <button type="button" class="btn btn-primary submit-btn" id="forgot-update-btn">
                        Update Password <i class="fa-solid fa-check"></i>
                    </button>
                </div>
                
                <div style="margin-top: 1.5rem; text-align: center; font-size: 0.85rem; color: var(--text-muted);">
                    Remembered credentials? <a href="#" id="back-to-login-btn" style="color: var(--primary); text-decoration: none; font-weight: 700;" onclick="backToLoginDirect(event)">Sign In</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <div class="container footer-grid">
            <div class="footer-brand">
                <a href="#" class="logo" style="margin-bottom: 1.5rem; display: inline-flex; align-items: center; gap: 0.25rem;">
                    <img src="images/logo.png" alt="AWS Logo" style="height: 56px; width: auto; border-radius: 6px; -webkit-text-fill-color: initial; vertical-align: middle;"> AWS
                </a>
                <p>Providing cutting-edge digital infrastructure to streamline operations, facilitate communications, and enrich class learning environments.</p>
            </div>
            <div class="footer-col">
                <h4>Sitemap</h4>
                <ul class="footer-links">
                    <li><a href="#home">Home</a></li>
                    <li><a href="#portals">Portals</a></li>
                    <li><a href="#features">Features</a></li>
                    <li><a href="#stats">Stats</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Support</h4>
                <ul class="footer-links">
                    <li><a href="#">Knowledge Base</a></li>
                    <li><a href="#">Help Center</a></li>
                    <li><a href="#">System Status</a></li>
                    <li><a href="#">Report Issue</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Contact Info</h4>
                <ul class="footer-links">
                    <li><a href="tel:+1234567890"><i class="fa-solid fa-phone"></i> +1 (234) 567-890</a></li>
                    <li><a href="mailto:info@apexacademy.edu"><i class="fa-solid fa-envelope"></i> info@apexacademy.edu</a></li>
                    <li><span style="font-size: 0.95rem;"><i class="fa-solid fa-location-dot"></i> 742 Evergreen Terrace, Springfield</span></li>
                </ul>
            </div>
        </div>
        <div class="container footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> AWS. All rights reserved.</p>
            <p>Designed for excellence in education management.</p>
        </div>
    </footer>

</body>
</html>
