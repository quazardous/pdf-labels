<?php
namespace Quazardous\PdfLabels;

use Quazardous\PdfLabels\UnitOfLengthConverterInterface;

/**
 * Unit of lenght converter using TCPDF logic.
 */
class TcPdfCompatibleUnitOfLengthConverter implements UnitOfLengthConverterInterface
{

    const TCPDF_DPI = 72.0;

    public function convert($length, $fromUnit, $toUnit)
    {
        if ($fromUnit instanceof HasUnitOfLengthInterface) {
            $fromUnit = $fromUnit->getUnitOfLength();
        }
        if ($toUnit instanceof HasUnitOfLengthInterface) {
            $toUnit = $toUnit->getUnitOfLength();
        }
        $fromUnit = strtolower($fromUnit);
        $toUnit = strtolower($toUnit);
        
        if ($fromUnit == $toUnit) return $length;
        
        // first convert to pt
        switch ($fromUnit) {
            case 'px':
            case 'pt':
                // nothing
                break;
            case 'mm':
                $length *= self::TCPDF_DPI / 25.4;
                break;
            case 'cm':
                $length *= self::TCPDF_DPI / 25.4 * 10;
                break;
            case 'm':
                $length *= self::TCPDF_DPI / 25.4 * 1000;
                break;
            case 'in':
                $length *= self::TCPDF_DPI;
                break;
            default:
                throw new \RuntimeException(sprintf("Cannot convert %f %s to %s", $length, $fromUnit, $toUnit));
        }
        
        // second convert from pt
        switch ($toUnit) {
            case 'px':
            case 'pt':
                // nothing
                break;
            case 'mm':
                $length /= self::TCPDF_DPI / 25.4;
                break;
            case 'cm':
                $length /= self::TCPDF_DPI / 25.4 * 10;
                break;
            case 'm':
                $length /= self::TCPDF_DPI / 25.4 * 1000;
                break;
            case 'in':
                $length /= self::TCPDF_DPI;
                break;
            default:
                throw new \RuntimeException(sprintf("Cannot convert %f %s to %s", $length, $fromUnit, $toUnit));
        }
        
        return $length;
    }
}
