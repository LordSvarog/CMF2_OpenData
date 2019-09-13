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
class OpendataSetDTO
{

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $version;

    /**
     * @var string
     */
    protected $changes;

    /**
     * @var string
     */
    protected $url;

    /**
     * @var string
     */
    protected $structureUrl;

    /**
     * @var \DateTime
     */
    protected $created_at;

    /**
     * @var \DateTime
     */
    protected $updated_at;

    /**
     * OpendataSetDTO constructor.
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
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * @param string $version
     */
    public function setVersion(string $version)
    {
        $this->version = $version;
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
    public function getStructureUrl(): string
    {
        return $this->structureUrl;
    }

    /**
     * @param string $structureUrl
     */
    public function setStructureUrl(string $structureUrl)
    {
        $this->structureUrl = $structureUrl;
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
    public function setCreatedAt($created_at)
    {
        $this->created_at = \DateTime::createFromFormat('Y-m-d H:i:s', $created_at);
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
        $this->updated_at = \DateTime::createFromFormat('Y-m-d H:i:s', $updated_at);
    }


    /**
     * @return \stdClass
     */
    public function toStdObject()
    {
        $std = new \stdClass();
        $std->source = $this->getUrl();
        $std->created = $this->getCreatedAt()->format("Ymd");
        $std->provenance = $this->getChanges();
        $std->valid = $this->getUpdatedAt()->format("Ymd");
        $std->structure = $this->getVersion();

        return $std;
    }

    /**
     * @return \stdClass
     */
    public function toStructureStdObject()
    {
        $std = new \stdClass();
        $std->source = $this->getStructureUrl();
        $std->created = $this->getCreatedAt()->format("Ymd");

        return $std;
    }

    /**
     * @return string
     */
    public function getDataCode()
    {
        return 'data-' . $this->getUpdatedAt()->format('Ymd') . '-structure-' . $this->getVersion();
    }

    /**
     * @return string
     */
    public function getSchemaCode()
    {
        return 'structure-' . $this->getVersion();
    }
}