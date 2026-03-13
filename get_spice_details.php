<?php
header('Content-Type: application/json');
$conn = new mysqli("localhost", "root", "mynewpass123", "spice_shop");

if (isset($_GET['name'])) {
    $name = $conn->real_escape_string($_GET['name']);
    
    // Notice we use 'quantity' because that's what your view_stocks.php uses
    $res = $conn->query("SELECT price, quantity FROM spices WHERE spice_name = '$name'");
    
    if ($row = $res->fetch_assoc()) {
        // We send it back as 'stock_quantity' so your JS doesn't have to change
        echo json_encode([
            'price' => $row['price'],
            'stock_quantity' => $row['quantity'] 
        ]);
    } else {
        echo json_encode(['price' => 0, 'stock_quantity' => 0]);
    }
}
?>