<?php
// get the internal active slide in a PPTX created from scratch

require_once __DIR__ . '/../../../Classes/Phppptx/Create/CreatePptx.php';

$pptx = new Phppptx\Create\CreatePptx();
$activeSlide = $pptx->getActiveSlide();
var_dump($activeSlide);

$pptx->addSlide();
// the first slide has the position 0
$pptx->setActiveSlide(array('position' => 1));

$activeSlide = $pptx->getActiveSlide();
var_dump($activeSlide);