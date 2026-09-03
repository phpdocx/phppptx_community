<?php
// add text contents with styles in layout placeholders in a PPTX created from scratch

require_once __DIR__ . '/../../../Classes/Phppptx/Create/CreatePptx.php';

$pptx = new Phppptx\Create\CreatePptx();

$content = array(
    'text' => 'My title',
);
// the getActiveSlideInformation method returns information about placeholders in the current active slide to add text contents
$pptx->addText($content, array('placeholder' => array('name' => 'Title 1')));

$content = array(
    'text' => 'My subtitle',
    'bold' => true,
    'underline' => 'single',
);
$pptx->addText($content, array('placeholder' => array('name' => 'Subtitle 2')));

// append a new content in the same placeholder position
$content = array(
    array(
        'text' => 'At vero',
        'highlight' => '628A54',
    ),
    array(
        'text' => ' eos et ',
        'bold' => true,
        'italic' => true,
        'font' => 'Times New Roman',
    ),
    array(
        'text' => 'accusamus et iusto.',
        'strikethrough' => true,
        'color' => '628A54',
        'italic' => true,
    ),
);
$pptx->addText($content, array('placeholder' => array('name' => 'Subtitle 2')));

// add new slide using the Title and Content layout
$pptx->addSlide(array('layout' => 'Title and Content', 'active' => true));

$content = array(
    'text' => 'My custom title',
    'bold' => true,
    'font' => 'Arial',
    'fontSize' => 60,
);
$pptx->addText($content, array('placeholder' => array('name' => 'Title 1')));

// replace the text contents in the placeholder position. By default appends the text contents
$content = array(
    'text' => "My new \ntitle",
);
$paragraphStyles = array(
    'align' => 'center',
    'parseLineBreaks' => true,
);
$pptx->addText($content, array('placeholder' => array('name' => 'Title 1')), $paragraphStyles, array('insertMode' => 'replace'));

$pptx->savePptx(__DIR__ . '/example_addText_1');