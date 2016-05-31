# pdf-labels
Yet another PDF labels class

It's slightly inspired on https://github.com/madvik/kiwi-label.

# install

    composer require quazardous/pdf-labels

# concepts

PDF Labels was designed to be easily extensible.

## Interfaces

I've split up PDF Labels in many simple components/interface. You can write your own components by implementing the requested interface.

### The engine

It's the top/shell class:
* does some validation
* cross acknowledge layout and writer
* fetches the data from the data provider component
* calculates where to put them according with the layout component
* triggers the render with the writer component

### The layout

interface: `LabelLayoutInterface`

A class implementing the layout is responsible to calculate the labels layout/grid taking in account page dimensions, label dimensions, margins, etc.

### The data provider

interface: `LabelDataProviderInterface`

Responsible of fetching label data.

### The writer

interface: `LabelWriterInterface`

Responsibe to render the labels to PDF or whatever.

## Current implementation

Current implementations provides:
* a simple engine
* 2 data providers (array or callback)
* a 'smart' layout trying to guess what is missing
* a TCPDF writer

Each components can have a different internal unit of length (mm, pt, in).

# usage

Here is a commented example.

```php
// A fluid label layout trying to auto fit 50mm x 30mm labels
// See SimpleFluidLabelLayout for more options.
// You can create your own layout with LabelLayoutInterface.
$options = [
    // the bare minimum is label dimensions
    'label_width' => 50,
    'label_height' => 30,
];
$layout = new SimpleFluidLabelLayout($options);

// Some simple labels data. Each row is passed to the render label callback.
// You can create your own data providers with LabelDataProviderInterface.
// see CallbackLabelDataProvider: a callback based data provider usefull to save memory (DB to PDF).
$labels = [
    ['Foo', 'Bar'],
    ['One', 'Two'],
    ['Isaac', 'Asimov'],
    ['Black', 'White'],
...
];

// The main class.
$engine = new LabelEngine($layout, $labels);

// create a TCPDF label writer.
$pdf = new TcPdfLabelWriter();
// add standard TCPDF attributes
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->SetAuthor('quazardous');

// set the render label callback
// the engine calculate the correct $x and $y
$pdf->setRenderLabelCallback(function ($x, $y, $data) use ($pdf) {
    $aff_border = 0;
    $pdf->SetFont("helvetica");
    $pdf->setX($x);
    $pdf->setY($y, false);
    $pdf->Cell(0 , 0, $data[0], $aff_border, 1, 'L', 0);
    $pdf->setX($x);
    $pdf->setY($y + 6, false);
    $pdf->Cell(0 , 0, $data[1], $aff_border, 1, 'L', 0);
});

// register the label writer with the engine
$engine->setWriter($pdf);

// main loop:
// fetch data row/label and for each we trigger the render label callback.
$engine->populate();

// standard TCPDF generation
$pdf->Output(__DIR__ . "/gen/example1.pdf", "F");
```


# tests

Copy and adapt the file tests/config-dist.php to tests/config.php then run:

    composer install
    phpunit

A sample PDF file is generated in `tests/gen`.