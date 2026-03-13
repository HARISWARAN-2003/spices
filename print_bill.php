<?php
require('fpdf.php'); // Ensure this file is in your folder!

// 1. DATABASE CONNECTION
$conn = new mysqli("localhost", "root", "mynewpass123", "spice_shop");
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

// 2. GET INVOICE ID
$inv = isset($_GET['inv']) ? $conn->real_escape_string($_GET['inv']) : die('Invoice not found');

// 3. FETCH HEADER DATA
$header_res = $conn->query("SELECT * FROM bills WHERE invoice_no = '$inv'");
$header = $header_res->fetch_assoc();

if (!$header) { die("Invoice record does not exist."); }

// 4. GENERATE PDF
class PDF extends FPDF {
    function Header() {
        $this->SetFont('Arial', 'B', 20);
        $this->SetTextColor(46, 125, 50); // Hinkar Green
        $this->Cell(0, 10, 'HINKAR TRADERS', 0, 1, 'C');
        $this->SetFont('Arial', '', 10);
        $this->SetTextColor(100, 100, 100);
        $this->Cell(0, 5, 'Quality Spices & Herbs | Address Line, City, State', 0, 1, 'C');
        $this->Cell(0, 5, 'Contact: +91 00000 00000 | Email: sales@hinkar.com', 0, 1, 'C');
        $this->Ln(10);
        $this->Line(10, 42, 200, 42); // Horizontal line
    }

    function Footer() {
        $this->SetY(-25);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 5, 'Thank you for your business!', 0, 1, 'C');
        $this->Cell(0, 5, 'This is a computer generated e-bill.', 0, 0, 'C');
    }
}

$pdf = new PDF();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 11);

// Bill Info
$pdf->SetTextColor(0, 0, 0);
$pdf->Cell(130, 8, 'Bill To: ' . strtoupper($header['buyer_name']), 0, 0);
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(60, 8, 'INVOICE: #' . $header['invoice_no'], 0, 1);

$pdf->SetFont('Arial', '', 11);
$pdf->Cell(130, 8, 'Phone: ' . $header['buyer_phone'], 0, 0);
$pdf->Cell(60, 8, 'Date: ' . date('d-m-Y', strtotime($header['created_at'])), 0, 1);

$pdf->Ln(10);

// Table Header
$pdf->SetFillColor(46, 125, 50);
$pdf->SetTextColor(255, 255, 255);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(100, 10, ' Spice Name', 1, 0, 'L', true);
$pdf->Cell(30, 10, 'Qty (kg)', 1, 0, 'C', true);
$pdf->Cell(30, 10, 'Rate', 1, 0, 'C', true);
$pdf->Cell(30, 10, 'Total', 1, 1, 'C', true);

// Table Rows
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('Arial', '', 11);

$items_res = $conn->query("SELECT * FROM bill_items WHERE invoice_no = '$inv'");
while ($item = $items_res->fetch_assoc()) {
    $pdf->Cell(100, 10, ' ' . $item['spice_name'], 1, 0, 'L');
    $pdf->Cell(30, 10, $item['quantity'], 1, 0, 'C');
    $pdf->Cell(30, 10, $item['rate'], 1, 0, 'C');
    $pdf->Cell(30, 10, number_format($item['subtotal'], 2), 1, 1, 'R');
}

// Grand Total
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(160, 10, 'GRAND TOTAL ', 1, 0, 'R');
$pdf->SetTextColor(46, 125, 50);
$pdf->Cell(30, 10, 'Rs ' . number_format($header['total_amount'], 2), 1, 1, 'R');

// Output
$pdf->Output('I', 'Hinkar_Bill_' . $inv . '.pdf'); // 'I' opens in browser
?>