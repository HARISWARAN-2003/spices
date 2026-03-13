<?php
include('db.php');

// Define the filename with the business name and current date
$filename = "Hinkar_Traders_Stock_" . date('Y-m-d') . ".csv";

// Force the browser to treat the response as a file download
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=' . $filename);

// Open the output stream
$output = fopen('php://output', 'w');

// Add column headers to the CSV
fputcsv($output, array('ID', 'Spice Name', 'Price (INR/kg)', 'Quantity (kg)', 'Branch Location'));

// Fetch all spices from the database
$sql = "SELECT * FROM spices";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        // Map database columns to CSV rows
        fputcsv($output, array(
            $row['id'],
            $row['spice_name'],
            $row['price'],
            $row['quantity'],
            $row['branch']
        ));
    }
}

fclose($output);
exit();
?>