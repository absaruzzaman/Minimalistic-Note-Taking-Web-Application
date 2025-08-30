<?php
require('fpdf186/fpdf.php');
include('db.php');
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $conn->prepare("SELECT title, content, category FROM notes WHERE id=? AND user_id=?");
$stmt->bind_param("ii", $id, $user_id);
$stmt->execute();
$note = $stmt->get_result()->fetch_assoc();

if(!$note){
    die("Note not found or access denied.");
}

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial','B',16);
$pdf->Cell(0,10,$note['title'],0,1);
$pdf->SetFont('Arial','',12);
$pdf->Ln(5);
if($note['category']) $pdf->Cell(0,10,"Category: ".$note['category'],0,1);
$pdf->Ln(5);
$pdf->MultiCell(0,6,$note['content']);

$filename = preg_replace('/[^A-Za-z0-9_\-]/', '_', $note['title']) . '.pdf';
$pdf->Output('D', $filename); // Forces download
