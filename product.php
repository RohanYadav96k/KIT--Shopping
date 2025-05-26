<?php
require_once 'includes/header.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<p>Invalid product ID.</p>";
    require_once 'includes/footer.php';
    exit;
}

$product_id = intval($_GET['id']);

$sql = "SELECT p.*, c.name as category_name FROM products p
        JOIN categories c ON p.category_id = c.id
        WHERE p.id = $product_id";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $product = $result->fetch_assoc();
} else {
    echo "<p>Product not found.</p>";
    require_once 'includes/footer.php';
    exit;
}
?>

<div class="container product-detail">
    <div class="product-image-lg">
        <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
    </div>
    <div class="product-info">
        <h1><?php echo htmlspecialchars($product['name']); ?></h1>
        <p class="category">Category: <?php echo htmlspecialchars($product['category_name']); ?></p>
        <p class="price-lg">Price: $<?php echo number_format($product['price'], 2); ?></p>
        <p class="description"><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
        <p>Stock: <?php echo $product['stock_quantity'] > 0 ? $product['stock_quantity'] . ' available' : 'Out of stock'; ?></p>

        <?php if ($product['stock_quantity'] > 0): ?>
        <form action="cart.php" method="post" class="add-to-cart-form">
            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
            <input type="hidden" name="action" value="add">
            <label for="quantity">Quantity:</label>
            <input type="number" id="quantity" name="quantity" value="1" min="1" max="<?php echo $product['stock_quantity']; ?>">
            <button type="submit">Add to Cart</button>
        </form>
        <?php else: ?>
            <p class="out-of-stock">This product is currently out of stock.</p>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>