<?php

namespace PhantomPdf;

use Symfony\Component\Process\Process;

class PdfGenerator
{

    const HTML_EXTENSION = 'html';
    const PDF_EXTENSION = 'pdf';

    /**
     * @var string
     */
    private $binaryPath;

    /**
     * @var \PhantomPdf\Base64ConverterInterface
     */
    private $base64Converter;

    /**
     * @var int
     */
    private $timeout = 120;

    /**
     * @var string
     */
    private $tempDirectory;

    /**
     * @var array
     */
    private $tempFiles = [];

    /**
     * @var array
     */
    private $commandLineOptions = [];

    /**
     * @param string $binaryPath
     */
    public function __construct($binaryPath)
    {
        $this->binaryPath = $binaryPath;
        $this->base64Converter = new Base64Converter();
    }

    /**
     * @param string $html
     * @param string $targetPath
     * @param \PhantomPdf\Options|null $options
     * @throws \PhantomPdf\PhantomPdfException
     *
     * @return void
     */
    public function renderFileFromHtml($html, $targetPath, Options $options = null)
    {
        $tmpFilePath = $this->createTempFilePath(self::HTML_EXTENSION);
        $this->createFile($tmpFilePath, $html);

        if ($options === null) {
            $options = new Options();
        }

        $options = $this->prepareOptions($options);

        $this->convertToPdf($tmpFilePath, $targetPath, $options);
    }

    /**
     * @param string $html
     * @param \PhantomPdf\Options|null $options
     *
     * @return string
     */
    public function renderOutputFromHtml($html, Options $options = null)
    {
        $tmpPdfFilePath = $this->createTempFilePath(self::PDF_EXTENSION);

        $this->renderFileFromHtml($html, $tmpPdfFilePath, $options);

        return file_get_contents($tmpPdfFilePath);
    }

    public function __destruct()
    {
        foreach ($this->tempFiles as $tempFile) {
            if (file_exists($tempFile)) {
                unlink($tempFile);
            }
        }
    }

    /**
     * @param string $binaryPath
     *
     * @return void
     */
    public function setBinaryPath($binaryPath)
    {
        $this->binaryPath = $binaryPath;
    }

    /**
     * @param int $timeout
     *
     * @return void
     */
    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;
    }

    /**
     * @param string $commandLineOption
     *
     * @return void
     */
    public function setCommandLineOption($commandLineOption)
    {
        $this->commandLineOptions[] = $commandLineOption;
    }

    /**
     * @param array $commandLineOptions
     *
     * @return void
     */
    public function setCommandLineOptions(array $commandLineOptions)
    {
        foreach ($commandLineOptions as $commandLineOption) {
            $this->setCommandLineOption($commandLineOption);
        }
    }

    /**
     * @param \PhantomPdf\Base64ConverterInterface $base64Converter
     *
     * @return void
     */
    public function setBase64Converter(Base64ConverterInterface $base64Converter)
    {
        $this->base64Converter = $base64Converter;
    }


    /**
     * @param string $tempDirectory
     *
     * @return void
     */
    public function setTempDirectory($tempDirectory)
    {
        $this->tempDirectory = $tempDirectory;
    }

    /**
     * @param string $content
     * @param string $filePath
     *
     * @return void
     */
    protected function createFile($filePath, $content)
    {
        file_put_contents($filePath, $content);
    }

    /**
     * @param string $extension
     *
     * @return string
     */
    protected function createTempFilePath($extension)
    {
        $tempDirectory = $this->getTempDirectory();
        $uniqueId = uniqid('phantom-pdf-', true);

        $filePath = sprintf(
            '%s/%s.%s',
            $tempDirectory,
            $uniqueId,
            $extension
        );

        $this->tempFiles[] = $filePath;

        return $filePath;
    }

    /**
     * @param string $resourcePath
     * @param string $targetPath
     * @param \PhantomPdf\Options $options
     * @throws \PhantomPdf\PhantomPdfException
     *
     * @return void
     */
    protected function convertToPdf($resourcePath, $targetPath, Options $options)
    {
        $command = $this->createCommand($resourcePath, $targetPath, $options);
        $process = new Process($command);

        if ($this->timeout !== null) {
            $process->setTimeout($this->timeout);
        }

        $process->run();
        $error = $process->getErrorOutput();

        if (!empty($error)) {
            throw new PhantomPdfException($error . ' ' . $process->getExitCodeText());
        }
    }

    /**
     * @param string $resourcePath
     * @param string $targetPath
     * @param \PhantomPdf\Options $options
     *
     * @return string
     */
    protected function createCommand($resourcePath, $targetPath, Options $options)
    {
        $commandLineOptions = implode(' ', $this->commandLineOptions);

        $optionArray = $options->toArray();
        $encodedOptions = escapeshellarg(json_encode($optionArray));

        $commandSegments = [];

        $commandSegments[] = $this->binaryPath;
        $commandSegments[] = $commandLineOptions;
        $commandSegments[] = __DIR__ . '/../js/phantom-pdf.js';
        $commandSegments[] = $resourcePath;
        $commandSegments[] = $targetPath;
        $commandSegments[] = $encodedOptions;

        return implode(' ', $commandSegments);
    }

    /**
     * @return string
     */
    protected function getTempDirectory()
    {
        if ($this->tempDirectory === null) {
            $this->tempDirectory = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'phantom-pdf';
        }

        if (!is_dir($this->tempDirectory)) {
            mkdir($this->tempDirectory);
        }

        return $this->tempDirectory;
    }

    /**
     * @param string $htmlString
     *
     * @return string
     */
    protected function convertImagesToBase64($htmlString)
    {
        if (!$htmlString) {
            return $htmlString;
        }

        return $this->base64Converter->convertImageSrcTo64Base($htmlString);
    }

    /**
     * @param \PhantomPdf\Options $options
     *
     * @return \PhantomPdf\Options
     */
    protected function prepareHeaderAndFooter(Options $options)
    {
        if ($options->getHeaderContent() !== null) {
            $headerPath = $this->putContentToTmpFile($options->getHeaderContent());

            $options->setHeaderPath($headerPath);
            $options->setHeaderContent(null);
        }

        if ($options->getFooterContent() !== null) {
            $footerPath = $this->putContentToTmpFile($options->getFooterContent());

            $options->setFooterPath($footerPath);
            $options->setFooterContent(null);
        }

        return $options;
    }

    /**
     * @param string $content
     *
     * @return string
     */
    protected function putContentToTmpFile($content)
    {
        $path = $this->createTempFilePath(self::HTML_EXTENSION);

        $this->createFile($path, $content);

        return $path;
    }

    /**
     * @param \PhantomPdf\Options $options
     *
     * @return \PhantomPdf\Options
     */
    protected function prepareOptions(Options $options)
    {
        if ($options->getConvertImagesToBase64() === true) {

            $preparedHeaderContent = $this->convertImagesToBase64($options->getHeaderContent());
            $preparedFooterContent = $this->convertImagesToBase64($options->getFooterContent());

            $options->setHeaderContent($preparedHeaderContent);
            $options->setFooterContent($preparedFooterContent);
        }

        $options = $this->prepareHeaderAndFooter($options);

        return $options;
    }

}
