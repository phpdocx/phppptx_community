<?php
// add list contents in layout placeholders in a PPTX created from scratch

require_once __DIR__ . '/../../../Classes/Phppptx/Create/CreatePptx.php';

$pptx = new Phppptx\Create\CreatePptx(array('layout' => 'Two Content'));

$content = array(
    'Item 1',
    'Item 2',
    'Item 3',
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
$pptx->addList($content, array('placeholder' => array('name' => 'Content Placeholder 3')));

$pptx->savePptx(__DIR__ . '/example_addList_1');