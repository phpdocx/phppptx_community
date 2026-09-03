<?php
namespace Phppptx\Utilities;
/**
 * XML functions
 *
 * @category   Phppptx
 * @package    utilities
 * @copyright  Copyright (c) Narcea Labs SL
 *             (https://www.narcealabs.com)
 * @license    phppptx Community License
 * @link       https://www.phppptx.com
 */
class XmlUtilities
{
    /**
     * Invalid XML characters regex
     *
     * @access public
     * @static
     * @var string
     */
    public static $invalidXmlChars = '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/';

    /**
     *
     * @access public
     * @static
     * @var mixed
     */
    public static $xmlFlags = null;

    /**
     * Sets LIBXML_PARSEHUGE option
     *
     * @access public
     * @static
     */
    public static function enableHugeXmlMode() {
        self::$xmlFlags = LIBXML_PARSEHUGE;
    }

    /**
     * Cleans a layout content
     *
     * @param string $layoutContent XML content
     * @param array $options
     *      'cleanLayoutParagraphContents' (bool) if false do not remove paragraph contents from the layout. Defaul as true
     *      'cleanSlidePlaceholderTypes' (array) placeholder types to be cleaned from the layout. Default as dt, ftr, hdr, sldNum
     *          Available types: title (Title), body (Body), ctrTitle (Centered Title), subTitle (Subtitle), dt (Date and Time), sldNum (Slide Number), ftr (Footer), hdr (Header), obj (Object), chart (Chart), tbl (Table), clipArt (Clip Art), dgm (Diagram), media (Media), sldImg (Slide Image), pic (Picture)
     * @return string
     */
    public function cleanLayout($layoutContent, $options = array())
    {
        if (!isset($options['cleanLayoutParagraphContents'])) {
            $options['cleanLayoutParagraphContents'] = true;
        }
        if (!isset($options['cleanSlidePlaceholderTypes']) || !is_array($options['cleanSlidePlaceholderTypes'])) {
            $options['cleanSlidePlaceholderTypes'] = array('dt', 'ftr', 'hdr', 'sldNum');
        }

        $layoutContentDOM = $this->generateDomDocument($layoutContent);
        $layoutContentXPath = new \DOMXPath($layoutContentDOM);
        $layoutContentXPath->registerNamespace('p', 'http://schemas.openxmlformats.org/presentationml/2006/main');
        $layoutContentXPath->registerNamespace('a', 'http://schemas.openxmlformats.org/drawingml/2006/main');
        $layoutContentXPath->registerNamespace('r', 'http://schemas.openxmlformats.org/officeDocument/2006/relationships');
        // remove clean slide placeholder types
        foreach ($options['cleanSlidePlaceholderTypes'] as $cleanPlaceholderType) {
            $nodesSpToRemove = $layoutContentXPath->query('//p:sp[.//p:ph[@type="'.$cleanPlaceholderType.'"]]');
            if ($nodesSpToRemove->length > 0) {
                foreach ($nodesSpToRemove as $nodeSpToRemove) {
                    $nodeSpToRemove->parentNode->removeChild($nodeSpToRemove);
                }
            }
        }
        // remove existing p:txBody/a:p/a:r contents to clean the layout
        if ($options['cleanLayoutParagraphContents']) {
            $nodesPRToRemove = $layoutContentXPath->query('//p:txBody/a:p/a:r');
            if ($nodesPRToRemove->length > 0) {
                foreach ($nodesPRToRemove as $nodePRToRemove) {
                    $nodePRToRemove->parentNode->removeChild($nodePRToRemove);
                }
            }
        }
        // remove preserve and type attributes from p:sldLayout. Required by old MS PowerPoint versions
        $nodesSldlayoutToClean = $layoutContentXPath->query('//p:sldLayout');
        if ($nodesSldlayoutToClean->length > 0) {
            $nodesSldlayoutToClean->item(0)->removeAttribute('preserve');
            $nodesSldlayoutToClean->item(0)->removeAttribute('type');
        }
        // only the first a:p must be included. Remove others a:p tags if any
        $nodesPToRemove = $layoutContentXPath->query('//p:txBody/a:p[position()>1]');
        if ($nodesPToRemove->length > 0) {
            foreach ($nodesPToRemove as $nodePToRemove) {
                $nodePToRemove->parentNode->removeChild($nodePToRemove);
            }
        }

        // get the cleaned layout
        $layoutContent = $layoutContentDOM->saveXML();
        $layoutContent = str_replace('p:sldLayout', 'p:sld', $layoutContent);

        return $layoutContent;
    }

    /**
     * Generates a DOM document from an XML string
     *
     * @param string $xml XML content
     * @return \DOMDocument
     */
    public function generateDomDocument($xml)
    {
        $domDocument = new \DOMDocument();
        if (PHP_VERSION_ID < 80000) {
            $optionEntityLoader = libxml_disable_entity_loader(true);
        }
        if (!self::$xmlFlags) {
            $domDocument->loadXML($xml);
        } else {
            $domDocument->loadXML($xml, self::$xmlFlags);
        }
        if (PHP_VERSION_ID < 80000) {
            libxml_disable_entity_loader($optionEntityLoader);
        }

        return $domDocument;
    }

    /**
     * Generates a SimpleXMLElement from an XML string
     *
     * @param string $xml XML content
     * @return \SimpleXMLElement
     */
    public function generateSimpleXmlElement($xml)
    {
        if (PHP_VERSION_ID < 80000) {
            $optionEntityLoader = libxml_disable_entity_loader(true);
        }
        if (!self::$xmlFlags) {
            $simpleXmlElement = simplexml_load_string($xml);
        } else {
            $simpleXmlElement = simplexml_load_string($xml, 'SimpleXMLElement', self::$xmlFlags);
        }
        if (PHP_VERSION_ID < 80000) {
            libxml_disable_entity_loader($optionEntityLoader);
        }

        return $simpleXmlElement;
    }

    /**
     * Parses and clean a text string to be added
     *
     * @access protected
     * @param string $content
     * @return string
     */
    public function parseAndCleanTextString($content)
    {
        $content = htmlspecialchars($content);

        // cleans UTF-8 charset removing not UTF-8 valid chars
        if (\Phppptx\Create\CreatePptx::$cleanUTF8) {
            // removes invalid XML characters
            $content = preg_replace(
                self::$invalidXmlChars,
                '',
                $content
            );
        }

        return $content;
    }
}