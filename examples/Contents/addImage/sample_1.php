<?php
// add images in a PPTX created from scratch

require_once __DIR__ . '/../../../Classes/Phppptx/Create/CreatePptx.php';

$pptx = new Phppptx\Create\CreatePptx();

$position = array(
    'coordinateX' => 1400000,
    'coordinateY' => 4500000,
    'sizeX' => 2000000,
    'sizeY' => 2000000,
);
$pptx->addImage(__DIR__ . '/../../files/image.jpg', $position);

$pptx->addSlide(array('layout' => 'Blank', 'active' => true));

$position = array(
    'coordinateX' => 1500000,
    'coordinateY' => 2000000,
    'sizeX' => 2000000,
    'sizeY' => 2000000,
);
$pptx->addImage(__DIR__ . '/../../files/imageP1.png', $position);

$position = array(
    'coordinateX' => 4000000,
    'coordinateY' => 2000000,
    'sizeX' => 2000000,
    'sizeY' => 2000000,
);
$pptx->addImage(__DIR__ . '/../../files/imageP2.png', $position);

$position = array(
    'coordinateX' => 6500000,
    'coordinateY' => 2000000,
    'sizeX' => 2000000,
    'sizeY' => 2000000,
);
$pptx->addImage(__DIR__ . '/../../files/imageP3.png', $position);

$pptx->savePptx(__DIR__ . '/example_addImage_1');