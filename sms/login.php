<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Handle Logout if action=logout is present
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
}

// If already logged in, redirect to respective dashboard
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: admin_dashboard.php');
    exit;
} elseif (isset($_SESSION['teacher_logged_in']) && $_SESSION['teacher_logged_in'] === true) {
    header('Location: teacher_dashboard.php');
    exit;
} elseif (isset($_SESSION['student_logged_in']) && $_SESSION['student_logged_in'] === true) {
    header('Location: student_dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Secure All-in-One Portal Authentication - AWS School Management System">
    <title>Sign In | AWS School Portal</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="images/logo.png">
    <!-- Google Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap">
    <!-- FontAwesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Base Stylesheet -->
    <link rel="stylesheet" href="style.css">

    <style>
        body.login-page {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow-x: hidden;
            background-color: var(--bg-main);
        }

        /* Ambient Glow Background Blobs */
        .ambient-blob-1 {
            position: fixed;
            top: -10%;
            left: -10%;
            width: 50vw;
            height: 50vw;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(99, 102, 241, 0.18) 0%, rgba(99, 102, 241, 0) 70%);
            filter: blur(50px);
            z-index: 0;
            pointer-events: none;
            animation: pulse-slow 12s ease-in-out infinite alternate;
        }

        .ambient-blob-2 {
            position: fixed;
            bottom: -15%;
            right: -10%;
            width: 55vw;
            height: 55vw;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(6, 182, 212, 0.16) 0%, rgba(6, 182, 212, 0) 70%);
            filter: blur(60px);
            z-index: 0;
            pointer-events: none;
            animation: pulse-slow 10s ease-in-out infinite alternate-reverse;
        }

        @keyframes pulse-slow {
            0% { transform: scale(1) translate(0, 0); }
            100% { transform: scale(1.15) translate(30px, 20px); }
        }

        /* Top Bar */
        .login-topbar {
            position: relative;
            z-index: 10;
            padding: 1.25rem 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            max-width: 1300px;
            margin: 0 auto;
            width: 100%;
        }

        .login-topbar .logo-link {
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
            color: var(--text-main);
            font-size: 1.5rem;
            font-weight: 800;
            letter-spacing: -0.5px;
        }

        .login-topbar .logo-link img {
            height: 44px;
            width: auto;
            border-radius: 8px;
        }

        .topbar-actions {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .btn-ghost-nav {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.6rem 1.1rem;
            border-radius: 10px;
            font-size: 0.88rem;
            font-weight: 600;
            color: var(--text-muted);
            text-decoration: none;
            background: var(--input-bg);
            border: 1px solid var(--card-border);
            transition: all 0.25s ease;
        }

        .btn-ghost-nav:hover {
            color: var(--text-main);
            border-color: var(--primary);
            background: rgba(99, 102, 241, 0.1);
            transform: translateY(-1px);
        }

        /* Main Container */
        .login-main-wrapper {
            position: relative;
            z-index: 10;
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2.5rem 1.5rem;
        }

        .login-card {
            width: 100%;
            max-width: 460px;
            background: var(--card-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--card-border);
            border-radius: 24px;
            padding: 2.5rem 2.25rem;
            box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.4);
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .login-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
        }

        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .login-header-icon {
            width: 68px;
            height: 68px;
            margin: 0 auto 1.25rem;
            border-radius: 20px;
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.15), rgba(6, 182, 212, 0.15));
            border: 1px solid var(--card-border);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: var(--primary);
            box-shadow: 0 10px 25px var(--primary-glow);
        }

        .login-header h1 {
            font-size: 1.8rem;
            font-weight: 800;
            margin-bottom: 0.45rem;
            letter-spacing: -0.5px;
            color: var(--text-main);
        }

        .login-header h1 span {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .login-header p {
            color: var(--text-muted);
            font-size: 0.92rem;
            line-height: 1.5;
        }

        /* Form Controls */
        .form-group {
            margin-bottom: 1.35rem;
        }

        .form-label {
            display: block;
            font-size: 0.88rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--text-main);
        }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-icon {
            position: absolute;
            left: 1.1rem;
            color: var(--text-muted);
            font-size: 1.05rem;
            pointer-events: none;
            transition: color 0.3s ease;
        }

        .form-control {
            width: 100%;
            padding: 0.85rem 2.75rem 0.85rem 2.85rem;
            background: var(--input-bg);
            border: 1px solid var(--card-border);
            border-radius: 12px;
            color: var(--text-main);
            font-size: 0.95rem;
            outline: none;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px var(--primary-glow);
            background: rgba(255, 255, 255, 0.07);
        }

        [data-theme="light"] .form-control:focus {
            background: #ffffff;
        }

        .password-toggle-btn {
            position: absolute;
            right: 1rem;
            background: none;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            font-size: 1rem;
            padding: 0.25rem;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: color 0.2s ease;
        }

        .password-toggle-btn:hover {
            color: var(--primary);
        }

        /* Form Options & Links */
        .form-row-options {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.6rem;
            font-size: 0.86rem;
        }

        .custom-checkbox {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
            color: var(--text-muted);
            user-select: none;
        }

        .custom-checkbox input[type="checkbox"] {
            accent-color: var(--primary);
            width: 16px;
            height: 16px;
            cursor: pointer;
        }

        .forgot-link {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .forgot-link:hover {
            text-decoration: underline;
            color: var(--secondary);
        }

        /* Submit Button */
        .submit-login-btn {
            width: 100%;
            padding: 0.95rem 1.5rem;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 700;
            color: #ffffff;
            background: linear-gradient(135deg, var(--primary) 0%, #4f46e5 100%);
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.65rem;
            box-shadow: 0 10px 25px rgba(99, 102, 241, 0.35);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .submit-login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 30px rgba(99, 102, 241, 0.45);
        }

        .submit-login-btn:active {
            transform: translateY(0);
        }

        .submit-login-btn:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }

        /* Alert Banner */
        .login-alert-box {
            display: none;
            align-items: center;
            gap: 0.65rem;
            padding: 0.85rem 1rem;
            border-radius: 12px;
            font-size: 0.86rem;
            font-weight: 600;
            margin-bottom: 1.25rem;
            animation: fadeIn 0.3s ease;
        }

        .login-alert-box.error {
            display: flex;
            background: rgba(244, 63, 94, 0.12);
            border: 1px solid rgba(244, 63, 94, 0.3);
            color: #fb7185;
        }

        .login-alert-box.success {
            display: flex;
            background: rgba(39, 201, 63, 0.12);
            border: 1px solid rgba(39, 201, 63, 0.3);
            color: #34d399;
        }

        /* Quick Help Section */
        .quick-help {
            margin-top: 1.75rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--card-border);
            text-align: center;
            font-size: 0.85rem;
            color: var(--text-muted);
        }

        .quick-help a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 700;
            transition: color 0.2s;
        }

        .quick-help a:hover {
            text-decoration: underline;
        }

        /* Footer */
        .login-footer {
            position: relative;
            z-index: 10;
            padding: 1.5rem;
            text-align: center;
            font-size: 0.82rem;
            color: var(--text-muted);
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-6px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 576px) {
            .login-card {
                padding: 2rem 1.5rem;
                border-radius: 18px;
            }
            .login-topbar {
                padding: 1rem 1.25rem;
            }
        }
    </style>
</head>
<body class="login-page">

    <!-- Ambient Glow Background -->
    <div class="ambient-blob-1"></div>
    <div class="ambient-blob-2"></div>

    <!-- Top Navigation -->
    <nav class="login-topbar">
        <a href="index.php" class="logo-link" title="Return to AWS Homepage">
            <img src="images/logo.png" alt="AWS Logo"> AWS
        </a>
        <div class="topbar-actions">
            <button class="theme-toggle" id="theme-toggle" aria-label="Toggle Dark/Light Theme">
                <i class="fa-solid fa-sun" id="theme-icon"></i>
            </button>
            <a href="index.php" class="btn-ghost-nav">
                <i class="fa-solid fa-house"></i> <span>Home</span>
            </a>
        </div>
    </nav>

    <!-- Main Login Section -->
    <main class="login-main-wrapper">
        <div class="login-card">
            
            <div class="login-header">
                <div class="login-header-icon">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
                <h1>Portal <span>Sign In</span></h1>
                <p>Welcome! Enter your account credentials to access your portal.</p>
            </div>

            <!-- Alert Notification -->
            <div class="login-alert-box" id="login-alert">
                <i class="fa-solid fa-circle-exclamation"></i>
                <span id="login-alert-text">Invalid credentials. Please try again.</span>
            </div>

            <!-- Universal Login Form -->
            <form id="standalone-login-form" autocomplete="on">
                <!-- Identifier Field -->
                <div class="form-group">
                    <label class="form-label" for="login-identifier">Username / ID / Email</label>
                    <div class="input-wrapper">
                        <i class="fa-solid fa-user input-icon"></i>
                        <input type="text" id="login-identifier" name="username" class="form-control" placeholder="Enter Username, Teacher ID, or Student ID" required autofocus autocomplete="username">
                    </div>
                </div>

                <!-- Password Field -->
                <div class="form-group">
                    <label class="form-label" for="login-password">Password</label>
                    <div class="input-wrapper">
                        <i class="fa-solid fa-lock input-icon"></i>
                        <input type="password" id="login-password" name="password" class="form-control" placeholder="Enter your password" required autocomplete="current-password">
                        <button type="button" class="password-toggle-btn" id="password-toggle-btn" aria-label="Show password" tabindex="-1">
                            <i class="fa-solid fa-eye" id="password-toggle-icon"></i>
                        </button>
                    </div>
                </div>

                <!-- Remember & Forgot Options -->
                <div class="form-row-options">
                    <label class="custom-checkbox">
                        <input type="checkbox" id="remember-me" name="remember">
                        <span>Remember me</span>
                    </label>
                    <a href="javascript:void(0)" class="forgot-link" id="login-forgot-trigger">Forgot Password?</a>
                </div>

                <!-- Action Button -->
                <button type="submit" class="submit-login-btn" id="login-btn-submit">
                    <span id="btn-text">Sign In</span>
                    <i class="fa-solid fa-arrow-right" id="btn-icon"></i>
                </button>
            </form>

        </div>
    </main>

    <!-- Forgot Password Modal -->
    <div class="modal-overlay" id="forgot-modal-overlay">
        <div class="modal-container" id="forgot-modal-container">
            <button class="modal-close" id="forgot-modal-close" aria-label="Close modal">&times;</button>
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

                    <button type="submit" class="btn btn-primary submit-btn" id="forgot-submit-btn" style="width: 100%;">
                        Verify Account <i class="fa-solid fa-key"></i>
                    </button>
                </div>

                <!-- Step 2: Set New Password -->
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

                    <button type="button" class="btn btn-primary submit-btn" id="forgot-update-btn" style="width: 100%;">
                        Update Password <i class="fa-solid fa-check"></i>
                    </button>
                </div>
                
                <div style="margin-top: 1.5rem; text-align: center; font-size: 0.85rem; color: var(--text-muted);">
                    Remembered your password? <a href="javascript:void(0)" id="back-to-login-link" style="color: var(--primary); text-decoration: none; font-weight: 700;">Sign In</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Footer -->
    <footer class="login-footer">
        &copy; <?php echo date('Y'); ?> AWS School Management System. All rights reserved.
    </footer>

    <!-- Interactive Logic -->
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        // ---------------------------------------------------------------------
        // Theme Engine
        // ---------------------------------------------------------------------
        const themeToggleBtn = document.getElementById('theme-toggle');
        const themeIcon = document.getElementById('theme-icon');
        const htmlElement = document.documentElement;

        const savedTheme = localStorage.getItem('theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
        setTheme(savedTheme);

        if (themeToggleBtn) {
            themeToggleBtn.addEventListener('click', () => {
                const current = htmlElement.getAttribute('data-theme') || 'dark';
                const next = current === 'dark' ? 'light' : 'dark';
                setTheme(next);
            });
        }

        function setTheme(theme) {
            htmlElement.setAttribute('data-theme', theme);
            localStorage.setItem('theme', theme);
            if (themeIcon) {
                if (theme === 'dark') {
                    themeIcon.className = 'fa-solid fa-sun';
                    if (themeToggleBtn) themeToggleBtn.style.color = '#eab308';
                } else {
                    themeIcon.className = 'fa-solid fa-moon';
                    if (themeToggleBtn) themeToggleBtn.style.color = '#4f46e5';
                }
            }
        }

        // ---------------------------------------------------------------------
        // Password Show/Hide Toggle
        // ---------------------------------------------------------------------
        const passwordInput = document.getElementById('login-password');
        const passwordToggleBtn = document.getElementById('password-toggle-btn');
        const passwordToggleIcon = document.getElementById('password-toggle-icon');

        if (passwordToggleBtn && passwordInput) {
            passwordToggleBtn.addEventListener('click', () => {
                const isPassword = passwordInput.type === 'password';
                passwordInput.type = isPassword ? 'text' : 'password';
                passwordToggleIcon.className = isPassword ? 'fa-solid fa-eye-slash' : 'fa-solid fa-eye';
            });
        }

        // ---------------------------------------------------------------------
        // Alerts
        // ---------------------------------------------------------------------
        const loginAlert = document.getElementById('login-alert');
        const loginAlertText = document.getElementById('login-alert-text');

        function showAlert(msg, isError = true) {
            if (!loginAlert) return;
            loginAlert.className = 'login-alert-box ' + (isError ? 'error' : 'success');
            loginAlert.querySelector('i').className = isError ? 'fa-solid fa-circle-exclamation' : 'fa-solid fa-circle-check';
            loginAlertText.textContent = msg;
            loginAlert.style.display = 'flex';
        }

        function hideAlert() {
            if (loginAlert) loginAlert.style.display = 'none';
        }

        // ---------------------------------------------------------------------
        // Universal Login Submission Handling
        // ---------------------------------------------------------------------
        const loginForm = document.getElementById('standalone-login-form');
        const identifierInput = document.getElementById('login-identifier');
        const submitBtn = document.getElementById('login-btn-submit');
        const btnText = document.getElementById('btn-text');
        const btnIcon = document.getElementById('btn-icon');

        if (loginForm) {
            loginForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                hideAlert();

                const username = identifierInput.value.trim();
                const password = passwordInput.value.trim();

                if (!username || !password) {
                    showAlert('Please enter both your Username/ID and password.');
                    return;
                }

                // Loading state
                submitBtn.disabled = true;
                btnText.textContent = 'Verifying Account...';
                btnIcon.className = 'fa-solid fa-circle-notch fa-spin';

                const formData = new FormData();
                formData.append('username', username);
                formData.append('password', password);

                try {
                    const response = await fetch('auth_login.php', {
                        method: 'POST',
                        body: formData
                    });
                    const data = await response.json();

                    if (data.success) {
                        const welcomeName = data.name ? ` (${data.name})` : '';
                        showAlert(`Login successful! Redirecting to ${data.role || 'dashboard'}${welcomeName}...`, false);
                        btnText.textContent = 'Success!';
                        btnIcon.className = 'fa-solid fa-check';
                        
                        setTimeout(() => {
                            window.location.href = data.redirect || 'index.php';
                        }, 500);
                    } else {
                        showAlert(data.error || 'Authentication failed. Please verify credentials.');
                        submitBtn.disabled = false;
                        btnText.textContent = 'Sign In';
                        btnIcon.className = 'fa-solid fa-arrow-right';
                    }
                } catch (error) {
                    console.error('Authentication error:', error);
                    showAlert('Unable to connect to database server. Please verify MySQL service is running.');
                    submitBtn.disabled = false;
                    btnText.textContent = 'Sign In';
                    btnIcon.className = 'fa-solid fa-arrow-right';
                }
            });
        }

        // ---------------------------------------------------------------------
        // Forgot Password System
        // ---------------------------------------------------------------------
        const forgotOverlay = document.getElementById('forgot-modal-overlay');
        const forgotClose = document.getElementById('forgot-modal-close');
        const forgotForm = document.getElementById('forgot-password-form');
        const forgotTrigger = document.getElementById('login-forgot-trigger');
        const backToLoginLink = document.getElementById('back-to-login-link');
        const forgotStep1 = document.getElementById('forgot-step-1');
        const forgotStep2 = document.getElementById('forgot-step-2');
        const forgotTitle = document.getElementById('forgot-modal-title');
        const forgotDesc = document.getElementById('forgot-modal-desc');
        const forgotUsername = document.getElementById('forgot-username');
        const forgotUpdateBtn = document.getElementById('forgot-update-btn');

        function openForgotModal() {
            if (forgotForm) forgotForm.reset();
            if (forgotStep1) forgotStep1.style.display = 'block';
            if (forgotStep2) forgotStep2.style.display = 'none';
            if (forgotTitle) forgotTitle.textContent = 'Reset Password';
            if (forgotDesc) forgotDesc.textContent = 'Please enter your Username or ID to recover your password.';
            
            // Pre-fill if identifier was already typed
            if (forgotUsername && identifierInput.value.trim()) {
                forgotUsername.value = identifierInput.value.trim();
            }

            if (forgotOverlay) {
                forgotOverlay.classList.add('active');
                document.body.style.overflow = 'hidden';
            }
        }

        function closeForgotModal() {
            if (forgotOverlay) {
                forgotOverlay.classList.remove('active');
                document.body.style.overflow = '';
            }
        }

        if (forgotTrigger) forgotTrigger.addEventListener('click', openForgotModal);
        if (forgotClose) forgotClose.addEventListener('click', closeForgotModal);
        if (backToLoginLink) backToLoginLink.addEventListener('click', closeForgotModal);

        if (forgotOverlay) {
            forgotOverlay.addEventListener('click', (e) => {
                if (e.target === forgotOverlay) closeForgotModal();
            });
        }

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && forgotOverlay && forgotOverlay.classList.contains('active')) {
                closeForgotModal();
            }
        });

        // Step 1: Verify Username
        if (forgotForm) {
            forgotForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                const u = forgotUsername ? forgotUsername.value.trim() : '';
                if (!u) {
                    Swal.fire({
                        title: 'Input Required',
                        text: 'Please enter your Username or ID.',
                        icon: 'warning',
                        confirmButtonColor: 'var(--primary)'
                    });
                    return;
                }

                Swal.fire({
                    title: 'Verifying Identity...',
                    text: 'Checking account in the database...',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                const fd = new FormData();
                fd.append('action', 'verify');
                fd.append('username', u);

                try {
                    const res = await fetch('reset_password.php', { method: 'POST', body: fd });
                    const data = await res.json();
                    Swal.close();

                    if (data.success) {
                        forgotStep1.style.display = 'none';
                        forgotStep2.style.display = 'block';
                        forgotTitle.textContent = 'Create New Password';
                        forgotDesc.textContent = data.message || 'Account verified. Enter your new password below.';
                        const newPassInput = document.getElementById('forgot-new-password');
                        if (newPassInput) newPassInput.focus();
                    } else {
                        Swal.fire({
                            title: 'Account Not Found',
                            text: data.error || 'No profile matched the identifier entered.',
                            icon: 'error',
                            confirmButtonColor: '#f43f5e'
                        });
                    }
                } catch (err) {
                    Swal.close();
                    Swal.fire({
                        title: 'System Error',
                        text: 'Connection failed. Ensure MySQL database is active.',
                        icon: 'error',
                        confirmButtonColor: 'var(--primary)'
                    });
                }
            });
        }

        // Step 2: Update Password
        if (forgotUpdateBtn) {
            forgotUpdateBtn.addEventListener('click', async () => {
                const u = forgotUsername.value.trim();
                const newPass = document.getElementById('forgot-new-password').value.trim();
                const confPass = document.getElementById('forgot-confirm-password').value.trim();

                if (!newPass) {
                    Swal.fire({ title: 'Password Required', text: 'Please enter your new password.', icon: 'warning', confirmButtonColor: 'var(--primary)' });
                    return;
                }
                if (newPass !== confPass) {
                    Swal.fire({ title: 'Mismatch', text: 'New password and confirm password do not match.', icon: 'warning', confirmButtonColor: 'var(--primary)' });
                    return;
                }

                Swal.fire({
                    title: 'Updating Password...',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                const fd = new FormData();
                fd.append('action', 'update');
                fd.append('username', u);
                fd.append('new_password', newPass);

                try {
                    const res = await fetch('reset_password.php', { method: 'POST', body: fd });
                    const data = await res.json();
                    Swal.close();

                    if (data.success) {
                        Swal.fire({
                            title: 'Password Updated!',
                            text: 'Your password has been changed successfully. You can now log in.',
                            icon: 'success',
                            confirmButtonText: 'Back to Sign In',
                            confirmButtonColor: 'var(--primary)'
                        }).then(() => {
                            closeForgotModal();
                            if (identifierInput) identifierInput.value = u;
                            if (passwordInput) {
                                passwordInput.value = '';
                                passwordInput.focus();
                            }
                        });
                    } else {
                        Swal.fire({
                            title: 'Update Failed',
                            text: data.error || 'Could not update password.',
                            icon: 'error',
                            confirmButtonColor: '#f43f5e'
                        });
                    }
                } catch (err) {
                    Swal.close();
                    Swal.fire({
                        title: 'System Error',
                        text: 'Connection failed. Ensure MySQL database is active.',
                        icon: 'error',
                        confirmButtonColor: 'var(--primary)'
                    });
                }
            });
        }

    });
    </script>
</body>
</html>
