<?php
// add tables applying sizes in a PPTX created from scratch

require_once __DIR__ . '/../../../Classes/Phppptx/Create/CreatePptx.php';

$pptx = new Phppptx\Create\CreatePptx(array('layout' => 'Blank'));

$content = array(
    array('Cell 1.1', 'Cell 1.2', 'Cell 1.3', 'Cell 1.4'),
    array('Cell 2.1', 'Cell 2.2', 'Cell 2.3', 'Cell 2.4'),
    array('Cell 3.1', 'Cell 3.2', 'Cell 3.3', 'Cell 3.4'),
);
$position = array(
    'coordinateX' => 850000,
    'coordinateY' => 1800000,
    'sizeX' => 10500000,
    'sizeY' => 750000,
);
// add a table using the shape size to set column and row sizes
$pptx->addTable($content, $position);

$pptx->addSlide(array('active' => true));

$content = array(
    array('Cell 1.1', 'Cell 1.2', 'Cell 1.3', 'Cell 1.4'),
    array('Cell 2.1', 'Cell 2.2', 'Cell 2.3', 'Cell 2.4'),
    array('Cell 3.1', 'Cell 3.2', 'Cell 3.3', 'Cell 3.4'),
);
$position = array(
    'coordinateX' => 850000,
    'coordinateY' => 1800000,
    'sizeX' => 10500000,
    'sizeY' => 750000,
);
// add a table applying a fixed column size and height sizes for rows
$tableStyles = array(
    'columnWidths' => 2625000,
);
$rowStyles = array(
    array(
        'height' => 650000,
    ),
    array(),
    array(
        'height' => 450000,
    ),
);
$pptx->addTable($content, $position, $tableStyles, $rowStyles);

$pptx->addSlide(array('active' => true));

$content = array(
    array('Cell 1.1 and more content', 'Cell 1.2', 'Cell 1.3', 'Cell 1.4'),
    array('Cell 2.1', 'Cell 2.2', 'Cell 2.3', 'Cell 2.4'),
    array('Cell 3.1', 'Cell 3.2', 'Cell 3.3', 'Cell 3.4'),
);
$position = array(
    'coordinateX' => 850000,
    'coordinateY' => 1800000,
    'sizeX' => 10500000,
    'sizeY' => 750000,
);
// add a table applying variable column sizes
$tableStyles = array(
    'columnWidths' => array(1000000, 3000000, 3000000)
);
$pptx->addTable($content, $position, $tableStyles);

$pptx->savePptx(__DIR__ . '/example_addTable_1');