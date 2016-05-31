<?php

namespace Quazardous\PdfLabels;

/**
 * Allow objects to present a unit of length.
 *
 */
interface HasUnitOfLengthInterface
{
    /**
     * Get the unit of length
     * @return string $uol
     */
    public function getUnitOfLength();
}