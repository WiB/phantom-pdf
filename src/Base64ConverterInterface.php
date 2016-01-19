<?php

namespace PhantomPdf;

interface Base64ConverterInterface
{
    /**
     * @param string $htmlString
     */
    public function convertImageSrcTo64Base($htmlString);
}
