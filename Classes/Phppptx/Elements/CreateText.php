<?php
namespace Phppptx\Elements;
/**
 * Create text
 *
 * @category   Phppptx
 * @package    elements
 * @copyright  Copyright (c) Narcea Labs SL
 *             (https://www.narcealabs.com)
 * @license    phppptx Community License
 * @link       https://www.phppptx.com
 */
class CreateText extends CreateElement
{
    /**
     * Creates text
     *
     * @access public
     * @param array $contents Text contents and styles
     *      'text' (string)
     *      'bold' (bool)
     *      'characterSpacing' (int)
     *      'color' (string) FFFFFF, FF0000 ...
     *      'font' (string) Arial, Times New Roman ...
     *      'fontSize' (int) 8, 9, 10, 11 ...
     *      'highlight' (string)
     *      'hyperlink' (string) hyperlink. External, bookmark (#firstslide, #lastslide, #nextslide, #previousslide) or slide (#slide + position)
     *      'italic' (bool)
     *      'strikethrough' (bool)
     *      'subscript' (bool)
     *      'superscript' (bool)
     *      'transparency' (int) 0 to 100. Use with the color option
     *      'underline' (string) single, double
     * Theme (Premium licenses):
     * 'theme' (array):
     *      'color' (string) accent1, accent2, accent3, accent4, accent5, accent6, dk1, dk2, folHlink, hlink, lt1, lt2...
     * @param array $paragraphStyles
     *      'align' (string) left, center, right, justify, distributed
     *      'indentation' (int) EMUs (English Metric Unit)
     *      'lineSpacing' (int|float) 1, 1.5, 2...
     *      'listLevel' (int)
     *      'listStyles' (array) @see addList
     *      'marginLeft' (int) EMUs (English Metric Unit) (0 >= and <= 51206400)
     *      'marginRight' (int) EMUs (English Metric Unit) (0 >= and <= 51206400)
     *      'noBullet' (bool) no bullet added. Default as true
     *      'parseLineBreaks' (bool) if true parses the line breaks. Default as false
     *      'rtl' (bool) RTL
     *      'spacingAfter' (int) points (0 >= and <= 158400)
     *      'spacingBefore' (int) points (0 >= and <= 158400)
     * @return string
     */
    public function createElementText($contents, $paragraphStyles = array())
    {
        $paragraphContents = '';

        if (is_array($contents) && count($contents) > 0) {
            $paragraphContents .= '<a:p xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main">';

            // a:p styles
            if (count($paragraphStyles) > 0) {
                if (\Phppptx\Create\CreatePptx::$rtl && !isset($paragraphStyles['rtl'])) {
                    // rtl handle
                    $paragraphStyles['rtl'] = \Phppptx\Create\CreatePptx::$rtl;
                    if (!isset($paragraphStyles['align'])) {
                        $paragraphStyles['align'] = 'right';
                    }
                }
                $paragraphContents .= '<a:pPr ' . $this->generateInlinePprStyles($paragraphStyles) . '>';
                $paragraphContents .= $this->generateExternalPprStyles($paragraphStyles);
                $paragraphContents .= '</a:pPr>';
            }

            foreach ($contents as $content) {
                if (!is_array($content)) {
                    $content = array('text' => $content);
                }

                if (isset($paragraphStyles['parseLineBreaks']) && $paragraphStyles['parseLineBreaks']) {
                    // parse line breaks
                    $content['text'] = str_replace(array('\n\r', '\r\n', '\n', '\r', "\n\r", "\r\n", "\n", "\r"), '<a:br/>', $content['text']);

                    // get breaks from the text content
                    $contentsBreaks = explode('<a:br/>', $content['text']);
                    foreach ($contentsBreaks as $indexContentsBreak => $contentsBreak) {
                        $paragraphContents .= '<a:r xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main">';

                        // a:r styles
                        $paragraphContents .= '<a:rPr ' . $this->generateInlineRprStyles($content) . '>';
                        $paragraphContents .= $this->generateExternalRprStyles($content);
                        $paragraphContents .= '</a:rPr>';

                        // text content
                        $paragraphContents .= '<a:t>' . $this->parseAndCleanTextString($contentsBreak) . '</a:t>';

                        $paragraphContents .= '</a:r>';

                        // generate break tag
                        if (isset($contentsBreaks[$indexContentsBreak + 1])) {
                            $paragraphContents .= '<a:br xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main">';
                            $paragraphContents .= '<a:rPr ' . $this->generateInlineRprStyles($content) . '>';
                            $paragraphContents .= $this->generateExternalRprStyles($content);
                            $paragraphContents .= '</a:rPr>';
                            $paragraphContents .= '</a:br>';
                        }
                    }
                } else {
                    $paragraphContents .= '<a:r xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main">';

                    // a:r styles
                    $paragraphContents .= '<a:rPr ' . $this->generateInlineRprStyles($content) . '>';
                    $paragraphContents .= $this->generateExternalRprStyles($content);
                    $paragraphContents .= '</a:rPr>';

                    // text content
                    $paragraphContents .= '<a:t>' . $this->parseAndCleanTextString($content['text']) . '</a:t>';

                    $paragraphContents .= '</a:r>';
                }
            }

            $paragraphContents .= '</a:p>';
        }

        return $paragraphContents;
    }

    /**
     * Generate external pPr styles
     *
     * @access protected
     * @param array $styles
     * @return string
     */
    protected function generateExternalPprStyles($styles)
    {
        $stylesContent = array();

        // keep a:pPr correct order
        if (isset($styles['lineSpacing'])) {
            $stylesContent[] = '<a:lnSpc><a:spcPct val="'.($styles['lineSpacing']*100000).'"/></a:lnSpc>';
        }
        if (isset($styles['spacingBefore']) && $styles['spacingBefore']) {
            $stylesContent[] = '<a:spcBef><a:spcPts val="'.($styles['spacingBefore']*100).'"/></a:spcBef>';
        }
        if (isset($styles['spacingAfter'])) {
            $stylesContent[] = '<a:spcAft><a:spcPts val="'.($styles['spacingAfter']*100).'"/></a:spcAft>';
        }
        if (isset($styles['noBullet']) && $styles['noBullet']) {
            $stylesContent[] = '<a:buNone/>';
        }
        if (isset($styles['listStyles'])) {
            if (isset($styles['listStyles']['color'])) {
                $stylesContent[] = '<a:buClr><a:srgbClr val="'.str_replace('#', '', $styles['listStyles']['color']).'"/></a:buClr>';
            }
            if (isset($styles['listStyles']['size'])) {
                $stylesContent[] = '<a:buSzPct val="'.((int)$styles['listStyles']['size']*1000).'"/>';
            }
            if (isset($styles['listStyles']['font'])) {
                $stylesContent[] = '<a:buFont typeface="'.$styles['listStyles']['font'].'"/>';
            }
            if (isset($styles['listStyles']['type'])) {
                $isBullet = false;
                $buChar = '';
                $font = '';

                // normalize values and get if it's bullet or numbering
                switch ($styles['listStyles']['type']) {
                    case 'filledRoundBullet':
                        $isBullet = true;
                        $buChar = '•';
                        $font = 'Arial';
                        break;
                    case 'filledSquareBullet':
                        $isBullet = true;
                        $buChar = '§';
                        $font = 'Wingdings';
                        break;
                    case 'hollowRoundBullet':
                        $isBullet = true;
                        $buChar = 'o';
                        $font = 'Arial';
                        break;
                    case 'hollowSquareBullet':
                        $isBullet = true;
                        $buChar = 'q';
                        $font = 'Wingdings';
                        break;
                    case 'starBullet':
                        $isBullet = true;
                        $buChar = 'v';
                        $font = 'Wingdings';
                        break;
                    case 'arrowBullet':
                        $isBullet = true;
                        $buChar = 'Ø';
                        $font = 'Wingdings';
                        break;
                    case 'checkmarkBullet':
                        $isBullet = true;
                        $buChar = 'ü';
                        $font = 'Wingdings';
                        break;
                    case 'decimal':
                        $styles['listStyles']['type'] = 'arabicPeriod';
                        break;
                    case 'romanUpperCase':
                        $styles['listStyles']['type'] = 'romanUcPeriod';
                        break;
                    case 'romanLowerCase':
                        $styles['listStyles']['type'] = 'romanLcPeriod';
                        break;
                    case 'alphaUpperCase':
                        $styles['listStyles']['type'] = 'alphaUcPeriod';
                        break;
                    case 'alphaLowerCase':
                        $styles['listStyles']['type'] = 'alphaLcPeriod';
                        break;
                    default:
                        break;
                }

                if ($isBullet) {
                    // bullet
                    if (!empty($buChar)) {
                        if (!isset($styles['listStyles']['font']) && !empty($font)) {
                            // default font
                            $stylesContent[] = '<a:buFont typeface="'.$font.'"/>';
                        }
                        $stylesContent[] = '<a:buChar char="'.$buChar.'"/>';
                    }
                } else {
                    // numbering
                    $startAtContent = '';
                    if (isset($styles['listStyles']['startAt'])) {
                        $startAtContent = ' startAt="'.$styles['listStyles']['startAt'].'" ';
                    }

                    $stylesContent[] = '<a:buAutoNum '.$startAtContent.' type="'.$styles['listStyles']['type'].'"/>';
                }
            }
        }

        $newStyles = implode('', $stylesContent);

        return $newStyles;
    }

    /**
     * Generate external rPr styles
     *
     * @access protected
     * @param array $styles
     * @return string
     */
    protected function generateExternalRprStyles($styles)
    {
        $stylesContent = array();

        // keep a:rPr correct order
        if (isset($styles['color']) || (isset($styles['theme']) && is_array($styles['theme']) && isset($styles['theme']['color']))) {
            $color = '<a:solidFill xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main">';
            if (isset($styles['theme']) && is_array($styles['theme']) && isset($styles['theme']['color'])) {
                $color .= '<a:schemeClr val="'.$styles['theme']['color'].'"/>';
            } else {
                $color .= '<a:srgbClr val="'.str_replace('#', '', $styles['color']).'">';
                if (isset($styles['transparency'])) {
                    $color .= '<a:alpha val="'.((100-(int)$styles['transparency'])*1000).'"/>';
                }
                $color .= '</a:srgbClr>';
            }
            $color .= '</a:solidFill>';
            $stylesContent[] = $color;
        }
        if (isset($styles['highlight'])) {
            $stylesContent[] = '<a:highlight><a:srgbClr val="'.str_replace('#', '', $styles['highlight']).'"/></a:highlight>';
        }
        if (isset($styles['font'])) {
            $stylesContent[] = '<a:latin typeface="'.$styles['font'].'"/><a:cs typeface="'.$styles['font'].'"/>';
        }
        if (isset($styles['hyperlink'])) {
            $hyperlinkContents = $this->handleHyperlinkContent($styles['hyperlink']);
            $stylesContent[] = '<a:hlinkClick '.$hyperlinkContents['hyperlinkRId'].' '.$hyperlinkContents['hyperlinkAction'].' xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships" />';
        }

        $newStyles = implode('', $stylesContent);

        return $newStyles;
    }

    /**
     * Generate inline pPr styles
     *
     * @access protected
     * @param array $styles
     * @return string
     */
    protected function generateInlinePprStyles($styles)
    {
        $stylesContent = array();

        if (isset($styles['align'])) {
            if ($styles['align'] == 'center') {
                $stylesContent[] = 'algn="ctr"';
            } else if ($styles['align'] == 'distributed') {
                $stylesContent[] = 'algn="dist"';
            } else if ($styles['align'] == 'left') {
                $stylesContent[] = 'algn="l"';
            } else if ($styles['align'] == 'justify') {
                $stylesContent[] = 'algn="just"';
            } else if ($styles['align'] == 'right') {
                $stylesContent[] = 'algn="r"';
            }
        }
        if (isset($styles['indentation'])) {
            $stylesContent[] = 'indent="'.$styles['indentation'].'"';
        }
        if (isset($styles['marginLeft'])) {
            $stylesContent[] = 'marL="'.$styles['marginLeft'].'"';
        }
        if (isset($styles['marginRight'])) {
            $stylesContent[] = 'marR="'.$styles['marginRight'].'"';
        }
        if (isset($styles['rtl']) && $styles['rtl']) {
            $stylesContent[] = 'rtl="1"';
        } else if (isset($styles['rtl']) && !$styles['rtl']) {
            $stylesContent[] = 'rtl="0"';
        }
        if (isset($styles['listLevel']) && $styles['listLevel'] > 0) {
            $stylesContent[] = 'lvl="'.$styles['listLevel'].'"';
        }
        if (isset($styles['listStyles'])) {
            if (isset($styles['listStyles']['marginLeft'])){
                $stylesContent[] = 'marL="'.$styles['listStyles']['marginLeft'].'"';
            }
            if (isset($styles['listStyles']['marginRight'])){
                $stylesContent[] = 'marR="'.$styles['listStyles']['marginRight'].'"';
            }
            if (isset($styles['listStyles']['indent'])){
                $stylesContent[] = 'indent="'.$styles['listStyles']['indent'].'"';
            } else {
                $stylesContent[] = 'indent="-250000"';
            }
        }

        $newStyles = implode(' ', $stylesContent);

        return $newStyles;
    }

    /**
     * Generate inline rPr styles
     *
     * @access protected
     * @param array $styles
     * @return string
     */
    protected function generateInlineRprStyles($styles)
    {
        $stylesContent = array();

        if (isset($styles['bold']) && $styles['bold']) {
            $stylesContent[] = 'b="1"';
        }
        if (isset($styles['characterSpacing'])) {
            $stylesContent[] = 'spc="'.$styles['characterSpacing'].'"';
        }
        if (isset($styles['fontSize'])) {
            $stylesContent[] = 'sz="'.((int)$styles['fontSize']*100).'"';
        }
        if (isset($styles['italic']) && $styles['italic']) {
            $stylesContent[] = 'i="1"';
        }
        if (isset($styles['lang'])) {
            $stylesContent[] = 'lang="'.$styles['lang'].'"';
        }
        if (isset($styles['strikethrough']) && $styles['strikethrough']) {
            $stylesContent[] = 'strike="sngStrike"';
        }
        if (isset($styles['underline']) && $styles['underline'] == 'single') {
            $stylesContent[] = 'u="sng"';
        }

        $newStyles = implode(' ', $stylesContent);

        return $newStyles;
    }
}