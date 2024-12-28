<?php
//// Database connection
//$host = 'localhost'; // Change to your database host
//$dbname = 'web'; // Change to your database name
//$username = 'root'; // Change to your database username
//$password = ''; // Change to your database password
//


//try {
//    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
//    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//
//    // SQL query to fetch product data
//    $sql = 'SELECT id, name, image, stars, rating_count, priceCents FROM products';
//    $stmt = $pdo->query($sql);
//
//    // Fetch all products as an associative array
//    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
//
//    // Format the products to the structure needed in the frontend
//    $formattedProducts = [];
//    foreach ($products as $product) {
//        $formattedProducts[] = [
//            'id' => $product['id'],
//            'name' => $product['name'],
//            'image' => $product['image'],
//            'rating' => [
//                'stars' => $product['stars'], // Assuming rating is a number out of 5
//                'count' => $product['rating_count']
//            ],
//            'priceCents' => $product['priceCents']
//        ];
//    }
//
//    // Return the products as JSON
//    header('Content-Type: application/json');
//    echo json_encode($formattedProducts);
//
//} catch (PDOException $e) {
//    // Handle database connection errors
//    http_response_code(500);
//    echo json_encode(['error' => 'Failed to fetch products: ' . $e->getMessage()]);
//}
//

// Path to the JSON file
$jsonFile = __DIR__ . '/products.json';

// Check if the file exists
if (file_exists($jsonFile)) {
    // Set the appropriate header and output the JSON content
    header('Content-Type: application/json');
    echo file_get_contents($jsonFile);
} else {
    // Handle the case where the file doesn't exist
    http_response_code(404);
    echo json_encode(['error' => 'Products file not found']);
}
