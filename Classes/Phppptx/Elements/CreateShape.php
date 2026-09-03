<?php
namespace Phppptx\Elements;

use Phppptx\Logger\PhppptxLogger;

/**
 * Create shape
 *
 * @category   Phppptx
 * @package    elements
 * @copyright  Copyright (c) Narcea Labs SL
 *             (https://www.narcealabs.com)
 * @license    phppptx Community License
 * @link       https://www.phppptx.com
 */
class CreateShape extends CreateElement
{
    /**
     * Generate a new shape
     *
     * @access public
     * @param \DOMDocument $slideDOM
     * @param mixed $type Shape type
     * @param array $position
     *      'coordinateX' (int) EMUs (English Metric Unit)
     *      'coordinateY' (int) EMUs (English Metric Unit)
     *      'sizeX' (int) EMUs (English Metric Unit)
     *      'sizeY' (int) EMUs (English Metric Unit)
     *      'name' (string) internal name. If not set, a random name is generated
     *      'order' (int) set the display order. Default after existing contents. 0 is the first order position. If the order position doesn't exist add after existing contents
     * @param array $options
     *      'customGeom' (string) custom geometry
     *      'fillColor' (string) FF0000, 00FFFF,...
     *      'fillColorTransparency' (int) 0 to 100
     *      'flipH' (bool) flipped horizontally. Default as false
     *      'flipV' (bool) flipped vertically. Default as false
     *      'imageContent' (mixed) image path, base64 or stream. Image formats: png, jpg, jpeg, gif, bmp, webp
     *      'name' (string) set a name value
     *      'outlineColor' (string) FF0000, 00FFFF,...
     *      'rId' (string) shape ID
     *      'rIdImage' (string) image ID
     *      'rotation' (int) 60.000ths of a degree
     *      'tailEnd' (string) arrow, diamond, none, oval, stealth, triangle
     *      'textContent' (PptxFragment)
     * @throws \Exception position not valid
     * @return \DOMNode
     */
    public function addElementShape($slideDOM, $type, $position, $options = array())
    {
        $slideXPath = new \DOMXPath($slideDOM);
        $slideXPath->registerNamespace('p', 'http://schemas.openxmlformats.org/presentationml/2006/main');
        $slideXPath->registerNamespace('a', 'http://schemas.openxmlformats.org/drawingml/2006/main');

        if (!isset($position['coordinateX']) || !isset($position['coordinateY']) || !isset($position['sizeX']) || !isset($position['sizeY'])) {
            PhppptxLogger::logger('The chosen position is not valid. Use a valid position.', 'fatal');
        }

        $name = 'Shape ' . $options['rId'];
        if (isset($options['name'])) {
            $name = $this->parseAndCleanTextString($options['name']);
        }

        // shape id. Generate a new random one that is not duplicated in the current slide
        $cNvPrId = null;
        while (!isset($cNvPrId)) {
            $randomId = rand(999999, 999999999);
            $nodesCnvPrId = $slideXPath->query('//p:cNvPr[@id="'.$randomId.'"]');
            if ($nodesCnvPrId->length == 0) {
                $cNvPrId = $randomId;
            }
        }

        $fillColorContents = '';
        if (isset($options['fillColor'])) {
            $fillColorContents = '<a:solidFill><a:srgbClr val="'.strtoupper(str_replace('#', '', $options['fillColor'])).'">';
            if (isset($options['fillColorTransparency'])) {
                $fillColorContents .= '<a:alpha val="'.((100 - (int)$options['fillColorTransparency'])*1000).'"/>';
            }
            $fillColorContents .= '</a:srgbClr></a:solidFill>';
        }
        $outlineContents = '';
        if (isset($options['outlineColor']) || isset($options['tailEnd'])) {
            $outlineContents = '<a:ln>';
            if (isset($options['outlineColor'])) {
                $outlineContents .= '<a:solidFill><a:srgbClr val="'.strtoupper(str_replace('#', '', $options['outlineColor'])).'"/></a:solidFill>';
            }
            if (isset($options['tailEnd'])) {
                $outlineContents .= '<a:tailEnd type="'.$options['tailEnd'].'"/>';
            }

            $outlineContents .= '</a:ln>';
        }
        $flipped = '';
        if (isset($options['flipV']) && $options['flipV']) {
            $flipped = ' flipV="1"';
        }
        if (isset($options['flipH']) && $options['flipH']) {
            $flipped .= ' flipH="1"';
        }
        $rotation = '';
        if (isset($options['rotation'])) {
            $rotation = ' rot="'.((int)$options['rotation']*60000).'"';
        }

        $prstGeomContents = '<a:prstGeom prst="'.$type.'"><a:avLst/></a:prstGeom>';
        if (isset($options['shapeGuide']) && isset($options['shapeGuide']['fmla']) && isset($options['shapeGuide']['guide'])) {
            $prstGeomContents = '<a:prstGeom prst="'.$type.'"><a:avLst><a:gd name="'.$options['shapeGuide']['guide'].'" fmla="'.$options['shapeGuide']['fmla'].'"/></a:avLst></a:prstGeom>';
        }
        if (isset($options['customGeom'])) {
            $prstGeomContents = '<a:custGeom>'.$options['customGeom'].'</a:custGeom>';
        }
        $styleContents = '<p:style><a:lnRef idx="2"><a:schemeClr val="accent1"><a:shade val="15000"/></a:schemeClr></a:lnRef><a:fillRef idx="1"><a:schemeClr val="accent1"/></a:fillRef><a:effectRef idx="0"><a:schemeClr val="accent1"/></a:effectRef><a:fontRef idx="minor"><a:schemeClr val="lt1"/></a:fontRef></p:style>';
        $textContents = '<a:p xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main" />';
        if (isset($options['textContents']) && $options['textContents'] instanceof PptxFragment) {
            $textContents = (string)$options['textContents'];

            // handle external relationships such as hyperlinks
            $externalRelationships = $options['textContents']->getExternalRelationships();
            foreach ($externalRelationships as $externalRelationship) {
                $this->externalRelationships[] = $externalRelationship;
            }
        }

        $imageContents = '';
        if (isset($options['imageContent']) && isset($options['rIdImage'])) {
            $imageContents = '<a:blipFill dpi="0" rotWithShape="1"><a:blip r:embed="rId'.$options['rIdImage'].'" xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships" /><a:srcRect/><a:stretch/></a:blipFill>';
        }

        $bodyPrContents = '<a:bodyPr/>';
        if (isset($options['textDirection']) || isset($options['verticalAlign'])) {
            $bodyPrContents = '<a:bodyPr ';
            if (isset($options['verticalAlign'])) {
                if ($options['verticalAlign'] == 'top') {
                    // no set any value
                } else if ($options['verticalAlign'] == 'middle') {
                    $bodyPrContents .= 'anchor="ctr" ';
                } else if ($options['verticalAlign'] == 'bottom') {
                    $bodyPrContents .= 'anchor="b" ';
                }
            }
            if (isset($options['textDirection'])) {
                $bodyPrContents .= 'vert="'.$options['textDirection'].'" ';
            }
            $bodyPrContents .= '/>';
        }

        $shapeContent = '<p:sp xmlns:p="http://schemas.openxmlformats.org/presentationml/2006/main" xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main"><p:nvSpPr><p:cNvPr id="'.$cNvPrId.'" name="'.$name.'"></p:cNvPr><p:cNvSpPr/><p:nvPr/></p:nvSpPr><p:spPr><a:xfrm'.$flipped.$rotation.'><a:off x="'.$position['coordinateX'].'" y="'.$position['coordinateY'].'"/><a:ext cx="'.$position['sizeX'].'" cy="'.$position['sizeY'].'"/></a:xfrm>'.$prstGeomContents.$imageContents.$fillColorContents.$outlineContents.'</p:spPr><p:txBody>'.$bodyPrContents.'<a:lstStyle/>'.$textContents.'</p:txBody></p:sp>';

        // insert the new content
        $nodeShape = $this->insertNewContentOrder($shapeContent, $position, $slideDOM, $slideXPath);

        return $nodeShape;
    }
}