<?php

namespace AgenterLab\PDF;

use BaconQrCode\Common\ErrorCorrectionLevel;
use BaconQrCode\Common\Version;
use BaconQrCode\Encoder\QrCode;
use BaconQrCode\Encoder\Encoder;

class QrCodeGenerator
{

    /**
     * @var ErrorCorrectionLevel
     */
    private ErrorCorrectionLevel $ecLevel;

    public function __construct(
        private string $encoding = Encoder::DEFAULT_BYTE_MODE_ECODING,
        ?ErrorCorrectionLevel $ecLevel = null,
        private ?Version $forcedVersion = null
    )
    {
        if (null === $ecLevel) {
            $this->ecLevel = ErrorCorrectionLevel::L();
        }
    } 

    /**
     * Return a array representation of barcode.
     *
     * @param string|array $content
     * @return \BaconQrCode\Encoder\QrCode
     */
    public function get(string|array $content): QrCode
    {

        if (empty($content)) {
            throw new \InvalidArgumentException('Found empty contents');
        }

        if (is_array($content)) {
            $content = json_encode($content);
        }

        return Encoder::encode(
            $content, 
            $this->ecLevel, 
            $this->encoding, 
            $this->forcedVersion
        );
    }
}