<?php
// Load the products JSON data
$productsJson = file_get_contents('C:\xampp\htdocs\kits-alb\backend\products.json');
$products = json_decode($productsJson, true);

// Get the product ID from the URL
$productId = isset($_GET['id']) ? $_GET['id'] : null;
$product = null;
$relatedProducts = [];

// Find the current product and related products
if ($productId) {
    foreach ($products as $p) {
        if ($p['id'] == $productId) {
            $product = $p;
        }
    }

    // Find related products based on overlapping keywords
    if ($product) {
        $productKeywords = $product['keywords'];

        foreach ($products as $p) {
            // Check if the product has overlapping keywords and is not the same product
            if ($p['id'] != $productId && !empty(array_intersect($productKeywords, $p['keywords']))) {
                $relatedProducts[] = $p;
            }
        }
    }
}

// If the product is not found
if ($product === null) {
    echo "Product not found.";
    exit;
}

// Limit related products to 5
$relatedProducts = array_slice($relatedProducts, 0, 5);

// Format price from cents to dollars
$price = number_format($product['priceCents'] / 100, 2);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?></title>

    <!-- Load the font Roboto from Google Fonts. -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    
    <!-- Our custom CSS for this page. -->
    <link rel="stylesheet" href="styles/shared/general.css">
    <link rel="stylesheet" href="styles/shared/kits-header.css">
    <link rel="stylesheet" href="styles/pages/catalog.css">
    <link rel="stylesheet" href="styles/pages/view-product.css">
</head>
<body>
    <header class="js-kits-header kits-header"></header>

    <!-- Return Button -->
    <div class="return-button">
        <a href="catalog.php" class="return-link">← Return</a>
    </div>

    <!-- Main Product Section -->
    <div class="product-container">
        <div class="product-details">
            <div class="product-image">
                <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" />
            </div>

            <div class="product-info">
                <h1><?php echo htmlspecialchars($product['name']); ?></h1>

                <!-- Size Selector -->
                <label for="size">Size:</label>
                <select id="size" name="size">
                    <option value="S">S</option>
                    <option value="M">M</option>
                    <option value="L">L</option>
                    <option value="XL">XL</option>
                    <option value="XXL">XXL</option>
                </select>

                <p class="product-price">$<?php echo $price; ?></p>

                <!-- Add to Cart Button -->
                <button class="add-to-cart" data-product-id="<?php echo $product['id']; ?>">Add to Cart</button>
            </div>
        </div>
    </div>

    <!-- Related Products Section -->
    <div class="related-products">
    <h2>Related Products</h2>
    <div id="related-products-list" class="related-products-list">
        <?php if (count($relatedProducts) > 0): ?>
            <?php foreach ($relatedProducts as $related): ?>
                <div class="related-product">
                    <a href="view-product.php?id=<?php echo htmlspecialchars($related['id']); ?>">
                        <img src="<?php echo htmlspecialchars($related['image']); ?>" alt="<?php echo htmlspecialchars($related['name']); ?>">
                        <p class="product-name"><?php echo htmlspecialchars($related['name']); ?></p>
                        <p class="product-price">$<?php echo number_format($related['priceCents'] / 100, 2); ?></p>
                    </a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No related products found.</p>
        <?php endif; ?>
    </div>
</div>

    <script type="module" src="scripts/pages/view-product.js"></script>
</body>
</html>
