<?php
// add text boxes in a PPTX created from scratch

require_once __DIR__ . '/../../../Classes/Phppptx/Create/CreatePptx.php';

$pptx = new Phppptx\Create\CreatePptx();

$position = array(
    'coordinateX' => 1470000,
    'coordinateY' => 600000,
    'sizeX' => 5000000,
    'sizeY' => 450000,
);
// if no style is applied or a content is added, a text box is added in the slide but it's not visually displayed
$textBoxStyles = array(
    'border' => array(
        'color' => '000000',
    ),
);
$pptx->addTextBox($position, $textBoxStyles);

$position = array(
    'coordinateX' => 3770000,
    'coordinateY' => 4507000,
    'sizeX' => 7000000,
    'sizeY' => 1450000,
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

$pptx->savePptx(__DIR__ . '/example_addTextBox_1');