<?php
namespace Phppptx\Utilities;

use Phppptx\Logger\PhppptxLogger;

/**
 * Storage PPTX internal structure
 *
 * @category   Phppptx
 * @package    utilities
 * @copyright  Copyright (c) Narcea Labs SL
 *             (https://www.narcealabs.com)
 * @license    phppptx Community License
 * @link       https://www.phppptx.com
 */
class PptxStructure
{
    /**
     * File type
     *
     * @var string
     */
    public $fileType = 'pptx';

    /**
     * Keep namespaces to work with transitional and strict variants
     *
     * @var array
     */
    public $namespaces = array();

    /**
     * PPTX structure
     *
     * @access private
     * @var array
     */
    private $pptxStructure = array();

    /**
     * Parse an PPTX file
     *
     * @access public
     */
    public function __construct() {
    }

    /**
     * Getter file type
     *
     * @return string
     */
    public function getFileType() {
        return $this->fileType;
    }

    /**
     * Setter file type
     *
     * @param string $fileType File type: pptx, pptm, potx. potm
     */
    public function setFileType($fileType) {
        $this->fileType = $fileType;
    }

    /**
     * Getter namespaces
     *
     * @return array
     */
    public function getNamespaces() {
        return $this->namespaces;
    }

    /**
     * Setter namespaces
     *
     * @param array $namespaces
     */
    public function setNamespaces($namespaces) {
        $this->namespaces = $namespaces;
    }

    /**
     * Getter pptxStructure
     *
     * @param string $format array or stream
     * @return mixed PPTX structure
     */
    public function getPptx($format) {
        return $this->pptxStructure;
    }

    /**
     * Setter pptxStructure
     *
     * @param array $pptxContents
     */
    public function setPptx($pptxContents) {
        $this->pptxStructure = $pptxContents;
    }

    /**
     * Add new content to the PPTX
     *
     * @param string $internalFilePath Path in the PPTX
     * @param string $content Content to be added
     */
    public function addContent($internalFilePath, $content)
    {
        $this->pptxStructure[$internalFilePath] = $content;
    }

    /**
     * Add a new file to the PPTX
     *
     * @param string $internalFilePath Path in the PPTX
     * @param string $file File path to be added
     */
    public function addFile($internalFilePath, $file)
    {
        $this->pptxStructure[$internalFilePath] = file_get_contents($file);
    }

    /**
     * Delete content in the PPTX
     *
     * @param string $internalFilePath Path in the PPTX
     */
    public function deleteContent($internalFilePath)
    {
        if (isset($this->pptxStructure[$internalFilePath])) {
            unset($this->pptxStructure[$internalFilePath]);
        }
    }

    /**
     * Get existing content from the PPTX
     *
     * @param string $internalFilePath Path in the PPTX
     * @param string $format null, string or DOMDocument
     * @return mixed File content as string, DOMDocument or false
     */
    public function getContent($internalFilePath, $format = null)
    {
        if (isset($this->pptxStructure[$internalFilePath])) {
            $content = $this->pptxStructure[$internalFilePath];
            if (empty($format) || $format == 'string') {
                // return content as string
                return $content;
            } else if ($format == 'DOMDocument') {
                // return content as DOMDocument
                $xmlUtilities = new XmlUtilities();
                $domDocument = $xmlUtilities->generateDomDocument($content);

                return $domDocument;
            }
        }

        return false;
    }

    /**
     * Get existing contents
     *
     * @param string $type Content type: comments, commentAuthors, handoutMasters, notesSlides, notesMasters, presentations, slides, slideLayouts, slideMasters, tableStyles, themes
     * @return array Contents
     */
    public function getContentByType($type)
    {
        // Content_Types
        $contentTypesDOM = $this->getContent('[Content_Types].xml', 'DOMDocument');
        $contentTypesXPath = new \DOMXPath($contentTypesDOM);
        $contentTypesXPath->registerNamespace('xmlns', 'http://schemas.openxmlformats.org/package/2006/content-types');

        $queryXpath = '';
        switch ($type) {
            case 'comments':
                $queryXpath = '//xmlns:Override[@ContentType="application/vnd.openxmlformats-officedocument.presentationml.comments+xml"]';
                break;
            case 'commentAuthors':
                $queryXpath = '//xmlns:Override[@ContentType="application/vnd.openxmlformats-officedocument.presentationml.commentAuthors+xml"]';
                break;
            case 'handoutMasters':
                $queryXpath = '//xmlns:Override[@ContentType="application/vnd.openxmlformats-officedocument.presentationml.handoutMaster+xml"]';
                break;
            case 'notesSlides':
                $queryXpath = '//xmlns:Override[@ContentType="application/vnd.openxmlformats-officedocument.presentationml.notesSlide+xml"]';
                break;
            case 'notesMasters':
                $queryXpath = '//xmlns:Override[@ContentType="application/vnd.openxmlformats-officedocument.presentationml.notesMaster+xml"]';
                break;
            case 'slides':
                $queryXpath = '//xmlns:Override[@ContentType="application/vnd.openxmlformats-officedocument.presentationml.slide+xml"]';
                break;
            case 'slideLayouts':
                $queryXpath = '//xmlns:Override[@ContentType="application/vnd.openxmlformats-officedocument.presentationml.slideLayout+xml"]';
                break;
            case 'slideMasters':
                $queryXpath = '//xmlns:Override[@ContentType="application/vnd.openxmlformats-officedocument.presentationml.slideMaster+xml"]';
                break;
            case 'tableStyles':
                $queryXpath = '//xmlns:Override[@ContentType="application/vnd.openxmlformats-officedocument.presentationml.tableStyles+xml"]';
                break;
            case 'themes':
                $queryXpath = '//xmlns:Override[@ContentType="application/vnd.openxmlformats-officedocument.theme+xml"]';
                break;
            case 'presentations':
            case 'default':
                $queryXpath = '//xmlns:Override[@ContentType="application/vnd.openxmlformats-officedocument.presentationml.presentation.main+xml"] | //xmlns:Override[@ContentType="application/vnd.ms-powerpoint.presentation.macroEnabled.main+xml"] | //xmlns:Override[@ContentType="application/vnd.ms-powerpoint.template.macroEnabled.main+xml"] | //xmlns:Override[@ContentType="application/vnd.openxmlformats-officedocument.presentationml.template.main+xml"]';
                break;
        }

        $contents = array();
        if (!empty($queryXpath)) {
            $xpathEntries = $contentTypesXPath->query($queryXpath);

            foreach ($xpathEntries as $xpathEntry) {
                $contents[] = array(
                    'content' => $this->getContent(substr($xpathEntry->getAttribute('PartName'), 1)),
                    'path' => substr($xpathEntry->getAttribute('PartName'), 1),
                );
            }
        }

        // free DOMDocument resources
        $contentTypesDOM = null;

        return $contents;
    }

    /**
     * Get existing layouts
     *
     * @return array Layouts contents
     */
    public function getLayouts()
    {
        $layoutsContents = array();

        // get layout contents
        $layouts = $this->getContentByType('slideLayouts');
        foreach ($layouts as $layout) {
            $layoutName = '';
            $layoutDOM = $this->getContent($layout['path'], 'DOMDocument');
            $nodesClSd = $layoutDOM->getElementsByTagNameNS('http://schemas.openxmlformats.org/presentationml/2006/main', 'cSld');
            if ($nodesClSd->length > 0) {
                if ($nodesClSd->item(0)->hasAttribute('name')) {
                    $layoutName = $nodesClSd->item(0)->getAttribute('name');
                }
            }

            $layoutsContents[] = array(
                'content' => $layout['content'],
                'name' => $layoutName,
                'path' => $layout['path'],
            );

            // free DOMDocument resources
            $layoutDOM = null;
        }

        return $layoutsContents;
    }

    /**
     * Get existing slides keeping presentation order
     *
     * @return array Slide contents
     */
    public function getSlides()
    {
        // presentation
        $presentationDOM = $this->getContent('ppt/presentation.xml', 'DOMDocument');
        // presentation rels
        $presentationRelsDOM = $this->getContent('ppt/_rels/presentation.xml.rels', 'DOMDocument');
        $presentationRelsXpath = new \DOMXPath($presentationRelsDOM);
        $presentationRelsXpath->registerNamespace('xmlns', 'http://schemas.openxmlformats.org/package/2006/relationships');
        // keep slide informations
        $slidesContents = array();

        $sldIdLstTags = $presentationDOM->getElementsByTagName('sldIdLst');
        if ($sldIdLstTags->length > 0) {
            $sldIdsTags = $sldIdLstTags->item(0)->getElementsByTagName('sldId');
            // iterate existing slides
            foreach ($sldIdsTags as $sldIdTags) {
                // get content from the slide id
                $slideRId = $sldIdTags->getAttribute('r:id');
                $queryByRelsId = '//xmlns:Relationship[@Id="' . $slideRId . '"]';
                $relationshipNodes = $presentationRelsXpath->query($queryByRelsId);
                if ($relationshipNodes->length > 0) {
                    $slidePath = 'ppt/' . $relationshipNodes->item(0)->getAttribute('Target');
                    $contentSlide = $this->getContent($slidePath);

                    $slidesContents[] = array(
                        'id' => $sldIdTags->getAttribute('r:id'),
                        'name' => $sldIdTags->getAttribute('name'),
                        'content' => $contentSlide,
                        'path' => $slidePath,
                    );
                }
            }
        }

        // free DOMDocument resources
        $presentationDOM = null;
        $presentationRelsDOM = null;

        return $slidesContents;
    }

    /**
     * Parse an existing PPTX
     * @param string $path File path
     * @throws \Exception error opening the source file
     */
    public function parsePptx($path)
    {
        $zip = new \ZipArchive();

        if ($zip->open($path) === TRUE) {
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $fileName = $zip->getNameIndex($i);
                $this->pptxStructure[$zip->getNameIndex($i)] = $zip->getFromName($fileName);
            }
        } else {
            PhppptxLogger::logger('Error while trying to open ' . $path . ' as a ZIP file', 'fatal');
        }

        // parse the Content_Types
        $contentTypesContent = $this->pptxStructure['[Content_Types].xml'];
        $xmlUtilities = new XmlUtilities();
        $contentTypesXml = $xmlUtilities->generateSimpleXmlElement($contentTypesContent);
        $contentTypesDom = dom_import_simplexml($contentTypesXml);
        $contentTypesXpath = new \DOMXPath($contentTypesDom->ownerDocument);
        $contentTypesXpath->registerNamespace('ct', 'http://schemas.openxmlformats.org/package/2006/content-types');

        // if there's no presentation.xml file, get application/vnd.openxmlformats-officedocument.presentationml.presentation.main+xml and rels files and rename them
        if (!isset($this->pptxStructure['ppt/presentation.xml'])) {
            $relsEntries = $contentTypesXpath->query('//ct:Override[@ContentType="application/vnd.openxmlformats-officedocument.presentationml.presentation.main+xml"]');
            $partNameMainXML = $relsEntries->item(0)->getAttribute('PartName');
            $partNameMainXMLNameStructure = substr($partNameMainXML, 1);
            $this->pptxStructure['ppt/presentation.xml'] = $this->pptxStructure[$partNameMainXMLNameStructure];
            $partNameMainRelsXMLNameStructure = str_replace('ppt/', 'ppt/_rels/', $partNameMainXMLNameStructure) . '.rels';
            $this->pptxStructure['ppt/_rels/presentation.xml.rels'] = $this->pptxStructure[$partNameMainRelsXMLNameStructure];
            unset($this->pptxStructure[$partNameMainXMLNameStructure]);
            unset($this->pptxStructure[$partNameMainRelsXMLNameStructure]);

            // replace the previous main document name by the new one
            $this->pptxStructure['[Content_Types].xml'] = str_replace('"/'.$partNameMainXMLNameStructure.'"', '"/ppt/presentation.xml"', $this->pptxStructure['[Content_Types].xml']);
            $this->pptxStructure['_rels/.rels'] = str_replace('"/'.$partNameMainXMLNameStructure.'"', '"/ppt/presentation.xml"', $this->pptxStructure['_rels/.rels']);
            $this->pptxStructure['_rels/.rels'] = str_replace('"'.$partNameMainXMLNameStructure.'"', '"ppt/presentation.xml"', $this->pptxStructure['_rels/.rels']);
        }

        // get file type
        $xpathPresentationOverride = $contentTypesXpath->query('//ct:Override[@PartName="/ppt/presentation.xml"]');
        if ($xpathPresentationOverride->length > 0) {
            $contentType = $xpathPresentationOverride->item(0)->getAttribute('ContentType');
            switch ($contentType) {
                case 'application/vnd.ms-powerpoint.presentation.macroEnabled.main+xml':
                    $this->fileType = 'pptm';
                    break;
                case 'application/vnd.openxmlformats-officedocument.presentationml.template.main+xml':
                    $this->fileType = 'potx';
                    break;
                case 'application/vnd.ms-powerpoint.template.macroEnabled.main+xml':
                    $this->fileType = 'potm';
                    break;
                case 'application/vnd.openxmlformats-officedocument.presentationml.presentation.main+xml':
                default:
                    $this->fileType = 'pptx';
                    break;
            }
        }

        // get XML namespaces based on strict variants if used in the PPTX
        // default as transitional mode
        $this->namespaces = array(
            'a' => 'http://schemas.openxmlformats.org/drawingml/2006/main',
            'p' => 'http://schemas.openxmlformats.org/presentationml/2006/main',
            'r' => 'http://schemas.openxmlformats.org/officeDocument/2006/relationships',
        );
        $powerpointPresentationDOM = $this->getContent('ppt/presentation.xml', 'DOMDocument');
        $nodesPresentation = $powerpointPresentationDOM->getElementsByTagName('presentation');
        if ($nodesPresentation->length > 0) {
            if ($nodesPresentation->item(0)->hasAttribute('conformance') && $nodesPresentation->item(0)->hasAttribute('conformance') == 'strict') {
                // strict mode
                $this->namespaces = array(
                    'a' => 'http://purl.oclc.org/ooxml/drawingml/main',
                    'p' => 'http://schemas.openxmlformats.org/presentationml/2006/main',
                    'r' => 'http://schemas.openxmlformats.org/officeDocument/2006/relationships',
                );

                // transform namespaces to transitional
                foreach ($this->pptxStructure as $key => $value) {
                    $xmlFile = substr_compare($key, '.xml', -strlen('.xml'));
                    $relsFile = substr_compare($key, '.rels', -strlen('.rels'));
                    if ($xmlFile === 0 || $relsFile === 0) {
                        $value = str_replace('http://purl.oclc.org/ooxml/drawingml/main', 'http://schemas.openxmlformats.org/drawingml/2006/main', $value);
                        $value = str_replace('http://purl.oclc.org/ooxml/presentationml/main', 'http://schemas.openxmlformats.org/presentationml/2006/main', $value);
                        $value = str_replace('http://purl.oclc.org/ooxml/officeDocument/relationships', 'http://schemas.openxmlformats.org/officeDocument/2006/relationships', $value);

                        $this->pptxStructure[$key] = $value;
                    }
                }
            }
        }

        // free DOMDocument resources
        $powerpointPresentationDOM = null;
    }

    /**
     * Save PptxStructure as ZIP
     * @param string $path File path
     * @param bool $forceFile Force PPTX as file, needed for charts when working with streams
     * @return PptxStructure Self
     */
    public function savePptx($path, $forceFile = false) {
        // check if the path has the correct extension
        if (substr($path, -5) !== '.' . $this->fileType) {
            $path .= '.' . $this->fileType;
        }


        $pptxFile = new \ZipArchive();

        // if dest file exits remove it to avoid duplicate content
        if (file_exists($path) && is_writable($path)) {
            unlink($path);
        }

        if ($pptxFile->open($path, \ZipArchive::CREATE) === TRUE) {
            foreach ($this->pptxStructure as $key => $value) {
                $pptxFile->addFromString($key, $value);
            }

            $pptxFile->close();
        } else {
            PhppptxLogger::logger('Error while trying to write to ' . $path, 'fatal');
        }

        // return the structure object after creating the file
        return $this;
    }
}