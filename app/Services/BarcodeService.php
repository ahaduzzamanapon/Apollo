<?php

namespace App\Services;

use Picqer\Barcode\Types\TypeCode128;
use Picqer\Barcode\Renderers\PngRenderer;
use Picqer\Barcode\Renderers\SvgRenderer;

class BarcodeService
{
    /**
     * Generate barcode image for bill number
     *
     * @param string $billNumber The bill number to encode (e.g., ADDC-000001)
     * @return string Base64 encoded barcode image
     */
    public static function generateBarcodeImage($billNumber)
    {
        try {
            $barcode = (new TypeCode128())->getBarcode(strtoupper($billNumber));
            $renderer = new PngRenderer();
            $png = $renderer->render($barcode);

            return base64_encode($png);
        } catch (\Exception $e) {
            \Log::error('Barcode generation error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Generate barcode SVG for bill number
     *
     * @param string $billNumber The bill number to encode
     * @return string SVG barcode
     */
    public static function generateBarcodeSVG($billNumber)
    {
        try {
            $barcode = (new TypeCode128())->getBarcode(strtoupper($billNumber));
            $renderer = new SvgRenderer();
            $svg = $renderer->render($barcode);

            return $svg;
        } catch (\Exception $e) {
            \Log::error('Barcode SVG generation error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get barcode as HTML img tag
     *
     * @param string $billNumber The bill number to encode
     * @param array $attributes HTML attributes for img tag
     * @return string HTML img tag with embedded barcode
     */
    public static function getHtmlImg($billNumber, $attributes = [])
    {
        $base64 = self::generateBarcodeImage($billNumber);

        if (!$base64) {
            return '';
        }

        $attr = '';
        foreach ($attributes as $key => $value) {
            $attr .= " {$key}=\"{$value}\"";
        }

        return "<img src=\"data:image/png;base64,{$base64}\"{$attr} />";
    }

    /**
     * Get barcode as SVG string
     *
     * @param string $billNumber The bill number to encode
     * @return string SVG string
     */
    public static function getSVG($billNumber)
    {
        return self::generateBarcodeSVG($billNumber);
    }
}
