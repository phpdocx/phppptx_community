<?php
// add a new slide in a PPTX created from scratch

require_once __DIR__ . '/../../../Classes/Phppptx/Create/CreatePptx.php';

$pptx = new Phppptx\Create\CreatePptx();
$pptx->addSlide();

$pptx->savePptx(__DIR__ . '/example_addSlide_1');