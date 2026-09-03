<?php
// set the presentation as read only

require_once __DIR__ . '/../../../Classes/Phppptx/Create/CreatePptx.php';

$pptx = new Phppptx\Create\CreatePptx();
$pptx->setPresentationSettings(array('readOnly' => true));

$pptx->savePptx(__DIR__ . '/example_setPresentationSettings_3');