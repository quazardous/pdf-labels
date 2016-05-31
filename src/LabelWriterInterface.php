<?php

namespace Quazardous\PdfLabels;

use Quazardous\PdfLabels\LabelLayoutInterface;
use Quazardous\PdfLabels\UnitOfLengthConverterInterface;
use Quazardous\PdfLabels\HasUnitOfLengthInterface;

/**
 * Describe the rendering/writing interface.
 *
 */
interface LabelWriterInterface extends HasUnitOfLengthInterface
{
    /**
     * Allow the writer to acknowledge the layout.
     * This is triggered once at init().
     * @param LabelLayoutInterface $layout
     * @throws \RuntimeException
     */
    public function acknowledgeLayout(LabelLayoutInterface $layout, UnitOfLengthConverterInterface $converter);
    
    /**
     * Add a new page.
     */
    public function addPageBreak();
    
    /**
     * Add a label to the writer.
     * @param number $x
     * @param number $y
     * @param mixed $data
     */
    public function addLabel($x, $y, $data);
    
    /**
     * Get the page width.
     * @return number page width
     */
    public function getPageWidth();
    
    /**
     * Get the page height.
     * @return number page height
     */
    public function getPageHeight();
    
    /**
     * @return number top margin
     */
    public function getTopMargin();
    
    /**
     * @return number bottom margin
     */
    public function getBottomMargin();
    
    /**
     * @return number left margin
     */
    public function getLeftMargin();
    
    /**
     * @return number right margin
     */
    public function getRightMargin();
}
