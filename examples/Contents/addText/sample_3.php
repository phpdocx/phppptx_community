<?php
// add text contents in layout placeholders using all position options in a PPTX created from scratch

require_once __DIR__ . '/../../../Classes/Phppptx/Create/CreatePptx.php';

$pptx = new Phppptx\Create\CreatePptx();

// add text contents using a placeholder name
$content = array(
    'text' => 'My title',
);
$pptx->addText($content, array('placeholder' => array('name' => 'Title 1')));

// add text contents using a placeholder type
$content = array(
    'text' => 'My subtitle',
);
$pptx->addText($content, array('placeholder' => array('type' => 'subTitle')));

// add new slide using the Title and Content layout
$pptx->addSlide(array('layout' => 'Title and Content', 'active' => true));

// add text contents using a placeholder position
$content = array(
    'text' => 'My new content',
);
$pptx->addText($content, array('placeholder' => array('position' => 0)));

// add new slide using the Blank layout
$pptx->addSlide(array('layout' => 'Blank', 'active' => true));

// add a text box with a custom altText (descr)
$position = array(
    'coordinateX' => 1470000,
    'coordinateY' => 600000,
    'sizeX' => 5000000,
    'sizeY' => 450000,
);
// if no style is applied or a content is added, a text box is added in the slide but it's not visually displayed
$textBoxStyles = array(
    'autofit' => 'noautofit',
    'border' => array(
        'color' => '000000',
    ),
    'descr' => 'Custom descr',
);
$pptx->addTextBox($position, $textBoxStyles);

// add text contents using a descr (altText) position
$content = array(
    'text' => 'Other content',
);
$pptx->addText($content, array('placeholder' => array('descr' => 'Custom descr')));

$pptx->savePptx(__DIR__ . '/example_addText_3');