<?php
require_once 'includes/header.php';

$search_query = "";
if (isset($_GET['query'])) {
    $search_query = trim($conn->real_escape_string($_GET['query']));
}
?>

<div class="container">
    <h1>Search Results for "<?php echo htmlspecialchars($search_query); ?>"</h1>
    <div class="product-grid">
        <?php
        if (!empty($search_query)) {
           
            $sql = "SELECT p.id, p.name, p.price, p.image_url, c.slug as category_slug
                    FROM products p
                    JOIN categories c ON p.category_id = c.id
                    WHERE p.name LIKE ? OR p.description LIKE ?";
            $search_term = "%" . $search_query . "%";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $search_term, $search_term);
            $stmt->execute();
            $result = $stmt->get_result();

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
                echo "<p>No products found matching your search criteria.</p>";
            }
            $stmt->close();
        } else {
            echo "<p>Please enter a search term.</p>";
        }
        ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>