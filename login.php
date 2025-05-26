<?php
session_start(); 


if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}


require_once 'includes/db.php';

$errors = [];
$message = ''; 
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($conn) { 
        $email_or_username = trim($_POST['email_or_username']);
        $password = $_POST['password'];

        if (empty($email_or_username)) $errors[] = "Email or Username is required.";
        if (empty($password)) $errors[] = "Password is required.";

        if (empty($errors)) {
            $stmt = $conn->prepare("SELECT id, username, password_hash FROM users WHERE email = ? OR username = ?");
            if ($stmt) {
                $stmt->bind_param("ss", $email_or_username, $email_or_username);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows == 1) {
                    $user = $result->fetch_assoc();
                    if (password_verify($password, $user['password_hash'])) {
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['username'] = $user['username'];
                        session_regenerate_id(true);
                        header("Location: index.php");
                        exit;
                    } else {
                        $errors[] = "Invalid email/username or password.";
                    }
                } else {
                    $errors[] = "Invalid email/username or password.";
                }
                $stmt->close();
            } else {
                $errors[] = "Database query preparation failed.";
            }
        }
    } else {
        $errors[] = "Database connection is not available.";
    }
   
}


require_once 'includes/header.php';
?>

<div class="container auth-form">
    <h2>Login</h2>
    <?php if (!empty($message)): ?>
        <div class="success-message"><p><?php echo htmlspecialchars($message); ?></p></div>
    <?php endif; ?>
    <?php if (!empty($errors)): ?>
        <div class="errors">
            <?php foreach ($errors as $error): ?>
                <p><?php echo htmlspecialchars($error); ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <form action="login.php" method="post">
        <div>
            <label for="email_or_username">Email or Username:</label>
            <input type="text" id="email_or_username" name="email_or_username" value="<?php echo isset($_POST['email_or_username']) ? htmlspecialchars($_POST['email_or_username']) : ''; ?>" required>
        </div>
        <div>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
        </div>
        <button type="submit">Login</button>
    </form>
    <p>Don't have an account? <a href="signup.php">Sign up here</a>.</p>
</div>

<?php
require_once 'includes/footer.php'; 
?>