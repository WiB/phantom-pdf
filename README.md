# phantom-pdf

[![Build Status](https://img.shields.io/travis/tobi812/phantom-pdf/master.svg?style=flat-square)](https://travis-ci.org/tobi812/phantom-pdf)

## Requirements

PHP 5.4

symfony/process: ~2.6.9

## Installation

Download PhantomJS: http://phantomjs.org/download.html

Install via Composer

```
composer require project-a/phantom-pdf

```

## Basic Usage


```php

<?php

use PhantomPdf/PdfGenerator;

$pdfGenerator = new PdfGenerator('/path/to/phantomjs-binary');

$htmlString = '<h1>Test Pdf</h1>';
$targetPath = '/path/to/target-file';

$pdfGenerator->renderFileFromHtml($htmlString, $targetPath);


```

Note: Right now it is only possible to build Pdf-Files from raw html-strings!


## Advanced Usage

### Page options:

```php

<?php

use PhantomPdf/PdfGenerator;

$pdfGenerator = new PdfGenerator('/path/to/phantomjs-binary');

$options = new Options();

// Set margin (by default all widths are in cm)
$options->setMargin(1.5);
$options->setMargin(15, 'mm');

// Set orientation
$options->setOrientationPortrait();
// or
$options->setOrientationLandscape();

// Set Format
$options->setFormat('A5');

$pdfGenerator->renderFileFromHtml($htmlString, $targetPath, $options);

```

### Header & Footer

```php

<?php

// Create a Header that appears on every page.
$options->setHeaderContent('<h1>Header</h1>');
$options->setHeaderHeight(3);

// Create a Footer for every page. 
$options->setFooterContent('<div>#pageNum / #totalPages</div>');
$options->setFooterHeight(2);

// Use custom Placeholder for PageNumber and TotalPageCount
$options->setPageNumPlaceholder('{{pageNum}}')
$options->setTotalPagesPlaceholder('{{totalPages}}')

```
