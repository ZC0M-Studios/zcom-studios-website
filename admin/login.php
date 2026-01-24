<?php
/* ========================================================
    ADMIN LOGIN PAGE
    Handles admin authentication
=========================================================== */

session_start();

require_once __DIR__ . '/../includes/db_config.php';

// Redirect if already logged in
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: /admin/index.php');
    exit;
}

$error = '';
$success = '';

// Handle logout message
if (isset($_GET['logout'])) {
    $success = 'You have been successfully logged out.';
}

// Handle timeout message
if (isset($_GET['timeout'])) {
    $error = 'Your session has expired. Please login again.';
}

// Handle expired session
if (isset($_GET['expired'])) {
    $error = 'Your session has expired. Please login again.';
}

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);
    
    // Rate limiting check
    $ip_address = $_SERVER['REMOTE_ADDR'];
    $stmt = $db->prepare("
        SELECT COUNT(*) as attempt_count 
        FROM login_attempts 
        WHERE ip_address = ? 
        AND attempted_at > DATE_SUB(NOW(), INTERVAL 15 MINUTE)
    ");
    $stmt->execute([$ip_address]);
    $attempts = $stmt->fetch();
    
    if ($attempts['attempt_count'] >= 5) {
        $error = 'Too many login attempts. Please try again in 15 minutes.';
    } else {
        // Validate credentials
        try {
            $stmt = $db->prepare("SELECT * FROM admin_users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['password_hash'])) {
                // Successful login
                
                // Clear old sessions for this user
                $stmt = $db->prepare("DELETE FROM admin_sessions WHERE user_id = ?");
                $stmt->execute([$user['id']]);
                
                // Create new session
                $session_token = bin2hex(random_bytes(32));
                $remember_token = $remember ? bin2hex(random_bytes(32)) : null;
                $expires_at = $remember ? date('Y-m-d H:i:s', strtotime('+30 days')) : date('Y-m-d H:i:s', strtotime('+12 hours'));
                
                $stmt = $db->prepare("
                    INSERT INTO admin_sessions (user_id, session_token, remember_token, ip_address, user_agent, expires_at)
                    VALUES (?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([
                    $user['id'],
                    $session_token,
                    $remember_token,
                    $ip_address,
                    $_SERVER['HTTP_USER_AGENT'] ?? '',
                    $expires_at
                ]);
                
                // Update last login
                $stmt = $db->prepare("UPDATE admin_users SET last_login = NOW() WHERE id = ?");
                $stmt->execute([$user['id']]);
                
                // Set session variables
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_user_id'] = $user['id'];
                $_SESSION['admin_username'] = $user['username'];
                $_SESSION['admin_email'] = $user['email'];
                $_SESSION['admin_session_token'] = $session_token;
                $_SESSION['admin_last_activity'] = time();
                
                // Set remember me cookie
                if ($remember && $remember_token) {
                    setcookie('admin_remember', $remember_token, time() + (30 * 24 * 60 * 60), '/', '', false, true);
                }
                
                // Clear login attempts
                $stmt = $db->prepare("DELETE FROM login_attempts WHERE ip_address = ?");
                $stmt->execute([$ip_address]);
                
                header('Location: /admin/index.php');
                exit;
            } else {
                // Failed login - log attempt
                $stmt = $db->prepare("INSERT INTO login_attempts (ip_address, username) VALUES (?, ?)");
                $stmt->execute([$ip_address, $username]);
                
                $error = 'Invalid username or password.';
            }
        } catch (PDOException $e) {
            error_log("Login error: " . $e->getMessage());
            $error = 'An error occurred. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BATCOMPUTER // ACCESS CONTROL</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/css/style-batcomputer.css">
    <style>
        /* ========================================================
            //ANCHOR [LOGIN_BATCOMPUTER_STYLES]
            FUNCTION: Batcomputer Login Page Styles
            UniqueID: 793104
        =========================================================== */
        body {
            background: #000000;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'JetBrains Mono', monospace;
        }
        .login-container {
            max-width: 400px;
            width: 100%;
            padding: 16px;
        }
        .login-card {
            background: #111111;
            border: 1px solid #333333;
            padding: 24px;
            position: relative;
        }
        .login-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: #cc0000;
            box-shadow: 0 0 10px rgba(255, 0, 51, 0.6);
        }
        .login-header {
            text-align: center;
            margin-bottom: 24px;
            padding-bottom: 16px;
            border-bottom: 1px solid #333333;
        }
        .login-header h1 {
            font-family: 'Orbitron', sans-serif;
            font-size: 14px;
            font-weight: 700;
            letter-spacing: 0.15em;
            color: #ff0033;
            margin-bottom: 4px;
            text-shadow: 0 0 8px #ff0033, 0 0 12px rgba(255, 0, 51, 0.5);
            text-transform: uppercase;
        }
        .login-header p {
            font-family: 'Share Tech Mono', monospace;
            font-size: 10px;
            color: #606060;
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }
        .form-control {
            background: #0a0a0a;
            border: 1px solid #333333;
            color: #ffffff;
            padding: 8px 12px;
            font-family: 'JetBrains Mono', monospace;
            font-size: 12px;
        }
        .form-control:focus {
            background: #0d0d0d;
            border-color: #cc0000;
            box-shadow: 0 0 10px rgba(255, 0, 51, 0.6);
            color: #fff;
        }
        .form-control::placeholder {
            color: #505050;
        }
        .form-label {
            font-family: 'Orbitron', sans-serif;
            font-size: 10px;
            font-weight: 600;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: #606060;
            margin-bottom: 4px;
        }
        .btn-login {
            background: #660000;
            border: 1px solid #cc0000;
            color: #ffffff;
            font-family: 'Orbitron', sans-serif;
            font-size: 10px;
            font-weight: 600;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            padding: 10px;
            width: 100%;
            transition: all 0.15s ease;
            cursor: pointer;
        }
        .btn-login:hover {
            background: #990000;
            border-color: #ff0033;
            box-shadow: 0 0 10px rgba(255, 0, 51, 0.6);
            color: #ffffff;
        }
        .alert {
            border: 1px solid;
            font-family: 'Share Tech Mono', monospace;
            font-size: 11px;
            padding: 8px 12px;
        }
        .alert-danger {
            background: rgba(204, 0, 0, 0.1);
            border-color: #cc0000;
            color: #ff0033;
        }
        .alert-success {
            background: rgba(0, 255, 102, 0.1);
            border-color: #00cc52;
            color: #00ff66;
        }
        .form-check-input {
            background-color: #0a0a0a;
            border-color: #333333;
        }
        .form-check-input:checked {
            background-color: #cc0000;
            border-color: #cc0000;
        }
        .form-check-label {
            font-family: 'Share Tech Mono', monospace;
            font-size: 10px;
            color: #808080;
            text-transform: uppercase;
        }
        .system-status {
            font-family: 'Share Tech Mono', monospace;
            font-size: 9px;
            color: #404040;
            text-align: center;
            margin-top: 16px;
            padding-top: 12px;
            border-top: 1px solid #333333;
        }
        .system-status .online {
            color: #00ff66;
        }
    </style>
</head>
<body class="bat-theme">
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <h1>BATCOMPUTER</h1>
                <p>SECURE ACCESS TERMINAL</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-danger" role="alert">
                    <strong>ACCESS DENIED:</strong> <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success" role="alert">
                    <strong>CONFIRMED:</strong> <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="mb-3">
                    <label for="username" class="form-label">OPERATOR ID</label>
                    <input type="text" class="form-control" id="username" name="username" placeholder="Enter operator ID" required autofocus>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">ACCESS CODE</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Enter access code" required>
                </div>

                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="remember" name="remember">
                    <label class="form-check-label" for="remember">
                        MAINTAIN SESSION [30 DAYS]
                    </label>
                </div>

                <button type="submit" class="btn btn-login">AUTHENTICATE</button>
            </form>

            <div class="system-status">
                SYSTEM STATUS: <span class="online">ONLINE</span> // ENCRYPTION: AES-256 // PROTOCOL: SECURE
            </div>
        </div>
    </div>
</body>
</html>
