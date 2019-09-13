<?php
/**
 * Created by PhpStorm.
 * User: elfuvo
 * Date: 01.08.17
 * Time: 9:56
 */

namespace app\modules\opendata\dto;

/**
 * Class OpendataListImportDTO
 *
 * @package app\modules\opendata\dto
 */
class OpendataListDTO
{
    /**
     * @var string
     */
    protected $url;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $identifier;

    /**
     * @var string
     */
    protected $format;

    /**
     * OpendataListDTO constructor.
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
     * @param string $url
     */
    public function setUrl(string $url)
    {
        $this->url = $url;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title)
    {
        $this->title = $title;
    }

    /**
     * @param string $identifier
     */
    public function setIdentifier(string $identifier)
    {
        $this->identifier = $identifier;
    }

    /**
     * @param string $format
     */
    public function setFormat(string $format)
    {
        $this->format = $format;
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
    public function getUrl()
    {
        return (string)$this->url;
    }

    /**
     * @return string
     */
    public function getIdentifier()
    {
        return (string)$this->identifier;
    }

    /**
     * @return string
     */
    public function getFormat()
    {
        return (string)$this->format;
    }

    /**
     * @return \stdClass
     */
    public function toStdObject()
    {
        $std = new \stdClass();
        $std->identifier = $this->getIdentifier();
        $std->title = $this->getTitle();
        $std->link = $this->getUrl();
        $std->format = $this->getFormat();

        return $std;
    }


}