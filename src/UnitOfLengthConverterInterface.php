<?php

namespace Quazardous\PdfLabels;

interface UnitOfLengthConverterInterface
{
    /**
     * Convert the given length.
     * @param number $length
     * @param string|\Quazardous\PdfLabels\HasUnitOfLengthInterface $fromUnit
     * @param string|\Quazardous\PdfLabels\HasUnitOfLengthInterface $toUnit
     */
    public function convert($length, $fromUnit, $toUnit);
}