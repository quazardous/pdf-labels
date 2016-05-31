<?php

use Quazardous\PdfLabels\TcPdfCompatibleUnitOfLengthConverter;
use Quazardous\PdfLabels\CallbackLabelDataProvider;
use Quazardous\PdfLabels\ArrayLabelDataProvider;
use Quazardous\PdfLabels\SimpleFluidLabelLayout;
use Quazardous\PdfLabels\Tests\DummyLabelWriter;
use Quazardous\PdfLabels\LabelEngine;
use Quazardous\PdfLabels\TcPdfLabelWriter;

class PdfLabelsMainTest extends PHPUnit_Framework_TestCase
{
    public function testConverter()
    {
        $converter = new TcPdfCompatibleUnitOfLengthConverter();
        $this->assertEquals(25.4, $converter->convert(1, 'IN', 'MM'));
        $this->assertEquals(1.6386527777777775, $converter->convert(4645, 'pt', 'm'));
        $this->assertEquals(1, $converter->convert(1, null, null));
        return $converter;
    }
    
    public function testCallbackLabelDataProvider()
    {
        $data = [5,8,7];
        $provider = new CallbackLabelDataProvider(function () use (&$data) {
            $res = list(,$v) = each($data);
            if ($res !== false) return $v;
            return false;
        });
        
        $this->assertEquals(5, $provider->fetch());  
        $this->assertEquals(8, $provider->fetch());  
        $this->assertEquals(7, $provider->fetch());  
    }
    
    public function testArrayLabelDataProvider()
    {
        $data = [5,8,7];
        $provider = new ArrayLabelDataProvider($data);
        
        $this->assertEquals(5, $provider->fetch());
        $this->assertEquals(8, $provider->fetch());
        $this->assertEquals(7, $provider->fetch());
    }
    
    public function testSimpleFluidLabelLayout1()
    {
        // full auto
        $options = [
            'grid_rows' => null,
            'grid_cols' => null,
            'label_width' => 200,
            'label_height' => 100,
            'horizontal_margin' => null,
            'vertical_margin' => null,
            'top_margin' => null,
            'bottom_margin' => null,
            'left_margin' => null,
            'right_margin' => null,
            'horizontal_anchor' => 'left',
            'vertical_anchor' => 'top',
        ];
        $layout = new SimpleFluidLabelLayout($options);
        
        // fixed margins
        $options = [
            'page_width' => 1000,
            'page_height' => 2000,
            'top_margin' => 50,
            'bottom_margin' => 60,
            'left_margin' => 20,
            'right_margin' => 20,
        ];
        $writer = new DummyLabelWriter($options);
        
        $converter = new TcPdfCompatibleUnitOfLengthConverter();
        $layout->acknowledgeWriter($writer, $converter);
        $writer->acknowledgeLayout($layout, $converter);
        
        $this->assertEquals(4, $layout->getGridCols());
        $this->assertEquals(18, $layout->getGridRows());
        $this->assertEquals(50, $writer->getTopMargin());
        $this->assertEquals(20, $writer->getLeftMargin());
        
        $width =
            $writer->getLeftMargin()
            + $layout->getGridCols() * $layout->getLabelWidth()
            + ($layout->getGridCols() - 1) * $layout->getHorizontalMargin()
            + $writer->getRightMargin();
                
        $height =
            $writer->getTopMargin()
            + $layout->getGridRows() * $layout->getLabelHeight()
            + ($layout->getGridRows() - 1) * $layout->getVerticalMargin()
            + $writer->getBottomMargin();
        
        $this->assertEquals(1000, $width);      
        $this->assertEquals(2000, $height);
    }
    
    public function testSimpleFluidLabelLayout2()
    {
        // fixed H margin and V margin
        $options = [
            'grid_rows' => null,
            'grid_cols' => null,
            'label_width' => 200,
            'label_height' => 100,
            'horizontal_margin' => 10,
            'vertical_margin' => 5,
            'top_margin' => null,
            'bottom_margin' => null,
            'left_margin' => null,
            'right_margin' => null,
            'horizontal_anchor' => 'left',
            'vertical_anchor' => 'top',
        ];
        $layout = new SimpleFluidLabelLayout($options);
    
        // fixed margins
        $options = [
            'page_width' => 1000,
            'page_height' => 2000,
            'top_margin' => 50,
            'bottom_margin' => 60,
            'left_margin' => 20,
            'right_margin' => 20,
        ];
        $writer = new DummyLabelWriter($options);
    
        $converter = new TcPdfCompatibleUnitOfLengthConverter();
        $layout->acknowledgeWriter($writer, $converter);
        $writer->acknowledgeLayout($layout, $converter);
    
        $this->assertEquals(4, $layout->getGridCols());
        $this->assertEquals(18, $layout->getGridRows());
        $this->assertEquals(50, $writer->getTopMargin());
        $this->assertEquals(20, $writer->getLeftMargin());
    
        $width =
            $writer->getLeftMargin()
            + $layout->getGridCols() * $layout->getLabelWidth()
            + ($layout->getGridCols() - 1) * $layout->getHorizontalMargin()
            + $writer->getRightMargin();
    
        $height =
            $writer->getTopMargin()
            + $layout->getGridRows() * $layout->getLabelHeight()
            + ($layout->getGridRows() - 1) * $layout->getVerticalMargin()
            + $writer->getBottomMargin();
    
        $this->assertEquals(1000, $width);
        $this->assertEquals(2000, $height);
    }
    
    public function testSimpleFluidLabelLayout3()
    {
        // fixed H margin and V margin right/bottom anchor
        $options = [
            'grid_rows' => null,
            'grid_cols' => null,
            'label_width' => 200,
            'label_height' => 100,
            'horizontal_margin' => 5,
            'vertical_margin' => 5,
            'top_margin' => null,
            'bottom_margin' => null,
            'left_margin' => null,
            'right_margin' => null,
            'horizontal_anchor' => 'right',
            'vertical_anchor' => 'bottom',
        ];
        $layout = new SimpleFluidLabelLayout($options);
    
        // fixed margins
        $options = [
            'page_width' => 1000,
            'page_height' => 2000,
            'top_margin' => 50,
            'bottom_margin' => 60,
            'left_margin' => 20,
            'right_margin' => 20,
        ];
        $writer = new DummyLabelWriter($options);
    
        $converter = new TcPdfCompatibleUnitOfLengthConverter();
        $layout->acknowledgeWriter($writer, $converter);
        $writer->acknowledgeLayout($layout, $converter);
    
        $this->assertEquals(4, $layout->getGridCols());
        $this->assertEquals(18, $layout->getGridRows());
        $this->assertEquals(60, $writer->getBottomMargin());
        $this->assertEquals(20, $writer->getRightMargin());
    
        $width =
        $writer->getLeftMargin()
            + $layout->getGridCols() * $layout->getLabelWidth()
            + ($layout->getGridCols() - 1) * $layout->getHorizontalMargin()
            + $writer->getRightMargin();
    
        $height =
        $writer->getTopMargin()
            + $layout->getGridRows() * $layout->getLabelHeight()
            + ($layout->getGridRows() - 1) * $layout->getVerticalMargin()
            + $writer->getBottomMargin();
    
        $this->assertEquals(1000, $width);
        $this->assertEquals(2000, $height);
    }
    
    public function testSimpleFluidLabelLayout4()
    {
        // fixed H margin and V margin center/middle anchor
        $options = [
            'grid_rows' => null,
            'grid_cols' => null,
            'label_width' => 200,
            'label_height' => 100,
            'horizontal_margin' => 5,
            'vertical_margin' => 5,
            'top_margin' => null,
            'bottom_margin' => null,
            'left_margin' => null,
            'right_margin' => null,
            'horizontal_anchor' => 'center',
            'vertical_anchor' => 'middle',
        ];
        $layout = new SimpleFluidLabelLayout($options);
    
        $options = [
            'page_width' => 1000,
            'page_height' => 2000,
            'top_margin' => 50,
            'bottom_margin' => 60,
            'left_margin' => 20,
            'right_margin' => 20,
        ];
        $writer = new DummyLabelWriter($options);
    
        $converter = new TcPdfCompatibleUnitOfLengthConverter();
        $layout->acknowledgeWriter($writer, $converter);
        $writer->acknowledgeLayout($layout, $converter);
    
        $this->assertEquals(4, $layout->getGridCols());
        $this->assertEquals(18, $layout->getGridRows());
        $this->assertEquals(57.5, $writer->getTopMargin());
        $this->assertEquals(57.5, $writer->getBottomMargin());
        $this->assertEquals(92.5, $writer->getLeftMargin());
        $this->assertEquals(92.5, $writer->getRightMargin());
    
        $width =
            $writer->getLeftMargin()
            + $layout->getGridCols() * $layout->getLabelWidth()
            + ($layout->getGridCols() - 1) * $layout->getHorizontalMargin()
            + $writer->getRightMargin();
    
        $height =
            $writer->getTopMargin()
            + $layout->getGridRows() * $layout->getLabelHeight()
            + ($layout->getGridRows() - 1) * $layout->getVerticalMargin()
            + $writer->getBottomMargin();
            
        $this->assertEquals(1000, $width);
        $this->assertEquals(2000, $height);
    }
    
    public function testSimpleFluidLabelLayout5()
    {
        // fixed H margin and V margin center/middle anchor and margin from layout
        $options = [
            'grid_rows' => null,
            'grid_cols' => null,
            'label_width' => 200,
            'label_height' => 100,
            'horizontal_margin' => 5,
            'vertical_margin' => 5,
            'top_margin' => 50,
            'bottom_margin' => 100,
            'left_margin' => 60,
            'right_margin' => 60,
            'horizontal_anchor' => 'center',
            'vertical_anchor' => 'middle',
        ];
        $layout = new SimpleFluidLabelLayout($options);
    
        $options = [
            'page_width' => 1000,
            'page_height' => 2000,
            'top_margin' => 50,
            'bottom_margin' => 60,
            'left_margin' => 20,
            'right_margin' => 20,
        ];
        $writer = new DummyLabelWriter($options);
    
        $converter = new TcPdfCompatibleUnitOfLengthConverter();
        $layout->acknowledgeWriter($writer, $converter);
        $writer->acknowledgeLayout($layout, $converter);
    
        $this->assertEquals(4, $layout->getGridCols());
        $this->assertEquals(17, $layout->getGridRows());
        $this->assertEquals(110, $writer->getTopMargin());
        $this->assertEquals(110, $writer->getBottomMargin());
        $this->assertEquals(92.5, $writer->getLeftMargin());
        $this->assertEquals(92.5, $writer->getRightMargin());

        $width =
            $writer->getLeftMargin()
            + $layout->getGridCols() * $layout->getLabelWidth()
            + ($layout->getGridCols() - 1) * $layout->getHorizontalMargin()
            + $writer->getRightMargin();
    
        $height =
            $writer->getTopMargin()
            + $layout->getGridRows() * $layout->getLabelHeight()
            + ($layout->getGridRows() - 1) * $layout->getVerticalMargin()
            + $writer->getBottomMargin();
            
        $this->assertEquals(1000, $width);
        $this->assertEquals(2000, $height);
    }
    
    public function testSimpleFluidLabelLayout6()
    {
        // fixed cols and rows
        $options = [
            'grid_rows' => 9,
            'grid_cols' => 3,
            'label_width' => 200,
            'label_height' => 100,
            'horizontal_margin' => null,
            'vertical_margin' => null,
            'top_margin' => null,
            'bottom_margin' => null,
            'left_margin' => null,
            'right_margin' => null,
            'horizontal_anchor' => 'left',
            'vertical_anchor' => 'top',
        ];
        $layout = new SimpleFluidLabelLayout($options);
    
        $options = [
            'page_width' => 1000,
            'page_height' => 2000,
            'top_margin' => 50,
            'bottom_margin' => 60,
            'left_margin' => 20,
            'right_margin' => 20,
        ];
        $writer = new DummyLabelWriter($options);
    
        $converter = new TcPdfCompatibleUnitOfLengthConverter();
        $layout->acknowledgeWriter($writer, $converter);
        $writer->acknowledgeLayout($layout, $converter);
    
        $this->assertEquals(9, $layout->getGridRows());
        $this->assertEquals(3, $layout->getGridCols());
        $this->assertEquals(50, $writer->getTopMargin());
        $this->assertEquals(60, $writer->getBottomMargin());
        $this->assertEquals(20, $writer->getLeftMargin());
        $this->assertEquals(20, $writer->getRightMargin());

        $width =
            $writer->getLeftMargin()
            + $layout->getGridCols() * $layout->getLabelWidth()
            + ($layout->getGridCols() - 1) * $layout->getHorizontalMargin()
            + $writer->getRightMargin();
    
        $height =
            $writer->getTopMargin()
            + $layout->getGridRows() * $layout->getLabelHeight()
            + ($layout->getGridRows() - 1) * $layout->getVerticalMargin()
            + $writer->getBottomMargin();
            
        $this->assertEquals(1000, $width);
        $this->assertEquals(2000, $height);
    }
    
    public function testExample1()
    {
        // a fluid label layout trying to auto fit 50mm x 30mm labels
        $options = [
            'label_width' => 50,
            'label_height' => 30,
        ];
        $layout = new SimpleFluidLabelLayout($options);
        
        // some simle labels data
        // you can create data providers
        $labels = [];
        
        for ($i=0; $i<50; ++$i) {
            $labels = array_merge($labels, [
                ['Foo', 'Bar'],
                ['One', 'Two'],
                ['Isaac', 'Asimov'],
                ['Black', 'White'],
            ]);
        }
        
        $engine = new LabelEngine($layout, $labels);
        
        $pdf = new TcPdfLabelWriter();
        // add standard TCPDF attributes
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetAuthor('quazardous');
        
        // set the render label callback
        $pdf->setRenderLabelCallback(function ($x, $y, $data) use ($pdf) {
            static $i;
            ++$i;
            $aff_border = 0;
            $pdf->SetFont("helvetica");
            $pdf->setX($x);
            $pdf->setY($y, false);
            $pdf->Cell(0 , 0, $data[0], $aff_border, 1, 'L', 0);
            $pdf->setX($x);
            $pdf->setY($y + 6, false);
            $pdf->Cell(0 , 0, $data[1], $aff_border, 1, 'L', 0);
            $pdf->setX($x);
            $pdf->setY($y + 12, false);
            $pdf->Cell(0 , 0, $i, $aff_border, 1, 'L', 0);
        });
        
        // define the label writer
        $engine->setWriter($pdf);
        
        // main loop
        $engine->populate();
        
        // standard TCPDF generation
        $pdf->Output(__DIR__ . "/gen/example1.pdf", "F");
    }
}