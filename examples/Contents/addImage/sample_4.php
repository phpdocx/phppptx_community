<?php
// add images applying styles in a PPTX created from scratch

require_once __DIR__ . '/../../../Classes/Phppptx/Create/CreatePptx.php';

$pptx = new Phppptx\Create\CreatePptx();

$position = array(
    'coordinateX' => 1000000,
    'coordinateY' => 4500000,
    'order' => 0,
);
$pptx->addImage(__DIR__ . '/../../files/image.png', $position);

$position = array(
    'coordinateX' => 4400000,
    'coordinateY' => 4500000,
);
$pptx->addImage(__DIR__ . '/../../files/image.png', $position);

$pptx->addSlide(array('layout' => 'Blank', 'active' => true));

$position = array(
    'coordinateX' => 5400000,
    'coordinateY' => 1500000,
);
$imageStyles = array(
    'border' => array(
        'color' => '0000FF',
        'dash' => 'dashDot',
    ),
    'rotation' => 35,
    'transparency' => 40,
);
$pptx->addImage(__DIR__ . '/../../files/image.png', $position, $imageStyles);

$position = array(
    'coordinateX' => 2400000,
    'coordinateY' => 3500000,
);
$imageStyles = array(
    'hyperlink' => 'https://www.phppptx.com',
);
$pptx->addImage(__DIR__ . '/../../files/image.png', $position, $imageStyles);

$pptx->savePptx(__DIR__ . '/example_addImage_4');