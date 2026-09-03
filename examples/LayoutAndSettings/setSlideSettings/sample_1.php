<?php
// modify the layout of the second and third slides in an existing PPTX

require_once __DIR__ . '/../../../Classes/Phppptx/Create/CreatePptx.php';

$pptx = new Phppptx\Create\CreatePptx();
$pptx->addSlide();

$pptx->setSlideSettings(array('layout' => 'Title Slide'));

$pptx->savePptx(__DIR__ . '/example_setSlideSettings_1');