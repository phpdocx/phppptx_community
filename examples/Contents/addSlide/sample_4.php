<?php
// add new slides setting specific positions in a PPTX created from scratch

require_once __DIR__ . '/../../../Classes/Phppptx/Create/CreatePptx.php';

$pptx = new Phppptx\Create\CreatePptx();
// add a new slide in the last position (default position)
$pptx->addSlide(array('layout' => 'Title and Content'));

// add a new slide in the first position
$pptx->addSlide(array('layout' => 'Title Only', 'position' => 0));

// add a new slide in the third position
$pptx->addSlide(array('layout' => 'Comparison', 'position' => 2));

$pptx->savePptx(__DIR__ . '/example_addSlide_4');