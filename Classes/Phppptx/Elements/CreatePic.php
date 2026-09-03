<?php
namespace Phppptx\Elements;

use Phppptx\Logger\PhppptxLogger;
use Phppptx\Resources\OOXMLResources;

/**
 * Create pic content
 *
 * @category   Phppptx
 * @package    elements
 * @copyright  Copyright (c) Narcea Labs SL
 *             (https://www.narcealabs.com)
 * @license    phppptx Community License
 * @link       https://www.phppptx.com
 */
class CreatePic extends CreateElement
{
    /**
     * Adds image in a slide
     *
     * @access public
     * @param \DOMDocument $slideDOM
     * @param array $position
     *      'coordinateX' (int) EMUs (English Metric Unit)
     *      'coordinateY' (int) EMUs (English Metric Unit)
     *      'sizeX' (int) EMUs (English Metric Unit)
     *      'sizeY' (int) EMUs (English Metric Unit)
     *      'name' (string) internal name. If not set, a random name is generated
     *      'order' (int) set the display order. Default after existing contents. 0 is the first order position. If the order position doesn't exist add after existing contents
     * @param array $imageStyles
     *      'border' (array)
     *          'dash' (string) solid, dot, dash, lgDash, dashDot, lgDashDot, lgDashDotDot, sysDash, sysDot, sysDashDot, sysDashDotDot
     *          'color' (string)
     *          'width' (int) EMUs (English Metric Unit). Default as 12700 (1pt)
     *      'contents' (array) image contents
     *      'descr' (string) set a descr value
     *      'dpi' (int) default as 96
     *      'hyperlink' (string) hyperlink
     *      'hyperlinkAction' (string) hyperlink action
     *      'hyperlinkId' (string) hyperlink ID
     *      'resizingFactor' (int) default as 0.75
     *      'rotation' (int) 60.000ths of a degree
     *      'rId' (string)
     *      'transparency' (int) 0 to 100
     * @param array $options
     * @return \DOMNode
     * @throws \Exception position not valid
     * @throws \Exception size not valid
     */
    public function addElementImage($slideDOM, $position, $imageStyles = array(), $options = array())
    {
        // default values
        if (!isset($imageStyles['dpi'])) {
            $imageStyles['dpi'] = 96;
        }
        if (!isset($imageStyles['resizingFactor'])) {
            $imageStyles['resizingFactor'] = 0.75;
        }

        $slideXPath = new \DOMXPath($slideDOM);
        $slideXPath->registerNamespace('p', 'http://schemas.openxmlformats.org/presentationml/2006/main');
        $slideXPath->registerNamespace('a', 'http://schemas.openxmlformats.org/drawingml/2006/main');

        // if not size options are set, get them
        if (!isset($position['sizeX']) && isset($imageStyles['contents']['width'])) {
            // px to EMUs
            if (isset($position['sizeY'])) {
                // get sizeX from the preset sizeY
                $relationSizeY = $position['sizeY'] / ($imageStyles['contents']['height'] * $imageStyles['resizingFactor'] * 12700);
                $position['sizeX'] = round(($imageStyles['contents']['width'] * $imageStyles['resizingFactor'] * 12700) * $relationSizeY);
            } else {
                $position['sizeX'] = $imageStyles['contents']['width'] * $imageStyles['resizingFactor'] * 12700;
                $position['sizeY'] = $imageStyles['contents']['height'] * $imageStyles['resizingFactor'] * 12700;
            }
        }
        if (!isset($position['sizeY']) && isset($imageStyles['contents']['height'])) {
            // px to EMUs
            if (isset($position['sizeX'])) {
                // get sizeY from the preset sizeX
                $relationSizeX = $position['sizeX'] / ($imageStyles['contents']['width'] * $imageStyles['resizingFactor'] * 12700);
                $position['sizeY'] = round(($imageStyles['contents']['height'] * $imageStyles['resizingFactor'] * 12700) * $relationSizeX);
            } else {
                $position['sizeY'] = $imageStyles['contents']['height'] * $imageStyles['resizingFactor'] * 12700;
                $position['sizeX'] = $imageStyles['contents']['width'] * $imageStyles['resizingFactor'] * 12700;
            }
        }

        if (!isset($position['coordinateX']) || !isset($position['coordinateY']) || !isset($position['sizeX']) || !isset($position['sizeY'])) {
            PhppptxLogger::logger('The chosen position is not valid. Use a valid position.', 'fatal');
        }

        $imageContent = OOXMLResources::$skeletonPicImage;

        // add transparency tag
        if (isset($imageStyles['transparency'])) {
            $transparency = '<a:alphaModFix amt="'.((100-(int)$imageStyles['transparency'])*1000).'"/>';
            $imageContent = str_replace('</a:blip>', $transparency . '</a:blip>', $imageContent);
        }

        // insert the new content
        $nodePic = $this->insertNewContentOrder($imageContent, $position, $slideDOM, $slideXPath);

        // picture name
        if (isset($options['name'])) {
            $newPlaceholderName = $options['name'];
        } else if (isset($position['name'])) {
            $newPlaceholderName = $position['name'];
        } else {
            $newPlaceholderName = 'Picture ' . $this->generateUniqueId();
        }
        $newPlaceholderName = $this->parseAndCleanTextString($newPlaceholderName);

        // picture id. Generate a new random one that is not duplicated in the current slide
        $newPlaceholderId = null;
        while (!isset($newPlaceholderId)) {
            $randomId = mt_rand(999, 999999);
            $nodesCnvPrId = $slideXPath->query('//p:cNvPr[@id="'.$randomId.'"]');
            if ($nodesCnvPrId->length == 0) {
                $newPlaceholderId = $randomId;
            }
        }

        // add image attributes and styles

        // hyperlink
        if (isset($imageStyles['hyperlink'])) {
            $hyperlinkContents = $this->handleHyperlinkContent($imageStyles['hyperlink']);
            $contentHyperlink = '<a:hlinkClick '.$hyperlinkContents['hyperlinkRId'].' '.$hyperlinkContents['hyperlinkAction'].' xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships" />';

            $nodesCNvPr = $nodePic->getElementsByTagNameNS('http://schemas.openxmlformats.org/presentationml/2006/main', 'cNvPr');
            $hyperlinkFragment = $slideDOM->createDocumentFragment();
            $hyperlinkFragment->appendXML($contentHyperlink);
            $nodesCNvPr->item(0)->appendChild($hyperlinkFragment);
        }

        // border line
        if (isset($imageStyles['border']) && is_array($imageStyles['border']) && count($imageStyles['border']) > 0) {
            if (!isset($imageStyles['border']['color'])) {
                $imageStyles['border']['color'] = '000000';
            }

            // clean # from color value
            $imageStyles['border']['color'] = str_replace('#', '', $imageStyles['border']['color']);

            if (!isset($imageStyles['border']['width'])) {
                $imageStyles['border']['width'] = 12700;
            }

            $contentDash = '';
            if (isset($imageStyles['border']['dash'])) {
                $contentDash = '<a:prstDash val="'.$imageStyles['border']['dash'].'"/>';
            }

            $contentBorder = '<a:ln w="'.$imageStyles['border']['width'].'" xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main"><a:solidFill><a:srgbClr val="'.$imageStyles['border']['color'].'"></a:srgbClr></a:solidFill>'.$contentDash.'</a:ln>';
            $nodesPrstGeom = $nodePic->getElementsByTagNameNS('http://schemas.openxmlformats.org/drawingml/2006/main', 'prstGeom');
            $borderFragment = $slideDOM->createDocumentFragment();
            $borderFragment->appendXML($contentBorder);
            $nodesPrstGeom->item(0)->parentNode->insertBefore($borderFragment, $nodesPrstGeom->item(0)->nextSibling);
        }

        // id, name and descr attributes
        $nodesCNvPr = $slideXPath->query('.//p:nvPicPr/p:cNvPr', $nodePic);
        if ($nodesCNvPr->length > 0) {
            $nodesCNvPr->item(0)->setAttribute('id', $newPlaceholderId);
            $nodesCNvPr->item(0)->setAttribute('name', $newPlaceholderName);
            if (isset($imageStyles['descr'])) {
                $nodesCNvPr->item(0)->setAttribute('descr', $this->parseAndCleanTextString($imageStyles['descr']));
            }
        }

        // sizes
        $nodesOff = $slideXPath->query('.//p:spPr/a:xfrm/a:off', $nodePic);
        $nodesOff->item(0)->setAttribute('x', $position['coordinateX']);
        $nodesOff->item(0)->setAttribute('y', $position['coordinateY']);
        $nodesExt = $slideXPath->query('.//p:spPr/a:xfrm/a:ext', $nodePic);
        $nodesExt->item(0)->setAttribute('cx', $position['sizeX']);
        $nodesExt->item(0)->setAttribute('cy', $position['sizeY']);

        // rotation
        if (isset($imageStyles['rotation'])) {
            $nodesXfrm = $slideXPath->query('.//p:spPr/a:xfrm', $nodePic);
            if ($nodesXfrm->length > 0) {
                $nodesXfrm->item(0)->setAttribute('rot', (int)$imageStyles['rotation'] * 60000);
            }
        }

        // rId
        $nodesABlip = $slideXPath->query('.//a:blip', $nodePic);
        if ($nodesABlip->length > 0) {
            $nodesABlip->item(0)->setAttribute('r:embed', 'rId' . $imageStyles['rId']);
        }

        return $nodePic;
    }

    /**
     * Adds media in a slide
     *
     * @access public
     * @param \DOMDocument $slideDOM
     * @param array $position
     *      'coordinateX' (int) EMUs (English Metric Unit)
     *      'coordinateY' (int) EMUs (English Metric Unit)
     *      'sizeX' (int) EMUs (English Metric Unit)
     *      'sizeY' (int) EMUs (English Metric Unit)
     *      'name' (string) internal name. If not set, a random name is generated
     *      'order' (int) set the display order. Default after existing contents. 0 is the first order position. If the order position doesn't exist add after existing contents
     * @param array $mediaStyles
     *      'descr' (string) set a descr value
     *      'rIdImage' (string)
     *      'rIdMedia2006' (string)
     *      'rIdMedia2007' (string)
     * @param array $options
     *      'type' (string) audio, video
     * @return \DOMNode
     * @throws \Exception position not valid
     */
    public function addElementMedia($slideDOM, $position, $mediaStyles = array(), $options = array())
    {
        $slideXPath = new \DOMXPath($slideDOM);
        $slideXPath->registerNamespace('p', 'http://schemas.openxmlformats.org/presentationml/2006/main');
        $slideXPath->registerNamespace('a', 'http://schemas.openxmlformats.org/drawingml/2006/main');
        $slideXPath->registerNamespace('p14', 'http://schemas.microsoft.com/office/powerpoint/2010/main');

        if (!isset($position['coordinateX']) || !isset($position['coordinateY']) || !isset($position['sizeX']) || !isset($position['sizeY'])) {
            PhppptxLogger::logger('The chosen position is not valid. Use a valid position.', 'fatal');
        }

        $mediaContent = OOXMLResources::$skeletonPicMedia;
        // set the correct media type
        $mediaContent = str_replace('__PHX=__MEDIA_TYPE_', $options['type'], $mediaContent);

        // insert the new content
        $nodePic = $this->insertNewContentOrder($mediaContent, $position, $slideDOM, $slideXPath);

        // media name
        if (isset($options['name'])) {
            $newPlaceholderName = $options['name'];
        } else if (isset($position['name'])) {
            $newPlaceholderName = $position['name'];
        } else {
            $newPlaceholderName = 'Picture ' . $this->generateUniqueId();
        }
        $newPlaceholderName = $this->parseAndCleanTextString($newPlaceholderName);

        // media id. Generate a new random one that is not duplicated in the current slide
        $newPlaceholderId = null;
        while (!isset($newPlaceholderId)) {
            $randomId = mt_rand(999, 999999);
            $nodesCnvPrId = $slideXPath->query('//p:cNvPr[@id="'.$randomId.'"]');
            if ($nodesCnvPrId->length == 0) {
                $newPlaceholderId = $randomId;
            }
        }

        // add media attributes and styles

        // id, name and descr attributes
        $nodesCNvPr = $slideXPath->query('.//p:nvPicPr/p:cNvPr', $nodePic);
        if ($nodesCNvPr->length > 0) {
            $nodesCNvPr->item(0)->setAttribute('id', $newPlaceholderId);
            $nodesCNvPr->item(0)->setAttribute('name', $newPlaceholderName);
            if (isset($mediaStyles['descr'])) {
                $nodesCNvPr->item(0)->setAttribute('descr', $this->parseAndCleanTextString($mediaStyles['descr']));
            }
        }

        // sizes
        $nodesOff = $slideXPath->query('.//p:spPr/a:xfrm/a:off', $nodePic);
        $nodesOff->item(0)->setAttribute('x', $position['coordinateX']);
        $nodesOff->item(0)->setAttribute('y', $position['coordinateY']);
        $nodesExt = $slideXPath->query('.//p:spPr/a:xfrm/a:ext', $nodePic);
        $nodesExt->item(0)->setAttribute('cx', $position['sizeX']);
        $nodesExt->item(0)->setAttribute('cy', $position['sizeY']);

        // rIds
        $nodesMedia2006 = $slideXPath->query('.//p:nvPr/a:'.$options['type'].'File', $nodePic);
        if ($nodesMedia2006->length > 0) {
            $nodesMedia2006->item(0)->setAttribute('r:link', 'rId' . $mediaStyles['rIdMedia2006']);
        }
        $nodesMedia2007 = $slideXPath->query('.//p:nvPr//p14:media', $nodePic);
        if ($nodesMedia2007->length > 0) {
            $nodesMedia2007->item(0)->setAttribute('r:embed', 'rId' . $mediaStyles['rIdMedia2007']);
        }
        $nodesABlip = $slideXPath->query('.//a:blip', $nodePic);
        if ($nodesABlip->length > 0) {
            $nodesABlip->item(0)->setAttribute('r:embed', 'rId' . $mediaStyles['rIdImage']);
        }

        return $nodePic;
    }

    /**
     * Adds svg in a slide
     *
     * @access public
     * @param \DOMDocument $slideDOM
     * @param array $position
     *      'coordinateX' (int) EMUs (English Metric Unit)
     *      'coordinateY' (int) EMUs (English Metric Unit)
     *      'sizeX' (int) EMUs (English Metric Unit)
     *      'sizeY' (int) EMUs (English Metric Unit)
     *      'name' (string) internal name. If not set, a random name is generated
     *      'order' (int) set the display order. Default after existing contents. 0 is the first order position. If the order position doesn't exist add after existing contents
     * @param array $svgStyles
     *      'border' (array)
     *          'dash' (string) solid, dot, dash, lgDash, dashDot, lgDashDot, lgDashDotDot, sysDash, sysDot, sysDashDot, sysDashDotDot
     *          'color' (string)
     *          'width' (int) EMUs (English Metric Unit). Default as 12700 (1pt)
     *      'contents' (array) image contents
     *      'descr' (string) set a descr value
     *      'resizingFactor' (int) default as 0.75
     *      'rotation' (int) 60.000ths of a degree
     *      'rIdAlt' (string)
     *      'rIdSvg' (string)
     * @param array $options
     * @return \DOMNode
     * @throws \Exception position not valid
     * @throws \Exception size not valid
     */
    public function addElementSvg($slideDOM, $position, $svgStyles = array(), $options = array())
    {
        // default values
        if (!isset($svgStyles['dpi'])) {
            $svgStyles['dpi'] = 96;
        }
        if (!isset($svgStyles['resizingFactor'])) {
            $svgStyles['resizingFactor'] = 0.75;
        }

        $slideXPath = new \DOMXPath($slideDOM);
        $slideXPath->registerNamespace('p', 'http://schemas.openxmlformats.org/presentationml/2006/main');
        $slideXPath->registerNamespace('a', 'http://schemas.openxmlformats.org/drawingml/2006/main');
        $slideXPath->registerNamespace('asvg', 'http://schemas.microsoft.com/office/drawing/2016/SVG/main');

        // if not size options are set, get them
        if (!isset($position['sizeX']) && isset($svgStyles['contents']['width'])) {
            // px to EMUs
            if (isset($position['sizeY'])) {
                // get sizeX from the preset sizeY
                $relationSizeY = $position['sizeY'] / ($svgStyles['contents']['height'] * $svgStyles['resizingFactor'] * 12700);
                $position['sizeX'] = round(($svgStyles['contents']['width'] * $svgStyles['resizingFactor'] * 12700) * $relationSizeY);
            } else {
                $position['sizeX'] = $svgStyles['contents']['width'] * $svgStyles['resizingFactor'] * 12700;
                $position['sizeY'] = $svgStyles['contents']['height'] * $svgStyles['resizingFactor'] * 12700;
            }
        }
        if (!isset($position['sizeY']) && isset($svgStyles['contents']['height'])) {
            // px to EMUs
            if (isset($position['sizeX'])) {
                // get sizeY from the preset sizeX
                $relationSizeX = $position['sizeX'] / ($svgStyles['contents']['width'] * $svgStyles['resizingFactor'] * 12700);
                $position['sizeY'] = round(($svgStyles['contents']['height'] * $svgStyles['resizingFactor'] * 12700) * $relationSizeX);
            } else {
                $position['sizeY'] = $svgStyles['contents']['height'] * $svgStyles['resizingFactor'] * 12700;
                $position['sizeX'] = $svgStyles['contents']['width'] * $svgStyles['resizingFactor'] * 12700;
            }
        }

        if (!isset($position['coordinateX']) || !isset($position['coordinateY']) || !isset($position['sizeX']) || !isset($position['sizeY'])) {
            PhppptxLogger::logger('The chosen position is not valid. Use a valid position.', 'fatal');
        }

        $svgContent = OOXMLResources::$skeletonPicSvg;

        // insert the new content
        $nodePic = $this->insertNewContentOrder($svgContent, $position, $slideDOM, $slideXPath);

        // picture name
        if (isset($options['name'])) {
            $newPlaceholderName = $options['name'];
        } else if (isset($position['name'])) {
            $newPlaceholderName = $position['name'];
        } else {
            $newPlaceholderName = 'Picture ' . $this->generateUniqueId();
        }
        $newPlaceholderName = $this->parseAndCleanTextString($newPlaceholderName);

        // picture id. Generate a new random one that is not duplicated in the current slide
        $newPlaceholderId = null;
        while (!isset($newPlaceholderId)) {
            $randomId = mt_rand(999, 999999);
            $nodesCnvPrId = $slideXPath->query('//p:cNvPr[@id="'.$randomId.'"]');
            if ($nodesCnvPrId->length == 0) {
                $newPlaceholderId = $randomId;
            }
        }

        // add image attributes and styles

        // border line
        if (isset($svgStyles['border']) && is_array($svgStyles['border']) && count($svgStyles['border']) > 0) {
            if (!isset($svgStyles['border']['color'])) {
                $svgStyles['border']['color'] = '000000';
            }

            // clean # from color value
            $svgStyles['border']['color'] = str_replace('#', '', $svgStyles['border']['color']);

            if (!isset($svgStyles['border']['width'])) {
                $svgStyles['border']['width'] = 12700;
            }

            $contentDash = '';
            if (isset($svgStyles['border']['dash'])) {
                $contentDash = '<a:prstDash val="'.$svgStyles['border']['dash'].'"/>';
            }

            $contentBorder = '<a:ln w="'.$svgStyles['border']['width'].'" xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main"><a:solidFill><a:srgbClr val="'.$svgStyles['border']['color'].'"></a:srgbClr></a:solidFill>'.$contentDash.'</a:ln>';
            $nodesPrstGeom = $nodePic->getElementsByTagNameNS('http://schemas.openxmlformats.org/drawingml/2006/main', 'prstGeom');
            $borderFragment = $slideDOM->createDocumentFragment();
            $borderFragment->appendXML($contentBorder);
            $nodesPrstGeom->item(0)->parentNode->insertBefore($borderFragment, $nodesPrstGeom->item(0)->nextSibling);
        }

        // id, name and descr attributes
        $nodesCNvPr = $slideXPath->query('.//p:nvPicPr/p:cNvPr', $nodePic);
        if ($nodesCNvPr->length > 0) {
            $nodesCNvPr->item(0)->setAttribute('id', $newPlaceholderId);
            $nodesCNvPr->item(0)->setAttribute('name', $newPlaceholderName);
            if (isset($svgStyles['descr'])) {
                $nodesCNvPr->item(0)->setAttribute('descr', $this->parseAndCleanTextString($svgStyles['descr']));
            }
        }

        // sizes
        $nodesOff = $slideXPath->query('.//p:spPr/a:xfrm/a:off', $nodePic);
        $nodesOff->item(0)->setAttribute('x', $position['coordinateX']);
        $nodesOff->item(0)->setAttribute('y', $position['coordinateY']);
        $nodesExt = $slideXPath->query('.//p:spPr/a:xfrm/a:ext', $nodePic);
        $nodesExt->item(0)->setAttribute('cx', $position['sizeX']);
        $nodesExt->item(0)->setAttribute('cy', $position['sizeY']);

        // rotation
        if (isset($svgStyles['rotation'])) {
            $nodesXfrm = $slideXPath->query('.//p:spPr/a:xfrm', $nodePic);
            if ($nodesXfrm->length > 0) {
                $nodesXfrm->item(0)->setAttribute('rot', (int)$svgStyles['rotation'] * 60000);
            }
        }

        // rIdAlt
        $nodesABlip = $slideXPath->query('.//a:blip', $nodePic);
        if ($nodesABlip->length > 0) {
            $nodesABlip->item(0)->setAttribute('r:embed', 'rId' . $svgStyles['rIdAlt']);
        }

        // rIdSvg
        $nodesSvgBlip = $slideXPath->query('.//asvg:svgBlip', $nodePic);
        if ($nodesSvgBlip->length > 0) {
            $nodesSvgBlip->item(0)->setAttribute('r:embed', 'rId' . $svgStyles['rIdSvg']);
        }

        return $nodePic;
    }
}