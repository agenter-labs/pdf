<?php

namespace AgenterLab\PDF;

class PDF extends FPDF {


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
    public function moveX(int $col, $padding = 0) {
        $x = $this->getColW($col);
        $this->SetX($this->lMargin + $x + $padding);
    }

    /**
     * Get column size
     */
    public function getColW(int $col) {
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
}