<?php
// set slide background colors in a PPTX created from scratch

require_once __DIR__ . '/../../../Classes/Phppptx/Create/CreatePptx.php';

$pptx = new Phppptx\Create\CreatePptx();

$pptx->setBackgroundColor('92D050');

$pptx->addSlide(array('active' => true));

$pptx->setBackgroundColor('00B0F0');

$pptx->addSlide(array('layout' => 'Title and Content'));

$pptx->setActiveSlide(array('position' => 2));
$pptx->setBackgroundColor('00B0F0');

$pptx->savePptx(__DIR__ . '/example_setBackgroundColor_1');