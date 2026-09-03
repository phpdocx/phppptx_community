<?php
namespace Phppptx\Elements;

use Phppptx\Logger\PhppptxLogger;
use Phppptx\Resources\OOXMLResources;

/**
 * Create graphicFrame content
 *
 * @category   Phppptx
 * @package    elements
 * @copyright  Copyright (c) Narcea Labs SL
 *             (https://www.narcealabs.com)
 * @license    phppptx Community License
 * @link       https://www.phppptx.com
 */
class CreateGraphicFrame extends CreateElement
{
    /**
     * Adds graphicFrame chart in a slide
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
     * @param array $chartStyles
     *      'rId' (string)
     * @param array $options
     * @return \DOMNode
     * @throws \Exception position not valid
     */
    public function addElementGraphicFrameChart($slideDOM, $position, $chartStyles = array(), $options = array())
    {
        $slideXPath = new \DOMXPath($slideDOM);
        $slideXPath->registerNamespace('p', 'http://schemas.openxmlformats.org/presentationml/2006/main');
        $slideXPath->registerNamespace('a', 'http://schemas.openxmlformats.org/drawingml/2006/main');
        $slideXPath->registerNamespace('c', 'http://schemas.openxmlformats.org/drawingml/2006/chart');

        if (!isset($position['coordinateX']) || !isset($position['coordinateY']) || !isset($position['sizeX']) || !isset($position['sizeY'])) {
            PhppptxLogger::logger('The chosen position is not valid. Use a valid position.', 'fatal');
        }

        $graphicFrameContent = OOXMLResources::$skeletonGraphicFrameChart;

        // insert the new content
        $nodeGraphicFrame = $this->insertNewContentOrder($graphicFrameContent, $position, $slideDOM, $slideXPath);

        // chart name
        if (isset($options['name'])) {
            $newPlaceholderName = $options['name'];
        } else if (isset($position['name'])) {
            $newPlaceholderName = $position['name'];
        } else {
            $newPlaceholderName = 'Chart ' . $this->generateUniqueId();
        }

        // chart id. Generate a new random one that is not duplicated in the current slide
        $newPlaceholderId = null;
        while (!isset($newPlaceholderId)) {
            $randomId = mt_rand(999, 999999);
            $nodesCnvPrId = $slideXPath->query('//p:cNvPr[@id="'.$randomId.'"]');
            if ($nodesCnvPrId->length == 0) {
                $newPlaceholderId = $randomId;
            }
        }

        // add chart attributes and styles

        // id, name and descr attributes
        $nodesCNvPr = $slideXPath->query('.//p:nvGraphicFramePr/p:cNvPr', $nodeGraphicFrame);
        if ($nodesCNvPr->length > 0) {
            $nodesCNvPr->item(0)->setAttribute('id', $newPlaceholderId);
            $nodesCNvPr->item(0)->setAttribute('name', $newPlaceholderName);
        }

        // sizes
        $nodesOff = $slideXPath->query('.//p:xfrm/a:off', $nodeGraphicFrame);
        $nodesOff->item(0)->setAttribute('x', $position['coordinateX']);
        $nodesOff->item(0)->setAttribute('y', $position['coordinateY']);
        $nodesExt = $slideXPath->query('.//p:xfrm/a:ext', $nodeGraphicFrame);
        $nodesExt->item(0)->setAttribute('cx', $position['sizeX']);
        $nodesExt->item(0)->setAttribute('cy', $position['sizeY']);

        // rId
        $nodesABlip = $slideXPath->query('.//a:graphic//c:chart', $nodeGraphicFrame);
        if ($nodesABlip->length > 0) {
            $nodesABlip->item(0)->setAttribute('r:id', 'rId' . $chartStyles['rId']);
        }

        return $nodeGraphicFrame;
    }

    /**
     * Adds graphicFrame table in a slide
     *
     * @access public
     * @param \DOMDocument $slideDOM
     * @param array $contents array of contents or PptxFragments
     *      'text' (string) @see addText
     *      'align' (string) left, center, right, justify
     *      'backgroundColor' (string)
     *      'border' (array) 'top', 'right', 'bottom', 'left' keys can be used to set borders
     *          'dash' (string) solid, dot, dash, lgDash, dashDot, lgDashDot, lgDashDotDot, sysDash, sysDot, sysDashDot, sysDashDotDot
     *          'color' (string) default as 000000. none to avoid adding the color
     *          'width' (int) EMUs (English Metric Unit). Default as 12700 (1pt)
     *      'cellMargin' (array)
     *          'top' (int)
     *          'right' (int)
     *          'bottom' (int)
     *          'left' (int)
     *      'colspan' (int)
     *      'rowspan' (int)
     *      'textDirection' (string) horz, vert, vert270, wordArtVert, eaVert, mongolianVert, wordArtVertRtl
     *      'verticalAlign' (string) top, middle, bottom, topCentered, middleCentered, bottomCentered
     *      'wrap' (string) square, none
     * @param array $position
     *      'new' (array) a new text box is generated
     *          'coordinateX' (int) EMUs (English Metric Unit)
     *          'coordinateY' (int) EMUs (English Metric Unit)
     *          'sizeX' (int) EMUs (English Metric Unit)
     *          'sizeY' (int) EMUs (English Metric Unit)
     *          'name' (string) internal name. If not set, a random name is generated
     *          'order' (int) set the display order. Default after existing contents. 0 is the first order position. If the order position doesn't exist add after existing contents
     * @param array $tableStyles
     *      'backgroundColor' (string) HEX color
     *      'bandedColumns' (bool) default as false
     *      'bandedRows' (bool) default as false
     *      'border' (array)
     *          'dash' (string) solid, dot, dash, lgDash, dashDot, lgDashDot, lgDashDotDot, sysDash, sysDot, sysDashDot, sysDashDotDot
     *          'color' (string) default as 000000
     *          'width' (int) EMUs (English Metric Unit). Default as 12700 (1pt)
     *      'cellMargin' (array)
     *          'top' (int)
     *          'right' (int)
     *          'bottom' (int)
     *          'left' (int)
     *      'columnWidths' (int|array) column width fix (int) or column width variable (array). If not set, get from the shape size. EMUs (English Metric Unit)
     *      'descr' (string) alt text (descr) value
     *      'firstColumn' (bool) default as false
     *      'headerRow' (bool) default as false
     *      'lastColumn' (bool) default as false
     *      'rtl' (bool) default as false
     *      'style' (string) table style name. Using a template, if the table style doesn't exist, try to import it from the base template
     *      'totalRow' (bool) default as false
     * @param array $rowStyles
     *      'height' (int)
     * @param array $options
     * @return \DOMNode
     * @throws \Exception position not valid
     */
    public function addElementGraphicFrameTable($slideDOM, $contents, $position, $tableStyles = array(), $rowStyles = array(), $options = array())
    {
        $slideXPath = new \DOMXPath($slideDOM);
        $slideXPath->registerNamespace('p', 'http://schemas.openxmlformats.org/presentationml/2006/main');
        $slideXPath->registerNamespace('a', 'http://schemas.openxmlformats.org/drawingml/2006/main');

        if (!isset($position['coordinateX']) || !isset($position['coordinateY']) || !isset($position['sizeX']) || !isset($position['sizeY'])) {
            PhppptxLogger::logger('The chosen position is not valid. Use a valid position.', 'fatal');
        }

        $graphicFrameContent = OOXMLResources::$skeletonGraphicFrameTable;

        // insert the new content
        $nodeGraphicFrame = $this->insertNewContentOrder($graphicFrameContent, $position, $slideDOM, $slideXPath);

        // table name
        if (isset($options['name'])) {
            $newPlaceholderName = $options['name'];
        } else if (isset($position['name'])) {
            $newPlaceholderName = $position['name'];
        } else {
            $newPlaceholderName = 'Table ' . $this->generateUniqueId();
        }

        // chart id. Generate a new random one that is not duplicated in the current slide
        $newPlaceholderId = null;
        while (!isset($newPlaceholderId)) {
            $randomId = mt_rand(999, 999999);
            $nodesCnvPrId = $slideXPath->query('//p:cNvPr[@id="'.$randomId.'"]');
            if ($nodesCnvPrId->length == 0) {
                $newPlaceholderId = $randomId;
            }
        }

        // add table attributes and styles

        // id, name and descr attributes
        $nodesCNvPr = $slideXPath->query('.//p:nvGraphicFramePr/p:cNvPr', $nodeGraphicFrame);
        if ($nodesCNvPr->length > 0) {
            $nodesCNvPr->item(0)->setAttribute('id', $newPlaceholderId);
            $nodesCNvPr->item(0)->setAttribute('name', $newPlaceholderName);
            if (isset($tableStyles['descr'])) {
                $nodesCNvPr->item(0)->setAttribute('descr', $tableStyles['descr']);
            }
        }

        // sizes
        $nodesOff = $slideXPath->query('.//p:xfrm/a:off', $nodeGraphicFrame);
        $nodesOff->item(0)->setAttribute('x', $position['coordinateX']);
        $nodesOff->item(0)->setAttribute('y', $position['coordinateY']);
        $nodesExt = $slideXPath->query('.//p:xfrm/a:ext', $nodeGraphicFrame);
        $nodesExt->item(0)->setAttribute('cx', $position['sizeX']);
        $nodesExt->item(0)->setAttribute('cy', $position['sizeY']);

        // add table contents
        if (is_array($contents) && count($contents) > 0) {
            // normalize table data
            $contents = $this->parseTableData($contents);

            $tableContents = '<a:tbl xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main" xmlns:p="http://schemas.openxmlformats.org/presentationml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">';

            //  table styles
            $tableContentsStyles = '<a:tblPr';
            if (isset($tableStyles['bandedColumns']) && $tableStyles['bandedColumns']) {
                $tableContentsStyles .= ' bandCol="1" ';
            }
            if (isset($tableStyles['bandedRows']) && $tableStyles['bandedRows']) {
                $tableContentsStyles .= ' bandRow="1" ';
            }
            if (isset($tableStyles['firstColumn']) && $tableStyles['firstColumn']) {
                $tableContentsStyles .= ' firstCol="1" ';
            }
            if (isset($tableStyles['headerRow']) && $tableStyles['headerRow']) {
                $tableContentsStyles .= ' firstRow="1" ';
            }
            if (isset($tableStyles['lastColumn']) && $tableStyles['lastColumn']) {
                $tableContentsStyles .= ' lastCol="1" ';
            }
            if (isset($tableStyles['rtl']) && $tableStyles['rtl']) {
                $tableContentsStyles .= ' rtl="1" ';
            }
            if (isset($tableStyles['totalRow']) && $tableStyles['totalRow']) {
                $tableContentsStyles .= ' lastRow="1" ';
            }
            $tableContentsStyles .= '>';
            if (isset($tableStyles['styleId'])) {
                $tableContentsStyles .= '<a:tableStyleId>'.$tableStyles['styleId'].'</a:tableStyleId>';
            }

            $tableContentsStyles .= '</a:tblPr>';

            // generate the table grid
            $tableContentsGrid = '<a:tblGrid>';
            // get the row with the maximum columns to generate the correct gridCol
            $numberColumnsTable = 1;
            foreach ($contents as $content) {
                $countColumns = count($content);
                if ($countColumns > $numberColumnsTable) {
                    $numberColumnsTable = $countColumns;
                }
            }
            // col sizes
            // col size from shape
            $colSizeFromShapeSize = (int)($position['sizeX']/$numberColumnsTable);

            for ($i = 0; $i < $numberColumnsTable; $i++) {
                if (isset($tableStyles['columnWidths'])) {
                    // fixed sizes
                    if (is_array($tableStyles['columnWidths'])) {
                        if (isset($tableStyles['columnWidths'][$i])) {
                            $colSize = $tableStyles['columnWidths'][$i];
                        } else {
                            // column not set, use the shape size
                            $colSize = $colSizeFromShapeSize;
                        }
                    } else {
                        // the same size for all columns
                        $colSize = $tableStyles['columnWidths'];
                    }
                } else {
                    // shape size
                    $colSize = $colSizeFromShapeSize;
                }

                $tableContentsGrid .= '<a:gridCol w="'.$colSize.'"></a:gridCol>';
            }
            $tableContentsGrid .= '</a:tblGrid>';

            // row sizes
            if (count($contents) > 0) {
                // row size from shape
                $rowSizeFromShapeSize = (int)($position['sizeY']/count($contents));
            }

            // iterate the table to add rows and cells
            $iRow = 0;
            $tableContentsRows = '';
            foreach ($contents as $row) {
                // calculate the row size
                if (isset($rowStyles[$iRow]) && isset($rowStyles[$iRow]['height'])) {
                    // fixed size
                    $rowSize = $rowStyles[$iRow]['height'];
                } else {
                    // shape size
                    $rowSize = $rowSizeFromShapeSize;
                }

                // row contents
                $tableContentsRows .= '<a:tr h="'.$rowSize.'">';
                foreach ($row as $cell) {
                    $gridSpanValue = '';
                    $tableContentCells = '';
                    $colspanInProgress = 0;
                    if (isset($cell['colspan'])) {
                        $gridSpanValue = ' gridSpan="'.$cell['colspan'].'"';
                        $colspanInProgress = (int)$cell['colspan'] - 1;
                    }
                    $rowSpanValue = '';
                    if (isset($cell['rowspan'])) {
                        $rowSpanValue = ' rowSpan="'.$cell['rowspan'].'"';
                    }
                    $vmergeValue = '';
                    if (isset($cell['vmerge']) && $cell['vmerge'] == 'continue') {
                        $vmergeValue = ' vMerge="1"';
                    }
                    // cell contents
                    $tableContentCells .= '<a:tc'.$gridSpanValue.$rowSpanValue.$vmergeValue.'><a:txBody><a:bodyPr/><a:lstStyle/>';

                    // allow adding empty images
                    if (isset($cell['image']) && !isset($cell['text'])) {
                        $cell['text'] = '';
                    }

                    // allow string as $contents instead of an array. Transform string to array
                    if (!is_array($cell) && !($cell instanceof PptxFragment)) {
                        $contentsNormalized = array();
                        $contentsNormalized['text'] = $cell;
                        $cell = $contentsNormalized;
                    }

                    $newContentText = '';
                    if (!($cell instanceof PptxFragment) && !($cell['text'] instanceof PptxFragment)) {
                        // get paragraph styles
                        $cellStyles = array();
                        if (isset($cell['align'])) {
                            $cellStyles['align'] = $cell['align'];
                        }
                        if (!isset($cell['text'])) {
                            $cell['text'] = '';
                        }
                        if (isset($cell['wrap'])) {
                            $cellStyles['wrap'] = $cell['wrap'];
                        }

                        // not PptxFragment. Create the text tags
                        $text = new CreateText();
                        $newContentText .= $text->createElementText(array($cell), $cellStyles);
                    } else {
                        // PptxFragment
                        if ($cell instanceof PptxFragment) {
                            $newContentText .= (string)$cell;

                            // handle external relationships such as hyperlinks
                            $externalRelationships = $cell->getExternalRelationships();
                            foreach ($externalRelationships as $externalRelationship) {
                                $this->externalRelationships[] = $externalRelationship;
                            }
                        } else if (isset($cell['text']) && $cell['text'] instanceof PptxFragment) {
                            $newContentText .= (string)$cell['text'];

                            // handle external relationships such as hyperlinks
                            $externalRelationships = $cell['text']->getExternalRelationships();
                            foreach ($externalRelationships as $externalRelationship) {
                                $this->externalRelationships[] = $externalRelationship;
                            }
                        }
                    }

                    $tableContentCells .= $newContentText;

                    $tableContentCells .= '</a:txBody>';

                    // cell styles
                    if (isset($tableStyles['border']) || isset($cell['border']) || isset($tableStyles['cellMargin']) || isset($cell['cellMargin']) || isset($tableStyles['backgroundColor']) || isset($cell['backgroundColor']) || isset($cell['textDirection'])) {
                        $tableCellStyles = '<a:tcPr';

                        // inline styles

                        // cell margins
                        if (isset($tableStyles['cellMargin']) || isset($cell['cellMargin'])) {
                            $marginStyles = array(
                                'top' => '',
                                'right' => '',
                                'bottom' => '',
                                'left' => '',
                            );

                            foreach (array('top', 'right', 'bottom', 'left') as $position) {
                                // table margin styles
                                if (isset($tableStyles['cellMargin']) && isset($tableStyles['cellMargin'][$position])) {
                                    $marginStyles[$position] = $tableStyles['cellMargin'][$position];
                                }

                                // cell margin styles
                                if (isset($cell['cellMargin']) && isset($cell['cellMargin'][$position])) {
                                    $marginStyles[$position] = $cell['cellMargin'][$position];
                                }
                            }

                            if (!empty($marginStyles['left'])) {
                                $tableCellStyles .= ' marL="'.$marginStyles['left'].'" ';
                            }
                            if (!empty($marginStyles['right'])) {
                                $tableCellStyles .= ' marR="'.$marginStyles['right'].'" ';
                            }
                            if (!empty($marginStyles['top'])) {
                                $tableCellStyles .= ' marT="'.$marginStyles['top'].'" ';
                            }
                            if (!empty($marginStyles['bottom'])) {
                                $tableCellStyles .= ' marB="'.$marginStyles['bottom'].'" ';
                            }
                        }

                        // text rotation
                        if (isset($cell['textDirection'])) {
                            $tableCellStyles .= ' vert="'.$cell['textDirection'].'" ';
                        }

                        // vertical align
                        if (isset($cell['verticalAlign'])) {
                            // normalize values
                            $anchorValue = null;
                            $anchorCtrValue = null;
                            if ($cell['verticalAlign'] == 'top') {
                                // no set any value
                            } else if ($cell['verticalAlign'] == 'middle') {
                                $anchorValue = 'ctr';
                                $anchorCtrValue = '0';
                            } else if ($cell['verticalAlign'] == 'bottom') {
                                $anchorValue = 'b';
                                $anchorCtrValue = '0';
                            } else if ($cell['verticalAlign'] == 'topCentered') {
                                $anchorValue = 't';
                                $anchorCtrValue = '1';
                            } else if ($cell['verticalAlign'] == 'middleCentered') {
                                $anchorValue = 'ctr';
                                $anchorCtrValue = '1';
                            } else if ($cell['verticalAlign'] == 'bottomCentered') {
                                $anchorValue = 'b';
                                $anchorCtrValue = '1';
                            }

                            if (isset($anchorValue) && isset($anchorCtrValue)) {
                                $tableCellStyles .= ' anchor="'.$anchorValue.'" anchorCtr="'.$anchorCtrValue.'" ';
                            }
                        }

                        $tableCellStyles .= '>';

                        // external styles

                        // cell borders
                        if (isset($tableStyles['border']) || isset($cell['border'])) {
                            $borderStyles = array(
                                'top' => array(),
                                'right' => array(),
                                'bottom' => array(),
                                'left' => array(),
                            );
                            if (isset($tableStyles['border'])) {
                                // table border styles

                                if (!isset($tableStyles['border']['dash'])) {
                                    // default dash
                                    $tableStyles['border']['dash'] = 'solid';
                                }

                                $borderStyles['top']['dash'] = $tableStyles['border']['dash'];
                                $borderStyles['right']['dash'] = $tableStyles['border']['dash'];
                                $borderStyles['bottom']['dash'] = $tableStyles['border']['dash'];
                                $borderStyles['left']['dash'] = $tableStyles['border']['dash'];

                                if (!isset($tableStyles['border']['color'])) {
                                    // default color
                                    $tableStyles['border']['color'] = '000000';
                                }

                                $borderStyles['top']['color'] = $tableStyles['border']['color'];
                                $borderStyles['right']['color'] = $tableStyles['border']['color'];
                                $borderStyles['bottom']['color'] = $tableStyles['border']['color'];
                                $borderStyles['left']['color'] = $tableStyles['border']['color'];

                                if (!isset($tableStyles['border']['width'])) {
                                    // default width
                                    $tableStyles['border']['width'] = 12700;
                                }

                                $borderStyles['top']['width'] = $tableStyles['border']['width'];
                                $borderStyles['right']['width'] = $tableStyles['border']['width'];
                                $borderStyles['bottom']['width'] = $tableStyles['border']['width'];
                                $borderStyles['left']['width'] = $tableStyles['border']['width'];
                            }
                            if (isset($cell['border'])) {
                                // cell border styles

                                foreach (array('dash', 'color', 'width') as $property) {
                                    foreach (array('top', 'right', 'bottom', 'left') as $position) {
                                        // default border styles
                                        if (isset($cell['border'][$property])) {
                                            $borderStyles[$position][$property] = $cell['border'][$property];
                                        }
                                        // position border styles
                                        if (isset($cell['border'][$position]) && isset($cell['border'][$position][$property])) {
                                            $borderStyles[$position][$property] = $cell['border'][$position][$property];
                                        }
                                    }
                                }

                                // set default options if some style is not set
                                foreach ($borderStyles as $borderStylesKey => $borderStylesValue) {
                                    if (!isset($borderStyles[$borderStylesKey]['dash'])) {
                                        $borderStyles[$borderStylesKey]['dash'] = 'solid';
                                    }
                                    if (!isset($borderStyles[$borderStylesKey]['color'])) {
                                        $borderStyles[$borderStylesKey]['color'] = '000000';
                                    }
                                    if (!isset($borderStyles[$borderStylesKey]['width'])) {
                                        $borderStyles[$borderStylesKey]['width'] = 12700;
                                    }
                                }
                            }

                            if (count($borderStyles['left']) > 0) {
                                if ($borderStyles['left']['color'] == 'none') {
                                    $colorBorder = '<a:noFill/>';
                                } else {
                                    $colorBorder = '<a:solidFill><a:srgbClr val="'.str_replace('#', '', $borderStyles['left']['color']).'"/></a:solidFill>';
                                }

                                $tableCellStyles .= '<a:lnL algn="ctr" cap="flat" cmpd="sng" w="'.$borderStyles['left']['width'].'">'.$colorBorder.'<a:prstDash val="'.$borderStyles['left']['dash'].'"/><a:round/><a:headEnd len="med" type="none" w="med"/><a:tailEnd len="med" type="none" w="med"/></a:lnL>';
                            }
                            if (count($borderStyles['right']) > 0) {
                                if ($borderStyles['right']['color'] == 'none') {
                                    $colorBorder = '<a:noFill/>';
                                } else {
                                    $colorBorder = '<a:solidFill><a:srgbClr val="'.str_replace('#', '', $borderStyles['right']['color']).'"/></a:solidFill>';
                                }

                                $tableCellStyles .= '<a:lnR algn="ctr" cap="flat" cmpd="sng" w="'.$borderStyles['right']['width'].'">'.$colorBorder.'<a:prstDash val="'.$borderStyles['right']['dash'].'"/><a:round/><a:headEnd len="med" type="none" w="med"/><a:tailEnd len="med" type="none" w="med"/></a:lnR>';
                            }
                            if (count($borderStyles['top']) > 0) {
                                if ($borderStyles['top']['color'] == 'none') {
                                    $colorBorder = '<a:noFill/>';
                                } else {
                                    $colorBorder = '<a:solidFill><a:srgbClr val="'.str_replace('#', '', $borderStyles['top']['color']).'"/></a:solidFill>';
                                }

                                $tableCellStyles .= '<a:lnT algn="ctr" cap="flat" cmpd="sng" w="'.$borderStyles['top']['width'].'">'.$colorBorder.'<a:prstDash val="'.$borderStyles['top']['dash'].'"/><a:round/><a:headEnd len="med" type="none" w="med"/><a:tailEnd len="med" type="none" w="med"/></a:lnT>';
                            }
                            if (count($borderStyles['bottom']) > 0) {
                                if ($borderStyles['bottom']['color'] == 'none') {
                                    $colorBorder = '<a:noFill/>';
                                } else {
                                    $colorBorder = '<a:solidFill><a:srgbClr val="'.str_replace('#', '', $borderStyles['bottom']['color']).'"/></a:solidFill>';
                                }

                                $tableCellStyles .= '<a:lnB algn="ctr" cap="flat" cmpd="sng" w="'.$borderStyles['bottom']['width'].'">'.$colorBorder.'<a:prstDash val="'.$borderStyles['bottom']['dash'].'"/><a:round/><a:headEnd len="med" type="none" w="med"/><a:tailEnd len="med" type="none" w="med"/></a:lnB>';
                            }
                        }

                        // cell background colors
                        if (isset($tableStyles['backgroundColor']) || isset($cell['backgroundColor'])) {
                            $backgroundColor = '';
                            if (isset($tableStyles['backgroundColor'])) {
                                $backgroundColor = str_replace('#', '', $tableStyles['backgroundColor']);
                            }
                            if (isset($cell['backgroundColor'])) {
                                $backgroundColor = str_replace('#', '', $cell['backgroundColor']);
                            }

                            if (!empty($backgroundColor)) {
                                $tableCellStyles .= '<a:solidFill><a:srgbClr val="'.$backgroundColor.'"/></a:solidFill>';
                            }
                        }

                        if (isset($cell['image']) && isset($cell['image']['xml']) && !empty($cell['image']['xml'])) {
                            $tableCellStyles .= $cell['image']['xml'];
                        }

                        $tableCellStyles .= '</a:tcPr>';
                    } else {
                        // empty cell styles
                        $tableCellStyles = '<a:tcPr>';

                        if (isset($cell['image']) && isset($cell['image']['xml']) && !empty($cell['image']['xml'])) {
                            $tableCellStyles .= $cell['image']['xml'];
                        }

                        $tableCellStyles .= '</a:tcPr>';
                    }

                    $tableContentCells .= $tableCellStyles . '</a:tc>';

                    // generate pending tc tags if a colspan exists
                    if ($colspanInProgress > 0) {
                        $contentsTc = $tableContentCells;
                        for ($iColspan = 0; $iColspan < $colspanInProgress; $iColspan++) {
                            $tableContentCells .= str_replace($gridSpanValue, ' hMerge="1"', $contentsTc);
                        }
                    }
                    $tableContentsRows .= $tableContentCells;
                }

                $tableContentsRows .= '</a:tr>';

                $iRow++;
            }

            $tableContents .= $tableContentsStyles . $tableContentsGrid . $tableContentsRows . '</a:tbl>';

            // add the table contents as graphicData child
            $newTableFragment = $slideDOM->createDocumentFragment();
            $newTableFragment->appendXML($tableContents);
            $nodesGraphicData = $nodeGraphicFrame->getElementsByTagNameNS('http://schemas.openxmlformats.org/drawingml/2006/main', 'graphicData');
            if ($nodesGraphicData->length > 0) {
                $nodesGraphicData->item(0)->appendChild($newTableFragment);
            }
        }

        return $nodeGraphicFrame;
    }

    /**
     * Prepares the table data for insertion
     *
     * @access private
     * @param array $tableData
     * @return array
     */
    private function parseTableData($tableData)
    {
        $parsedData = array();
        $colCount = array();
        foreach ($tableData as $rowNumber => $row) {
            $parsedData[$rowNumber] = array();
            $colNumber = 0;
            foreach ($row as $col => $cell) {
                // check if in the previous row there was a cell with rowspan > 1
                while (isset($parsedData[$rowNumber - 1][$colNumber]['rowspan']) &&
                $parsedData[$rowNumber - 1][$colNumber]['rowspan'] > 1) {
                    // replicate the array
                    $parsedData[$rowNumber][$colNumber] = $parsedData[$rowNumber - 1][$colNumber];
                    // reduce by one the rowspan
                    $parsedData[$rowNumber][$colNumber]['rowspan'] = $parsedData[$rowNumber - 1][$colNumber]['rowspan'] - 1;
                    // set up the vmerge and content values
                    $parsedData[$rowNumber][$colNumber]['vmerge'] = 'continue';
                    $parsedData[$rowNumber][$colNumber]['text'] = null;
                    if (isset($parsedData[$rowNumber - 1][$colNumber]['colspan'])) {
                        $colNumber += $parsedData[$rowNumber - 1][$colNumber]['colspan'];
                    } else {
                        $colNumber++;
                    }
                }
                if (is_array($cell)) {
                    $parsedData[$rowNumber][$colNumber] = $cell;
                } else {
                    $parsedData[$rowNumber][$colNumber]['text'] = $cell;
                }
                if (isset($parsedData[$rowNumber][$colNumber]['colspan'])) {
                    $colNumber += $parsedData[$rowNumber][$colNumber]['colspan'];
                } else {
                    $colNumber++;
                }
            }
            // check that there are no trailing rawspans after running through all cols
            if ($rowNumber > 0) {
                $colDiff = $colCount[$rowNumber - 1] - $colNumber;
                if ($colDiff > 0) {
                    for ($k = 0; $k < $colDiff; $k++) {
                        //Check if in the previous row there was a cell with rowspan > 1
                        while (isset($parsedData[$rowNumber - 1][$colNumber]['rowspan']) &&
                        $parsedData[$rowNumber - 1][$colNumber]['rowspan'] > 1) {
                            //replicate the array
                            $parsedData[$rowNumber][$colNumber] = $parsedData[$rowNumber - 1][$colNumber];
                            //reduce by one the rowspan
                            $parsedData[$rowNumber][$colNumber]['rowspan'] = $parsedData[$rowNumber - 1][$colNumber]['rowspan'] - 1;
                            //set up the vmerge and content values
                            $parsedData[$rowNumber][$colNumber]['vmerge'] = 'continue';
                            $parsedData[$rowNumber][$colNumber]['text'] = NULL;
                            if (isset($parsedData[$rowNumber - 1][$colNumber]['colspan'])) {
                                $colNumber += $parsedData[$rowNumber - 1][$colNumber]['colspan'];
                            } else {
                                $colNumber++;
                            }
                        }
                    }
                }
            }
            $colCount[$rowNumber] = $colNumber;
        }
        return $parsedData;
    }
}