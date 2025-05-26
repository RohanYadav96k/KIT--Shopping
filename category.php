<?php
require_once 'includes/header.php';

if (!isset($_GET['slug'])) {
    echo "<p>No category specified.</p>";
    require_once 'includes/footer.php';
    exit;
}

$category_slug = $conn->real_escape_string($_GET['slug']);


$cat_sql = "SELECT id, name FROM categories WHERE slug = '$category_slug'";
$cat_res = $conn->query($cat_sql);

if ($cat_res && $cat_res->num_rows > 0) {
    $category = $cat_res->fetch_assoc();
    $category_id = $category['id'];
    $category_name = $category['name'];
} else {
    echo "<p>Category not found.</p>";
    require_once 'includes/footer.php';
    exit;
}
?>

<div class="container">
    <h1><?php echo htmlspecialchars($category_name); ?></h1>
    <div class="product-grid">
        <?php
        $prod_sql = "SELECT id, name, price, image_url FROM products WHERE category_id = $category_id ORDER BY name ASC";
        $prod_result = $conn->query($prod_sql);

        if ($prod_result && $prod_result->num_rows > 0) {
            while ($product = $prod_result->fetch_assoc()) {
                echo '<div class="product-card">';
                echo '<a href="product.php?id=' . $product['id'] . '">';
                echo '<img src="' . htmlspecialchars($product['image_url']) . '" alt="' . htmlspecialchars($product['name']) . '">';
                echo '<h3>' . htmlspecialchars($product['name']) . '</h3>';
                echo '</a>';
                echo '<p class="price">$' . number_format($product['price'], 2) . '</p>';
                echo '<form action="cart.php" method="post">';
                echo '<input type="hidden" name="product_id" value="' . $product['id'] . '">';
                echo '<input type="hidden" name="action" value="add">';
                echo '<input type="number" name="quantity" value="1" min="1" style="width: 50px;">';
                echo '<button type="submit">Add to Cart</button>';
                echo '</form>';
                echo '</div>';
            }
        } else {
            echo "<p>No products found in this category.</p>";
        }
        ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>