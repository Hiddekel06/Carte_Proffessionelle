<?php

namespace App\Support;

use chillerlan\QRCode\Common\EccLevel;
use chillerlan\QRCode\Data\QRMatrix;
use chillerlan\QRCode\Output\QRGdImagePNG;
use chillerlan\QRCode\Output\QRMarkupSVG;
use chillerlan\QRCode\QRCode as Generator;
use chillerlan\QRCode\QROptions;

/**
 * Génération locale des QR codes (aucun service externe).
 */
class QrCode
{
    /**
     * QR code SVG en data URI, style « points » comme sur le design Canva
     * (modules ronds, repères carrés).
     */
    public static function svgDataUri(string $data): string
    {
        $options = new QROptions([
            'outputInterface'     => QRMarkupSVG::class,
            'outputBase64'        => true,
            'eccLevel'            => EccLevel::M,
            'addQuietzone'        => false,
            'svgAddXmlHeader'     => false,
            'drawLightModules'    => false,
            'drawCircularModules' => true,
            'circleRadius'        => 0.42,
            'keepAsSquare'        => [
                QRMatrix::M_FINDER_DARK,
                QRMatrix::M_FINDER_DOT,
                QRMatrix::M_ALIGNMENT_DARK,
            ],
            'moduleValues'        => [
                QRMatrix::M_DATA_DARK => '#000000',
            ],
        ]);

        return (new Generator($options))->render($data);
    }

    /**
     * QR code PNG brut (binaire), noir sur blanc, pour téléchargement.
     */
    public static function png(string $data, int $scale = 25): string
    {
        $options = new QROptions([
            'outputInterface' => QRGdImagePNG::class,
            'outputBase64'    => false,
            'eccLevel'        => EccLevel::M,
            'scale'           => $scale,
            'quietzoneSize'   => 2,
        ]);

        return (new Generator($options))->render($data);
    }
}
