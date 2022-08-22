<?php

namespace AgenterLab\PDF;

class PDF extends TFPDF {


    /**
     * @var array
     */
    private $_ys = [];    
    
    /**
    * @var int
    */
   private $_activePage;

   /**
   * @var float
   */
  private $_activeY;

    /**
     * @var int
     */
    protected $gridSize = 12; 

    public function __construct($orientation='P', $unit='mm', $size='A4', $gridSize = 12)
    {
        parent::__construct($orientation, $unit, $size);
        $this->gridSize = $gridSize;

    }

    public function initLine() {
        $this->_activeY = $this->y;
        $this->_ys = [$this->_activeY];
        $this->_activePage = $this->PageNo();
    }

    public function adjustLine() {

        if ($this->_activePage == $this->PageNo()) {
            $this->_ys[] = $this->GetY();
        } else {
            $this->_activeY = $this->tMargin;
            $this->_ys = [$this->y];
            $this->_activePage = $this->PageNo();
        }
        $this->SetY($this->_activeY, false);
    }

    public function endLine($margin = 0) {
        $this->SetY(max($this->_ys) + $margin);
    }

    /**
     * Available width excluding margin
     */
    public function availablePageWidth() {
        return $this->w - ($this->lMargin + $this->rMargin);
    }

    /**
     * Move to X axis
     */
    public function moveX(float $col, $padding = 0) {
        $x = $this->getColW($col);
        $this->SetX($this->lMargin + $x + $padding);
    }

    /**
     * Get column size
     */
    public function getColW(float $col) {
        return ($this->availablePageWidth() / $this->gridSize) * $col;
    }

    /**
     * Move to Y axis
     */
    public function moveY($y, $relative = false) {
        $y = ($y == 0 ? $this->tMargin : 0) + $y;
        if ($relative) {
            $y += $this->y;
        }
        $this->y = $y;
    }

    /**
     * Page Border
     */
    public function pageBorder(float $size = 0.1, array $style = []) {
        if ($style) {
            $this->SetFillColor(...$style);
        }

        $w = $this->w - ($this->lMargin + $this->rMargin);
        $h = $this->h - ($this->tMargin + $this->tMargin);

        $this->SetXY($this->lMargin, $this->tMargin);
        $this->hr();
        $this->vr($h);
        $this->SetY(-$this->tMargin);
        $this->hr();
        $this->SetXY(-$this->rMargin, $this->tMargin);
        $this->vr($h);
    }

    /**
     * Hr Line
     */
    public function hr(float $size = 0.1, float $margin = 0, float $width = 0, array $style = []) {

        if ($style) {
            $this->SetFillColor(...$style);
        }

        if ($margin > 0) {
            $this->moveY($margin, true);
        }

        $this->Cell($width,$size,'',0,1,'C', true);
    }

    /**
     * Vr Line
     */
    public function vr(float $height, float $margin = 0, float $size = 0.1, array $style = []) {

        if ($style) {
            $this->SetFillColor(...$style);
        }

        if ($margin > 0) {
            $this->addX($margin);
        }

        $this->Cell($size,$height,'',0,1,'C', true);
    }

    /**
     * Add X
     */
    public function addX(float $x)
    {
        $this->SetX($this->GetX() + $x);
    }

    /**
     * Add X
     */
    public function addY(float $x)
    {
        $this->SetX($this->GetX() + $x);
    }

     /**
     * @param \Picqer\Barcode\Barcode $barcode
     * @param $widthFactor (int) Minimum width of a single bar in user units.
     * @param $height (int) Height of barcode in user units.
     * @param $foregroundColor (string) Foreground color (in SVG format) for bar elements (background is transparent)..
     */
    public function Barcode(\Picqer\Barcode\Barcode $barcode, int $width = 0, int $height = 30, bool $showlabel = true,  array $fill = [0, 0, 0])
    {
        
       
        $widthFactor = 1;
        if ($width > 0) {
            $widthFactor = $width/96;
        }

        $x = $this->x;
        $y = 0;
        $this->SetFillColor(...$fill);
        
        foreach ($barcode->getBars() as $bar) {

            $w = round(($bar->getWidth() * $widthFactor), 3);
            $h = round(($bar->getHeight() * $height / $barcode->getHeight()), 3);
            
            if ($bar->isBar() && $w > 0) {
                $this->Rect($x, $this->y, $w, $h, 'F'); 

                $y = $h > $y ? $h : $y;
            }

            $x += $w;
        }
        
        if($showlabel) {
            $width = round(($barcode->getWidth() * $widthFactor), 3);
            $this->SetY($this->y + $y, false);
            $this->Cell($width, 5, $barcode->getBarcode(), 0,2,'C');  
        }

    }

    /**
     * QrCode
     */
    public function QrCode(
        \BaconQrCode\Encoder\QrCode $qrCode,
        int $size = 40,
        float $margin = 2,
        array $background=[255,255,255], 
        array $color=[0,0,0]
    )
    {
        $matrix = $qrCode->getMatrix();
        $matrixSize = $matrix->getWidth();

        if ($matrixSize !== $matrix->getHeight()) {
            throw new \InvalidArgumentException('Matrix must have the same width and height');
        }

        $rows = $matrix->getArray()->toArray();

        if (0 !== $matrixSize % 2) {
            $rows[] = array_fill(0, $matrixSize, 0);
        }

        $s = $size/$matrixSize;

        $ox = $this->x + $margin;
        $oy = $y = $this->y + $margin;

        $this->SetFillColor(...$background);
        $this->Rect($ox, $oy, $matrixSize, $matrixSize, 'F');
        $this->SetFillColor(...$color);
  
        for ($i = 0; $i < $matrixSize; $i += 2) {

            $upperRow = $rows[$i];
            $lowerRow = $rows[$i + 1];

            $x = $ox;
            $nl = true;
            for ($j = 0; $j < $matrixSize; ++$j) {
                $upperBit = $upperRow[$j];
                $lowerBit = $lowerRow[$j];

                if ($upperBit) {
                    $result = $lowerBit ? 'F' : 'U';
                } else {
                    $result = $lowerBit ? 'L' : 'E';
                }

                $this->drawQrBlock($result, $x, $y, $s);
                $x += ($s/2);
            }

            $y += $s;
        }
    }

    public function drawQrBlock(string $blockType, $x, $y, float $size) {

        if ($blockType == 'E') {
            return;
        }
        
        $w = $h = $size;
        if ($blockType == 'U') {
            $h = $h/2;
        } else if ($blockType == 'L') {
            $h = $h/2;
            $y += $h; 
        }

        $this->Rect($x, $y, $w/2, $h, 'F');
    }
}