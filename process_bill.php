<?php
$conn = new mysqli("localhost", "root", "mynewpass123", "spice_shop");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action']; 
    $buyer_name = $conn->real_escape_string($_POST['buyer_name']);
    $buyer_phone = $conn->real_escape_string($_POST['buyer_phone']);
    $final_total = floatval($_POST['final_total']);
    
    // Generate Invoice Number
    $invoice_no = "HT-" . strtoupper(substr(md5(time()), 0, 6));

    $conn->begin_transaction();

    try {
        // 1. Insert into 'bills' table
        $stmt1 = $conn->prepare("INSERT INTO bills (invoice_no, buyer_name, buyer_phone, total_amount) VALUES (?, ?, ?, ?)");
        $stmt1->bind_param("sssd", $invoice_no, $buyer_name, $buyer_phone, $final_total);
        $stmt1->execute();

        // 2. Process each spice item
        $spice_names = $_POST['spice_names'];
        $quantities = $_POST['quantities'];
        $rates = $_POST['rates'];
        $row_totals = $_POST['row_totals'];

        for ($i = 0; $i < count($spice_names); $i++) {
            if (!empty($spice_names[$i])) {
                $name = $conn->real_escape_string($spice_names[$i]);
                $qty = floatval($quantities[$i]);
                $rate = floatval($rates[$i]);
                $sub = floatval($row_totals[$i]);

                // A. Record the item in 'bill_items'
                $stmt2 = $conn->prepare("INSERT INTO bill_items (invoice_no, spice_name, quantity, rate, subtotal) VALUES (?, ?, ?, ?, ?)");
                $stmt2->bind_param("ssddd", $invoice_no, $name, $qty, $rate, $sub);
                $stmt2->execute();

                // B. THE KEY STEP: Update the stock in 'spices' table
                // This line makes the 'view_stocks.php' change!
                $updateStock = "UPDATE spices SET quantity = quantity - $qty WHERE spice_name = '$name'";
                $conn->query($updateStock);
            }
        }

        $conn->commit();

        // Redirect based on button clicked
        if ($action === 'save_print') {
            // Open print in new tab and go to history in main tab
            echo "<script>
                window.open('print_bill.php?inv=$invoice_no', '_blank');
                window.location.href = 'view_bills.php?msg=success';
            </script>";
        } else {
            header("Location: view_bills.php?msg=saved");
        }
        exit();

    } catch (Exception $e) {
        $conn->rollback();
        die("Transaction Failed: " . $e->getMessage());
    }
}
?>