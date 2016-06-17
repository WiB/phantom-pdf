<?php

namespace PhantomPdf;

class Options
{

    const ORIENTATION_PORTRAIT = 'portrait';
    const ORIENTATION_LANDSCAPE = 'landscape';

    /**
     * @var string
     */
    private $format = 'A4';

    /**
     * @var string
     */
    private $orientation = self::ORIENTATION_PORTRAIT;

    /**
     * @var array
     */
    private $customHeaders;

    /**
     * @var int
     */
    private $zoomFactor = 1;

    /**
     * @var string
     */
    private $margin;

    /**
     * @var string
     */
    private $headerContent;

    /**
     * @var string
     */
    private $headerPath;

    /**
     * @var string
     */
    private $headerHeight;

    /**
     * @var string
     */
    private $footerContent;

    /**
     * @var string
     */
    private $footerPath;

    /**
     * @var string
     */
    private $footerHeight;

    /**
     * @var string
     */
    private $pageNumPlaceholder;

    /**
     * @var string
     */
    private $totalPagesPlaceholder;

    /**
     * @var bool
     */
    private $convertImagesToBase64 = true;

    /**
     * @return array
     */
    public function toArray()
    {
        $classVars = get_object_vars($this);

        foreach ($classVars as $key => $value) {
            if ($value === null) {
                unset($classVars[$key]);
            }
        }

        return $classVars;
    }

    /**
     * @param string $format
     *
     * @return void
     */
    public function setFormat($format)
    {
        $this->format = $format;
    }

    /**
     * @return void
     */
    public function setOrientationPortrait()
    {
        $this->orientation = self::ORIENTATION_PORTRAIT;
    }

    /**
     * @return void
     */
    public function setOrientationLandscape()
    {
        $this->orientation = self::ORIENTATION_LANDSCAPE;
    }

    /**
     * @param array $customHeaders
     *
     * @return void
     */
    public function setCustomHeaders(array $customHeaders)
    {
        $this->customHeaders = $customHeaders;
    }

    /**
     * @param int $zoomFactor
     *
     * @return void
     */
    public function setZoomFactor($zoomFactor)
    {
        $this->zoomFactor = $zoomFactor;
    }

    /**
     * @param int $width
     * @param string $unit
     *
     * @return void
     */
    public function setMargin($width, $unit = 'cm')
    {
        $this->margin = $width . $unit;
    }

    /**
     * @param string $headerContent
     *
     * @return void
     */
    public function setHeaderContent($headerContent)
    {
        $this->headerContent = $headerContent;
    }

    /**
     * @return string
     */
    public function getHeaderContent()
    {
        return $this->headerContent;
    }

    /**
     * @param int $width
     * @param string $unit
     *
     * @return void
     */
    public function setHeaderHeight($width, $unit = 'cm')
    {
        $this->headerHeight = $width . $unit;
    }

    /**
     * @param string $footerContent
     *
     * @return void
     */
    public function setFooterContent($footerContent)
    {
        $this->footerContent = $footerContent;
    }

    /**
     * @return string
     */
    public function getFooterContent()
    {
        return $this->footerContent;
    }

    /**
     * @param int $width
     * @param string $unit
     *
     * @return void
     */
    public function setFooterHeight($width, $unit = 'cm')
    {
        $this->footerHeight = $width . $unit;
    }

    /**
     * @param string $pageNumPlaceholder
     *
     * @return void
     */
    public function setPageNumPlaceholder($pageNumPlaceholder)
    {
        $this->pageNumPlaceholder = $pageNumPlaceholder;
    }

    /**
     * @param string $totalPagesPlaceholder
     *
     * @return void
     */
    public function setTotalPagesPlaceholder($totalPagesPlaceholder)
    {
        $this->totalPagesPlaceholder = $totalPagesPlaceholder;
    }

    /**
     * @return bool
     */
    public function getConvertImagesToBase64()
    {
        return $this->convertImagesToBase64;
    }

    /**
     * @param bool $convertImagesToBase64
     *
     * @return void
     */
    public function setConvertImagesToBase64($convertImagesToBase64)
    {
        $this->convertImagesToBase64 = $convertImagesToBase64;
    }

    /**
     * @return mixed
     */
    public function getHeaderPath()
    {
        return $this->headerPath;
    }

    /**
     * @param string $headerPath
     *
     * @return void
     */
    public function setHeaderPath($headerPath)
    {
        $this->headerPath = $headerPath;
    }

    /**
     * @return string
     */
    public function getFooterPath()
    {
        return $this->footerPath;
    }

    /**
     * @param string $footerPath
     *
     * @return void
     */
    public function setFooterPath($footerPath)
    {
        $this->footerPath = $footerPath;
    }

}
