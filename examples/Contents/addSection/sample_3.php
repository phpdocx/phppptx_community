<?php
// new PPTX created from scratch setting a new section to be used in the first slide.

require_once __DIR__ . '/../../../Classes/Phppptx/Create/CreatePptx.php';

$pptx = new Phppptx\Create\CreatePptx(array('section' => 'My section'));

$pptx->savePptx(__DIR__ . '/example_addSection_3');