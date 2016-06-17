<?php

namespace PhantomPdf\Test;

use PhantomPdf\Options;

class OptionTest extends \PHPUnit_Framework_TestCase
{

    public function testOptionsToArray()
    {
        $options = new Options();
        $options->setFormat('MockFormat');
        $options->setMargin(1);
        $options->setOrientationLandscape();

        $actualOptionArray = $options->toArray();
        $expectedOptionArray = [
            'format' => 'MockFormat',
            'margin' => '1cm',
            'orientation' => 'landscape',
            'zoomFactor' => 1,
            'convertImagesToBase64' => true,
        ];

        $this->assertEquals($expectedOptionArray, $actualOptionArray);
    }

    public function testDefaultOptions()
    {
        $options = new Options();

        $actualOptionArray = $options->toArray();
        $expectedOptionArray = [
            'format' => 'A4',
            'orientation' => 'portrait',
            'zoomFactor' => 1,
            'convertImagesToBase64' => true,
        ];

        $this->assertEquals($expectedOptionArray, $actualOptionArray);
    }

}
