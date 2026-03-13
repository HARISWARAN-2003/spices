<?php
include('db.php');

$msg = "";
$msg_type = "";

// Handle form submission
if(isset($_POST['add'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];
    $branch = $_POST['branch'];

    try {
        // Insert into spices table
        $sql = "INSERT INTO spices(id, spice_name, price, quantity, branch) 
                VALUES('$id', '$name', '$price', '$quantity', '$branch')";

        if(mysqli_query($conn, $sql)) {
            $msg = "✅ Spice Added Successfully!";
            $msg_type = "success";
        }
    } 
    catch (mysqli_sql_exception $e) {
        // Handle Duplicate ID Error
        if ($e->getCode() == 1062) {
            $msg = "❌ Error: ID number <strong>$id</strong> is already present in the inventory!";
            $msg_type = "error";
        } else {
            $msg = "❌ Database Error: " . addslashes($e->getMessage());
            $msg_type = "error";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Spice - Spice Shop</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; text-align: center; background-color: #f4f7f6; margin: 20px; color: #333; }
        h2 { color: #2e7d32; margin-top: 30px; }

        /* --- STYLED NOTIFICATION (Toast) --- */
        #notification {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            padding: 15px 25px;
            border-radius: 8px;
            color: white;
            font-weight: bold;
            z-index: 1000;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            display: <?php echo $msg ? 'block' : 'none'; ?>;
            animation: fadeIn 0.5s;
        }
        .success { background-color: #4CAF50; border: 1px solid #388E3C; }
        .error { background-color: #f44336; border: 1px solid #D32F2F; }

        @keyframes fadeIn { from { opacity: 0; top: 0; } to { opacity: 1; top: 20px; } }

        .form-box {
            background: white;
            padding: 30px;
            width: 400px;
            margin: 20px auto 40px auto;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .form-box label { display: block; text-align: left; margin-top: 10px; font-weight: bold; color: #555; }

        .form-box input, .form-box select {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }

        .form-box button {
            margin-top: 25px;
            width: 100%;
            padding: 12px;
            background: #2e7d32;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
        }

        .form-box button:hover { background: #1b5e20; }

        table {
            border-collapse: collapse;
            margin: 20px auto;
            width: 90%;
            background: white;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
            border-radius: 8px;
            overflow: hidden;
        }

        th { background-color: #2e7d32; color: white; padding: 15px; text-transform: uppercase; font-size: 13px; }
        td { border-bottom: 1px solid #eee; padding: 12px; font-size: 15px; }
        tr:hover { background-color: #f1f8e9; }

        /* OUT OF STOCK Badge */
        .out-stock {
            color: white;
            background-color: red;
            padding: 3px 8px;
            border-radius: 5px;
            font-weight: bold;
            display: inline-block;
        }
    </style>
</head>
<body>

    <div class="header-container">
        <div style="display: flex; align-items: center; gap: 15px;">
            <a href="dashboard.php" style="text-decoration: none; display: flex; align-items: center; gap: 10px;">
                <h2 style="margin: 0;">🌿 HINKAR TRADERS</h2>
            </a>
        </div>
    </div>

    <?php if($msg): ?>
        <div id="notification" class="<?php echo $msg_type; ?>">
            <?php echo $msg; ?>
        </div>
    <?php endif; ?>

    <h2>🌿 Add New Spice</h2>

    <div class="form-box">
        <form method="POST">
            <label>Spice ID (Must be Unique)</label>
            <input type="number" name="id" required placeholder="e.g. 101">

            <label>Spice Name</label>
            <input type="text" name="name" required placeholder="e.g. Cardamom">

            <label>Price (₹/kg)</label>
            <input type="number" name="price" step="0.01" required placeholder="0.00">

            <label>Quantity (kg)</label>
            <input type="number" name="quantity" required placeholder="0">

            <label>Branch</label>
            <select name="branch">
                <option>HINKAR SHOP</option>
                <option>GODOWN1</option>
                <option>GODOWN2</option>
            </select>

            <button type="submit" name="add">Add to Inventory</button>
        </form>
    </div>

    <h2>📦 Current Stock Overview</h2>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Spice Name</th>
                <th>Price (₹/kg)</th>
                <th>Quantity (kg)</th>
                <th>Branch Location</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sql = "SELECT * FROM spices ORDER BY id ASC";
            $result = mysqli_query($conn, $sql);

            if(mysqli_num_rows($result) > 0) {
                while($row = mysqli_fetch_assoc($result)) {
                    // Display OUT OF STOCK if quantity is 0
                    $quantity_display = $row['quantity'] > 0 ? $row['quantity']." kg" : '<span class="out-stock">OUT OF STOCK</span>';

                    echo "<tr>
                            <td><strong>".$row['id']."</strong></td>
                            <td>".htmlspecialchars($row['spice_name'])."</td>
                            <td>₹ ".$row['price']."</td>
                            <td>$quantity_display</td>
                            <td>".$row['branch']."</td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='5'>No stock available in the database.</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <script>
        // Automatically hide the message after 3.5 seconds
        setTimeout(function() {
            var notify = document.getElementById('notification');
            if(notify) {
                notify.style.opacity = '0';
                setTimeout(function(){ notify.style.display = 'none'; }, 500);
            }
        }, 3500);
    </script>

</body>
</html>