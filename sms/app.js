document.addEventListener('DOMContentLoaded', () => {
    // -------------------------------------------------------------------------
    // 1. Theme Toggle System
    // -------------------------------------------------------------------------
    const themeToggleBtn = document.getElementById('theme-toggle');
    const themeIcon = document.getElementById('theme-icon');
    const htmlElement = document.documentElement;

    // Check saved theme or system preference
    const savedTheme = localStorage.getItem('theme');
    const userPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    
    if (savedTheme) {
        setTheme(savedTheme);
    } else {
        setTheme(userPrefersDark ? 'dark' : 'light');
    }

    themeToggleBtn.addEventListener('click', () => {
        const currentTheme = htmlElement.getAttribute('data-theme');
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        setTheme(newTheme);
    });

    function setTheme(theme) {
        htmlElement.setAttribute('data-theme', theme);
        localStorage.setItem('theme', theme);
        
        if (theme === 'dark') {
            themeIcon.className = 'fa-solid fa-sun';
            themeToggleBtn.style.color = '#eab308'; // Sunny gold icon
        } else {
            themeIcon.className = 'fa-solid fa-moon';
            themeToggleBtn.style.color = '#4f46e5'; // Deep Indigo icon
        }
    }

    // -------------------------------------------------------------------------
    // 2. Mobile Responsive Menu
    // -------------------------------------------------------------------------
    const menuBtn = document.getElementById('menu-btn');
    const navLinks = document.getElementById('nav-links');

    menuBtn.addEventListener('click', () => {
        navLinks.classList.toggle('active');
        const icon = menuBtn.querySelector('i');
        if (navLinks.classList.contains('active')) {
            icon.className = 'fa-solid fa-xmark';
        } else {
            icon.className = 'fa-solid fa-bars';
        }
    });

    // Close menu when clicking outside or on a link
    document.addEventListener('click', (e) => {
        if (!menuBtn.contains(e.target) && !navLinks.contains(e.target)) {
            navLinks.classList.remove('active');
            menuBtn.querySelector('i').className = 'fa-solid fa-bars';
        }
    });

    navLinks.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', () => {
            navLinks.classList.remove('active');
            menuBtn.querySelector('i').className = 'fa-solid fa-bars';
        });
    });

    // -------------------------------------------------------------------------
    // 3. Portal Modal Interface
    // -------------------------------------------------------------------------
    const modalOverlay = document.getElementById('login-modal-overlay');
    const modalClose = document.getElementById('modal-close');
    const portalTitlePrefix = document.getElementById('portal-title-prefix');
    const loginForm = document.getElementById('portal-login-form');
    const portalCards = document.querySelectorAll('.portal-card');

    portalCards.forEach(card => {
        card.addEventListener('click', () => {
            const portalType = card.getAttribute('data-portal');
            if (portalType === 'Parent') {
                window.location.href = 'fees_payment_portal.php';
            } else {
                window.location.href = 'login.php';
            }
        });
    });

    const loginBtn = document.getElementById('login-btn');
    if (loginBtn) {
        loginBtn.addEventListener('click', (e) => {
            e.preventDefault();
            window.location.href = 'login.php';
        });
    }


    if (modalClose) {
        modalClose.addEventListener('click', closePortalModal);
    }
    if (modalOverlay) {
        modalOverlay.addEventListener('click', (e) => {
            if (e.target === modalOverlay) {
                closePortalModal();
            }
        });
    }

    // Forgot Password Modal System
    const forgotOverlay = document.getElementById('forgot-modal-overlay');
    const forgotClose = document.getElementById('forgot-modal-close');
    const forgotForm = document.getElementById('forgot-password-form');
    const forgotPasswordLink = document.getElementById('forgot-password-link');
    const backToLoginBtn = document.getElementById('back-to-login-btn');

    if (forgotPasswordLink) {
        forgotPasswordLink.addEventListener('click', (e) => {
            e.preventDefault();
            closePortalModal();
            openForgotModal();
        });
    }

    if (backToLoginBtn) {
        backToLoginBtn.addEventListener('click', (e) => {
            e.preventDefault();
            closeForgotModal();
            openPortalModal('Secure');
        });
    }

    if (forgotClose) {
        forgotClose.addEventListener('click', closeForgotModal);
    }

    if (forgotOverlay) {
        forgotOverlay.addEventListener('click', (e) => {
            if (e.target === forgotOverlay) {
                closeForgotModal();
            }
        });
    }

    function openForgotModal() {
        if (forgotForm) forgotForm.reset();
        const step1 = document.getElementById('forgot-step-1');
        const step2 = document.getElementById('forgot-step-2');
        const title = document.getElementById('forgot-modal-title');
        const desc = document.getElementById('forgot-modal-desc');

        if (step1) step1.style.display = 'block';
        if (step2) step2.style.display = 'none';
        if (title) title.textContent = 'Reset Password';
        if (desc) desc.textContent = 'Please enter your Username or ID to recover your password.';

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

    // Step 1: Verify Username & Reveal Password Reset Inputs
    if (forgotForm) {
        forgotForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const usernameInput = document.getElementById('forgot-username');
            const username = usernameInput ? usernameInput.value.trim() : '';

            if (!username) {
                Swal.fire({
                    title: 'Input Required',
                    text: 'Please enter your Username or ID.',
                    icon: 'warning',
                    background: '#ffffff',
                    color: '#1e293b',
                    confirmButtonColor: 'var(--primary)'
                });
                return;
            }

            Swal.fire({
                title: 'Verifying ID...',
                text: 'Locating your profile in the database...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            const verifyData = new FormData();
            verifyData.append('action', 'verify');
            verifyData.append('username', username);

            fetch('reset_password.php', {
                method: 'POST',
                body: verifyData
            })
            .then(res => res.json())
            .then(data => {
                Swal.close();
                if (data.success) {
                    // Reveal Step 2 Password Reset Inputs
                    const step1 = document.getElementById('forgot-step-1');
                    const step2 = document.getElementById('forgot-step-2');
                    const title = document.getElementById('forgot-modal-title');
                    const desc = document.getElementById('forgot-modal-desc');

                    if (step1) step1.style.display = 'none';
                    if (step2) step2.style.display = 'block';
                    if (title) title.textContent = 'Create New Password';
                    if (desc) desc.textContent = data.message || 'Account verified. Please set your new password below.';
                    
                    const newPassInput = document.getElementById('forgot-new-password');
                    if (newPassInput) newPassInput.focus();
                } else {
                    Swal.fire({
                        title: 'Account Not Found',
                        text: data.error || 'No account found matching this Username / ID.',
                        icon: 'error',
                        background: '#ffffff',
                        color: '#1e293b',
                        confirmButtonColor: '#f43f5e'
                    });
                }
            })
            .catch(err => {
                Swal.close();
                console.error('Verify error:', err);
                Swal.fire({
                    title: 'System Error',
                    text: 'Connection failed. Please verify MySQL server is running.',
                    icon: 'error',
                    background: '#ffffff',
                    color: '#1e293b',
                    confirmButtonColor: 'var(--primary)'
                });
            });
        });
    }

    // Step 2: Handle New Password Update
    const forgotUpdateBtn = document.getElementById('forgot-update-btn');
    if (forgotUpdateBtn) {
        forgotUpdateBtn.addEventListener('click', () => {
            const usernameInput = document.getElementById('forgot-username');
            const username = usernameInput ? usernameInput.value.trim() : '';
            const newPassword = document.getElementById('forgot-new-password').value.trim();
            const confirmPassword = document.getElementById('forgot-confirm-password').value.trim();

            if (!newPassword) {
                Swal.fire({
                    title: 'Password Required',
                    text: 'Please enter your new password.',
                    icon: 'warning',
                    background: '#ffffff',
                    color: '#1e293b',
                    confirmButtonColor: 'var(--primary)'
                });
                return;
            }

            if (newPassword !== confirmPassword) {
                Swal.fire({
                    title: 'Password Mismatch',
                    text: 'The new password and confirm password inputs do not match.',
                    icon: 'warning',
                    background: '#ffffff',
                    color: '#1e293b',
                    confirmButtonColor: 'var(--primary)'
                });
                return;
            }

            Swal.fire({
                title: 'Updating Password...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            const updateData = new FormData();
            updateData.append('action', 'update');
            updateData.append('username', username);
            updateData.append('new_password', newPassword);

            fetch('reset_password.php', {
                method: 'POST',
                body: updateData
            })
            .then(res => res.json())
            .then(data => {
                Swal.close();
                if (data.success) {
                    Swal.fire({
                        title: 'Password Updated!',
                        text: 'Your portal password has been changed successfully.',
                        icon: 'success',
                        confirmButtonText: 'Log In Now',
                        background: '#ffffff',
                        color: '#1e293b',
                        confirmButtonColor: 'var(--primary)',
                        customClass: {
                            popup: 'swal2-popup'
                        }
                    }).then(() => {
                        closeForgotModal();
                        openPortalModal('Secure');
                    });
                } else {
                    Swal.fire({
                        title: 'Update Failed',
                        text: data.error || 'Failed to update password.',
                        icon: 'error',
                        background: '#ffffff',
                        color: '#1e293b',
                        confirmButtonColor: '#f43f5e'
                    });
                }
            })
            .catch(err => {
                Swal.close();
                console.error('Update error:', err);
                Swal.fire({
                    title: 'System Error',
                    text: 'Connection failed. Please verify database connection.',
                    icon: 'error',
                    background: '#ffffff',
                    color: '#1e293b',
                    confirmButtonColor: 'var(--primary)'
                });
            });
        });
    }

    // Support escape key to close
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            if (modalOverlay.classList.contains('active')) {
                closePortalModal();
            }
            if (forgotOverlay && forgotOverlay.classList.contains('active')) {
                closeForgotModal();
            }
        }
    });

    // Handle Form Submission with authentication validation
    loginForm.addEventListener('submit', (e) => {
        e.preventDefault();
        const usernameVal = document.getElementById('username').value.trim();
        const passwordVal = document.getElementById('password').value.trim();
        const errorDiv = document.getElementById('login-error');
        const activePortal = portalTitlePrefix.textContent.trim();

        // Reset error state
        errorDiv.style.display = 'none';
        errorDiv.textContent = '';

        function authenticateStudent(u, p) {
            const loginData = new FormData();
            loginData.append('username', u);
            loginData.append('password', p);

            fetch('student_login.php', {
                method: 'POST',
                body: loginData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    window.location.href = 'student_dashboard.php';
                } else {
                    showLoginError(data.error || 'Authentication failed. Please verify credentials.');
                }
            })
            .catch(err => {
                console.error('Login error:', err);
                showLoginError('Server connection failed. Ensure MySQL is running.');
            });
        }

        function authenticateTeacher(u, p) {
            const loginData = new FormData();
            loginData.append('username', u);
            loginData.append('password', p);

            fetch('teacher_login.php', {
                method: 'POST',
                body: loginData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    window.location.href = 'teacher_dashboard.php';
                } else {
                    showLoginError(data.error || 'Authentication failed. Please verify credentials.');
                }
            })
            .catch(err => {
                console.error('Login error:', err);
                showLoginError('Server connection failed. Ensure MySQL is running.');
            });
        }

        function authenticateAdmin(u, p) {
            const loginData = new FormData();
            loginData.append('username', u);
            loginData.append('password', p);

            fetch('admin_login.php', {
                method: 'POST',
                body: loginData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    window.location.href = 'admin_dashboard.php';
                } else {
                    showLoginError(data.error || 'Authentication failed. Please verify credentials.');
                }
            })
            .catch(err => {
                console.error('Admin login error:', err);
                showLoginError('Server connection failed. Ensure MySQL is running.');
            });
        }

        const lowerUser = usernameVal.toLowerCase();

        if (activePortal === 'Administrator' || activePortal === 'Admin' || lowerUser.startsWith('adm-') || lowerUser.includes('admin')) {
            authenticateAdmin(usernameVal, passwordVal);
        } else if (activePortal === 'Teacher' || lowerUser.startsWith('tch-')) {
            authenticateTeacher(usernameVal, passwordVal);
        } else if (activePortal === 'Student' || lowerUser.startsWith('apx-')) {
            authenticateStudent(usernameVal, passwordVal);
        } else {
            // General / Universal Login: Check Admin -> Student -> Teacher
            const loginData = new FormData();
            loginData.append('username', usernameVal);
            loginData.append('password', passwordVal);

            fetch('admin_login.php', {
                method: 'POST',
                body: loginData
            })
            .then(resAdm => resAdm.json())
            .then(dataAdm => {
                if (dataAdm.success) {
                    window.location.href = 'admin_dashboard.php';
                } else if (dataAdm.error === 'Incorrect password') {
                    showLoginError('Incorrect password');
                } else {
                    // Try Student login
                    return fetch('student_login.php', {
                        method: 'POST',
                        body: loginData
                    })
                    .then(resS => resS.json())
                    .then(dataS => {
                        if (dataS.success) {
                            window.location.href = 'student_dashboard.php';
                        } else if (dataS.error === 'Incorrect password') {
                            showLoginError('Incorrect password');
                        } else {
                            // Try Teacher login
                            return fetch('teacher_login.php', {
                                method: 'POST',
                                body: loginData
                            })
                            .then(resT => resT.json())
                            .then(dataT => {
                                if (dataT.success) {
                                    window.location.href = 'teacher_dashboard.php';
                                } else if (dataT.error === 'Incorrect password') {
                                    showLoginError('Incorrect password');
                                } else {
                                    showLoginError('Invalid credentials. Please verify your login details.');
                                }
                            });
                        }
                    });
                }
            })
            .catch(err => {
                console.error('Login error:', err);
                showLoginError('Server connection failed. Ensure MySQL is running.');
            });
        }

        function showLoginError(message) {
            const isPasswordError = !message || message.toLowerCase().includes('password');
            const alertTitle = isPasswordError ? 'Incorrect Password' : 'Login Failed';
            const alertText = message || 'Incorrect password. Please try again.';

            Swal.fire({
                title: alertTitle,
                text: alertText,
                icon: 'error',
                confirmButtonColor: '#f43f5e',
                confirmButtonText: 'Try Again',
                background: '#ffffff',
                color: '#1e293b',
                customClass: {
                    popup: 'swal2-popup'
                }
            });

            errorDiv.innerHTML = `
                <div style="background: rgba(244, 63, 94, 0.08); border: 1px solid rgba(244, 63, 94, 0.2); border-radius: 12px; padding: 0.75rem 1rem; display: flex; align-items: center; justify-content: center; gap: 0.5rem; color: #f43f5e; font-size: 0.85rem; font-weight: 600; animation: slideDown 0.3s ease-out; margin-bottom: 1.25rem;">
                    <i class="fa-solid fa-circle-exclamation" style="font-size: 1rem;"></i>
                    <span>${alertText}</span>
                </div>
            `;
            errorDiv.style.display = 'block';
            shakeModal();
        }

        function shakeModal() {
            // Add shake animation to the modal container
            const container = document.getElementById('login-modal-container');
            container.style.animation = 'none';
            void container.offsetWidth; // Trigger DOM reflow to restart animation
            container.style.animation = 'shake 0.4s ease-out';
        }
    });

    function openPortalModal(portalType) {
        portalTitlePrefix.textContent = portalType;
        loginForm.reset();
        
        // Reset validation error state
        const errorDiv = document.getElementById('login-error');
        if (errorDiv) {
            errorDiv.style.display = 'none';
            errorDiv.textContent = '';
        }

        // Update username label and placeholder for Student Portal
        const usernameLabel = document.getElementById('username-label');
        const usernameInput = document.getElementById('username');
        if (usernameLabel && usernameInput) {
            if (portalType === 'Student') {
                usernameLabel.textContent = 'Enrollment No';
                usernameInput.placeholder = 'e.g. APX-1001';
            } else {
                usernameLabel.textContent = 'Username / ID';
                usernameInput.placeholder = 'Enter your identifier';
            }
        }


        modalOverlay.classList.add('active');
        document.body.style.overflow = 'hidden'; // Lock background scroll
    }

    function closePortalModal() {
        modalOverlay.classList.remove('active');
        document.body.style.overflow = ''; // Unlock background scroll
    }

    // -------------------------------------------------------------------------
    // 4. Statistics Counter Animation
    // -------------------------------------------------------------------------
    const counterElements = document.querySelectorAll('.counter');
    const statsSection = document.getElementById('stats');

    const animateCounters = () => {
        counterElements.forEach(counter => {
            const target = parseInt(counter.getAttribute('data-target'), 10);
            const duration = 1500; // Total duration in ms
            const stepTime = 15; // Interval time in ms
            const steps = duration / stepTime;
            const increment = target / steps;
            let current = 0;

            const updateCounter = setInterval(() => {
                current += increment;
                if (current >= target) {
                    counter.textContent = target + (target === 2500 ? '+' : (target === 120 ? '+' : ''));
                    clearInterval(updateCounter);
                } else {
                    counter.textContent = Math.floor(current);
                }
            }, stepTime);
        });
    };

    // Intersection Observer to trigger statistics counters
    const observerOptions = {
        root: null, // Viewport
        threshold: 0.3 // Trigger when 30% of the element is visible
    };

    const statsObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) { 
                animateCounters();
                observer.unobserve(entry.target); // Trigger only once
            }
        });
    }, observerOptions);

    if (statsSection) {
        statsObserver.observe(statsSection);
    }
});

// Expose functions globally for bulletproof HTML inline click access
window.openForgotModalDirect = function(e) {
    if (e) e.preventDefault();
    
    // Close login modal
    const loginOverlay = document.getElementById('login-modal-overlay');
    if (loginOverlay) {
        loginOverlay.classList.remove('active');
    }
    
    // Open forgot modal
    const forgotOverlay = document.getElementById('forgot-modal-overlay');
    const forgotForm = document.getElementById('forgot-password-form');
    if (forgotForm) forgotForm.reset();

    const step1 = document.getElementById('forgot-step-1');
    const step2 = document.getElementById('forgot-step-2');
    const title = document.getElementById('forgot-modal-title');
    const desc = document.getElementById('forgot-modal-desc');

    if (step1) step1.style.display = 'block';
    if (step2) step2.style.display = 'none';
    if (title) title.textContent = 'Reset Password';
    if (desc) desc.textContent = 'Please enter your Username or ID to recover your password.';

    if (forgotOverlay) {
        forgotOverlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
};

window.closeForgotModalDirect = function(e) {
    if (e) e.preventDefault();
    
    const forgotOverlay = document.getElementById('forgot-modal-overlay');
    if (forgotOverlay) {
        forgotOverlay.classList.remove('active');
        document.body.style.overflow = '';
    }
};

window.backToLoginDirect = function(e) {
    if (e) e.preventDefault();
    
    window.closeForgotModalDirect();
    
    const loginOverlay = document.getElementById('login-modal-overlay');
    if (loginOverlay) {
        loginOverlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
};
