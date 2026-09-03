<?php
// add rtl contents in a PPTX created from scratch

require_once __DIR__ . '/../../../Classes/Phppptx/Create/CreatePptx.php';

$pptx = new Phppptx\Create\CreatePptx();
$pptx->setPresentationSettings(array('rtl' => true));
$pptx->setRtl();

$pptx->addSlide(array('layout' => 'Title and Content', 'active' => true));

$content = array(
    'text' => 'My custom title',
    'bold' => true,
    'font' => 'Arial',
    'fontSize' => 60,
);
$pptx->addText($content, array('placeholder' => array('name' => 'Title 1')));

$content = array(
    'Item 1',
    'Item 2',
    'Item 3',
);
$pptx->addList($content, array('placeholder' => array('name' => 'Content Placeholder 2')));

$pptx->savePptx(__DIR__ . '/example_setRtl_2');