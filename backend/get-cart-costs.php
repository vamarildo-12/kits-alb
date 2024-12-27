<?php
// Database connection
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'web';

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die(json_encode(['error' => 'Database connection failed']));
}

// Get the cart items from the request
$cartItems = json_decode(file_get_contents('php://input'), true);

// Make sure cart_items is an array and is not empty
if (empty($cartItems['cart_items'])) {
    echo json_encode(['error' => 'No cart items provided']);
    exit;
}

$cartItems = $cartItems['cart_items'];  // Extract cart items array

// Initialize total costs
$productCostCents = 0;
$shippingCostCents = 0;
$taxRate = 0.1; // Example tax rate, adjust as necessary

// Prepare the SQL query to fetch product and shipping costs for each item in the cart
$productIds = array_map(function ($item) {
    return "'" . $item['productId'] . "'";
}, $cartItems);
$productIdsStr = implode(',', $productIds);

// Query the database for the product details
$sql = "
    SELECT 
        p.id AS product_id,
        p.priceCents,
        d.costCents AS shipping_cost
    FROM products p
    JOIN cart c ON p.id = c.product_id
    JOIN delivery_options d ON c.delivery_option_id = d.id
    WHERE p.id IN ($productIdsStr)
";

$result = $conn->query($sql);

if (!$result) {
    echo json_encode(['error' => 'Database query failed']);
    exit;
}

while ($row = $result->fetch_assoc()) {
    // Find the corresponding cart item in the request and calculate product and shipping costs
    foreach ($cartItems as $cartItem) {
        if ($cartItem['productId'] === $row['product_id']) {
            $productCostCents += $row['priceCents'] * $cartItem['quantity'];
            $shippingCostCents += $row['shipping_cost'] * $cartItem['quantity'];
        }
    }
}

// Calculate tax
$taxCents = ($productCostCents + $shippingCostCents) * $taxRate;

// Calculate total cost
$totalCents = round($productCostCents + $shippingCostCents + $taxCents);

// Return the calculated costs
echo json_encode([
    'costs' => [
        'productCostCents' => $productCostCents,
        'shippingCostCents' => $shippingCostCents,
        'taxCents' => $taxCents,
        'totalCents' => $totalCents
    ]
]);

$conn->close();
?>
