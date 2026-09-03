<?php
// add an image using an image resource

require_once __DIR__ . '/../../../Classes/Phppptx/Create/CreatePptx.php';

$pptx = new Phppptx\Create\CreatePptx();

$imageResource = imagecreatefromjpeg(__DIR__ . '/../../files/image.jpg');
$position = array(
    'coordinateX' => 1400000,
    'coordinateY' => 4500000,
);
$pptx->addImage($imageResource, $position);

$pptx->savePptx(__DIR__ . '/example_addImage_5');