<?php
// add text contents with links in a PPTX created from scratch

require_once __DIR__ . '/../../../Classes/Phppptx/Create/CreatePptx.php';

$pptx = new Phppptx\Create\CreatePptx();

$content = array(
    'text' => 'My link',
    'hyperlink' => 'https://www.phppptx.com'
);
// the getActiveSlideInformation method returns information about placeholders in the current active slide to add text contents
$pptx->addText($content, array('placeholder' => array('name' => 'Title 1')));

// add new slide using the Title and Content layout
$pptx->addSlide(array('layout' => 'Title and Content'));

// the new slide has not been set as the active one. Add a new link in the active slide (the first slide)
$content = array(
    array(
        'text' => 'Open second slide: ',
    ),
    array(
        'text' => 'link',
        'bold' => true,
        'hyperlink' => '#slide1' // 0 is the first slide
    ),
);
$pptx->addText($content, array('placeholder' => array('name' => 'Subtitle 2')));

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
// text box created in addText
$content = array(
    array(
        'text' => 'Content in the slide. ',
    ),
    array(
        'text' => 'Link to the first slide.',
        'bold' => true,
        'hyperlink' => '#firstslide'
    ),
);
$pptx->addText($content, array('new' => $position));

$pptx->savePptx(__DIR__ . '/example_addText_4');