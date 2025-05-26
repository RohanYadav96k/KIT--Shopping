<?php require_once 'includes/header.php'; ?>

<div class="container">
    <h1>Welcome to Our Store!</h1>
    <h2>Featured Products</h2>
    <div class="product-grid">
        <?php
       
        $sql = "SELECT p.id, p.name, p.price, p.image_url, c.slug as category_slug
                FROM products p
                JOIN categories c ON p.category_id = c.id
                ORDER BY RAND() LIMIT 9"; 
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            while ($product = $result->fetch_assoc()) {
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
            echo "<p>No products found.</p>";
        }
        ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>