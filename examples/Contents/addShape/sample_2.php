<?php
// add a shape with a text content in a PPTX created from scratch

require_once __DIR__ . '/../../../Classes/Phppptx/Create/CreatePptx.php';

$pptx = new Phppptx\Create\CreatePptx();

$textContent = array(
    'text' => 'Lorem ipsum dolor sit amet',
    'bold' => true,
    'color' => '#FFFFFF',
);
$textFragment = new Phppptx\Elements\PptxFragment();
$textFragment->addText($textContent, array(), array('align' => 'center'));

$position = array(
    'coordinateX' => 1000000,
    'coordinateY' => 400000,
    'sizeX' => 3500000,
    'sizeY' => 1800000,
);
$options = array(
    'fillColor' => '#FF0000',
    'outlineColor' => '#0000FF',
    'textContents' => $textFragment,
    'verticalAlign' => 'middle',
);
$pptx->addShape('roundRect', $position, $options);

$pptx->savePptx(__DIR__ . '/example_addShape_2');