<?php
// 1. SET TIMEZONE
date_default_timezone_set('Asia/Kolkata'); 

// 2. DATABASE CONNECTION
$host = "localhost"; $user = "root"; $pass = "mynewpass123"; $db = "spice_shop";
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

// --- DELETE HANDLER ---
if (isset($_GET['delete_inv'])) {
    $inv = $conn->real_escape_string($_GET['delete_inv']);
    $conn->begin_transaction();
    try {
        $conn->query("DELETE FROM bill_items WHERE invoice_no = '$inv'");
        $conn->query("DELETE FROM bills WHERE invoice_no = '$inv'");
        $conn->commit();
        header("Location: dashboard.php?msg=deleted"); exit();
    } catch (Exception $e) { $conn->rollback(); }
}

// 3. FETCH DATA
$today = date('Y-m-d');

// Today's Count from 'bills' table
$count_query = "SELECT COUNT(*) as t FROM bills WHERE DATE(created_at) = '$today'";
$today_count = $conn->query($count_query)->fetch_assoc()['t'] ?? 0;

// Today's Revenue from 'bills' table
$rev_query = "SELECT SUM(total_amount) as t FROM bills WHERE DATE(created_at) = '$today'";
$today_rev = $conn->query($rev_query)->fetch_assoc()['t'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hinkar Traders | Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary: #10b981; --dark: #0f172a; --slate: #64748b; --bg: #f1f5f9; --white: #ffffff; --border: #e2e8f0; }
        body { font-family: 'Segoe UI', sans-serif; background: var(--bg); margin: 0; display: flex; height: 100vh; color: var(--dark); overflow: hidden; }
        
        .sidebar { width: 260px; background: var(--dark); color: white; display: flex; flex-direction: column; padding: 25px; box-sizing: border-box; }
        .nav-link { display: flex; align-items: center; gap: 12px; padding: 12px 15px; color: #94a3b8; text-decoration: none; border-radius: 10px; margin-bottom: 8px; font-weight: 600; transition: 0.3s; }
        .nav-link:hover, .nav-link.active { background: #1e293b; color: var(--primary); }
        .nav-link.active { background: var(--primary); color: white; }

        .main { flex: 1; overflow-y: auto; padding: 40px; box-sizing: border-box; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 35px; }
        
        /* Stats Grid - Now 2 Columns */
        .stats-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 25px; margin-bottom: 35px; }
        .stat-card { background: var(--white); padding: 30px; border-radius: 20px; border: 1px solid var(--border); display: flex; align-items: center; gap: 20px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); }
        .stat-icon { width: 60px; height: 60px; border-radius: 15px; display: flex; align-items: center; justify-content: center; font-size: 24px; }

        .content-box { background: var(--white); border-radius: 20px; border: 1px solid var(--border); padding: 30px; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.05); }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; color: var(--slate); font-size: 11px; text-transform: uppercase; padding: 15px; border-bottom: 1px solid var(--border); }
        td { padding: 18px 15px; border-bottom: 1px solid #f8fafc; font-size: 14px; }
        
        .btn-new { background: var(--primary); color: white; text-decoration: none; padding: 12px 24px; border-radius: 12px; font-weight: 700; transition: 0.3s; }
    </style>
</head>
<body>

    <aside class="sidebar">
        <div style="display:flex; align-items:center; gap:10px; margin-bottom:40px;">
            <i class="fas fa-leaf fa-2x" style="color: var(--primary);"></i>
            <h2 style="margin:0; font-size: 18px;">Hinkar Traders</h2>
        </div>
        <nav class="nav-group">
            <a href="dashboard.php" class="nav-link active"><i class="fas fa-chart-line"></i> Dashboard</a>
            <a href="add_spice.php" class="nav-link"><i class="fas fa-circle-plus"></i> Add Spice</a>
            <a href="update_stock.php" class="nav-link"><i class="fas fa-pen-to-square"></i> Update Stock</a>
            <a href="view_stock.php" class="nav-link"><i class="fas fa-warehouse"></i> Inventory</a>
            <a href="view_bills.php" class="nav-link"><i class="fas fa-history"></i> Sales History</a>
        </nav>
    </aside>

    <main class="main">
        <header class="header">
            <div>
                <h1 style="margin:0; font-weight: 800; font-size: 28px;">Daily Summary</h1>
                <p style="margin:0; color: var(--slate);"><?php echo date('l, d M Y'); ?></p>
            </div>
            <a href="new_order.php" class="btn-new"><i class="fas fa-plus"></i> NEW INVOICE</a>
        </header>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon" style="background: #ecfdf5; color: #10b981;"><i class="fas fa-indian-rupee-sign"></i></div>
                <div>
                    <div style="color: var(--slate); font-size: 14px; font-weight: 600;">Today's Revenue</div>
                    <div style="font-size: 24px; font-weight: 800;">₹<?php echo number_format($today_rev, 2); ?></div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: #eff6ff; color: #3b82f6;"><i class="fas fa-receipt"></i></div>
                <div>
                    <div style="color: var(--slate); font-size: 14px; font-weight: 600;">Bills Generated</div>
                    <div style="font-size: 24px; font-weight: 800;"><?php echo $today_count; ?></div>
                </div>
            </div>
        </div>

        <div class="content-box">
            <h3 style="margin-top:0;"><i class="fas fa-list-check" style="color: var(--primary); margin-right: 10px;"></i>Recent Sales Today</h3>
            <table>
                <thead>
                    <tr>
                        <th>BILL NO</th>
                        <th>Buyer Name</th>
                        <th>Products Sold</th>
                        <th>Net Total</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $sql = "SELECT h.invoice_no, h.buyer_name, h.total_amount, 
                            GROUP_CONCAT(CONCAT(i.spice_name, ' (', i.quantity, 'kg)') SEPARATOR ', ') as item_str
                            FROM bills h
                            LEFT JOIN bill_items i ON h.invoice_no = i.invoice_no
                            WHERE DATE(h.created_at) = '$today'
                            GROUP BY h.invoice_no ORDER BY h.id DESC";
                    
                    $res = $conn->query($sql);
                    if($res && $res->num_rows > 0): 
                        while($row = $res->fetch_assoc()): ?>
                        <tr>
                            <td><strong><?php echo $row['invoice_no']; ?></strong></td>
                            <td><?php echo htmlspecialchars($row['buyer_name']); ?></td>
                            <td style="color: var(--slate); font-size: 13px;"><?php echo htmlspecialchars($row['item_str']); ?></td>
                            <td style="font-weight: 700;">₹<?php echo number_format($row['total_amount'], 2); ?></td>
                            <td style="text-align: right;">
            
                                <a href="dashboard.php?delete_inv=<?php echo $row['invoice_no']; ?>" onclick="return confirm('Delete this record?')" style="color: #cbd5e1;"><i class="fas fa-trash-can"></i></a>
                            </td>
                        </tr>
                        <?php endwhile; 
                    else: ?>
                        <tr><td colspan="5" style="text-align:center; padding: 50px; color: var(--slate);">No sales recorded today.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>     