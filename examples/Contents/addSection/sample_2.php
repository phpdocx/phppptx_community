<?php
// add new sections in a PPTX created from scratch

require_once __DIR__ . '/../../../Classes/Phppptx/Create/CreatePptx.php';

$pptx = new Phppptx\Create\CreatePptx();
$pptx->addSection();
$pptx->addSection(array('name' => 'My section'));
$pptx->addSection(array('position' => 0, 'name' => 'First section'));

$pptx->savePptx(__DIR__ . '/example_addSection_2');