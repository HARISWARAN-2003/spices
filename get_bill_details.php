<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

$conn = new mysqli("localhost", "root", "mynewpass123", "spice_shop");

if ($conn->connect_error) {
    echo json_encode(["error" => "Connection failed: " . $conn->connect_error]);
    exit;
}

if (isset($_GET['inv'])) {
    $inv = $conn->real_escape_string($_GET['inv']);
    
    // DEBUG: We trim the invoice number just in case there are hidden spaces
    $inv = trim($inv);

    // Update 'invoice_no' below if your column name is different (e.g., 'bill_id' or 'inv_id')
    $query = "SELECT spice_name, quantity, rate, subtotal FROM bill_items WHERE invoice_no = '$inv'";
    $result = $conn->query($query);
    
    $items = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $items[] = $row;
        }
    } else {
        // If the query fails, return the SQL error
        echo json_encode(["error" => "SQL Error: " . $conn->error]);
        exit;
    }
    
    echo json_encode($items);
} else {
    echo json_encode(["error" => "No invoice number provided"]);
}
?>