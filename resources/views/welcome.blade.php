<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HR Management System</title>
    <link rel="stylesheet" href="/css/styles.css">
</head>
<body>
    <header class="header-white">
        <div class="logo">
            <img src="/images/logo.png" alt="Logo">
        </div>
        <div class="header-buttons">


        @if (Route::has('login'))
            <div class="navigation">
                @auth
                    <a href="{{ url('/home') }}">
                        <button class="signup-btn">
                            Dashboard
                        </button>
                    </a>
                @else
                    <a href="{{ route('login') }}">
                        <button class="login-btn">
                            Login
                        </button>
                    </a>

                    @if (Route::has('register'))
                        <a href="{{ route('register') }}"> 
                            <button class="signup-btn">
                                Register
                            </button>
                        </a>
                    @endif
                @endauth
            </div>
        @endif

            <label class="switch">
                <input type="checkbox" id="dark-mode-toggle">
                <span class="slider">
                    <span class="icon-sun">☀️</span>
                    <span class="icon-moon">🌙</span>
                </span>
            </label>
        </div>
    </header>

    <main>
        <div class="content">
            <h1>Welcome to HR Management System</h1>
            <p>Empowering your workforce through innovative management solutions.</p>
            <a href="#" class="read-more">Learn more</a>
        </div>
        <div class="illustration">
            <img src="/images/hr.png" alt="Illustration" />
        </div>
    </main>

    <!-- New Feature Section -->
    <section class="features">
        <h2>Key Features</h2>
        <div class="feature-list">
            <div class="feature-item">
                <h3>Employee Management</h3>
                <p>Manage employee records, track performance, and handle payroll efficiently.</p>
            </div>
            <div class="feature-item">
                <h3>Attendance Tracking</h3>
                <p>Monitor employee attendance and leaves with our advanced tracking system.</p>
            </div>
            <div class="feature-item">
                <h3>Performance Evaluation</h3>
                <p>Conduct thorough performance evaluations and provide constructive feedback.</p>
            </div>
        </div>
    </section>

    <!-- Call to Action Section -->
    <section class="cta">
        <h2>Get Started Today!</h2>
        <p>Join with our HR Management System to streamline the HR processes. Sign up now and take your HR operations to the next level.</p>
        <a href="{{ route('register') }}">
            <button class="signup-btn">Sign Up Now</button>
        <a>
    </section>

    <footer>
        <p>&copy; 2024 Arrogance Technologies (Pvt) Ltd. All rights reserved.</p>
    </footer>

    <script src="/js/script.js"></script>
</body>
</html>
