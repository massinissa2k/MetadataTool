<?php

namespace Massinissa\MetadataTool;

use \Massinissa\MetadataTool\Lib\MetadataToolInterface;
use \Massinissa\MetadataTool\Lib\MetadataToolPDF;

/**
 * Metadata des fichiers PDF et IMAGES, permet aussi de structurer les meta dans le cadre de santiane (metier)
 * !! Le fichier generé est temporaire, il doit etre deplacé apres generation
 *
 * Class MetadataTool
 *
 * @package Massinissa\MetadataTool
 */
class MetadataTool
{
    /** @var MetadataToolInterface */
    protected $metadataToolLib;

    /**
     * MetadataTool constructor.
     *
     * @param string      $fileName
     * @param string|null $fileNameOutput
     *
     * @throws \Exception
     */
    public function __construct(string $fileName, ?string $metadataToolLib = null)
    {
        if ($metadataToolLib) {
            $this->metadataToolLib = new $metadataToolLib($fileName);
        } else {
            $this->metadataToolLib = new MetadataToolPDF($fileName);
        }
    }

    /**
     * @return string[]
     */
    public function getMetadatas(): array
    {
        return $this->metadataToolLib->getMetadatas();
    }

    /**
     * @param string $key
     * @param string $value
     *
     * @return $this
     */
    public function setMetadata(string $key, string $value): self
    {
        $this->metadataToolLib->setMetadata($key, $value);

        return $this;
    }

    /**
     * @param $key
     *
     * @return string|null
     */
    public function getMetadata($key): ?string
    {
        return $this->metadataToolLib->getMetadata($key);
    }

    /**
     * @return bool
     */
    public function unsetMetadata($key): bool
    {
        return $this->metadataToolLib->unsetMetadata($key);
    }

    /**
     * @param string|null $fileNameOutput
     *
     * @return string temporary filename or your output filename
     * @throws \Exception
     */
    public function generate(?string $fileNameOutput = null): string
    {
        return $this->metadataToolLib->generate($fileNameOutput);
    }

    /**
     * @return string
     */
    public function getFileName(): string
    {
        return $this->metadataToolLib->getFileName();
    }

    /**
     * @return string|null
     */
    public function getLastFileNameOutput(): ?string
    {
        return $this->metadataToolLib->getLastFileNameOutput();
    }

    /**
     * Clear tmp files
     */
    public function clear(): void
    {
        $this->metadataToolLib->clear();
    }
}