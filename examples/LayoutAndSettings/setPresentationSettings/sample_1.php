<?php
// modify the size of the presentation using a preset size in a PPTX created from scratch

require_once __DIR__ . '/../../../Classes/Phppptx/Create/CreatePptx.php';

$pptx = new Phppptx\Create\CreatePptx();
$pptx->setPresentationSettings(array('type' => 'A3'));

$pptx->savePptx(__DIR__ . '/example_setPresentationSettings_1');