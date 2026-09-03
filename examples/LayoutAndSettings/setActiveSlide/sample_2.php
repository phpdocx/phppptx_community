<?php
// create a new slide as the internal active one in a PPTX created from scratch

require_once __DIR__ . '/../../../Classes/Phppptx/Create/CreatePptx.php';

$pptx = new Phppptx\Create\CreatePptx();

$activeSlide = $pptx->getActiveSlide();
var_dump($activeSlide);

$pptx->addSlide(array('active' => true));

$activeSlide = $pptx->getActiveSlide();
var_dump($activeSlide);