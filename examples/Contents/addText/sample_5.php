<?php
// add list contents as texts with styles (addList is the recommended method to add lists)

require_once __DIR__ . '/../../../Classes/Phppptx/Create/CreatePptx.php';

$pptx = new Phppptx\Create\CreatePptx();

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
    'text' => 'Item 1',
    'bold' => true,
);
$paragraphStyles = array(
    'listLevel' => 0,
    'listStyles' => array(
        'type' => 'decimal',
    ),
    'noBullet' => false,
);
$pptx->addText($content, array('placeholder' => array('name' => 'Custom textbox 1')), $paragraphStyles);

$content = array(
    'text' => 'Item 2',
    'bold' => true,
    'italic' => true,
    'underline' => 'single',
);
$paragraphStyles = array(
    'listLevel' => 0,
    'listStyles' => array(
        'type' => 'filledRoundBullet',
    ),
    'noBullet' => false,
);
$pptx->addText($content, array('placeholder' => array('name' => 'Custom textbox 1')), $paragraphStyles);

$content = array(
    'text' => 'Item 3',
    'fontSize' => 32,
);
$paragraphStyles = array(
    'listLevel' => 1,
    'listStyles' => array(
        'type' => 'decimal',
    ),
    'noBullet' => false,
);
$pptx->addText($content, array('placeholder' => array('name' => 'Custom textbox 1')), $paragraphStyles);

$pptx->savePptx(__DIR__ . '/example_addText_5');