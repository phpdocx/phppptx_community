<?php
// add list contents with styles in placeholders and new text boxes in a PPTX created from scratch

require_once __DIR__ . '/../../../Classes/Phppptx/Create/CreatePptx.php';

$pptx = new Phppptx\Create\CreatePptx();

$content = array(
    array(
        'text' => 'Item 1',
        'bold' => true,
    ),
    array(
        'text' => 'Item 2',
        'bold' => true,
        'italic' => true,
        'underline' => 'single',
    ),
    array(
        'text' => 'Item 3',
        'fontSize' => 32,
    ),
);
$listStyles = array(
    array(
        'type' => 'checkmarkBullet',
        'indent' => -500000,
    ),
);
$pptx->addList($content, array('placeholder' => array('name' => 'Subtitle 2')), $listStyles);

$pptx->addSlide(array('layout' => 'Blank', 'active' => true));

// text box position and styles
$content = array(
    'Item A',
    array(
        'Item A.1',
        'Item A.2',
        array(
            'Item A.2.sub1',
            'Item A.2.sub2',
        ),
        'Item A.3',
    ),
    'Item B',
    'Item C',
);
$position = array(
    'coordinateX' => 770000,
    'coordinateY' => 507000,
    'sizeX' => 7000000,
    'sizeY' => 3450000,
);
$listStyles = array(
    array(
        'type' => 'filledRoundBullet',
    ),
    array(
        'type' => 'hollowRoundBullet',
    ),
    array(
        'type' => 'decimal',
    ),
);
$pptx->addList($content, array('new' => $position), $listStyles);

$pptx->savePptx(__DIR__ . '/example_addList_4');