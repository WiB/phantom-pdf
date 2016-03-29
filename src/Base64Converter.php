<?php

namespace PhantomPdf;

class Base64Converter implements Base64ConverterInterface
{
    const IMG = 'img';
    const SRC = 'src';
    const EXTENSION = 'extension';

    /**
     * @var array
     */
    private $supportedExtensions = [
        self::BMP,
        self::JPG,
        self::GIF,
        self::PNG,
    ];

    /**
     * @info Image Extensions
     */
    const PNG = 'png';
    const JPG = 'jpg';
    const GIF = 'gif';
    const BMP = 'bmp';

    /**
     * @param string $htmlString
     *
     * @throws PhantomPdfException
     * @return string
     */
    public function convertImageSrcTo64Base($htmlString)
    {
        $dom = new \DOMDocument();

        $dom->loadHTML($htmlString);
        $images = $dom->getElementsByTagName(self::IMG);

        foreach ($images as $image) {
            /* @var \DOMNamedNodeMap $attributes */
            $attributes = $image->attributes;
            $sourceNode = $attributes->getNamedItem(self::SRC);
            $sourceNode->nodeValue = $this->encodeSourceToBase64($sourceNode->nodeValue);
        }

        return $dom->saveHTML();
    }

    /**
     * @param string $source
     *
     * @throws \Exception
     * @return string
     */
    protected function encodeSourceToBase64($source)
    {
        $pathInfo = pathinfo($source);
        $imageExtension = $pathInfo[self::EXTENSION];

        if (!in_array($imageExtension, $this->supportedExtensions)) {
            throw new PhantomPdfException(
                sprintf('Image extension: %s is not supported.', $imageExtension)
            );
        }

        $sourceBase64encoded = base64_encode(
            file_get_contents($source)
        );

        return sprintf(
            'data:image/%s;base64,%s',
            $imageExtension,
            $sourceBase64encoded
        );
    }
}
