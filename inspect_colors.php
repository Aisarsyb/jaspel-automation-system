<?php
declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

$filePath = __DIR__ . '/REKAP JASPEL + PAJAK RAJAL JANUARI 2026 FIX.xlsx';
$spreadsheet = IOFactory::load($filePath);
$sheet = $spreadsheet->getSheetByName('ORGANIK RSGM') ?? $spreadsheet->getSheetByName('PROSTODONSIA');

echo "=== INSPECTING COLORS IN SHEET: " . $sheet->getTitle() . " ===" . PHP_EOL;

// Check row 6 (Table Header Row)
echo "Row 6 (Header No-Jaspel) fill type: " . $sheet->getStyle('A6')->getFill()->getFillType() . PHP_EOL;
echo "Row 6 (Header A6) RGB: " . $sheet->getStyle('A6')->getFill()->getStartColor()->getRGB() . PHP_EOL;
echo "Row 6 (Header A6) ARGB: " . $sheet->getStyle('A6')->getFill()->getStartColor()->getARGB() . PHP_EOL;

// Check row 8/9 (Total Row)
for ($r = 7; $r <= 30; $r++) {
    $valD = $sheet->getCell([4, $r])->getValue();
    if (strpos((string)$valD, 'TOTAL') !== false || strpos((string)$valD, 'PAJAK') !== false) {
        $rgbA = $sheet->getStyle("A{$r}")->getFill()->getStartColor()->getRGB();
        $rgbD = $sheet->getStyle("D{$r}")->getFill()->getStartColor()->getRGB();
        $typeD = $sheet->getStyle("D{$r}")->getFill()->getFillType();
        echo "Row {$r} ('{$valD}'): A fill={$rgbA}, D fill={$rgbD} (type={$typeD})" . PHP_EOL;
    }
}
