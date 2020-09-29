<?php

namespace Massinissa\MetadataTool\Libraries;

/**
 * Metadata des fichiers PDF et IMAGES, permet aussi de structurer les meta dans le cadre de santiane (metier)
 * !! Le fichier generé est temporaire, il doit etre deplacé apres generation
 *
 * Class MetadataTool
 *
 * @package Massinissa\MetadataTool
 */
interface MetadataToolInterface
{
    /**
     * MetadataTool constructor.
     *
     * @param string      $fileName
     * @param string|null $fileNameOutput
     *
     * @throws \Exception
     */
    public function __construct(string $fileName);

    /**
     * @return string[]
     */
    public function getMetadatas(): array;

    /**
     * @param array $metadatas
     *
     * @return $this
     */
    public function setMetadatas(array $metadatas);

    /**
     * @param string $key
     * @param string $value
     *
     * @return $this
     */
    public function setMetadata(string $key, string $value);

    /**
     * @param $key
     *
     * @return string|null
     */
    public function getMetadata(string $key): ?string;

    /**
     * @return void
     */
    public function unsetMetadata(string $key): void;

    /**
     * @param string|null $fileNameOutput
     *
     * @return string temporary filename or your output filename
     * @throws \Exception
     */
    public function generate(?string $fileNameOutput = null): string;

    /**
     * @return string
     */
    public function getFileName(): string;

    /**
     * @return string|null
     */
    public function getLastFileNameOutput(): ?string;

    /**
     * Clear tmp files
     */
    public function clear(): void;

    /**
     *
     */
    public function __destruct();
}