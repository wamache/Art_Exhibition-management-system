<?php
session_start();
include '../config/db.php';
require_once '../fpdf/fpdf.php';
include '../phpqrcode/qrlib.php';

if (!isset($_SESSION['user']) || !isset($_GET['ticket_id'])) {
    die("Access denied.");
}

$ticket_id = intval($_GET['ticket_id']);
$visitor_id = $_SESSION['user']['id'];

// Fetch ticket info
$stmt = $conn->prepare("
    SELECT t.id, t.ticket_type, e.title AS exhibition_title, u.name AS visitor_name
    FROM tickets t
    JOIN exhibitions e ON t.exhibition_id = e.id
    JOIN users u ON t.visitor_id = u.id
    WHERE t.id = ? AND t.visitor_id = ?
");
$stmt->bind_param("ii", $ticket_id, $visitor_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Ticket not found.");
}

$ticket = $result->fetch_assoc();

// ------------------ Generate QR Code ------------------
$qrData = "Ticket ID: {$ticket['id']}\nName: {$ticket['visitor_name']}\nExhibition: {$ticket['exhibition_title']}\nType: {$ticket['ticket_type']}";
$tempDir = sys_get_temp_dir();
$qrFile = $tempDir . "/qrcode_" . $ticket['id'] . ".png";
QRcode::png($qrData, $qrFile, QR_ECLEVEL_L, 4);

// ------------------ Generate PDF ------------------
$pdf = new FPDF();
$pdf->AddPage();

// Header
$pdf->SetFont('Arial', 'B', 20);
$pdf->SetTextColor(40, 40, 40);
$pdf->Cell(0, 15, '🎟 Art Exhibition Ticket', 0, 1, 'C');

$pdf->SetFont('Arial', '', 12);
$pdf->SetTextColor(80, 80, 80);
$pdf->Ln(8);

// Ticket Info
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 10, "Ticket ID:      {$ticket['id']}", 0, 1);
$pdf->Cell(0, 10, "Name:           {$ticket['visitor_name']}", 0, 1);
$pdf->Cell(0, 10, "Exhibition:     {$ticket['exhibition_title']}", 0, 1);
$pdf->Cell(0, 10, "Ticket Type:    {$ticket['ticket_type']}", 0, 1);

// QR Code
$pdf->Ln(10);
$pdf->SetFont('Arial', 'I', 10);
$pdf->Cell(0, 10, 'Scan the QR code at the event entrance:', 0, 1);
$pdf->Image($qrFile, $pdf->GetX(), $pdf->GetY(), 50);

// Cleanup QR code file
unlink($qrFile);

// Output PDF for download
$pdf->Output("D", "ticket_{$ticket['id']}.pdf");
exit;
