<?php

namespace Massinissa\MetadataTool;

/**
 * Metadata des fichiers PDF et IMAGES, permet aussi de structurer les meta dans le cadre de santiane (metier)
 * Class MetadataTool
 *
 * @package Massinissa\MetadataTool
 */
class MetadataTool
{
    /** @var string */
    protected $filename;

    /**
     * @var bool
     */
    protected $isBase64 = false;

    public function __construct(string $filename)
    {
        $this->setFilename($filename);
    }

    /**
     * @return bool
     */
    public function addMetadata(): bool
    {
        $success = PDF_set_info($this->getFilename(), 'isMyKey', 'isMyValue');

        return $success;
    }

    /**
     * @return string
     */
    public function getFilename(): string
    {
        return $this->filename;
    }

    /**
     * @param string $filename
     *
     * @return $this
     */
    public function setFilename(string $filename): self
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * @return bool
     */
    public function isBase64(): bool
    {
        return $this->isBase64;
    }

    /**
     * @param bool $isBase64
     *
     * @return MetadataTool
     */
    public function setIsBase64(bool $isBase64): MetadataTool
    {
        $this->isBase64 = $isBase64;

        return $this;
    }
}