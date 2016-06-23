<?php

namespace Quazardous\PdfLabels;

use Quazardous\PdfLabels\LabelLayoutInterface;
use Quazardous\PdfLabels\LabelWriterInterface;
use Quazardous\PdfLabels\LabelDataProviderInterface;
use Quazardous\PdfLabels\UnitOfLengthConverterInterface;
use Quazardous\PdfLabels\HasUnitOfLengthInterface;
use Quazardous\PdfLabels\TcPdfCompatibleUnitOfLengthConverter;

/**
 * The main class.
 *
 */
class LabelEngine implements HasUnitOfLengthInterface
{
    /** @var \Quazardous\PdfLabels\LabelLayoutInterface */
    protected $layout;
    /** @var \Quazardous\PdfLabels\LabelWriterInterface */
    protected $writer;
    /** @var \Quazardous\PdfLabels\LabelDataProviderInterface */
    protected $data;
    /** @var \Quazardous\PdfLabels\UnitOfLengthConverterInterface */
    protected $converter;
    
    public function __construct($layout, $data = null, LabelWriterInterface $writer = null, UnitOfLengthConverterInterface $converter = null)
    {
        if ($layout instanceof LabelLayoutInterface) {
            $this->layout = $layout;
        } elseif (is_array($layout)) {
            $this->layout = new SimpleArrayLabelLayout($options);
        } else {
            throw new \InvalidArgumentException('Incorrect layout');
        }
        
        if (!empty($data)) {
            $this->setDataProvider($data);
        }
        
        if (!empty($writer)) {
            $this->setWriter($writer);
        }
    }
    
    /**
     * Get the layout.
     * @return \Quazardous\PdfLabels\LabelLayoutInterface
     */
    public function getLayout()
    {
        return $this->layout;
    }
    
    /**
     * Set the data provider.
     * @param array|callable|\Quazardous\PdfLabels\LabelDataProviderInterface $data
     * @throws \InvalidArgumentException
     */
    public function setDataProvider($data)
    {
        if ($data instanceof LabelDataProviderInterface) {
            $this->data = $data;
        } elseif (is_array($data)) {
            $this->data = new ArrayLabelDataProvider($data);
        } elseif (is_callable($data)) {
            $this->data = new CallbackLabelDataProvider($data);
        } else {
            throw new \InvalidArgumentException('Incorrect data provider');
        }
    }
    
    /**
     * Get the data provider.
     * @return \Quazardous\PdfLabels\LabelDataProviderInterface
     */
    public function getDataProvider()
    {
        return $this->data;
    }
    
    /**
     * Set the writer.
     * @param LabelWriterInterface $writer
     */
    public function setWriter(LabelWriterInterface $writer)
    {
        $this->writer = $writer;
    }
    
    /**
     * Get the writer.
     * @return \Quazardous\PdfLabels\LabelWriterInterface
     */
    public function getWriter()
    {
        return $this->writer;
    }
    
    /**
     * Set the converter.
     * @param \Quazardous\PdfLabels\UnitOfLengthConverterInterface $converter
     */
    public function setConverter($converter)
    {
        $this->converter = $converter;
    }
    
    /**
     * Get the converter.
     * @return \Quazardous\PdfLabels\UnitOfLengthConverterInterface
     */
    public function getConverter()
    {
        return $this->converter;
    }
    
    /**
     * Convert unit of length.
     * @param number $length
     * @param string $fromUnit
     * @param string $toUnit
     * @throws \RuntimeException
     * @return number
     */
    protected function convert($length, $fromUnit, $toUnit = null)
    {
        if (empty($fromUnit)) {
            $fromUnit = $this->getUnitOfLength();
        }
        if (empty($toUnit)) {
            $toUnit = $this->getUnitOfLength();
        }
        return $this->getConverter()->convert($length, $fromUnit, $toUnit);
    }
    
    protected $init = false;
    /**
     * Init/check stuff.
     * @throws \RuntimeException
     */
    protected function init()
    {
        if ($this->init) return;
        $this->init = true;
        
        if (empty($this->getDataProvider())) {
            throw new \RuntimeException('No data provider');
        }
        
        if (empty($this->getWriter())) {
            throw new \RuntimeException('No writer');
        }
        
        if (empty($this->getConverter())) {
            $this->converter = new TcPdfCompatibleUnitOfLengthConverter();
        }
        
        // layout has precedence
        $this->getLayout()->acknowledgeWriter($this->getWriter(), $this->getConverter());
        $this->getWriter()->acknowledgeLayout($this->getLayout(), $this->getConverter());
        
        if (empty($this->getLayout()->getLabelWidth())
            || empty($this->getLayout()->getLabelHeight())
            || empty($this->getLayout()->getGridCols())
            || empty($this->getLayout()->getGridRows())
        ) {
            throw new \InvalidArgumentException('Incomplete layout');
        }
        if (!($this->getLayout()->getGridCols() > 0 && $this->getLayout()->getGridRows() > 0)) {
            throw new \InvalidArgumentException('Incorrect layout');
        }
    }
    
    protected $populated = false;
    /**
     * Main loop.
     * Add all labels to the writer.
     */
    public function populate()
    {
        $this->init();
        if ($this->populated) {
            // allow multiple populate() but add a page break 
            $this->getWriter()->addPageBreak();
        }
        $this->populated = true;
        $cols = $this->getLayout()->getGridCols();
        $rows = $this->getLayout()->getGridRows();
        $col = 1;
        $row = 1;
        $page = 1;
        // convert all length to internal unit
        $lm = $this->convert($this->getWriter()->getLeftMargin(), $this->getWriter());
        $tm = $this->convert($this->getWriter()->getTopMargin(), $this->getWriter());
        $gw = $this->convert($this->getLayout()->getLabelWidth() + $this->getLayout()->getHorizontalMargin(), $this->getLayout());
        $gh = $this->convert($this->getLayout()->getLabelHeight() + $this->getLayout()->getVerticalMargin(), $this->getLayout());;
        while ($data = $this->getDataProvider()->fetch()) {
            if ($col > $cols) {
                ++$row;
                $col = 1;
            }
            if ($row > $rows) {
                ++$page;
                $row = 1;
                $this->getWriter()->addPageBreak();
            }
            $x = $lm + $gw * ($col - 1);
            $y = $tm + $gh * ($row - 1);
            $this->getWriter()->addLabel(
                $this->convert($x, null, $this->getWriter()),
                $this->convert($y, null, $this->getWriter()),
                $data);
            ++$col;
        }
    }
    
    public function getUnitOfLength()
    {
        return 'mm';
    }
}