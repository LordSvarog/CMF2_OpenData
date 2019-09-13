<?php
/**
 * Created by PhpStorm.
 * User: elfuvo
 * Date: 01.08.17
 * Time: 10:04
 */

namespace app\modules\opendata\export\data;

use app\modules\opendata\dto\OpendataPassportDTO;
use app\modules\opendata\dto\OpendataPropertyDTO;
use yii\web\Response;

class ExportDataSchemaXml implements ExportDataSchemaInterface
{

    /**
     * @var OpendataPassportDTO
     */
    protected $passport;

    /**
     * @var OpendataPropertyDTO[]
     */
    protected $properties = [];

    /**
     * @return string
     */
    public function render(): string
    {
        $dom = new \DOMDocument();
        $schema = $dom->createElementNS('http://www.w3.org/2001/XMLSchema', 'xs:schema');
        $schema->setAttribute('attributeFormDefault', 'unqualified');
        $schema->setAttribute('elementFormDefault', 'qualified');
        $elementSet = $dom->createElementNS('http://www.w3.org/2001/XMLSchema', 'xs:element');
        $elementSet->setAttribute('name', $this->passport->getCode());
        $complexTypeSet = $dom->createElementNS('http://www.w3.org/2001/XMLSchema', 'xs:complexType');
        $sequenceSet = $dom->createElementNS('http://www.w3.org/2001/XMLSchema', 'xs:sequence');

        // set item appends
        $element = $dom->createElementNS('http://www.w3.org/2001/XMLSchema', 'xs:element');
        $element->setAttribute('name', $this->passport->getCode() . '_item');
        $complexType = $dom->createElementNS('http://www.w3.org/2001/XMLSchema', 'xs:complexType');
        $sequence = $dom->createElementNS('http://www.w3.org/2001/XMLSchema', 'xs:sequence');

        // properties
        foreach ($this->properties as $property) {
            $propElement = $dom->createElementNS('http://www.w3.org/2001/XMLSchema', 'xs:element');
            $propElement->setAttribute('name', $property->getName());
            $propElement->setAttribute('type', 'xs:' . $property->getFormat());

            $propAnnotation = $dom->createElementNS('http://www.w3.org/2001/XMLSchema', 'xs:annotation');
            $propDocumentation = $dom->createElementNS('http://www.w3.org/2001/XMLSchema', 'xs:documentation', $property->getTitle());

            $propAnnotation->appendChild($propDocumentation);
            $propElement->appendChild($propAnnotation);
            $sequence->appendChild($propElement);
        }

        unset($propElement);
        unset($propAnnotation);
        unset($propDocumentation);

        // set item appends
        $complexType->appendChild($sequence);
        $element->appendChild($complexType);
        // set appends
        $sequenceSet->appendChild($element);
        $complexTypeSet->appendChild($sequenceSet);
        $elementSet->appendChild($complexTypeSet);

        $schema->appendChild($elementSet);
        $dom->appendChild($schema);

        return $dom->saveXML();
    }

    /**
     * @param OpendataPassportDTO $passport
     *
     * @return void
     */
    public function loadPassport(OpendataPassportDTO $passport)
    {
        $this->passport = $passport;
    }

    /**
     * @param OpendataPropertyDTO $property
     */
    public function addProperty(OpendataPropertyDTO $property)
    {
        array_push($this->properties, $property);
    }

    /**
     * @return string
     */
    public function getResponseFormat(): string
    {
        return Response::FORMAT_XML;
    }
}