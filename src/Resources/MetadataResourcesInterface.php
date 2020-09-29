<?php

namespace Massinissa\MetadataTool\Resources;

/**
 * Interface Structured datas for metadata
 */
interface MetadataResourcesInterface
{
    /**
     * MetadataDefault constructor.
     *
     * @param array|null $data
     */
    public function __construct(?array $data = null);

    /**
     * @return \DateTime|null
     */
    public function getCreatedDate(): ?\DateTime;

    /**
     * @param \DateTime $createdDate
     *
     * @return static
     */
    public function setCreatedDate(\DateTime $createdDate);

    /**
     * @return \DateTime|null
     */
    public function getUpdatedDate(): ?\DateTime;

    /**
     * @param \DateTime $createdDate
     *
     * @return static
     */
    public function setUpdatedDate(\DateTime $updatedDate);

    /**
     * @return string|null
     */
    public function getApplicationId(): ?int;

    /**
     * @param string $webserviceId
     *
     * @return static
     */
    public function setApplicationId(int $webserviceId);

    /**
     * @return array
     */
    public function toArray(): array;

    /**
     * @return static
     */
    public static function fromArray(array $data);
}