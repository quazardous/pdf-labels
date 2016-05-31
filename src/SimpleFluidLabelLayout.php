<?php

namespace Quazardous\PdfLabels;
use Quazardous\PdfLabels\LabelLayoutInterface;
use Quazardous\PdfLabels\LabelWriterInterface;
use Quazardous\PdfLabels\UnitOfLengthConverterInterface;

/**
 * A fluid label layout able to guess missing parameters.
 *
 */
class SimpleFluidLabelLayout implements LabelLayoutInterface
{
    protected $options = [
        'unit_of_length' => 'mm',
        'grid_rows' => null,
        'grid_cols' => null,
        'label_width' => null,
        'label_height' => null,
        'horizontal_margin' => null,
        'vertical_margin' => null,
        'top_margin' => null,
        'bottom_margin' => null,
        'left_margin' => null,
        'right_margin' => null,
        'horizontal_anchor' => 'left', // left, right or center
        'vertical_anchor' => 'top', // top, middle or bottom
    ];
    
    public function __construct(array $options)
    {
        $this->options = array_replace($this->options, $options);
        
        if (empty($this->options['label_width']) || empty($this->options['label_height'])) {
            throw new \InvalidArgumentException('You have to provide label_width and label_height');
        }
    }
    
    public function acknowledgeWriter(LabelWriterInterface $writer, UnitOfLengthConverterInterface $converter)
    {
        // get the writer page margins by default
        if (is_null($this->options['top_margin'])) {
            $this->options['top_margin'] = $writer->getTopMargin();
        }
        if (is_null($this->options['bottom_margin'])) {
            $this->options['bottom_margin'] = $writer->getBottomMargin();
        }
        if (is_null($this->options['left_margin'])) {
            $this->options['left_margin'] = $writer->getLeftMargin();
        }
        if (is_null($this->options['right_margin'])) {
            $this->options['right_margin'] = $writer->getRightMargin();
        }

        // guess cols and H margin
        $totalUsableWidth = $writer->getPageWidth() - $this->getLeftMargin() - $this->getRightMargin();
        if (is_null($this->options['horizontal_margin'])) {
            if (is_null($this->options['grid_cols'])) {
                // guess number of cols
                $this->options['grid_cols'] = intval(floor($totalUsableWidth / $this->getLabelWidth()));
            }
            // guess H margin
            if ($this->getGridCols() > 1) {
                $this->options['horizontal_margin'] = ($totalUsableWidth - $this->getGridCols() * $this->getLabelWidth()) / ($this->getGridCols() - 1);
            } else {
                $this->options['horizontal_margin'] = 0;
            }
        } else {
            if (is_null($this->options['grid_cols'])) {
                // guess number of cols with given H margin
                $this->options['grid_cols'] = intval(floor(($totalUsableWidth + $this->getHorizontalMargin()) / ($this->getLabelWidth() + $this->getHorizontalMargin())));
            }
        }
        
        // guess rows and V margin
        $totalUsableHeight = $writer->getPageHeight() - $this->getTopMargin() - $this->getBottomMargin();
        if (is_null($this->options['vertical_margin'])) {
            if (is_null($this->options['grid_rows'])) {
                // guess number of rows
                $this->options['grid_rows'] = intval(floor($totalUsableHeight / $this->getLabelHeight()));
            }
            // guess V margin
            if ($this->getGridRows() > 1) {
                $this->options['vertical_margin'] = ($totalUsableHeight - $this->getGridRows() * $this->getLabelHeight()) / ($this->getGridRows() - 1);
            } else {
                $this->options['vertical_margin'] = 0;
            }
        } else {
            if (is_null($this->options['grid_rows'])) {
                // guess number of rows with given V margin
                $this->options['grid_rows'] = intval(floor(($totalUsableHeight + $this->getVerticalMargin()) / ($this->getLabelHeight() + $this->getVerticalMargin())));
            }
        }
        
        // handle H anchor
        if ($this->getGridCols() > 0) {
            $totalUsedWidth = $this->getLabelWidth() * $this->getGridCols() + $this->getHorizontalMargin() * ($this->getGridCols()-1);
            if ($totalUsedWidth != $totalUsableWidth) {
                switch ($this->options['horizontal_anchor']) {
                    case 'left':
                        $this->options['right_margin'] = $writer->getPageWidth() - ($this->getLeftMargin() + $totalUsedWidth);
                        break;
                    case 'center':
                        $this->options['left_margin'] = $this->options['right_margin'] = ($writer->getPageWidth() - $totalUsedWidth) / 2.0;
                        break;
                    case 'right':
                        $this->options['left_margin'] = $writer->getPageWidth() - ($this->getRightMargin() + $totalUsedWidth);
                        break;
                }
            }
        }
        
        // handle V anchor
        if ($this->getGridRows() > 0) {
            $totalUsedHeight = $this->getLabelHeight() * $this->getGridRows() + $this->getVerticalMargin() * ($this->getGridRows()-1);
            if ($totalUsedHeight != $totalUsableHeight) {
                switch ($this->options['vertical_anchor']) {
                    case 'top':
                        $this->options['bottom_margin'] = $writer->getPageHeight() - ($this->getTopMargin() + $totalUsedHeight);
                        break;
                    case 'middle':
                        $this->options['top_margin'] = $this->options['bottom_margin'] = ($writer->getPageHeight() - $totalUsedHeight) / 2.0;
                        break;
                    case 'bottom':
                        $this->options['top_margin'] = $writer->getPageHeight() - ($this->getBottomMargin() + $totalUsedHeight);
                        break;
                }
            }
        }
    }
    
    public function getUnitOfLength()
    {
        return $this->options['unit_of_length'];
    }
    
    public function getGridRows()
    {
        return floatval($this->options['grid_rows']);
    }
    
    public function getGridCols()
    {
        return floatval($this->options['grid_cols']);
    }
    
    public function getLabelWidth()
    {
        return floatval($this->options['label_width']);
    }
    
    public function getLabelHeight()
    {
        return floatval($this->options['label_height']);
    }
    
    public function getHorizontalMargin()
    {
        return floatval($this->options['horizontal_margin']);
    }
    
    public function getVerticalMargin()
    {
        return floatval($this->options['vertical_margin']);
    }

    public function getTopMargin()
    {
        return floatval($this->options['top_margin']);
    }
    
    public function getBottomMargin()
    {
        return floatval($this->options['bottom_margin']);
    }
    
    public function getLeftMargin()
    {
        return floatval($this->options['left_margin']);
    }
    
    public function getRightMargin()
    {
        return floatval($this->options['right_margin']);
    }

}
