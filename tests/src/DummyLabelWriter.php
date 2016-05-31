<?php

namespace Quazardous\PdfLabels\Tests;

use Quazardous\PdfLabels\LabelWriterInterface;
use Quazardous\PdfLabels\LabelLayoutInterface;
use Quazardous\PdfLabels\UnitOfLengthConverterInterface;

class DummyLabelWriter implements LabelWriterInterface
{
    protected $options = [
        'unit_of_length' => 'mm',
        'page_width' => null,
        'page_height' => null,
        'top_margin' => null,
        'bottom_margin' => null,
        'left_margin' => null,
        'right_margin' => null,
    ];
    public function __construct(array $options) {
        $this->options = array_replace($this->options, $options);
    }
    
    public function acknowledgeLayout(LabelLayoutInterface $layout, UnitOfLengthConverterInterface $converter) {
       $this->options['left_margin'] = $converter->convert($layout->getLeftMargin(), $layout, $this);
       $this->options['right_margin'] = $converter->convert($layout->getRightMargin(), $layout, $this);
       $this->options['top_margin'] = $converter->convert($layout->getTopMargin(), $layout, $this);
       $this->options['bottom_margin'] = $converter->convert($layout->getBottomMargin(), $layout, $this);
    }
    
    public function getUnitOfLength()
    {
        return $this->options['unit_of_length'];
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

    public function getPageWidth()
    {
        return floatval($this->options['page_width']);
    }
    
    public function getPageHeight()
    {
        return floatval($this->options['page_height']);
    }
    
    public function addPageBreak()
    {
        
    }
    
    public function addLabel($x, $y, $data)
    {
        
    }
}