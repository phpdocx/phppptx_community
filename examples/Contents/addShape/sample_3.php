<?php
// add a shape with an image content in a PPTX created from scratch

require_once __DIR__ . '/../../../Classes/Phppptx/Create/CreatePptx.php';

$pptx = new Phppptx\Create\CreatePptx();

$position = array(
    'coordinateX' => 1000000,
    'coordinateY' => 400000,
    'sizeX' => 3500000,
    'sizeY' => 1800000,
);
$options = array(
    'imageContent' => __DIR__ . '/../../files/image.png',
    'outlineColor' => '#0000FF',
);
$pptx->addShape('triangle', $position, $options);

$pptx->savePptx(__DIR__ . '/example_addShape_3');