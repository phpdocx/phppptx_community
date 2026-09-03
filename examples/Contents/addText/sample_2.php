<?php
// add text contents with styles in new text boxes in a PPTX created from scratch

require_once __DIR__ . '/../../../Classes/Phppptx/Create/CreatePptx.php';

$pptx = new Phppptx\Create\CreatePptx();

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
    'text' => 'New content',
);
$pptx->addText($content, array('new' => $position));

// add new slide using the Blank layout
$pptx->addSlide(array('layout' => 'Blank', 'active' => true));

// text box created with addTextBox
$position = array(
    'coordinateX' => 3770000,
    'coordinateY' => 4507000,
    'sizeX' => 7000000,
    'sizeY' => 1450000,
    'name' => 'Custom textbox 1',
);
$textBoxStyles = array(
    'autofit' => 'noautofit',
    'border' => array(
        'color' => '000000',
    ),
    'fill' => array(
        'color' => '99B369',
    ),
);
$pptx->addTextBox($position, $textBoxStyles);

$content = array(
    'text' => 'Content added to the custom textbox 1.',
    'bold' => true,
    'underline' => 'single',
);
$pptx->addText($content, array('placeholder' => array('name' => 'Custom textbox 1')));

$content = array(
    array(
        'text' => 'Item 1',
        'bold' => true,
    ),
    array(
        'text' => 'Item 2',
        'bold' => true,
        'italic' => true,
        'underline' => 'single',
    ),
    array(
        'text' => 'Item 3',
        'fontSize' => 32,
    ),
);
$listStyles = array(
    array(
        'type' => 'checkmarkBullet',
        'indent' => -500000,
    ),
);
$pptx->addList($content, array('placeholder' => array('name' => 'Custom textbox 1')), $listStyles);

$pptx->savePptx(__DIR__ . '/example_addText_2');