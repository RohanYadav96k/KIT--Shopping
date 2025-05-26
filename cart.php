<?php
require_once 'includes/header.php'; 


if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;

    if ($product_id > 0) {
        // Fetch product details 
        $stmt_prod = $conn->prepare("SELECT name, price, stock_quantity FROM products WHERE id = ?");
        $stmt_prod->bind_param("i", $product_id);
        $stmt_prod->execute();
        $product_result = $stmt_prod->get_result();

        if ($product_result->num_rows > 0) {
            $product_data = $product_result->fetch_assoc();

            if ($_POST['action'] === 'add') {
                if ($quantity <= 0) $quantity = 1; // Ensures quantity is available or not
                if ($quantity > $product_data['stock_quantity']) {
                     $_SESSION['cart_message'] = "Not enough stock for " . htmlspecialchars($product_data['name']);
                } else {
                    if (isset($_SESSION['cart'][$product_id])) {
                        $new_quantity = $_SESSION['cart'][$product_id]['quantity'] + $quantity;
                        if ($new_quantity > $product_data['stock_quantity']) {
                            $_SESSION['cart_message'] = "Cannot add more. Not enough stock for " . htmlspecialchars($product_data['name']);
                            $_SESSION['cart'][$product_id]['quantity'] = $product_data['stock_quantity']; 
                        } else {
                            $_SESSION['cart'][$product_id]['quantity'] = $new_quantity;
                        }
                    } else {
                        $_SESSION['cart'][$product_id] = [
                            'name' => $product_data['name'],
                            'price' => $product_data['price'],
                            'quantity' => $quantity,
                            'image_url' => '' 
                        ];
                    }
                    $_SESSION['cart_message'] = htmlspecialchars($product_data['name']) . " added to cart.";
                }
            } elseif ($_POST['action'] === 'update') {
                if ($quantity > 0 && $quantity <= $product_data['stock_quantity']) {
                    $_SESSION['cart'][$product_id]['quantity'] = $quantity;
                } elseif ($quantity <= 0) {
                    unset($_SESSION['cart'][$product_id]); // 
                } else {
                    $_SESSION['cart_message'] = "Not enough stock for " . htmlspecialchars($product_data['name']) . ". Max quantity is " . $product_data['stock_quantity'];
                    $_SESSION['cart'][$product_id]['quantity'] = $product_data['stock_quantity']; 
                }
            }
        }
        $stmt_prod->close();

    } elseif ($_POST['action'] === 'remove' && $product_id > 0) {
         unset($_SESSION['cart'][$product_id]);
         $_SESSION['cart_message'] = "Item removed from cart.";
    } elseif ($_POST['action'] === 'clear') {
        $_SESSION['cart'] = [];
        $_SESSION['cart_message'] = "Cart cleared.";
    }
    // goto to cart on refresh
    header("Location: cart.php");
    exit;
}
?>

<div class="container cart-page">
    <h1>Your Shopping Cart</h1>

    <?php
    if (isset($_SESSION['cart_message'])) {
        echo '<p class="cart-message">' . $_SESSION['cart_message'] . '</p>';
        unset($_SESSION['cart_message']);
    }
    ?>

    <?php if (empty($_SESSION['cart'])): ?>
        <p>Your cart is empty. <a href="index.php">Continue shopping</a>.</p>
    <?php else: ?>
        <table class="cart-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Subtotal</th>
                    <th>Action</th>
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
                    <td>
                        <?php
                        // Fetch image if not stored in session (or store it on add)
                        $stmt_img = $conn->prepare("SELECT image_url FROM products WHERE id = ?");
                        $stmt_img->bind_param("i", $id);
                        $stmt_img->execute();
                        $img_res = $stmt_img->get_result();
                        $img_data = $img_res->fetch_assoc();
                        if ($img_data && $img_data['image_url']) {
                            echo '<img src="'.htmlspecialchars($img_data['image_url']).'" alt="'.htmlspecialchars($item['name']).'" style="width:50px; height:auto; margin-right:10px;">';
                        }
                        $stmt_img->close();
                        echo htmlspecialchars($item['name']);
                        ?>
                    </td>
                    <td>$<?php echo number_format($item['price'], 2); ?></td>
                    <td>
                        <form action="cart.php" method="post" style="display:inline;">
                            <input type="hidden" name="product_id" value="<?php echo $id; ?>">
                            <input type="hidden" name="action" value="update">
                            <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1" style="width: 60px;">
                            <button type="submit" class="update-btn">Update</button>
                        </form>
                    </td>
                    <td>$<?php echo number_format($subtotal, 2); ?></td>
                    <td>
                        <form action="cart.php" method="post" style="display:inline;">
                            <input type="hidden" name="product_id" value="<?php echo $id; ?>">
                            <input type="hidden" name="action" value="remove">
                            <button type="submit" class="remove-btn">Remove</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" style="text-align:right;"><strong>Total:</strong></td>
                    <td colspan="2"><strong>$<?php echo number_format($total_price, 2); ?></strong></td>
                </tr>
            </tfoot>
        </table>
        <div class="cart-actions">
            <form action="cart.php" method="post" style="display:inline-block;">
                <input type="hidden" name="action" value="clear">
                <button type="submit" class="clear-cart-btn">Clear Cart</button>
            </form>
            <a href="checkout.php" class="checkout-btn">Proceed to Checkout</a>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>