<?php
include('db.php'); 

$msg = "";
$msg_type = ""; 

/* UPDATE LOGIC */
if(isset($_POST['update']))
{
    $old_id = $_POST['old_id'];
    $new_id = $_POST['id'];
    $price  = $_POST['price'];
    $qty    = $_POST['qty'];
    $branch = $_POST['branch'];

    try {
        $sql = "UPDATE spices SET id='$new_id', price='$price', quantity='$qty', branch='$branch' WHERE id='$old_id'";
        if(mysqli_query($conn, $sql)) {
            $msg = "✅ Spice ID and details updated successfully!";
            $msg_type = "success";
        }
    } 
    catch (mysqli_sql_exception $e) {
        if ($e->getCode() == 1062) {
            $msg = "❌ Error: ID number <strong>$new_id</strong> is already present!";
            $msg_type = "error";
        } else {
            $msg = "❌ Database Error: " . addslashes($e->getMessage());
            $msg_type = "error";
        }
    }
}

/* DELETE LOGIC */
if(isset($_POST['delete']))
{
    $id = $_POST['id'];
    $sql = "DELETE FROM spices WHERE id='$id'";
    mysqli_query($conn, $sql);
    $msg = "🗑️ Spice deleted successfully!";
    $msg_type = "success";
}

$sql = "SELECT * FROM spices";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Spice Stock | HINKAR TRADERS</title>
    <style>
        body { font-family: 'Segoe UI', Arial; background: #f4f7f6; text-align: center; margin: 20px; }
        
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

        h2 { color: #2e7d32; }
        .search-container { margin-bottom: 25px; }
        input[type="text"]#search { padding: 12px 20px; width: 350px; border-radius: 25px; border: 1px solid #ccc; outline: none; }

        table { border-collapse: collapse; margin: auto; width: 95%; background: white; box-shadow: 0 4px 15px rgba(0,0,0,0.1); border-radius: 8px; }
        th { background: #2e7d32; color: white; padding: 15px; }
        td { border-bottom: 1px solid #eee; padding: 12px; }
        
        .id-input { width: 55px; padding: 6px; border: 1px solid #999; border-radius: 4px; background: #f1f8e9; font-weight: bold; text-align: center; }
        .price-input, .qty-input { padding: 6px; border: 1px solid #ddd; border-radius: 4px; width: 70px; }
        
        button { padding: 8px 16px; cursor: pointer; border: none; border-radius: 4px; font-weight: bold; transition: 0.2s; }
        button[name="update"] { background: #2e7d32; color: white; }
        button[name="update"]:hover { background: #1b5e20; }
        .delete { background: #d32f2f; color: white; }
        
        /* UPDATED HIGHLIGHT STYLE */
        .highlight { 
            background: transparent; /* Removed yellow background */
            color: #2e7d32;           /* Green font color */
            font-weight: bold; 
            text-decoration: underline; /* Added underline to make it stand out slightly */
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

    <?php if($msg): ?>
        <div id="notification" class="<?php echo $msg_type; ?>">
            <?php echo $msg; ?>
        </div>
    <?php endif; ?>

    <h2>🌿 Spice Inventory Management</h2>

    <div class="search-container">
        <input type="text" id="search" placeholder="🔍 Search spice name..." onkeyup="searchSpice()">
    </div>

    <table id="spiceTable">
        <thead>
            <tr>
                <th>ID</th>
                <th>Spice Name</th>
                <th>Price (₹/kg)</th>
                <th>Quantity (kg)</th>
                <th>Branch</th>
                <th>Action</th>
                <th>Remove</th>
            </tr>
        </thead>
        <tbody>
        <?php while($row = mysqli_fetch_assoc($result)) { ?>
            <tr>
                <form method='POST'>
                    <td>
                        <input type="hidden" name="old_id" value="<?php echo $row['id']; ?>">
                        <input type="text" name="id" class="id-input" value="<?php echo $row['id']; ?>">
                    </td>
                    <td class='spicename'><?php echo htmlspecialchars($row['spice_name']); ?></td>
                    <td>
                        ₹ <input type='text' name='price' class="price-input" value='<?php echo $row['price']; ?>'>
                    </td>
                    <td>
                        <input type='number' name='qty' class="qty-input" value='<?php echo $row['quantity']; ?>'>
                    </td>
                    <td>
                        <select name='branch'>
                            <option value="HINKAR SHOP" <?php if($row['branch']=="HINKAR SHOP") echo "selected"; ?>>HINKAR SHOP</option>
                            <option value="GODOWN1" <?php if($row['branch']=="GODOWN1") echo "selected"; ?>>GODOWN1</option>
                            <option value="GODOWN2" <?php if($row['branch']=="GODOWN2") echo "selected"; ?>>GODOWN2</option>
                        </select>
                    </td>
                    <td>
                        <button type="submit" name='update'>Update</button>
                    </td>
                    <td>
                        <button type="submit" class='delete' name='delete' onclick="return confirm('Delete this spice?')">Delete</button>
                    </td>
                </form>
            </tr>
        <?php } ?>
        </tbody>
    </table>

    <script>
    // Auto-hide the message after 3 seconds
    setTimeout(function() {
        var notify = document.getElementById('notification');
        if(notify) {
            notify.style.display = 'none';
        }
    }, 3000);

    function searchSpice() {
        let input = document.getElementById("search").value.toLowerCase();
        let table = document.getElementById("spiceTable");
        let tr = table.getElementsByTagName("tr");

        for (let i = 1; i < tr.length; i++) {
            let td = tr[i].getElementsByClassName("spicename")[0];
            if (td) {
                // We use a data attribute or original text to avoid recursive span wrapping
                if (!td.dataset.original) {
                    td.dataset.original = td.innerHTML;
                }
                
                let text = td.dataset.original;
                let plainText = text.replace(/<[^>]*>/g, ""); // strip any existing tags for comparison

                if (plainText.toLowerCase().indexOf(input) > -1) {
                    tr[i].style.display = "";
                    if(input !== "") {
                        let regex = new RegExp(input, "gi");
                        td.innerHTML = plainText.replace(regex, (match) => `<span class='highlight'>${match}</span>`);
                    } else {
                        td.innerHTML = plainText;
                    }
                } else {
                    tr[i].style.display = "none";
                }
            }
        }
    }
    </script>

</body>
</html>