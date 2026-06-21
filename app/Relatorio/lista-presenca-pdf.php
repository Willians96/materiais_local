<?php
require_once '../../lib/Database/Connection.php';
require_once '../Model/Turma.php';
require_once '../Model/Discente.php';
require '../../lib/fpdf/fpdf.php';

$id = $_GET['id'];

$turma     = Turma::buscaTurma($id);
$discentes = Discente::listarDiscentes($id);

$primeiro_dia = $turma[0]['inicio'];
$segundo_dia = date('Y-m-d', strtotime($primeiro_dia . ' +1 day'));
$terceiro_dia = $turma[0]['termino'];

function formatarData($data) {
    return date('d/m/Y', strtotime($data));
}

// Criação do PDF
$pdf = new FPDF('P', 'mm', 'A4'); // L = Landscape, mm = milímetros, A4 = tamanho da página
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 10);

$pdf->Cell(0, 10, 'Lista de Assinatura - Turma ID ' . $id, 0, 1, 'C');
$pdf->Ln(5);

// Cabeçalho da tabela
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(7, 10, 'N.', 1);
$pdf->Cell(20, 10, 'PT/GRAD', 1);
$pdf->Cell(15, 10, 'RE', 1);
$pdf->Cell(7, 10, 'DIG', 1);
$pdf->Cell(70, 10, 'NOME', 1);
$pdf->Cell(20, 10, 'OPM', 1);
$pdf->Cell(20, 10, formatarData($primeiro_dia), 1);
$pdf->Cell(20, 10, formatarData($segundo_dia), 1);
$pdf->Cell(20, 10, formatarData($terceiro_dia), 1);
$pdf->Ln();

// Linhas da tabela
$pdf->SetFont('Arial', '', 8);
foreach ($discentes as $i => $discente) {
    $pdf->Cell(7, 10, $i + 1, 1);
    $pdf->Cell(20, 10, $discente['pt_gr'], 1);
    $pdf->Cell(15, 10, $discente['re'], 1);
    $pdf->Cell(7, 10, $discente['dg_re'], 1);
    $pdf->Cell(70, 10, $discente['nome'], 1);
    $pdf->Cell(20, 10, $discente['opm'], 1);
    $pdf->Cell(20, 10, '', 1); // Assinatura dia 1
    $pdf->Cell(20, 10, '', 1); // Assinatura dia 2
    $pdf->Cell(20, 10, '', 1); // Assinatura dia 3
    $pdf->Ln();
}

$pdf->Output();
