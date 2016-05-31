<?php
namespace Quazardous\PdfLabels;

use Quazardous\PdfLabels\LabelWriterInterface;
use Quazardous\PdfLabels\LabelLayoutInterface;
use Quazardous\PdfLabels\UnitOfLengthConverterInterface;

/**
 * A simple TCPDF labels writer using a callback to render the labels.
 *
 */
class TcPdfLabelWriter extends \TCPDF implements LabelWriterInterface
{
    protected $renderLabelCallback;
    
    /**
     * Set the render label callback.
     * @param callable $callback
     * 
     * callback signature : function ($x, $y, $data)
     */
    public function setRenderLabelCallback(callable $callback)
    {
        $this->renderLabelCallback = $callback;
    }
    
    public function acknowledgeLayout(LabelLayoutInterface $layout, UnitOfLengthConverterInterface $converter)
    {
        $this->SetAutoPageBreak(false);
        $this->AddPage();
        $this->SetMargins(
            $converter->convert($layout->getLeftMargin(), $layout, $this),
            $converter->convert($layout->getTopMargin(), $layout, $this),
            $converter->convert($layout->getRightMargin(), $layout, $this));
    }
    
    public function addPageBreak()
    {
        $this->AddPage();
    }
    
    public function addLabel($x, $y, $data)
    {
        if (empty($this->renderLabelCallback)) {
            throw new \RuntimeException('No render label callback');
        }
        $f = $this->renderLabelCallback;
        $f($x, $y, $data);
    }

    public function getUnitOfLength()
    {
        return $this->pdfunit;
    }

    public function getTopMargin()
    {
        return $this->tMargin;
    }

    public function getBottomMargin()
    {
        return $this->bMargin;
    }

    public function getLeftMargin()
    {
        return $this->lMargin;
    }

    public function getRightMargin()
    {
        return $this->rMargin;
    }
}