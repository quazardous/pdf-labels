<?php

namespace Quazardous\PdfLabels;

use Quazardous\PdfLabels\LabelWriterInterface;
use Quazardous\PdfLabels\UnitOfLengthConverterInterface;
use Quazardous\PdfLabels\HasUnitOfLengthInterface;

/**
 * Describe the label layout (grid, format, width, height, etc)
 *
 */
interface LabelLayoutInterface extends HasUnitOfLengthInterface
{
    /**
     * Allow the layout to acknowledge the writer.
     * This is triggered once at init().
     * @param LabelWriterInterface $layout
     * @throws \RuntimeException
     */
    public function acknowledgeWriter(LabelWriterInterface $writer, UnitOfLengthConverterInterface $converter);
    
    /**
     * @return number of rows by page
     */
    public function getGridRows();

    /**
     * @return number of columns by page
     */
    public function getGridCols();
    
    /**
     * @return number width of one label
     */
    public function getLabelWidth();
    
    /**
     * @return number height of one label
     */
    public function getLabelHeight();
    
    /**
     * @return number vertical margin between 2 rows
     */
    public function getVerticalMargin();
    
    /**
     * @return number horizontal margin between 2 cols
     */
    public function getHorizontalMargin();
    
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
