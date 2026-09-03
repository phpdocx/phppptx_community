<?php
// modify the size of the presentation using a custom size in a PPTX created from scratch

require_once __DIR__ . '/../../../Classes/Phppptx/Create/CreatePptx.php';

$pptx = new Phppptx\Create\CreatePptx();
$pptx->setPresentationSettings(array('type' => 'custom', 'height' => 7858000, 'width' => 18144000));

$pptx->savePptx(__DIR__ . '/example_setPresentationSettings_2');