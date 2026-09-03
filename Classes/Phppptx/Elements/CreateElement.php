<?php
namespace Phppptx\Elements;

use Phppptx\Utilities\XmlUtilities;

/**
 * Create tag elements
 *
 * @category   Phppptx
 * @package    elements
 * @copyright  Copyright (c) Narcea Labs SL
 *             (https://www.narcealabs.com)
 * @license    phppptx Community License
 * @link       https://www.phppptx.com
 */
class CreateElement
{
    /**
     *
     * @access public
     * @var array
     */
    public $externalRelationships = array();

    /**
     *
     * @access protected
     * @var string
     */
    protected $xml;

    /**
     * Getter externalRelationships
     *
     * @access public
     * @return array
     */
    public function getExternalRelationships()
    {
        return $this->externalRelationships;
    }

    /**
     * Generates uniqueID
     *
     * @access protected
     * @return string
     */
    public function generateUniqueId()
    {
        $uniqueId = uniqid((string)mt_rand(999, 9999));

        return $uniqueId;
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
        $xmlUtilities = new XmlUtilities();
        $content = $xmlUtilities->parseAndCleanTextString($content);

        return $content;
    }

    /**
     * Delete pending tags
     *
     * @access protected
     */
    protected function cleanTemplate()
    {
        $this->xml = preg_replace('/__PHX=__[A-Z]+__/', '', $this->xml);
    }

    /**
     * Handle hyperlink content
     *
     * @param string $hyperlink
     * @return array
     */
    protected function handleHyperlinkContent($hyperlink)
    {
        $hyperlinkRId = 'r:id=""';
        $idHyperlink = null;
        $hyperlinkAction = '';
        if (strpos($hyperlink, '#slide') === 0) {
            // slide position
            $idHyperlink = $this->generateUniqueId();
            $hyperlinkRId = 'r:id="rId'.$idHyperlink.'"';
            $hyperlinkAction = 'action="ppaction://hlinksldjump"';
        } else if (strpos($hyperlink, '#') === 0) {
            // bookmark
            $hyperlinkAction = 'action="ppaction://hlinkshowjump?jump=' . $this->parseAndCleanTextString(str_replace('#', '', $hyperlink)) . '"';
        } else {
            // external
            $idHyperlink = $this->generateUniqueId();
            $hyperlinkRId = 'r:id="rId'.$idHyperlink.'"';
        }

        // keep relationship information to be added to the PPTX
        if (!empty($idHyperlink)) {
            $this->externalRelationships[] = array(
                'type' => 'hyperlink',
                'id' => $idHyperlink,
                'hyperlink' => $hyperlink,
            );
        }

        return array(
            'hyperlinkRId' => $hyperlinkRId,
            'idHyperlink' => $idHyperlink,
            'hyperlinkAction' => $hyperlinkAction,
        );
    }

    /**
     * Inserts a fragment in the chosen orden in a slide
     *
     * @access protected
     * @param string $content
     * @param array $position
     * @param \DOMDocument $slideDOM
     * @param \DOMXPath $slideXPath
     * @return \DOMNode
     */
    protected function insertNewContentOrder($content, $position, $slideDOM, $slideXPath)
    {
        $fragment = $slideDOM->createDocumentFragment();
        $fragment->appendXML($content);

        $nodesSpTree = $slideXPath->query('//p:spTree');
        if (isset($position['order'])) {
            if ($position['order'] == 0) {
                // get elements that p:spPr to get the correct order
                $nodesWithPSpPr = $slideXPath->query('//p:spTree/*[.//p:spPr][1]');
                if ($nodesWithPSpPr->length > 0) {
                    // first position
                    $node = $nodesWithPSpPr->item(0)->parentNode->insertBefore($fragment, $nodesWithPSpPr->item(0));
                } else {
                    // last position
                    $node = $nodesSpTree->item(0)->appendChild($fragment);
                }
            } else {
                $nodesWithPSpPr = $slideXPath->query('//p:spTree/*[.//p:spPr]['.((int)$position['order']+1).']');
                if ($nodesWithPSpPr->length > 0) {
                    $node = $nodesWithPSpPr->item(0)->parentNode->insertBefore($fragment, $nodesWithPSpPr->item(0));
                } else {
                    // the position doesn't exist, append the text box in the last position
                    $node = $nodesSpTree->item(0)->appendChild($fragment);
                }
            }
        } else {
            // last position
            $node = $nodesSpTree->item(0)->appendChild($fragment);
        }

        return $node;
    }
}