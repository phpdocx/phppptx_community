<?php
// add new slides setting layouts in a PPTX created from scratch

require_once __DIR__ . '/../../../Classes/Phppptx/Create/CreatePptx.php';

$pptx = new Phppptx\Create\CreatePptx();
$pptx->addSlide(array('layout' => 'Title and Content'));
$pptx->addSlide(array('layout' => 'Comparison'));

$pptx->savePptx(__DIR__ . '/example_addSlide_2');