<?php
namespace Phppptx\Elements;

use Phppptx\Utilities\XmlUtilities;
/**
 * Create properties
 *
 * @category   Phppptx
 * @package    elements
 * @copyright  Copyright (c) Narcea Labs SL
 *             (https://www.narcealabs.com)
 * @license    phppptx Community License
 * @link       https://www.phppptx.com
 */
class CreateProperties extends CreateElement
{
    /**
     * Creates properties
     *
     * @access public
     * @param array $values
     * @param \DOMDocument $props
     * @return \DOMDocument
     */
    public function createElementProperties($values, $props)
    {
        $generalProperties = array('title', 'subject', 'creator', 'keywords', 'description', 'category', 'contentStatus', 'created', 'modified', 'lastModifiedBy', 'revision');
        $nameSpaces = array('title' => 'dc', 'subject' => 'dc', 'creator' => 'dc', 'keywords' => 'cp', 'description' => 'dc', 'category' => 'cp', 'contentStatus' => 'cp', 'created' => 'dcterms', 'modified' => 'dcterms', 'lastModifiedBy' => 'cp', 'revision' => 'cp');
        $nameSpacesURI = array(
            'dc' => 'http://purl.org/dc/elements/1.1/',
            'cp' => 'http://schemas.openxmlformats.org/package/2006/metadata/core-properties',
            'dcterms' => 'http://purl.org/dc/terms/'
        );

        foreach ($values as $key => $value) {
            if (in_array($key, $generalProperties)) {
                $coreNodes = $props->getElementsByTagName($key);
                if ($coreNodes->length > 0) {
                    $coreNodes->item(0)->nodeValue = $this->parseAndCleanTextString($value);
                } else {
                    if ($key == 'created' || $key == 'modified') {
                        $strNode = '<' . $nameSpaces[$key] . ':' . $key . ' xmlns:' . $nameSpaces[$key] . '="' . $nameSpacesURI[$nameSpaces[$key]] . '" xsi:type="dcterms:W3CDTF">' . $value . '</' . $nameSpaces[$key] . ':' . $key . '>';
                    } else {
                        $strNode = '<' . $nameSpaces[$key] . ':' . $key . ' xmlns:' . $nameSpaces[$key] . '="' . $nameSpacesURI[$nameSpaces[$key]] . '">' . $value . '</' . $nameSpaces[$key] . ':' . $key . '>';
                    }
                    $tempNode = $props->createDocumentFragment();
                    $tempNode->appendXML($strNode);
                    $props->documentElement->appendChild($tempNode);
                }
            }
        }
        return $props;
    }

    /**
     * Create properties
     *
     * @access public
     * @param array $values
     * @param \DOMDocument $props
     * @return \DOMDocument
     */
    public function createPropertiesApp($values, $props)
    {
        $appProperties = array('Manager', 'Company');

        foreach ($values as $key => $value) {
            if (in_array($key, $appProperties)) {
                $appNodes = $props->getElementsByTagName($key);
                if ($appNodes->length > 0) {
                    $appNodes->item(0)->nodeValue = $this->parseAndCleanTextString($value);
                } else {
                    $strNode = '<' . $key . '>' . $this->parseAndCleanTextString($value) . '</' . $key . '>';
                    $tempNode = $props->createDocumentFragment();
                    $tempNode->appendXML($strNode);
                    $props->documentElement->appendChild($tempNode);
                }
            }
        }
        return $props;
    }

    /**
     * Create custom properties
     *
     * @access public
     * @param array $values
     * @param \DOMDocument $props
     * @return \DOMDocument
     */
    public function createPropertiesCustom($values, $props)
    {
        $tagName = array('text' => 'lpwstr', 'date' => 'filetime', 'number' => 'r8', 'boolean' => 'bool');

        //Now we begin the insertion of the custom properties
        foreach ($values as $key => $value) {

            $myKey = array_keys($value);
            $myValue = array_values($value);

            if (array_key_exists($myKey[0], $tagName)) {
                $customNodes = $props->getElementsByTagName('property');
                $numberNodes = $customNodes->length;
                if ($myValue[0] === true) {
                    $myValue[0] = 1;
                } else if ($myValue[0] === false) {
                    $myValue[0] = 0;
                }
                $strNode = '';
                if ($numberNodes > 0) {
                    $existingPropery = false;
                    for ($j = 0; $j < $numberNodes; $j++) {
                        if ($customNodes->item($j)->getAttribute('name') == $key) {
                            $customNodes->item($j)->firstChild->nodeValue = $this->parseAndCleanTextString($myValue[0]);
                            $existingPropery = true;
                            $strNode = '';
                        } else if (!$existingPropery) {
                            $strNode = '<property fmtid="{D5CDD505-2E9C-101B-9397-08002B2CF9AE}" pid="' . rand(999, 99999999) . '" name="' . $key . '"><vt:' . $tagName[$myKey[0]] . ' xmlns:vt="http://schemas.openxmlformats.org/officeDocument/2006/docPropsVTypes" temp="xxx">' . $this->parseAndCleanTextString((string) $myValue[0]) . '</vt:' . $tagName[$myKey[0]] . '></property>';
                        }
                    }
                } else {
                    $strNode = '<property fmtid="{D5CDD505-2E9C-101B-9397-08002B2CF9AE}" pid="' . rand(999, 99999999) . '" name="' . $key . '"><vt:' . $tagName[$myKey[0]] . ' xmlns:vt="http://schemas.openxmlformats.org/officeDocument/2006/docPropsVTypes"  temp="xxx">' . $this->parseAndCleanTextString((string) $myValue[0]) . '</vt:' . $tagName[$myKey[0]] . '></property>';
                }
                if (!empty($strNode)) {
                    $tempNode = $props->createDocumentFragment();
                    $tempNode->appendXML($strNode);
                    $props->documentElement->appendChild($tempNode);
                }
            }
        }
        $propData = str_replace('xmlns:vt="http://schemas.openxmlformats.org/officeDocument/2006/docPropsVTypes" temp="xxx">', '>', $props->saveXML());

        $xmlUtilities = new XmlUtilities();
        $propsCustom = $xmlUtilities->generateDomDocument($propData);

        return $propsCustom;
    }
}