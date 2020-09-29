<?php

namespace Massinissa\MetadataTool\Resources;

/**
 * Structured default datas for metadata
 */
class MetadataResourcesDefault implements MetadataResourcesInterface
{
    /** @var int[] */
    public const CLIENT_IDS = [
        'BROWSER'  => 0,
        'NWS'      => 1,
        'EXTRANET' => 2,
        'GESTION'  => 3,
        'FINANCE'  => 4,
        //['ETC...']  => n°,
    ];
    /** @var int[] */
    public const APPLICATION_IDS = self::CLIENT_IDS;
    /** @var array<string, int> */
    protected const METADATA_KEY_MAPPER = [
        'CREATED_DATE'   => 0,
        'UPDATED_DATE'   => 1,
        'APPLICATION_ID' => 2,
        'USER_ID'        => 3,
        'CLIENT_ID'      => 4,
    ];
    /** @var array<string, int> */
    protected const METADATA_ENUM_MAPPER = [
        self::METADATA_KEY_MAPPER['CREATED_DATE']   => 'CREATED_DATE',
        self::METADATA_KEY_MAPPER['UPDATED_DATE']   => 'UPDATED_DATE',
        self::METADATA_KEY_MAPPER['APPLICATION_ID'] => 'APPLICATION_ID',
        self::METADATA_KEY_MAPPER['USER_ID']        => 'USER_ID',
    ];

    /** @var \DateTime|null */
    protected $createdDate;

    /** @var \DateTime|null */
    protected $updatedDate;

    /** @var string|null */
    protected $applicationId;

    /** @var int|null */
    protected $userId;

    /** @var int|null */
    protected $clientId;

    /**
     * MetadataDefault constructor.
     *
     * @param array|null $data
     */
    public function __construct(?array $data = null)
    {
        if ($data) {
            $this->fromArray($data);
        }

        if (!$this->getCreatedDate()) {
            $this->setCreatedDate(new \DateTime());
        }

        $this->setUpdatedDate(new \DateTime());
    }

    /**
     * @return \DateTime|null
     */
    public function getCreatedDate(): ?\DateTime
    {
        return $this->createdDate;
    }

    /**
     * @param \DateTime $createdDate
     *
     * @return static
     */
    public function setCreatedDate(\DateTime $createdDate)
    {
        $this->createdDate = $createdDate;

        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getUpdatedDate(): ?\DateTime
    {
        return $this->updatedDate;
    }

    /**
     * @param \DateTime $updatedDate
     *
     * @return static
     */
    public function setUpdatedDate(\DateTime $updatedDate)
    {
        $this->updatedDate = $updatedDate;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getApplicationId(): ?int
    {
        return $this->applicationId;
    }

    /**
     * @param string $applicationId
     *
     * @return static
     */
    public function setApplicationId(int $applicationId)
    {
        $this->applicationId = $applicationId;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getUserId(): ?int
    {
        return $this->userId;
    }

    /**
     * @param int|null $userId
     *
     * @return static
     */
    public function setUserId(?int $userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getClientId(): ?int
    {
        return $this->clientId;
    }

    /**
     * @param int|null $clientId
     *
     * @return static
     */
    public function setClientId(?int $clientId)
    {
        $this->clientId = $clientId;

        return $this;
    }

    /**
     * @return array<int, mixed>
     */
    public function toArray(): array
    {
        return [
            static::METADATA_KEY_MAPPER['CREATED_DATE']   => $this->getCreatedDate()->getTimestamp(),
            static::METADATA_KEY_MAPPER['UPDATED_DATE']   => $this->getUpdatedDate()->getTimestamp(),
            static::METADATA_KEY_MAPPER['APPLICATION_ID'] => $this->getApplicationId(),
            static::METADATA_KEY_MAPPER['USER_ID']        => $this->getUserId(),
            static::METADATA_KEY_MAPPER['CLIENT_ID']      => $this->getClientId(),
        ];
    }

    /**
     * @param array $data
     *
     * @return static
     * @throws \Exception
     */
    public static function fromArray(array $data)
    {
        $self = new static();

        $createdDate = $data[static::METADATA_KEY_MAPPER['CREATED_DATE']] ?? null;
        $createdDate = $createdDate ? new \DateTime($createdDate) : null;

        $updatedDate = $data[static::METADATA_KEY_MAPPER['CREATED_DATE']] ?? null;
        $updatedDate = $updatedDate ? new \DateTime($updatedDate) : null;

        $self->setCreatedDate($createdDate);
        $self->setUpdatedDate($updatedDate);
        $self->setApplicationId((int)$data[static::METADATA_KEY_MAPPER['APPLICATION_ID']]);
        $self->setUserId((int)$data[static::METADATA_KEY_MAPPER['USER_ID']]);
        $self->setClientId((int)$data[static::METADATA_KEY_MAPPER['CLIENT_ID']]);

        return $self;
    }
}