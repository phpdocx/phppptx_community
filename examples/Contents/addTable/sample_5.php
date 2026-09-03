<?php
// add a table with images in a PPTX created from scratch

require_once __DIR__ . '/../../../Classes/Phppptx/Create/CreatePptx.php';

$pptx = new Phppptx\Create\CreatePptx(array('layout' => 'Blank'));

$content = array(
    array('Cell 1.1', 'Cell 1.2', 'Cell 1.3', 'Cell 1.4'),
    array(array('image' => __DIR__ . '/../../files/imageP1.png'), array('text' => 'Cell content', 'align' => 'center', 'image' => __DIR__ . '/../../files/imageP2.png'), array('image' => __DIR__ . '/../../files/imageP3.png'), 'Cell 2.4'),
);
$position = array(
    'coordinateX' => 850000,
    'coordinateY' => 1800000,
    'sizeX' => 10500000,
    'sizeY' => 750000,
);
$rowStyles = array(
    array(
        'height' => 1000000,
    ),
    array(
        'height' => 1000000,
    ),
);
// add a table using the shape size to set column and row sizes
$pptx->addTable($content, $position, array(), $rowStyles);

$pptx->savePptx(__DIR__ . '/example_addTable_5');