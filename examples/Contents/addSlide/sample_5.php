<?php
// add new slides in sections in a PPTX created from scratch

require_once __DIR__ . '/../../../Classes/Phppptx/Create/CreatePptx.php';

$pptx = new Phppptx\Create\CreatePptx(array('section' => 'First Section'));

// add a new slide. If the section is not set, the slide is added without a section
$pptx->addSlide(array('layout' => 'Title Only', 'section' => 0));

$pptx->addSection(array('name' => 'Other section'));

// add a new slide in the last section. Last position as default
$pptx->addSlide(array('layout' => 'Title and Content', 'section' => -1));

// add a new slide in the first section. Last position as default
$pptx->addSlide(array('layout' => 'Comparison', 'section' => 0));

// add a new slide in the first section in the second position (0 is the first position)
$pptx->addSlide(array('layout' => 'Title Only', 'section' => 1, 'position' => 1));

$pptx->savePptx(__DIR__ . '/example_addSlide_5');