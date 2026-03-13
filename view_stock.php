<?php
include('db.php'); 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Spice Stock | HINKAR TRADERS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            text-align: center;
            background-color: #f4f4f4;
            margin: 20px;
        }

        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 85%;
            margin: 0 auto 20px auto;
        }

        h2 { color: #2e7d32; margin: 0; }

        .download-btn {
            background-color: #1b5e20;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            font-size: 14px;
            transition: 0.3s;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            display: flex;
            align-items: center; gap: 8px;
        }

        .download-btn:hover { background-color: #2e7d32; transform: translateY(-2px); }

        .search-container {
            margin-bottom: 25px;
            display: flex;
            justify-content: center;
            gap: 15px;
        }

        .search-box {
            padding: 12px 20px;
            width: 250px;
            border: 1px solid #ccc;
            border-radius: 25px;
            outline: none;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        table {
            border-collapse: collapse;
            margin: auto;
            width: 85%;
            background: white;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            border-radius: 8px;
            overflow: hidden;
        }

        th {
            background-color: #2e7d32;
            color: white;
            padding: 15px;
            text-transform: uppercase;
            font-size: 14px;
        }

        td { border-bottom: 1px solid #ddd; padding: 12px; color: #333; }
        tr:hover { background-color: #f1f8e9; }

        /* --- FULL RED BOX STYLE FOR ZERO QUANTITY --- */
        .out-of-stock-cell {
            background-color: #d32f2f !important; /* Solid Red */
            color: white !important;              /* White text for contrast */
            font-weight: bold;
            text-transform: uppercase;
        }
    </style>

    <script>
    function multiSearch() {
        let spice = document.getElementById("searchSpice").value;
        let branch = document.getElementById("searchBranch").value;
        let xmlhttp = new XMLHttpRequest();

        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("tableData").innerHTML = this.responseText;
            }
        };

        xmlhttp.open("GET", "search.php?q=" + encodeURIComponent(spice) + "&b=" + encodeURIComponent(branch), true);
        xmlhttp.send();
    }
    </script>
</head>

<body>
    <body>

    <div class="header-container">
        <div style="display: flex; align-items: center; gap: 15px;">
            <a href="dashboard.php" style="text-decoration: none; display: flex; align-items: center; gap: 10px;">
                <h2 style="margin: 0;">🌿 HINKAR TRADERS</h2>
            </a>
        </div>
        
        <a href="download.php" class="download-btn"><i class="fas fa-file-csv"></i> Download CSV</a>
    </div>

    <div class="search-container">
        <input type="text" id="searchSpice" class="search-box" onkeyup="multiSearch()" placeholder="🔍 Search Spice Name...">
        <input type="text" id="searchBranch" class="search-box" onkeyup="multiSearch()" placeholder="📍 Search Branch Location...">
    </div>



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
        <tbody id="tableData">
            <?php
            // Updated Query: ORDER BY id ASC
            $sql = "SELECT * FROM spices ORDER BY id ASC";
            $result = mysqli_query($conn, $sql);
            
            if (mysqli_num_rows($result) > 0) {
                while($row = mysqli_fetch_assoc($result)) {
                    $qty = $row['quantity'];
                    
                    // Apply class to the <td> box if quantity is 0
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
                echo "<tr><td colspan='5' style='padding:20px;'>No stock available.</td></tr>";
            }
            ?>
        </tbody>
    </table>

</body>
</html>