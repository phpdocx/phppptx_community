<?php
// add tables with colspans and rowspans in a PPTX created from scratch

require_once __DIR__ . '/../../../Classes/Phppptx/Create/CreatePptx.php';

$pptx = new Phppptx\Create\CreatePptx(array('layout' => 'Blank'));

$content = array(
    array(
        array(
            'text' => 'Custom table',
            'colspan' => 4,
            'align' => 'center',
        ),
    ),
    array('Cell 1.1', 'Cell 1.2', 'Cell 1.3', 'Cell 1.4'),
    array(
        array(
            'text' => 'Cell 2.1 and 2.2',
            'colspan' => 2,
        ),
        array(
            'text' => 'Rotated text',
            'textDirection' => 'vert',
            'colspan' => 2,
            'rowspan' => 3,
        ),
    ),
    array('Cell 3.1', 'Cell 3.2'),
    array(
        array(
            'text' => 'Cell 4.1 and 4.2',
            'colspan' => 2,
        ),
    ),
    array('Cell 5.1', 'Cell 5.2', 'Cell 5.3', 'Cell 5.4'),
);
$position = array(
    'coordinateX' => 850000,
    'coordinateY' => 1800000,
    'sizeX' => 10500000,
    'sizeY' => 750000,
);
$tableStyles = array(
    'bandedRows' => true,
    'headerRow' => true,
    'style' => 'Medium Style 2 - Accent 1',
    'totalRow' => true,
);
$pptx->addTable($content, $position, $tableStyles);

$pptx->savePptx(__DIR__ . '/example_addTable_4');