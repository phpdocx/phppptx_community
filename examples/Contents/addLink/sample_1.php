<?php
// add links in a PPTX created from scratch

require_once __DIR__ . '/../../../Classes/Phppptx/Create/CreatePptx.php';

$pptx = new Phppptx\Create\CreatePptx();

$pptx->addLink('https://www.phppptx.com', 'My link', array('placeholder' => array('name' => 'Title 1')));

// add new slide using the Title and Content layout
$pptx->addSlide(array('layout' => 'Title and Content'));

// the new slide has not been set as the active one. Add a new link in the active slide (the first slide)
$pptx->addLink('#slide1', 'link', array('placeholder' => array('name' => 'Subtitle 2')), array('bold' => true));

// add new slide using the Blank layout and set is the active slide
$pptx->addSlide(array('layout' => 'Blank', 'active' => true));

// text box position and styles
$position = array(
    'coordinateX' => 770000,
    'coordinateY' => 507000,
    'sizeX' => 7000000,
    'sizeY' => 450000,
    'textBoxStyles' => array(
        'border' => array(
            'color' => 'FF0000',
            'dash' => 'dash',
            'width' => 48575,
        ),
    ),
);
$pptx->addLink('#firstslide', 'Link to the first slide.', array('new' => $position), array(), array('align' => 'center'));

$pptx->savePptx(__DIR__ . '/example_addLink_1');