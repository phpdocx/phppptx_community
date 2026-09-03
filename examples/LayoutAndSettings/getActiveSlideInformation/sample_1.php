<?php
// get the internal active slide information in a PPTX created from scratch

require_once __DIR__ . '/../../../Classes/Phppptx/Create/CreatePptx.php';

$pptx = new Phppptx\Create\CreatePptx();

$activeSlideInformation = $pptx->getActiveSlideInformation();
var_dump($activeSlideInformation);