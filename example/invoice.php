<?php

$dir = dirname(__FILE__);
include $dir.'/../vendor/autoload.php';

class PDF extends \AgenterLab\PDF\PDF {


    function Footer()
    {
        // Position at 1.5 cm from bottom
        $this->SetY(-11);
        // ubuntu italic 8
        $this->SetFont('ubuntu','I',8);
        // Page number
        $this->Cell(0,10,'Page '.$this->PageNo(),0,0,'C');

        $this->pageBorder();
    }

    
    /**
     * Line Item
     */
    public function lineItem($line, bool $taxEnabled = false) {
        $descWidth = $taxEnabled ? 4 : 7;
        $priceWidth = $taxEnabled ? 1 : 2;
        
        $this->initLine();

        $this->MultiCell($this->getColW($descWidth),4, $line['name']);
        $this->adjustLine();
        $this->moveX($descWidth);
        $this->Cell($this->getColW(1), 6,$line['quantity'], 0, 0, 'R');
        $this->MultiCell($this->getColW($priceWidth),4, $line['price'], 0, 'R');
        $this->adjustLine();

        if ($taxEnabled) {
            $this->moveX(6);
            $this->Cell($this->getColW(1), 6, '19%', 0, 0, 'C');
            $this->MultiCell($this->getColW(1), 6, '132.58', 0, 'R');
            $this->adjustLine();
            $this->moveX(8);
            $this->Cell($this->getColW(1), 6, '19%', 0, 0, 'C');
            $this->MultiCell($this->getColW(1), 6, '132.58', 0, 'R');
            $this->adjustLine();
            $this->moveX(10);
        }

        $this->moveX(10);
        $this->MultiCell($this->getColW(2),4, $line['total'], 0, 'R');

        $this->endLine(2);
    }
}
$itemSize = 35;
$billingAddress = [
    'Agenter Technologies PRivate limited Agenter Technologies Private limited',
    'Nadapuram',
    'Kakkam velli',
    '76355342 Kerala',
    'India',
    '18AABCU9603R1ZM'
];

$shippingAddress = [
    'Agenter Technologies PRivate limited Agenter Technologies Private limited',
    'Nadapuram',
    'Kakkam velli',
    '76355342 Kerala',
    'India',
    '18AABCU9603R1ZM'
];

$taxEnabled = true;

$items = [];
for ($i = 1; $i <= $itemSize; $i++) {
    $items[] = [
        'name' => 'Agenter Technologies PRivate limited Agenter ' . $i,
        'description' => 'Teak wooden ' . $i,
        'quantity' => $i,
        'price' => '$13,750.00',
        'total' => '₹1,3850.85',
        'taxes' => [
            [
                'name' => 'CGST',
                'rate' => '6%',
                'amount' => '$188.65'
            ],
            [
                'name' => 'SGST',
                'rate' => '6%',
                'amount' => '$188.65'
            ]
        ]
    ];
}

$taxCategories = ['CGST', 'SGST'];

$pdf = new PDF();
$pdf->AddFont('ubuntu', '', 'Ubuntu-Regular.ttf', true);
$pdf->AddFont('ubuntu','I', 'Ubuntu-Italic.ttf', true);
$pdf->AddFont('ubuntu','B', 'Ubuntu-Bold.ttf', true);
$pdf->AddFont('ubuntu','BI', 'Ubuntu-BoldItalic.ttf', true);
$pdf->AddPage();

// Header
$pdf->SetMargins(4,4);
$pdf->SetFont('ubuntu');

$pdf->moveX(0);
$pdf->Image($dir . '/logo.png', null, 2, $pdf->getColW(3));
$pdf->moveX(3);
$pdf->moveY(0);
$pdf->MultiCell($pdf->getColW(6), 5, implode("\n", [
    'Agenter Technologies Pvt.Ltd',
    'Kerala',
    'India'
]), 0, 2);

$pdf->SetFont('ubuntu', 'B');
$pdf->moveX(3);
$pdf->Cell(0,5,'32AABCU9603R1ZW');

$pdf->moveY(0);
$pdf->SetFontSize(24);
$pdf->Cell(0,20, ($taxEnabled ? 'TAX ' : '') .  'INVOICE', 0, 1, 'R');
$pdf->hr(0.1, 2);
// END Header


// INFO
$pdf->moveY(2, true);
$y = $pdf->GetY();
$pdf->SetFontSize(12);

$pdf->Cell(0,6,'Invoice Number:', 0, 2);
$pdf->Cell(0,6,'Invoice Date:', 0, 2);
$pdf->Cell(0,6,'Tax Number:', 0, 2);
$pdf->Cell(0,6,'Payment Terms:', 0, 2);
$pdf->Cell(0,6,'Payment Due:', 0, 2);

$pdf->SetY($y);
$pdf->moveX(3);
$pdf->SetFont('ubuntu');
$pdf->Cell(0,6,'INV-00003', 0, 2);
$pdf->Cell(0,6,'17 Mar 2022', 0, 2);
$pdf->Cell(0,6,'32AABCU9603R1ZW', 0, 2);
$pdf->Cell(0,6,'Due on receipt', 0, 2);
$pdf->Cell(0,6,'17 Mar 2022', 0, 2);

// Border
$y1 = $pdf->GetY();
$pdf->SetY($y);
$pdf->moveX(6);
$pdf->Cell(0,6,'Place of supply:', 0, 0);
$pdf->moveX(9);
$pdf->Cell(0,6,'Kerala (KL)', 0, 1);
$pdf->SetY($y - 2);

$pdf->vr($y + 4, $pdf->getColW(6));
$pdf->moveY(-2, true);
$pdf->hr(0.1, 2);
// END INFO

// ADDRESS
$pdf->SetFillColor(201, 197, 197);
$y = $pdf->GetY();
$pdf->SetFont('ubuntu', 'B');
$pdf->Cell($pdf->getColW(6), 8,'Bill To',0,2,'L', true);
$pdf->Cell(0,6,'Nadapuram', 0, 2);
$pdf->SetFont('ubuntu');
$pdf->MultiCell($pdf->getColW(6),5, implode("\n", $billingAddress), 0, 2);

$pdf->moveX(6);
$pdf->SetY($y, false);
$pdf->SetFont('ubuntu', 'B');
$pdf->Cell($pdf->getColW(6), 8,'Shipp To',0,2,'L', true);
$pdf->Cell(0,6,'Nadapuram', 0, 2);
$pdf->SetFont('ubuntu');
$pdf->MultiCell($pdf->getColW(6),5, implode("\n", $shippingAddress), 0, 2);

$y2 = $pdf->GetY();
$pdf->SetY($y, false);
$pdf->SetFillColor(0, 0, 0);
$pdf->vr(max($y1, $y2) - $y + 2, $pdf->getColW(6));
$pdf->moveY(-2, true);
$pdf->hr(0.1, 2);
// END ADDRESS

// LINE ITEM
$pdf->SetFillColor(201, 197, 197);
$y = $pdf->GetY();
$pdf->Cell($pdf->availablePageWidth(), 12,'',0,0,'C', true);

$pdf->moveX(0);
$descWidth = $taxEnabled ? 4 : 7;
$priceWidth = $taxEnabled ? 1 : 2;
$pdf->Cell($pdf->getColW($descWidth),12,'Item Name & Description', 'R');
$pdf->Cell($pdf->getColW(1),12,'Qty', 'R', 0, 'R');
$pdf->Cell($pdf->getColW($priceWidth),12,'Price', 'R', 0, 'R');
if ($taxEnabled) {
    foreach($taxCategories as $tc) {
        $pdf->SetFontSize(10);
        $pdf->Cell($pdf->getColW(2),6,$tc, 'R', 2, 'C');
        $pdf->SetFontSize(8);
        $pdf->Cell($pdf->getColW(1),6, '%', 'R', 0, 'C');
        $pdf->Cell($pdf->getColW(1),6, 'Amt', 'R', 0, 'C');
        $pdf->SetY($y, false);
    }
    $pdf->moveX(6);
    $pdf->hr(0.1, 6, $pdf->getColW(4), [0,0,0]);
    $pdf->SetY($y, false);
    $pdf->moveX(10);
}
$pdf->SetFontSize(12);
$pdf->Cell($pdf->getColW(2),12,'Amount', 0, 1, 'R');

$pdf->SetFontSize(10);
foreach ($items as $item) {
    $pdf->lineItem($item, $taxEnabled);
}
$pdf->hr(0.1);

$y = $pdf->GetY();
$pdf->moveY(10, true);
$pdf->SetFontSize(12);
$pdf->Cell(0,4,'Total in words', 0, 2);
$pdf->SetFont('ubuntu', 'B');
$pdf->MultiCell($pdf->getColW(6),6, 'One thousand one hundred twenty rupees only', 0, 2);
$pdf->SetFont('ubuntu', 'I');
$pdf->Ln(2);
$pdf->MultiCell($pdf->getColW(6),6, 'Terms of use', 0, 2);
$pdf->MultiCell($pdf->getColW(6),6, 'Values of different types will be compared using the standard comparison rules. For instance, a non-numeric string will be compared to an int as though it were 0, but multiple non-numeric string values will be compared alphanumerically. The actual value returned will be of the original type with no conversion applied. ', 0, 2);
$y1 = $pdf->GetY();

$pdf->SetY($y);
$pdf->moveX(8);
$pdf->SetFont('ubuntu', 'B');
$pdf->Cell(0,8,'Sub Toal:', 0, 2);
$pdf->Cell(0,8,'Toal:', 0, 2);
$pdf->Cell(0,8,'Balance Due:', 0, 2);

$pdf->SetY($y);
$pdf->SetFont('ubuntu');
$pdf->Cell(0,8,'1,000.00', 0, 2, 'R');
$pdf->Cell(0,8,'1,120.00', 0, 2, 'R');
$pdf->Cell(0,8,'300.00', 0, 2, 'R');

$pdf->Ln(3);
$pdf->Rect($pdf->getColW(9), $pdf->GetY(), $pdf->getColW(3), 15,'D');

$pdf->Image($dir . '/signature.png', $pdf->moveX(9), $pdf->GetY(), $pdf->getColW(2.5));

$pdf->moveY(15, true);
$pdf->moveX(8);
$pdf->Cell($pdf->getColW(4),4,'Authorized Signature', 0, 2, 'C');

$y1 = $y1 > $pdf->GetY() ? $y1 : $pdf->GetY();

$pdf->SetY($y);
$pdf->moveX(7);
$pdf->vr($y1 - $y + 2);
$pdf->hr(0.1);


// END LINE ITEM

$pdf->Output();
