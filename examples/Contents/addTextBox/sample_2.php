<?php
// add text boxes with styles in a PPTX created from scratch

require_once __DIR__ . '/../../../Classes/Phppptx/Create/CreatePptx.php';

$pptx = new Phppptx\Create\CreatePptx();

$position = array(
    'coordinateX' => 1470000,
    'coordinateY' => 600000,
    'sizeX' => 5000000,
    'sizeY' => 450000,
    'name' => 'Textbox custom 1',
);
$textBoxStyles = array(
    'fill' => array(
        'color' => '99B369',
    ),
);
$pptx->addTextBox($position, $textBoxStyles);

$position = array(
    'coordinateX' => 3770000,
    'coordinateY' => 4507000,
    'sizeX' => 7000000,
    'sizeY' => 1450000,
    'name' => 'Textbox custom 2',
);
$textBoxStyles = array(
    'autofit' => 'noautofit',
    'border' => array(
        'color' => '154C79',
        'dash' => 'dash',
        'width' => 48575,
    ),
    'margin' => array(
        'left' => 260000,
        'top' => 360000,
    ),
    'columns' => array(
        'number' => 2,
        'spacing' => 180000,
    )
);
$pptx->addTextBox($position, $textBoxStyles);

$pptx->addSlide(array('active' => true, 'layout' => 'Blank'));

$position = array(
    'coordinateX' => 2770000,
    'coordinateY' => 3507000,
    'sizeX' => 700000,
    'sizeY' => 1450000,
    'name' => 'Textbox custom A',
);
$textBoxStyles = array(
    'border' => array(
        'color' => '000000',
    ),
    'descr' => 'My description',
    'textDirection' => 'vert',
);
$pptx->addTextBox($position, $textBoxStyles);

$position = array(
    'coordinateX' => 5770000,
    'coordinateY' => 1507000,
    'sizeX' => 700000,
    'sizeY' => 1450000,
    'name' => 'Textbox custom B',
);
$textBoxStyles = array(
    'autofit' => 'noautofit',
    'border' => array(
        'color' => '0000FF',
    ),
    'rotation' => 20,
    'verticalAlign' => 'bottomCentered',
);
$pptx->addTextBox($position, $textBoxStyles);

$pptx->addSlide(array('active' => true));

$position = array(
    'coordinateX' => 1470000,
    'coordinateY' => 600000,
    'sizeX' => 4000000,
    'sizeY' => 2350000,
    'name' => 'Textbox custom 1',
);
$textBoxStyles = array(
    'autofit' => 'noautofit',
    'fill' => array(
        'image' => __DIR__ . '/../../files/image.png',
    ),
);
$pptx->addTextBox($position, $textBoxStyles);

$pptx->savePptx(__DIR__ . '/example_addTextBox_2');