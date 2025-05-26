<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'db.php'; 
$conn = $GLOBALS['conn']; // Ensure connection variable is available


if (!$conn || !$conn->ping()) {
    die("MySQL connection is closed!");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KITShopping</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <nav>
            <div class="logo">
                <a href="index.php">KITShopping</a>
            </div>
            <ul class="nav-links">
                <li><a href="index.php">Home</a></li>
                <?php
                // Fetch categories for navbar
                $cat_result = $conn->query("SELECT * FROM categories ORDER BY name ASC");
                if ($cat_result && $cat_result->num_rows > 0) {
                    while ($category = $cat_result->fetch_assoc()) {
                        echo '<li><a href="category.php?slug=' . htmlspecialchars($category['slug']) . '">' . htmlspecialchars($category['name']) . '</a></li>';
                    }
                }
                ?>
            </ul>
            <div class="nav-actions">
                <form action="search.php" method="GET" class="search-form">
                    <input type="text" name="query" placeholder="Search products..." required>
                    <button type="submit">Search</button>
                </form>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <span>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</span>
                    <a href="cart.php">Cart (<?php
                        $cart_count = 0;
                        if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
                            foreach ($_SESSION['cart'] as $item) {
                                $cart_count += $item['quantity'];
                            }
                        }
                        echo $cart_count;
                     ?>)</a>
                    <a href="logout.php">Logout</a>
                <?php else: ?>
                    <a href="login.php">Login</a>
                    <a href="signup.php">Sign Up</a>
                    <a href="cart.php">Cart (<?php
                        $cart_count = 0;
                        if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
                            foreach ($_SESSION['cart'] as $item) {
                                $cart_count += $item['quantity'];
                            }
                        }
                        echo $cart_count;
                     ?>)</a>
                <?php endif; ?>
            </div>
        </nav>
    </header>
    <main>
