<?php

namespace Massinissa\MetadataTool;

use \Massinissa\MetadataTool\Libraries\MetadataToolInterface;
use \Massinissa\MetadataTool\Libraries\MetadataToolPDF;
use Massinissa\MetadataTool\Resources\MetadataDefault;
use Massinissa\MetadataTool\Resources\MetadataResourcesInterface;

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

    public const METADATA_RESOURCE_KEY = 'inf_s_%d';

    /**
     * MetadataTool constructor.
     *
     * @param string      $fileName
     * @param string|null $metadataToolLib
     * @param string|null $metadataDefault
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
     * @return $this
     */
    public function addMetadataFromResource(MetadataResourcesInterface $metadataResources): void
    {
        $strDatas = [];
        foreach ($metadataResources->toArray() as $key => $value) {
            $strDatas[] = "{$key}:{$value}";
        }

        $metadatas = $this->getMetadatas();

        $index = 0;

        while (key_exists(sprintf(static::METADATA_RESOURCE_KEY, $index), $metadatas)) {
            $index++;
        }

        $this->setMetadata(sprintf(static::METADATA_RESOURCE_KEY, $index), implode(',', $strDatas));
    }

    /**
     * @return $this
     */
    public function getMetadatasResources(): array
    {
        $resources = [];
        $index     = 0;

        while ($resourceStr = $this->getMetadata(sprintf(static::METADATA_RESOURCE_KEY, $index))) {
            $resource         = [];
            $resourceStrArray = explode(',', $resourceStr);
            foreach ($resourceStrArray as $key => $value) {
                $list = sscanf($value, '%[^:]:%s');

                $resource[$list[0]] = $list[1];
            }
            $resources[] = $resource;
            $index++;
        }

        return $resources;
    }

    /**
     * @return array
     */
    public function getMetadatasResourceLast(): array
    {
        $resources = $this->getMetadatasResources();

        return end($resources);
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