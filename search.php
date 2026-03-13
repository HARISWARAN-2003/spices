<?php
include('db.php');

$q = mysqli_real_escape_string($conn, $_GET['q']);
$b = mysqli_real_escape_string($conn, $_GET['b']);

// Search query logic
$sql = "SELECT * FROM spices WHERE spice_name LIKE '%$q%' AND branch LIKE '%$b%' ORDER BY id ASC";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    while($row = mysqli_fetch_assoc($result)) {
        $qty = $row['quantity'];
        
        // This is the logic you need to add/ensure is inside search.php
        $tdClass = ($qty <= 0) ? 'class="out-of-stock-cell"' : '';
        $displayQty = ($qty <= 0) ? "OUT OF STOCK" : $qty . " kg";

        echo "<tr>
                <td>".$row['id']."</td>
                <td><strong>".$row['spice_name']."</strong></td>
                <td>₹ ".number_format($row['price'], 2)."</td>
                <td $tdClass>".$displayQty."</td>
                <td>".$row['branch']."</td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='5' style='padding:20px;'>No matching stock found.</td></tr>";
}
?>