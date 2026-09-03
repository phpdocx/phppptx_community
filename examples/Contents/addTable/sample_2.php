<?php
// add tables applying styles in a PPTX created from scratch

require_once __DIR__ . '/../../../Classes/Phppptx/Create/CreatePptx.php';

$pptx = new Phppptx\Create\CreatePptx(array('layout' => 'Blank'));

$content = array(
    array('Header A', 'Header B', 'Header C', 'Header D'),
    array('Cell 2.1', 'Cell 2.2', 'Cell 2.3', 'Cell 2.4'),
    array('Cell 3.1', 'Cell 3.2', 'Cell 3.3', 'Cell 3.4'),
    array('Cell 4.1', 'Cell 4.2', 'Cell 4.3', 'Cell 4.4'),
    array('Total A', 'Total B', 'Total C', 'Total D'),
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
// table using a table style and custom styles
$pptx->addTable($content, $position, $tableStyles);

$pptx->addSlide(array('active' => true));

$tableStyles = array(
    'bandedRows' => true,
    'headerRow' => true,
    'totalRow' => true,
    'style' => 'Dark Style 1 - Accent 6',
);
// table using a table style and custom styles
$pptx->addTable($content, $position, $tableStyles);

$pptx->addSlide(array('active' => true));

$content = array(
    array(
        array(
            'text' => 'Title A',
            'backgroundColor' => '000000',
            'color' => 'FFFFFF',
            'border' => array(
                'color' => 'none',
            ),
            'cellMargin' => array(
                'top' => 90000,
                'left' => 200000,
            ),
        ),
        array(
            'text' => 'Title B',
            'backgroundColor' => '000000',
            'color' => 'FFFFFF',
            'border' => array(
                'color' => 'none',
            ),
            'cellMargin' => array(
                'top' => 90000,
                'left' => 200000,
            ),
        ),
        array(
            'text' => 'Title C',
            'backgroundColor' => '000000',
            'color' => 'FFFFFF',
            'border' => array(
                'color' => 'none',
            ),
            'cellMargin' => array(
                'top' => 90000,
                'left' => 200000,
            ),
        ),
        array(
            'text' => 'Title D',
            'backgroundColor' => '000000',
            'color' => 'FFFFFF',
            'border' => array(
                'color' => 'none',
            ),
            'cellMargin' => array(
                'top' => 90000,
                'left' => 200000,
            ),
        ),
    ),
    array(
        'Cell 2.1',
        'Cell 2.2',
        'Cell 2.3',
        array(
            'text' => 'Cell 2.4',
            'border' => array(
                'bottom' => array(
                    'dash' => 'sysDot',
                    'color' => '0000FF',
                    'width' => 50000,
                ),
            ),
        ),
    ),
    array(
        'Cell 3.1',
        'Cell 3.2',
        array(
            'text' => 'Cell 3.3',
            'border' => array(
                'right' => array(
                    'dash' => 'sysDot',
                    'color' => '0000FF',
                    'width' => 50000,
                ),
                'bottom' => array(
                    'dash' => 'sysDot',
                    'color' => '0000FF',
                    'width' => 50000,
                ),
            ),
        ),
        array(
            'text' => 'Cell 3.4',
            'bold' => true,
            'border' => array(
                'dash' => 'sysDot',
                'color' => '0000FF',
                'width' => 50000,
            ),
        ),
    ),
);
$tableStyles = array(
    'backgroundColor' => '7FAAF0',
    'border' => array(
        'dash' => 'sysDash',
        'color' => '224887',
        'width' => 50000,
    ),
);
// table using a table style and custom styles
$pptx->addTable($content, $position, $tableStyles);

$pptx->savePptx(__DIR__ . '/example_addTable_2');