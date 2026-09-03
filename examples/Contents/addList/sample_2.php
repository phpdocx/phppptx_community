<?php
// add list contents with styles in layout placeholders in a PPTX created from scratch

require_once __DIR__ . '/../../../Classes/Phppptx/Create/CreatePptx.php';

$pptx = new Phppptx\Create\CreatePptx(array('layout' => 'Two Content'));

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
        'fontSize' => 16,
    ),
);
$pptx->addList($content, array('placeholder' => array('name' => 'Content Placeholder 2')));

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
$listStyles = array(
    array(
        'type' => 'decimal',
    ),
    array(),
    array(
        'type' => 'romanLowerCase',
        'startAt' => 3,
        'color' => 'FF0000',
        'marginLeft' => 1800000,
        'indent' => -400000,
        'size' => 110,
    ),
);
$pptx->addList($content, array('placeholder' => array('name' => 'Content Placeholder 3')), $listStyles);

$pptx->savePptx(__DIR__ . '/example_addList_2');