<?php
require_once 'includes/header.php';

if (!isset($_SESSION['order_id'])) {
    
    header("Location: index.php");
    exit;
}

$order_id = $_SESSION['order_id'];
unset($_SESSION['order_id']); // Clear it after displaying

// Fetch order details 
$stmt = $conn->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $order_id, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();
$stmt->close();

if (!$order) {
    echo "<p>Order not found or access denied.</p>";
    require_once 'includes/footer.php';
    exit;
}
?>

<div class="container order-confirmation-page">
    <h1>Order Confirmed!</h1>
    <p>Thank you for your purchase, <?php echo htmlspecialchars($_SESSION['username']); ?>!</p>
    <p>Your Order ID is: <strong><?php echo $order_id; ?></strong></p>
    <p>Total Amount: <strong>$<?php echo number_format($order['total_amount'], 2); ?></strong></p>
    <p>Shipping Address: <?php echo nl2br(htmlspecialchars($order['shipping_address'])); ?></p>

    <h2>Dummy Delivery Information:</h2>
    <p>Your order is currently: <strong><?php echo htmlspecialchars($order['status']); ?></strong>.</p>
    <p>Estimated Delivery: <strong>In 3-5 business days (This is a dummy estimate).</strong></p>
    <p>You will receive a (dummy) email shortly with tracking information.</p>

    <p><a href="index.php" class="btn">Continue Shopping</a></p>
</div>

<?php require_once 'includes/footer.php'; ?>