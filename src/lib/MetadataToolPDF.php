<?php

namespace Massinissa\MetadataTool\Lib;

/**
 * Metadata des fichiers PDF et IMAGES, permet aussi de structurer les meta dans le cadre de santiane (metier)
 * !! Le fichier generé est temporaire, il doit etre deplacé apres generation
 *
 * Class MetadataTool
 *
 * @package Massinissa\MetadataTool
 */
class MetadataToolPDF implements MetadataToolInterface
{
    /** @var string */
    protected $fileName;

    /** @var string|null */
    protected $fileNameOutput;

    /** @var string[] */
    protected $tmpFiles = [];

    /** @var string[] */
    protected $metadatas = [];

    /**
     * @var bool
     */
    protected $isBase64 = false;

    /**
     * MetadataTool constructor.
     *
     * @param string      $fileName
     * @param string|null $fileNameOutput
     *
     * @throws \Exception
     */
    public function __construct(string $fileName)
    {
        $this->setFileName($fileName);
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
        $metas = $this->getMetadatas();
        $index = count($metas);

        //Move index before empty values
        while ($index !== 0 && empty($metas[$index])) {
            $index--;
        }

        $mKey   = $this->formatMetaKey($key);
        $mValue = $this->formatMetaValue($value);

        $indexSearch = array_search($mKey, $metas);

        if ($indexSearch !== false) {
            //Replace value of an existing key
            $metas[$indexSearch + 1] = $mValue;
        } else {
            array_splice($metas, $index + 1, 0, [$mKey, $mValue]);
        }

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
        $mKey        = $this->formatMetaKey($key);
        $metas       = $this->getMetadatas();
        $indexSearch = array_search($mKey, $metas);

        if ($indexSearch !== false && isset($metas[$indexSearch + 1])) {
            return preg_replace("/{$this->formatMetaValue('')}/", '', $metas[$indexSearch + 1]);
        }

        return null;
    }

    /**
     * @return bool
     */
    public function unsetMetadata($key): bool
    {
        $mKey        = $this->formatMetaKey($key);
        $indexSearch = array_search($mKey, $metas);

        if ($indexSearch !== false) {
            unset($this->metadatas[$indexSearch]);
            unset($this->metadatas[$indexSearch + 1]);
        }
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
     */
    protected function setFileNameOutput(?string $fileNameOutput): self
    {
        if (file_exists($fileNameOutput)) {
            throw new \Exception('La librairie '.static::class.' ne peut avoir la responsabilité de modifier un fichier existant '.$fileNameOutput);
        }

        $this->fileNameOutput = $fileNameOutput;

        return $this;
    }

    /**
     * @param string $fileName
     *
     * @return array
     */
    protected function getMetadataFromFileName(string $fileName): array
    {
        return explode(PHP_EOL, shell_exec("pdftk {$fileName} dump_data_utf8"));
    }

    /**
     * @param string $fileName
     * @param string $fileNameOutput
     * @param array  $metas
     *
     * @return string|null
     */
    protected function generateFileWithMetadatas(string $fileName, string $fileNameOutput, array $metas): ?string
    {
        $tempReportFile = $this->getNewTmpFile();

        $metasString = implode(PHP_EOL, $metas);

        file_put_contents($tempReportFile, $metasString);

        return shell_exec("pdftk {$fileName} update_info_utf8 {$tempReportFile} output {$fileNameOutput}");
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
        return "InfoKey: {$key}";
    }

    /**
     * @param string $value
     *
     * @return string
     */
    protected function formatMetaValue(string $value): string
    {
        return "InfoValue: {$value}";
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