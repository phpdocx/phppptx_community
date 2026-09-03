<?php
namespace Phppptx\Elements;

use Phppptx\Logger\PhppptxLogger;
use Phppptx\Resources\OOXMLResources;

/**
 * Create text box
 *
 * @category   Phppptx
 * @package    elements
 * @copyright  Copyright (c) Narcea Labs SL
 *             (https://www.narcealabs.com)
 * @license    phppptx Community License
 * @link       https://www.phppptx.com
 */
class CreateTextBox extends CreateElement
{
    /**
     * Adds text box in a slide
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
     * @param array $textBoxStyles
     *      'autofit' (string) autofit (default), noautofit, shrink
     *      'border' (array)
     *          'cap' (string) rnd, sq, flat
     *          'dash' (string) solid, dot, dash, lgDash, dashDot, lgDashDot, lgDashDotDot, sysDash, sysDot, sysDashDot, sysDashDotDot
     *          'color' (string) default as 000000
     *          'transparency' (int) 0 to 100
     *          'width' (int) EMUs (English Metric Unit). Default as 12700 (1pt)
     *      'columns' (array)
     *          'number' (int)
     *          'spacing' (int) EMUs (English Metric Unit)
     *      'descr' (string)
     *      'margin' (array)
     *          'bottom' (int) EMUs (English Metric Unit)
     *          'left' (int) EMUs (English Metric Unit)
     *          'right' (int) EMUs (English Metric Unit)
     *          'top' (int) EMUs (English Metric Unit)
     *      'fill' (array)
     *          'color' (string) FFFF00, CCCCCC ...
     *          'image' (string) image
     *          'imageAsTexture' (bool) Default as false
     *          'transparency' (int) 0 to 100
     *      'rotation' (int) 60.000ths of a degree
     *      'textDirection' (string) horz, vert, vert270, wordArtVert, eaVert, mongolianVert, wordArtVertRtl
     *      'verticalAlign' (string) top, middle, bottom, topCentered, middleCentered, bottomCentered
     *      'wrap' (string) square, none
     * Theme (Premium licenses):
     * 'theme' (array):
     *      'fill' (array)
     *          'color' (string) accent1, accent2, accent3, accent4, accent5, accent6, dk1, dk2, folHlink, hlink, lt1, lt2...
     * @param array $options
     * @return \DOMNode
     * @throws \Exception position not valid
     */
    public function addElementTextBox($slideDOM, $position, $textBoxStyles = array(), $options = array())
    {
        $slideXPath = new \DOMXPath($slideDOM);
        $slideXPath->registerNamespace('p', 'http://schemas.openxmlformats.org/presentationml/2006/main');
        $slideXPath->registerNamespace('a', 'http://schemas.openxmlformats.org/drawingml/2006/main');

        if (!isset($position['coordinateX']) || !isset($position['coordinateY']) || !isset($position['sizeX']) || !isset($position['sizeY'])) {
            PhppptxLogger::logger('The chosen position is not valid. Use a valid position.', 'fatal');
        }

        $textBoxContent = OOXMLResources::$skeletonShape;

        // handle autofit text value
        if (isset($textBoxStyles['autofit'])) {
            if ($textBoxStyles['autofit'] == 'noautofit') {
                $textBoxContent = str_replace('<a:spAutoFit/>', '<a:noAutofit/>', $textBoxContent);
            } else if ($textBoxStyles['autofit'] == 'shrink') {
                $textBoxContent = str_replace('<a:spAutoFit/>', '<a:normAutofit fontScale="100000" lnSpcReduction="10000"/>', $textBoxContent);
            }
        }

        // insert the new content
        $nodePSp = $this->insertNewContentOrder($textBoxContent, $position, $slideDOM, $slideXPath);

        // text box name
        $newTextBoxName = 'TextBox ' . $this->generateUniqueId();
        if (isset($position['name'])) {
            $newTextBoxName = $position['name'];
        }
        if (isset($options['name'])) {
            $newTextBoxName = $options['name'];
        }

        // text box id. Generate a new random one that is not duplicated in the current slide
        $newTextBoxId = null;
        while (!isset($newTextBoxId)) {
            $randomId = mt_rand(999, 999999);
            $nodesCnvPrId = $slideXPath->query('//p:cNvPr[@id="'.$randomId.'"]');
            if ($nodesCnvPrId->length == 0) {
                $newTextBoxId = $randomId;
            }
        }

        // add text box attributes and styles

        // fill
        if ((isset($textBoxStyles['fill']) && is_array($textBoxStyles['fill']) && count($textBoxStyles['fill']) > 0) || (isset($textBoxStyles['theme']) && is_array($textBoxStyles['theme']) && isset($textBoxStyles['theme']['fill']))) {
            // fill using a color
            if (isset($textBoxStyles['fill']['color']) || (isset($textBoxStyles['theme']) && is_array($textBoxStyles['theme']) && isset($textBoxStyles['theme']['fill']['color']))) {
                if (isset($textBoxStyles['theme']) && is_array($textBoxStyles['theme']) && isset($textBoxStyles['theme']['fill']['color'])) {
                    $contentColor = '<a:solidFill xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main"><a:schemeClr val="'.$textBoxStyles['theme']['fill']['color'].'"></a:schemeClr></a:solidFill>';
                } else {
                    // clean # from color value
                    $textBoxStyles['fill']['color'] = str_replace('#', '', $textBoxStyles['fill']['color']);
                    $contentColor = '<a:solidFill xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main"><a:srgbClr val="'.$textBoxStyles['fill']['color'].'">';
                    if (isset($textBoxStyles['fill']['transparency'])) {
                        $contentColor .= '<a:alpha val="'.((100-(int)$textBoxStyles['fill']['transparency'])*1000).'"/>';
                    }
                    $contentColor .= '</a:srgbClr></a:solidFill>';
                }

                $nodesSpPr = $nodePSp->getElementsByTagNameNS('http://schemas.openxmlformats.org/presentationml/2006/main', 'spPr');
                if ($nodesSpPr->length > 0) {

                    $colorFragment = $slideDOM->createDocumentFragment();
                    $colorFragment->appendXML($contentColor);
                    $nodesSpPr->item(0)->appendChild($colorFragment);
                }
            }

            // fill using an image
            if (isset($textBoxStyles['fill']['image']) && isset($textBoxStyles['fill']['imageId'])) {
                $nodesSpPr = $nodePSp->getElementsByTagNameNS('http://schemas.openxmlformats.org/presentationml/2006/main', 'spPr');
                if ($nodesSpPr->length > 0) {
                    $contentImageAlpha = '';
                    if (isset($textBoxStyles['fill']['transparency'])) {
                        $contentImageAlpha = '<a:alphaModFix amt="'.((100-(int)$textBoxStyles['fill']['transparency'])*1000).'"/>';
                    }
                    $contentImageFillType = '<a:stretch><a:fillRect/></a:stretch>';
                    if (isset($textBoxStyles['fill']['imageAsTexture'])) {
                        $contentImageFillType = '<a:tile algn="tl" flip="none" sx="100000" sy="100000" tx="0" ty="0"/>';
                    }

                    $contentImage = '<a:blipFill dpi="0" rotWithShape="1" xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"><a:blip r:embed="rId'.$textBoxStyles['fill']['imageId'].'">'.$contentImageAlpha.'</a:blip><a:srcRect/>'.$contentImageFillType.'</a:blipFill>';

                    $imageFragment = $slideDOM->createDocumentFragment();
                    $imageFragment->appendXML($contentImage);
                    $nodesSpPr->item(0)->appendChild($imageFragment);
                }
            }
        }

        // border line
        if (isset($textBoxStyles['border']) && is_array($textBoxStyles['border']) && count($textBoxStyles['border']) > 0) {
            if (!isset($textBoxStyles['border']['color'])) {
                $textBoxStyles['border']['color'] = '000000';
            }

            // clean # from color value
            $textBoxStyles['border']['color'] = str_replace('#', '', $textBoxStyles['border']['color']);

            if (!isset($textBoxStyles['border']['width'])) {
                $textBoxStyles['border']['width'] = 12700;
            }

            $contentCap = '';
            if (isset($textBoxStyles['border']['cap'])) {
                $contentCap = ' cap="'.$textBoxStyles['cap'].'" ';
            }

            $contentAlpha = '';
            if (isset($textBoxStyles['border']['transparency'])) {
                $contentAlpha = '<a:alpha val="'.((100-(int)$textBoxStyles['border']['transparency'])*1000).'"/>';
            }

            $contentDash = '';
            if (isset($textBoxStyles['border']['dash'])) {
                $contentDash = '<a:prstDash val="'.$textBoxStyles['border']['dash'].'"/>';
            }

            $contentBorder = '<a:ln '.$contentCap.' w="'.$textBoxStyles['border']['width'].'" xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main"><a:solidFill><a:srgbClr val="'.$textBoxStyles['border']['color'].'">'.$contentAlpha.'</a:srgbClr></a:solidFill>'.$contentDash.'</a:ln>';

            // there's a fill, add the border after it
            $nodesSolidFill = $nodePSp->getElementsByTagNameNS('http://schemas.openxmlformats.org/drawingml/2006/main', 'solidFill');
            $nodeReferenceBorder = null;
            if ($nodesSolidFill->length > 0) {
                $nodeReferenceBorder = $nodesSolidFill->item(0);
            }
            // there's no fill, add the image at last prstGeom child after a new a:noFill tag
            if (!isset($nodeReferenceBorder)) {
                $nodesSpPr = $nodePSp->getElementsByTagNameNS('http://schemas.openxmlformats.org/presentationml/2006/main', 'spPr');
                if ($nodesSpPr->length > 0) {
                    $contentNoFill = '<a:noFill xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main"/>';
                    $noFillFragment = $slideDOM->createDocumentFragment();
                    $noFillFragment->appendXML($contentNoFill);
                    $nodeReferenceBorder = $nodesSpPr->item(0)->appendChild($noFillFragment);
                }
            }

            $borderFragment = $slideDOM->createDocumentFragment();
            $borderFragment->appendXML($contentBorder);
            $nodeReferenceBorder->parentNode->insertBefore($borderFragment, $nodeReferenceBorder->nextSibling);
        }

        // id, name and descr attributes
        $nodesCNvPr = $slideXPath->query('.//p:nvSpPr/p:cNvPr', $nodePSp);
        if ($nodesCNvPr->length > 0) {
            $nodesCNvPr->item(0)->setAttribute('id', $newTextBoxId);
            $nodesCNvPr->item(0)->setAttribute('name', $newTextBoxName);
            if (isset($textBoxStyles['descr'])) {
                $nodesCNvPr->item(0)->setAttribute('descr', $textBoxStyles['descr']);
            }
        }

        // sizes
        $nodesOff = $slideXPath->query('.//p:spPr/a:xfrm/a:off', $nodePSp);
        $nodesOff->item(0)->setAttribute('x', $position['coordinateX']);
        $nodesOff->item(0)->setAttribute('y', $position['coordinateY']);
        $nodesExt = $slideXPath->query('.//p:spPr/a:xfrm/a:ext', $nodePSp);
        $nodesExt->item(0)->setAttribute('cx', $position['sizeX']);
        $nodesExt->item(0)->setAttribute('cy', $position['sizeY']);

        // rotation
        if (isset($textBoxStyles['rotation'])) {
            $nodesXfrm = $slideXPath->query('.//p:spPr/a:xfrm', $nodePSp);
            if ($nodesXfrm->length > 0) {
                $nodesXfrm->item(0)->setAttribute('rot', (int)$textBoxStyles['rotation'] * 60000);
            }
        }

        // bodyPr styles
        if (isset($textBoxStyles['columns']) || isset($textBoxStyles['margin']) || isset($textBoxStyles['textDirection']) || isset($textBoxStyles['verticalAlign']) || isset($textBoxStyles['wrap'])) {
            $nodesBodyPr = $slideXPath->query('.//p:txBody/a:bodyPr', $nodePSp);
            if ($nodesBodyPr->length > 0) {
                if (isset($textBoxStyles['columns'])) {
                    if (isset($textBoxStyles['columns']['number'])) {
                        $nodesBodyPr->item(0)->setAttribute('numCol', $textBoxStyles['columns']['number']);
                    }
                    if (isset($textBoxStyles['columns']['spacing'])) {
                        $nodesBodyPr->item(0)->setAttribute('spcCol', $textBoxStyles['columns']['spacing']);
                    }
                }
                if (isset($textBoxStyles['margin'])) {
                    if (isset($textBoxStyles['margin']['bottom'])) {
                        $nodesBodyPr->item(0)->setAttribute('bIns', $textBoxStyles['margin']['bottom']);
                    }
                    if (isset($textBoxStyles['margin']['left'])) {
                        $nodesBodyPr->item(0)->setAttribute('lIns', $textBoxStyles['margin']['left']);
                    }
                    if (isset($textBoxStyles['margin']['right'])) {
                        $nodesBodyPr->item(0)->setAttribute('rIns', $textBoxStyles['margin']['right']);
                    }
                    if (isset($textBoxStyles['margin']['top'])) {
                        $nodesBodyPr->item(0)->setAttribute('tIns', $textBoxStyles['margin']['top']);
                    }
                }
                if (isset($textBoxStyles['textDirection'])) {
                    $nodesBodyPr->item(0)->setAttribute('vert', $textBoxStyles['textDirection']);
                }
                if (isset($textBoxStyles['verticalAlign'])) {
                    // normalize values
                    $anchorValue = null;
                    $anchorCtrValue = null;
                    if ($textBoxStyles['verticalAlign'] == 'top') {
                        // no set any value
                    } else if ($textBoxStyles['verticalAlign'] == 'middle') {
                        $anchorValue = 'ctr';
                        $anchorCtrValue = '0';
                    } else if ($textBoxStyles['verticalAlign'] == 'bottom') {
                        $anchorValue = 'b';
                        $anchorCtrValue = '0';
                    } else if ($textBoxStyles['verticalAlign'] == 'topCentered') {
                        $anchorValue = 't';
                        $anchorCtrValue = '1';
                    } else if ($textBoxStyles['verticalAlign'] == 'middleCentered') {
                        $anchorValue = 'ctr';
                        $anchorCtrValue = '1';
                    } else if ($textBoxStyles['verticalAlign'] == 'bottomCentered') {
                        $anchorValue = 'b';
                        $anchorCtrValue = '1';
                    }

                    if (isset($anchorValue)) {
                        $nodesBodyPr->item(0)->setAttribute('anchor', $anchorValue);
                    }
                    if (isset($anchorCtrValue)) {
                        $nodesBodyPr->item(0)->setAttribute('anchorCtr', $anchorCtrValue);
                    }
                }
                if (isset($textBoxStyles['wrap'])) {
                    $nodesBodyPr->item(0)->setAttribute('wrap', $textBoxStyles['wrap']);
                }
            }
        }

        return $nodePSp;
    }
}