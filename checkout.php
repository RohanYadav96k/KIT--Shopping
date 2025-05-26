<?php
require_once 'includes/header.php';

// User must be logged in 
if (!isset($_SESSION['user_id'])) {
    $_SESSION['message'] = "Please login to proceed to checkout.";
    header("Location: login.php");
    exit;
}

// Cart must not be empty
if (empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$errors = [];
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $shipping_address = trim($_POST['shipping_address']);
    
    $payment_method = $_POST['payment_method'];

    if (empty($shipping_address)) {
        $errors[] = "Shipping address is required.";
    }

    if (empty($errors)) {
        $total_amount = 0;
        foreach ($_SESSION['cart'] as $item) {
            $total_amount += $item['price'] * $item['quantity'];
        }

        $conn->begin_transaction(); 

        try {
            //  Creates order
            $stmt_order = $conn->prepare("INSERT INTO orders (user_id, total_amount, shipping_address, status) VALUES (?, ?, ?, 'Processing')");
            $stmt_order->bind_param("ids", $user_id, $total_amount, $shipping_address);
            $stmt_order->execute();
            $order_id = $stmt_order->insert_id;
            $stmt_order->close();

            //  Insert order items and update stock
            foreach ($_SESSION['cart'] as $product_id => $item) {
                $stmt_item = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price_at_purchase) VALUES (?, ?, ?, ?)");
                $stmt_item->bind_param("iiid", $order_id, $product_id, $item['quantity'], $item['price']);
                $stmt_item->execute();
                $stmt_item->close();

                // Update stock quantity
                $stmt_stock = $conn->prepare("UPDATE products SET stock_quantity = stock_quantity - ? WHERE id = ? AND stock_quantity >= ?");
                $stmt_stock->bind_param("iii", $item['quantity'], $product_id, $item['quantity']);
                if (!$stmt_stock->execute() || $stmt_stock->affected_rows == 0) {
                    throw new Exception("Stock update failed for product ID: " . $product_id . ". Order rolled back.");
                }
                $stmt_stock->close();
            }

            $conn->commit(); 

            //  Clear cart
            $_SESSION['cart'] = [];
            $_SESSION['order_id'] = $order_id; // Store order ID for confirmation page

            header("Location: order_confirmation.php");
            exit;

        } catch (Exception $e) {
            $conn->rollback(); // Rollback transaction on error
            $errors[] = "Order processing failed: " . $e->getMessage() . " Please try again.";
        }
    }
}
?>

<div class="container checkout-page">
    <h1>Checkout</h1>

    <?php if (!empty($errors)): ?>
        <div class="errors">
            <?php foreach ($errors as $error): ?>
                <p><?php echo $error; ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form action="checkout.php" method="post">
        <div class="form-group">
            <label for="shipping_address">Shipping Address:</label>
            <textarea id="shipping_address" name="shipping_address" rows="3" required><?php echo isset($_POST['shipping_address']) ? htmlspecialchars($_POST['shipping_address']) : ''; ?></textarea>
        </div>

        <div class="form-group">
            <label>Payment Method (Dummy):</label>
            <div>
                <input type="radio" id="credit_card" name="payment_method" value="credit_card" checked>
                <label for="credit_card">Credit Card (Dummy)</label>
            </div>
            <div>
                <input type="radio" id="paypal" name="payment_method" value="paypal_dummy">
                <label for="paypal">PayPal (Dummy)</label>
            </div>
        </div>
        <p><em>This is a dummy payment. No real transaction will occur.</em></p>

        <button type="submit" class="btn-proceed">Place Order</button>
    </form>

    <h2>Order Summary</h2>
    <table class="cart-table">
         <thead>
            <tr>
                <th>Product</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $total_price = 0;
            foreach ($_SESSION['cart'] as $id => $item):
                $subtotal = $item['price'] * $item['quantity'];
                $total_price += $subtotal;
            ?>
            <tr>
                <td><?php echo htmlspecialchars($item['name']); ?></td>
                <td>$<?php echo number_format($item['price'], 2); ?></td>
                <td><?php echo $item['quantity']; ?></td>
                <td>$<?php echo number_format($subtotal, 2); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" style="text-align:right;"><strong>Total:</strong></td>
                <td><strong>$<?php echo number_format($total_price, 2); ?></strong></td>
            </tr>
        </tfoot>
    </table>
</div>

<?php require_once 'includes/footer.php'; ?>