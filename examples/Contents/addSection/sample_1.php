<?php
// add a new section in a PPTX created from scratch

require_once __DIR__ . '/../../../Classes/Phppptx/Create/CreatePptx.php';

$pptx = new Phppptx\Create\CreatePptx();
$pptx->addSection();

$pptx->savePptx(__DIR__ . '/example_addSection_1');