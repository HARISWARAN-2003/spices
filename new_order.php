<?php
$conn = new mysqli("localhost", "root", "mynewpass123", "spice_shop");
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

$spice_list = $conn->query("SELECT spice_name FROM spices ORDER BY spice_name ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hinkar Traders | Premium Billing</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { 
            --primary: #10b981; 
            --primary-dark: #059669;
            --dark: #0f172a;
            --bg: #f8fafc; 
            --card: #ffffff;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --border: #e2e8f0;
            --accent: #8b5cf6;
        }

        body { font-family: 'Inter', 'Segoe UI', sans-serif; background: var(--bg); margin: 0; display: flex; height: 100vh; color: var(--text-main); overflow: hidden; }

        /* Sidebar Nav */
        .nav-slim { width: 80px; background: var(--dark); display: flex; flex-direction: column; align-items: center; padding: 30px 0; gap: 30px; border-right: 1px solid var(--border); }
        .nav-slim i { color: #475569; font-size: 22px; cursor: pointer; transition: 0.3s; }
        .nav-slim a { text-decoration: none; }
        .nav-slim i:hover, .nav-slim .active i { color: var(--primary); transform: scale(1.1); }

        .main-layout { flex: 1; display: flex; overflow: hidden; }

        /* Billing Area */
        .entry-area { flex: 1; padding: 30px 40px; overflow-y: auto; }
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; }
        .page-title { margin: 0; font-size: 24px; font-weight: 800; color: var(--dark); letter-spacing: -0.5px; }
        
        .data-card { background: var(--card); border-radius: 20px; border: 1px solid var(--border); box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05); overflow: hidden; }
        
        table { width: 100%; border-collapse: collapse; }
        th { padding: 18px; text-align: left; background: #f1f5f9; font-size: 12px; font-weight: 700; text-transform: uppercase; color: var(--text-muted); border-bottom: 1px solid var(--border); }
        td { padding: 12px 18px; border-bottom: 1px solid #f8fafc; }

        input { border: 1.5px solid var(--border); padding: 10px 14px; border-radius: 10px; font-size: 14px; width: 100%; transition: 0.2s; background: #fcfdfe; }
        input:focus { outline: none; border-color: var(--primary); background: #fff; box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1); }

        .status-pill { font-size: 11px; font-weight: 700; padding: 4px 10px; border-radius: 8px; display: inline-flex; align-items: center; gap: 5px; }
        .ok { background: #ecfdf5; color: #065f46; border: 1px solid #d1fae5; }
        .err { background: #fef2f2; color: #991b1b; border: 1px solid #fee2e2; }

        .btn-row { background: #fff; border: 2px dashed var(--border); color: var(--text-muted); padding: 15px; border-radius: 15px; width: 100%; margin-top: 20px; cursor: pointer; font-weight: 600; transition: 0.3s; }
        .btn-row:hover { border-color: var(--primary); color: var(--primary); background: #f0fdf4; }

        /* Right Checkout Panel */
        .checkout-area { width: 380px; background: var(--card); border-left: 1px solid var(--border); padding: 30px; display: flex; flex-direction: column; box-shadow: -10px 0 30px rgba(0,0,0,0.02); }
        .section-label { font-size: 11px; font-weight: 800; color: var(--text-muted); text-transform: uppercase; margin-bottom: 12px; display: block; letter-spacing: 1px; }

        .customer-card { background: #f8fafc; padding: 20px; border-radius: 15px; margin-bottom: 25px; border: 1px solid var(--border); }
        
        .summary-card { background: var(--dark); border-radius: 20px; padding: 25px; color: white; margin-top: auto; }
        .summary-line { display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 14px; opacity: 0.8; }
        .grand-total-row { border-top: 1px solid #334155; margin-top: 15px; padding-top: 15px; }
        .grand-total-label { font-size: 12px; text-transform: uppercase; color: var(--primary); font-weight: 800; }
        .grand-total-val { font-size: 32px; font-weight: 800; color: #fff; display: block; }

        .btn-submit { padding: 16px; border-radius: 14px; border: none; font-weight: 700; cursor: pointer; color: white; transition: 0.3s; display: flex; align-items: center; justify-content: center; gap: 10px; margin-top: 15px; font-size: 15px; }
        .btn-print { background: var(--primary); box-shadow: 0 10px 20px -5px rgba(16, 185, 129, 0.4); }
        .btn-save { background: #334155; }
        .btn-submit:hover { transform: translateY(-2px); opacity: 0.95; }

        .discount-input-group { position: relative; display: flex; align-items: center; }
        .discount-input-group i { position: absolute; left: 12px; color: var(--accent); }
        .discount-input-group input { padding-left: 35px; border-color: #ddd6fe; color: var(--accent); font-weight: 700; }
    </style>
</head>
<body>

<datalist id="spiceItems">
    <?php while($row = $spice_list->fetch_assoc()) echo "<option value='".htmlspecialchars($row['spice_name'])."'>"; ?>
</datalist>

<nav class="nav-slim">
    <div style="background: var(--primary); width: 45px; height: 45px; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 20px;">
        <i class="fas fa-leaf" style="color:white; font-size:20px;"></i>
    </div>
    <a href="dashboard.php"><i class="fas fa-grid-2"></i><i class="fas fa-chart-line"></i></a>
    <a href="view_bills.php" class="active"><i class="fas fa-receipt"></i></a>
</nav>

<form id="invoiceForm" action="process_bill.php" method="POST" class="main-layout">
    <main class="entry-area">
        <div class="page-header">
            <h1 class="page-title">Create New Invoice</h1>
            <span style="font-size: 13px; color: var(--text-muted);"><i class="far fa-calendar-alt"></i> <?php echo date('d M, Y'); ?></span>
        </div>
        
        <div class="data-card">
            <table>
                <thead>
                    <tr>
                        <th width="35%">Spice Product</th>
                        <th width="15%">Inventory</th>
                        <th width="15%">Qty (kg)</th>
                        <th width="15%">Rate/kg</th>
                        <th width="15%">Subtotal</th>
                        <th width="5%"></th>
                    </tr>
                </thead>
                <tbody id="billBody">
                    <tr class="item-row">
                        <td>
                            <input type="text" name="spice_names[]" list="spiceItems" oninput="checkMatch(this)" placeholder="Type spice name..." required>
                        </td>
                        <td>
                            <span class="status-pill ok"><i class="fas fa-check-circle"></i> <span class="avail-val">0</span></span>
                            <input type="hidden" class="hidden-stock">
                        </td>
                        <td><input type="number" name="quantities[]" class="qty-input" step="0.01" oninput="validateAllStock()" placeholder="0.00" required></td>
                        <td><input type="number" name="rates[]" class="rate" step="0.01" oninput="calcRow(this.closest('tr'))" placeholder="0.00" required></td>
                        <td><div style="font-weight:700; color:var(--dark);">₹<span class="row-total-text">0.00</span></div><input type="hidden" name="row_totals[]" class="total"></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <button type="button" class="btn-row" onclick="addRow()"><i class="fas fa-plus"></i> Press <b>F2</b> or Click to Add Item</button>
    </main>

    <aside class="checkout-area">
        <span class="section-label">Customer Details</span>
        <div class="customer-card">
            <div style="margin-bottom: 12px;">
                <input type="text" name="buyer_name" placeholder="Customer Name" required>
            </div>
            <input type="text" name="buyer_phone" placeholder="Phone Number" maxlength="10">
        </div>

        <span class="section-label">Discounts & Offers</span>
        <div class="discount-input-group">
            <i class="fas fa-tag"></i>
            <input type="number" name="discount_amount" id="discountInput" step="0.01" value="0" oninput="updateGrand()" placeholder="Discount Amount (₹)">
        </div>

        <div class="summary-card">
            <div class="summary-line">
                <span>Sub-Total</span>
                <span>₹<span id="subTotalText">0.00</span></span>
            </div>
            <div class="summary-line" style="color: #a78bfa;">
                <span>Discount</span>
                <span>- ₹<span id="discountText">0.00</span></span>
            </div>
            <div class="grand-total-row">
                <span class="grand-total-label">Total Payable</span>
                <span class="grand-total-val">₹<span id="grandText">0.00</span></span>
            </div>
            <input type="hidden" name="final_total" id="finalInput">
        </div>

        <button type="submit" name="action" value="save_only" class="btn-submit btn-save">
            <i class="fas fa-hdd"></i> SAVE
        </button>
    
    </aside>
</form>

<script>
function addRow() {
    const tr = document.createElement('tr');
    tr.className = 'item-row';
    tr.innerHTML = `
        <td><input type="text" name="spice_names[]" list="spiceItems" oninput="checkMatch(this)" placeholder="Type spice name..." required></td>
        <td><span class="status-pill ok"><i class="fas fa-check-circle"></i> <span class="avail-val">0</span></span><input type="hidden" class="hidden-stock"></td>
        <td><input type="number" name="quantities[]" class="qty-input" step="0.01" oninput="validateAllStock()" placeholder="0.00" required></td>
        <td><input type="number" name="rates[]" class="rate" step="0.01" oninput="calcRow(this.closest('tr'))" placeholder="0.00" required></td>
        <td><div style="font-weight:700; color:var(--dark);">₹<span class="row-total-text">0.00</span></div><input type="hidden" name="row_totals[]" class="total"></td>
        <td><i class="fas fa-trash" style="color:#cbd5e1; cursor:pointer;" onclick="this.closest('tr').remove(); updateGrand();"></i></td>
    `;
    document.getElementById('billBody').appendChild(tr);
}

function checkMatch(input) {
    const options = document.getElementById('spiceItems').options;
    for (let i = 0; i < options.length; i++) {
        if (input.value === options[i].value) {
            fetch(`get_spice_details.php?name=${encodeURIComponent(input.value)}`)
                .then(res => res.json())
                .then(data => {
                    const row = input.closest('tr');
                    row.querySelector('.rate').value = data.price || 0;
                    row.querySelector('.hidden-stock').value = data.stock_quantity || 0;
                    validateAllStock();
                });
            break;
        }
    }
}

function validateAllStock() {
    const rows = document.querySelectorAll('.item-row');
    const totals = {};
    rows.forEach(r => {
        const name = r.querySelector('input[name="spice_names[]"]').value;
        const q = parseFloat(r.querySelector('.qty-input').value) || 0;
        if(name) totals[name] = (totals[name] || 0) + q;
    });

    rows.forEach(r => {
        const name = r.querySelector('input[name="spice_names[]"]').value;
        const stock = parseFloat(r.querySelector('.hidden-stock').value) || 0;
        const pill = r.querySelector('.status-pill');
        if(name) {
            const remains = stock - totals[name];
            pill.innerHTML = remains < 0 ? `<i class="fas fa-exclamation-triangle"></i> Short: ${Math.abs(remains)}` : `<i class="fas fa-check-circle"></i> Avail: ${remains.toFixed(2)}`;
            pill.className = remains < 0 ? "status-pill err" : "status-pill ok";
        }
        calcRow(r);
    });
}

function calcRow(row) {
    const q = parseFloat(row.querySelector('.qty-input').value) || 0;
    const r = parseFloat(row.querySelector('.rate').value) || 0;
    const total = q * r;
    row.querySelector('.total').value = total.toFixed(2);
    row.querySelector('.row-total-text').innerText = total.toFixed(2);
    updateGrand();
}

function updateGrand() {
    let subtotal = 0;
    document.querySelectorAll('.total').forEach(t => subtotal += parseFloat(t.value || 0));
    
    const discount = parseFloat(document.getElementById('discountInput').value) || 0;
    const final = subtotal - discount;

    document.getElementById('subTotalText').innerText = subtotal.toFixed(2);
    document.getElementById('discountText').innerText = discount.toFixed(2);
    document.getElementById('grandText').innerText = final.toLocaleString('en-IN', {minimumFractionDigits: 2});
    document.getElementById('finalInput').value = final.toFixed(2);
}

document.addEventListener('keydown', (e) => { if(e.key === 'F2') addRow(); });
</script>

</body>
</html>