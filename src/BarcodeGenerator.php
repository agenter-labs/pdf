<?php

namespace AgenterLab\PDF;

class BarcodeGenerator extends \Picqer\Barcode\BarcodeGenerator
{

    /**
     * Return a array representation of barcode.
     *
     * @param $barcode (string) code to print
     * @param $type (const) type of barcode
     * @return \Picqer\Barcode\Barcode
     */
    public function getBarcode($barcode, $type)
    {
        return $this->getBarcodeData($barcode, $type);
    }
}