<?php
// 1. DATABASE CONNECTION
$conn = new mysqli("localhost", "root", "mynewpass123", "spice_shop");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 2. DELETE LOGIC (Simplified thanks to ON DELETE CASCADE)
if (isset($_GET['delete_inv'])) {
    $inv_to_delete = $conn->real_escape_string($_GET['delete_inv']);
    $sql = "DELETE FROM bills WHERE invoice_no = '$inv_to_delete'";
    
    if ($conn->query($sql)) {
        header("Location: view_bills.php?msg=deleted");
        exit();
    } else {
        $error = "Delete failed: " . $conn->error;
    }
}

// 3. SEARCH & FETCH LOGIC
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$query = "SELECT * FROM bills"; 
if (!empty($search)) {
    $query .= " WHERE buyer_name LIKE '%$search%' OR invoice_no LIKE '%$search%' OR buyer_phone LIKE '%$search%'";
}
$query .= " ORDER BY id DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Hinkar Traders | Sales History</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary: #2E7D32; --dark: #1b3d1e; --bg: #f4f7f9; --white: #ffffff; --text: #334155; --border: #e2e8f0; --danger: #ef4444; }
        body { font-family: 'Segoe UI', sans-serif; background-color: var(--bg); margin: 0; display: flex; height: 100vh; color: var(--text); }
        .sidebar { width: 260px; background: var(--white); border-right: 1px solid var(--border); padding: 25px; display: flex; flex-direction: column; }
        .nav-link { text-decoration: none; color: #64748b; padding: 12px; border-radius: 10px; margin-bottom: 8px; display: flex; align-items: center; gap: 12px; font-weight: 600; }
        .nav-link.active { background: var(--primary); color: white; }
        .main-content { flex: 1; padding: 40px; overflow-y: auto; }
        .card { background: var(--white); border-radius: 16px; border: 1px solid var(--border); box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        .search-area { padding: 20px; display: flex; gap: 12px; background: #fafbfc; border-bottom: 1px solid var(--border); }
        .search-input { flex: 1; padding: 12px; border: 1px solid var(--border); border-radius: 8px; }
        .btn-search { padding: 10px 25px; background: var(--primary); color: white; border: none; border-radius: 8px; cursor: pointer; text-decoration: none; font-weight: 600; }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 18px; background: #fafbfc; color: #64748b; font-size: 11px; text-transform: uppercase; }
        td { padding: 18px; border-bottom: 1px solid #f1f5f9; }
        .inv-badge { background: #dcfce7; color: #166534; padding: 6px 12px; border-radius: 6px; font-weight: bold; cursor: pointer; }
        .modal { display: none; position: fixed; z-index: 100; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); backdrop-filter: blur(4px); }
        .modal-content { background: white; margin: 5% auto; padding: 30px; border-radius: 16px; width: 600px; }
    </style>
</head>
<body>

    <nav class="sidebar">
        <div style="color:var(--primary); margin-bottom:30px; display:flex; align-items:center; gap:10px;">
            <i class="fas fa-leaf" style="font-size:24px;"></i> <h2>Hinkar Traders</h2>
        </div>
        <a href="dashboard.php" class="nav-link"><i class="fas fa-chart-pie"></i> Dashboard</a>
        <a href="new_order.php" class="nav-link"><i class="fas fa-plus-circle"></i> New Invoice</a>
        <a href="view_bills.php" class="nav-link active"><i class="fas fa-history"></i> Sales History</a>
    </nav>

    <main class="main-content">
        <h1>Sales History</h1>
        
        <?php if(isset($_GET['msg'])): ?>
            <div style="background:#dcfce7; color:#166534; padding:15px; border-radius:8px; margin-bottom:20px;">Action completed successfully!</div>
        <?php endif; ?>

        <div class="card">
            <form class="search-area" method="GET">
                <input type="text" name="search" class="search-input" placeholder="Search customer or invoice..." value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit" class="btn-search">Search</button>
            </form>

            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Invoice No</th>
                        <th>Customer</th>
                        <th>Total</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo date('d M Y', strtotime($row['created_at'])); ?></td>
                        <td>
                            <span class="inv-badge" onclick="showDetails('<?php echo $row['invoice_no']; ?>')">
                                <?php echo $row['invoice_no']; ?> <i class="fas fa-eye"></i>
                            </span>
                        </td>
                        <td><?php echo $row['buyer_name']; ?></td>
                        <td>₹<?php echo number_format($row['total_amount'], 2); ?></td>
                        <td>
                           
                            <a href="view_bills.php?delete_inv=<?php echo $row['invoice_no']; ?>" 
                               onclick="return confirm('Delete this bill?')" style="color:var(--danger);"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </main>

    <div id="itemModal" class="modal">
        <div class="modal-content">
            <h3>Items for Invoice: <span id="modalInv"></span></h3>
            <hr>
            <table style="width:100%; margin-top:20px;">
                <thead>
                    <tr style="background:#f8fafc;">
                        <th style="padding:10px;">Item</th>
                        <th>Qty</th>
                        <th>Rate</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody id="modalRows"></tbody>
            </table>
            <button onclick="closeModal()" style="margin-top:20px; padding:10px 20px; cursor:pointer;">Close</button>
        </div>
    </div>

    <script>
    function showDetails(inv) {
        document.getElementById('modalInv').innerText = inv;
        const tbody = document.getElementById('modalRows');
        tbody.innerHTML = '<tr><td colspan="4">Loading...</td></tr>';
        document.getElementById('itemModal').style.display = 'block';

        fetch('get_bill_details.php?inv=' + inv)
            .then(res => res.json())
            .then(data => {
                tbody.innerHTML = '';
                if(data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="4">No items found.</td></tr>';
                } else {
                    data.forEach(item => {
                        tbody.innerHTML += `<tr>
                            <td>${item.spice_name}</td>
                            <td>${item.quantity} kg</td>
                            <td>₹${item.rate}</td>
                            <td>₹${item.subtotal}</td>
                        </tr>`;
                    });
                }
            });
    }
    function closeModal() { document.getElementById('itemModal').style.display = 'none'; }
    </script>
</body>
</html>