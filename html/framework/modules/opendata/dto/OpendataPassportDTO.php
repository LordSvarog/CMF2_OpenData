<?php
/**
 * Created by PhpStorm.
 * User: elfuvo
 * Date: 01.08.17
 * Time: 9:56
 */

namespace app\modules\opendata\dto;

/**
 * Class OpendataSetDTO
 *
 * @package app\modules\opendata\dto
 */
class OpendataPassportDTO
{
    /**
     * @var string
     */
    protected $url;

    /**
     * @var string
     */
    protected $schema_url;

    /**
     * @var string
     */
    protected $identifier;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var string
     */
    protected $code;

    /**
     * @var string
     */
    protected $subject;

    /**
     * @var string
     */
    protected $owner;

    /**
     * @var string
     */
    protected $publisher_name;

    /**
     * @var string
     */
    protected $publisher_email;

    /**
     * @var string
     */
    protected $publisher_phone;

    /**
     * @var string
     */
    protected $update_frequency;

    /**
     * @var \DateTime|bool
     */
    protected $created_at;

    /**
     * @var \DateTime|bool
     */
    protected $updated_at;

    /**
     * @var string
     */
    protected $changes;

    /**
     * @var OpendataSetDTO[]
     */
    protected $sets = [];

    /**
     * OpendataPassportDTO constructor.
     *
     * @param array $params
     */
    public function __construct(array $params = [])
    {
        if (!empty($params)) {
            foreach ($params as $paramName => $paramValue) {
                if (method_exists($this, 'set' . $paramName)) {
                    call_user_func([$this, 'set' . $paramName], $paramValue);
                }
            }
        }
    }

    /**
     * @param $title
     */
    public function setTitle($title)
    {
        $this->title = (string)$title;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return (string)$this->title;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description)
    {
        $this->description = $description;
    }

    /**
     * @param $code
     */
    public function setCode($code)
    {
        $this->code = (string)$code;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return (string)$this->code;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl(string $url)
    {
        $this->url = $url;
    }

    /**
     * @return string
     */
    public function getSubject(): string
    {
        return $this->subject;
    }

    /**
     * @param string $subject
     */
    public function setSubject(string $subject)
    {
        $this->subject = $subject;
    }

    /**
     * @return string
     */
    public function getOwner(): string
    {
        return $this->owner;
    }

    /**
     * @param string $owner
     */
    public function setOwner(string $owner)
    {
        $this->owner = $owner;
    }

    /**
     * @return string
     */
    public function getPublisherName(): string
    {
        return $this->publisher_name;
    }

    /**
     * @param string $publisher_name
     */
    public function setPublisherName(string $publisher_name)
    {
        $this->publisher_name = $publisher_name;
    }

    /**
     * @return string
     */
    public function getPublisherEmail(): string
    {
        return $this->publisher_email;
    }

    /**
     * @param string $publisher_email
     */
    public function setPublisherEmail(string $publisher_email)
    {
        $this->publisher_email = $publisher_email;
    }

    /**
     * @return string
     */
    public function getPublisherPhone(): string
    {
        return $this->publisher_phone;
    }

    /**
     * @param string $publisher_phone
     */
    public function setPublisherPhone(string $publisher_phone)
    {
        $this->publisher_phone = $publisher_phone;
    }

    /**
     * @return string
     */
    public function getUpdateFrequency(): string
    {
        return $this->update_frequency;
    }

    /**
     * @param string $update_frequency
     */
    public function setUpdateFrequency(string $update_frequency)
    {
        $this->update_frequency = $update_frequency;
    }

    /**
     * @return string
     */
    public function getSchemaUrl(): string
    {
        return $this->schema_url;
    }

    /**
     * @param string $schema_url
     */
    public function setSchemaUrl(string $schema_url)
    {
        $this->schema_url = $schema_url;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->created_at ?: new \DateTime();
    }

    /**
     * @param string $created_at
     */
    public function setCreatedAt(string $created_at)
    {
        $this->created_at = \DateTime::createFromFormat('d.m.Y', $created_at);
        if (!$this->created_at) {
            $this->created_at = \DateTime::createFromFormat('Y-m-d H:i:s', $created_at);
        }
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt(): \DateTime
    {
        return $this->updated_at ?: new \DateTime();
    }

    /**
     * @param string $updated_at
     */
    public function setUpdatedAt(string $updated_at)
    {
        $this->updated_at = \DateTime::createFromFormat('d.m.Y', $updated_at);
        if (!$this->updated_at) {
            $this->updated_at = \DateTime::createFromFormat('Y-m-d H:i:s', $updated_at);
        }
    }

    /**
     * @return string
     */
    public function getChanges(): string
    {
        return $this->changes;
    }

    /**
     * @param string $changes
     */
    public function setChanges(string $changes)
    {
        $this->changes = $changes;
    }

    /**
     * @param OpendataSetDTO $set
     */
    public function addSet(OpendataSetDTO $set)
    {
        array_push($this->sets, $set);
    }

    /**
     * @return OpendataSetDTO[]
     */
    public function getSets(): array
    {
        return $this->sets;
    }

    /**
     * @param OpendataSetDTO[] $sets
     */
    public function setSets(array $sets)
    {
        $this->sets = $sets;
    }

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * @param string $identifier
     */
    public function setIdentifier(string $identifier)
    {
        $this->identifier = $identifier;
    }

}