<?php

namespace Massinissa\MetadataTool\Libraries;

use \Imagick;

/**
 * Metadata des fichiers PDF et IMAGES, permet aussi de structurer les meta dans le cadre de santiane (metier)
 * !! Le fichier generé est temporaire, il doit etre deplacé apres generation
 *
 * Class MetadataTool
 *
 * @package Massinissa\MetadataTool
 */
class MetadataToolImage implements MetadataToolInterface
{
    /** @var string */
    protected $fileName;

    /** @var string|null */
    protected $fileNameOutput;

    /** @var string[] */
    protected $tmpFiles = [];

    /** @var string[] */
    protected $metadatas = [];

    /** @var Imagick */
    protected $imagick = [];

    /** @var string Prefix de la cle metadata */
    protected const KEY_PREFIX = 'InfoKey:';

    /**
     * MetadataToolImage constructor.
     *
     * @param string $fileName
     *
     * @throws \ImagickException
     */
    public function __construct(string $fileName)
    {
        $this->setFileName($fileName);
        $this->imagick = new Imagick($this->getFileName());

        /** Forcer le png pour permettre des cles custom  */
        $this->imagick->setImageFormat("png");
        $this->setMetadatas($this->getMetadataFromFileName($this->getFileName()));
    }

    /**
     * @return string[]
     */
    public function getMetadatas(): array
    {
        return $this->metadatas;
    }

    /**
     * @param array $metadatas
     *
     * @return $this
     */
    public function setMetadatas(array $metadatas): self
    {
        $this->metadatas = $metadatas;

        return $this;
    }

    /**
     * @param string $key
     * @param string $value
     *
     * @return $this
     */
    public function setMetadata(string $key, string $value): self
    {
        $metas                             = $this->getMetadatas();
        $metas[$this->formatMetaKey($key)] = $this->formatMetaValue($value);

        $this->metadatas = $metas;

        return $this;
    }

    /**
     * @param $key
     *
     * @return string|null
     */
    public function getMetadata(string $key): ?string
    {
        $mKey  = $this->formatMetaKey($key);
        $metas = $this->getMetadatas();

        $value = $metas[$mKey] ?? null;

        if ($value) {
            return preg_replace("/{$this->formatMetaValue('')}/", '', $value);
        }

        return null;
    }

    /**
     * @return void
     */
    public function unsetMetadata($key): void
    {
        $mKey = $this->formatMetaKey($key);
        unset($this->metadatas[$mKey]);
    }

    /**
     * @param string|null $fileNameOutput
     *
     * @return string temporary filename or your output filename
     * @throws \Exception
     */
    public function generate(?string $fileNameOutput = null): string
    {
        $this->setFileNameOutput($fileNameOutput);

        $message = $this->generateFileWithMetadatas($this->getFileName(), $this->getLastFileNameOutput(), $this->getMetadatas());

        if ($message) {
            throw new \Exception($message);
        }

        return $this->getLastFileNameOutput();
    }

    /**
     * @return string
     */
    public function getFileName(): string
    {
        return $this->fileName;
    }

    /**
     * @param string $fileName
     *
     * @return $this
     */
    protected function setFileName(string $fileName): self
    {
        $this->fileName = $fileName;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getLastFileNameOutput(): ?string
    {
        if (!$this->fileNameOutput) {
            $this->fileNameOutput = $this->getNewTmpFile();
        }

        return $this->fileNameOutput;
    }

    /**
     * @param string|null $fileNameOutput
     *
     * @return $this
     * @throws \Exception
     */
    protected function setFileNameOutput(?string $fileNameOutput): self
    {
        if (file_exists($fileNameOutput)) {
            throw new \Exception(
                "La librairie ".(static::class)." ne peut avoir la responsabilité de modifier un fichier existant {$fileNameOutput}"
            );
        }

        $this->fileNameOutput = $fileNameOutput;

        return $this;
    }

    /**
     * @param string $fileName
     *
     * @return array
     * @throws \ImagickException
     */
    protected function getMetadataFromFileName(string $fileName): array
    {
        return $this->imagick->getImageProperties();
    }

    /**
     * @param string $fileName
     * @param string $fileNameOutput
     * @param array  $metas
     *
     * @return string|null
     * @throws \ImagickException
     */
    protected function generateFileWithMetadatas(string $fileName, string $fileNameOutput, array $metas): ?string
    {
        $tempReportFile = $this->getNewTmpFile();

        $oldMetas = $this->getMetadataFromFileName($fileName);

        $metas = $this->getMetadatas();

        foreach ($metas as $key => $meta) {
            if (!isset($oldMetas[$key]) || $oldMetas[$key] !== $meta) {
                if (!$this->imagick->setImageProperty($key, $meta)) {
                    throw new \Exception("Can't set property {$key} with value {$meta} from {$fileName}");
                }
            }
        }

        if (!$this->imagick->writeImage($fileNameOutput)) {
            throw new \Exception("Can't write image {$fileNameOutput}");
        }

        return null;
    }

    /**
     * @return string
     */
    protected function getNewTmpFile(): string
    {
        $tempFile         = tempnam(sys_get_temp_dir(), 'nws_');
        $this->tmpFiles[] = $tempFile;

        return $tempFile;
    }

    /**
     * @param string $key
     *
     * @return string
     */
    protected function formatMetaKey(string $key): string
    {
        return static::KEY_PREFIX."{$key}";
    }

    /**
     * @param string $value
     *
     * @return string
     */
    protected function formatMetaValue(string $value): string
    {
        return $value;
    }

    /**
     * Clear tmp files
     */
    public function clear(): void
    {
        foreach ($this->tmpFiles as $tmpFile) {
            if (file_exists($tmpFile)) {
                unlink($tmpFile);
            }
        }
    }

    public function __destruct()
    {
        $this->clear();
    }
}