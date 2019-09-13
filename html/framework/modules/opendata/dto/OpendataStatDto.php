<?php
/**
 * Created by PhpStorm.
 * User: elfuvo
 * Date: 23.03.18
 * Time: 15:09
 */

namespace app\modules\opendata\dto;


class OpendataStatDto
{

    /**
     * @var int
     */
    protected $shows = 0;

    /**
     * @var int
     */
    protected $downloads = 0;


    /**
     * OpendataStatDto constructor.
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
     * @return int
     */
    public function getShows(): int
    {
        return $this->shows;
    }

    /**
     * @param int $shows
     */
    public function setShows(int $shows): void
    {
        $this->shows = $shows;
    }

    /**
     * @return int
     */
    public function getDownloads(): int
    {
        return $this->downloads;
    }

    /**
     * @param int $downloads
     */
    public function setDownloads(int $downloads): void
    {
        $this->downloads = $downloads;
    }

}