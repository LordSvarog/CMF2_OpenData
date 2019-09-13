<?php
/**
 * Created by PhpStorm.
 * User: elfuvo
 * Date: 01.08.17
 * Time: 9:56
 */

namespace app\modules\opendata\dto;

/**
 * Class OpendataPropertyImportDTO
 *
 * @package app\modules\opendata\dto
 */
class OpendataPropertyDTO
{

    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $format;

    /**
     * OpendataPropertyImportDTO constructor.
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
    public function getName(): string
    {
        return substr($this->name, 0, 127);
    }

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = preg_replace('#([^a-z_\d\-]+)#i', '', $name);
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return substr($this->title, 0, 512);
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
    public function getFormat(): string
    {
        return $this->format;
    }

    /**
     * @param string $format
     */
    public function setFormat(string $format)
    {
        $this->format = $format;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id)
    {
        $this->id = $id;
    }

}