<?php

namespace PhantomPdf\Test;

use PhantomPdf\PdfGenerator;


class PdfGeneratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PdfGenerator
     */
    private $pdfGenerator;


    public function setUp()
    {
        $this->pdfGenerator = new PdfGenerator('phantomjs');
    }

    public function testHtmlToPdfFile()
    {
        $filePath = '/tmp/HtmlToPdfFile.pdf';
        $htmlMock = $this->getContentMock();

        $this->pdfGenerator->renderFileFromHtml($htmlMock, $filePath);

        $this->assertFileExists($filePath);

        unlink($filePath);
    }

    public function testHtmlToPdfOutput()
    {
        $htmlMock = $this->getContentMock();

        $result = $this->pdfGenerator->renderOutputFromHtml($htmlMock);

        $this->assertNotEmpty($result);
        $this->assertInternalType('string', $result);
    }

    /**
     * @expectedException \PhantomPdf\Exception\PhantomPdfException
     * @expectedExceptionMessage /wrong/binary
     */
    public function testExceptionBinaryDoesNotExist()
    {
        $wrongBinaryPath = '/wrong/binary';
        $this->setExpectedException(
            'Exception', $wrongBinaryPath
        );

        $htmlMock = $this->getContentMock();
        $pdfGenerator = new PdfGenerator('/wrong/binary');

        $pdfGenerator->renderOutputFromHtml($htmlMock);
    }

    /**
     * @return string
     */
    protected function getContentMock()
    {
        return '<h1>Test</h1>';
    }

}
