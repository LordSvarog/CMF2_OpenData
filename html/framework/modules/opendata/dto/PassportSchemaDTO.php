<?php
/**
 * Created by PhpStorm.
 * User: elfuvo
 * Date: 01.08.17
 * Time: 15:49
 */

namespace app\modules\opendata\dto;

class PassportSchemaDTO
{
    /**
     * @var array
     */
    protected $properties = [];

    /**
     * @param OpendataPropertyDTO $prop
     */
    public function addProperty(OpendataPropertyDTO $prop)
    {
        if ($prop->getName() && $prop->getTitle()) {
            $this->properties[$prop->getName()] = $prop;
        }
    }

    /**
     * @return OpendataPropertyDTO[]
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * @param $name
     *
     * @return OpendataPropertyDTO|null
     */
    public function getProperty($name)
    {
        if (isset($this->properties[$name])) {
            return $this->properties[$name];
        }

        return null;
    }
}