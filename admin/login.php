<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../config/auth.php';

$auth = new Auth();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Please fill in all fields';
    } else {
        $result = $auth->login($email, $password);
        if ($result['success']) {
            // Debug: Check session
            error_log("Login successful for: $email");
            error_log("Session logged_in: " . (isset($_SESSION['logged_in']) ? 'yes' : 'no'));
            error_log("Session user_role: " . ($_SESSION['user_role'] ?? 'not set'));

            header('Location: dashboard.php');
            exit();
        } else {
            $error = $result['message'];
            error_log("Login failed for $email: " . $result['message']);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - RetailRow</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .login-container {
            max-width: 400px;
            margin: 100px auto;
            padding: 40px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .login-title {
            text-align: center;
            margin-bottom: 30px;
            color: var(--jumia-dark);
            font-size: 24px;
            font-weight: 700;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: var(--jumia-dark);
        }

        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid var(--border-gray);
            border-radius: 4px;
            font-size: 14px;
        }

        .form-group input:focus {
            outline: none;
            border-color: var(--jumia-orange);
        }

        .login-btn {
            width: 100%;
            background: var(--jumia-orange);
            color: white;
            border: none;
            padding: 14px;
            border-radius: 4px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background 200ms;
        }

        .login-btn:hover {
            background: #e67e0e;
        }

        .error-message {
            background: #fee;
            color: var(--jumia-red);
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
            text-align: center;
        }

        .success-message {
            background: #efe;
            color: #28a745;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
            text-align: center;
        }

        body {
            background: var(--jumia-orange);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-container">
            <h1 class="login-title">RetailRow Admin</h1>

            <?php if ($error): ?>
                <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="success-message"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <button type="submit" class="login-btn">Login</button>
            </form>

            <div style="text-align: center; margin-top: 20px; color: var(--jumia-gray); font-size: 14px;">
                Default login: admin@retailrow.com / admin123
            </div>
        </div>
    </div>
</body>
</html>