<?php
// add a comment in a PPTX created from scratch

require_once __DIR__ . '/../../../Classes/Phppptx/Create/CreatePptx.php';

$pptx = new Phppptx\Create\CreatePptx();

// at least one author must be added
$pptx->addCommentAuthor('phppptx');
$pptx->addCommentAuthor('phpdocx');

$pptx->addComment('New comment', 'phppptx');

// add a new slide as the internal active slide
$pptx->addSlide(array('layout' => 'Title and Content', 'active' => true));

$pptx->addComment('Another comment', 'phppptx', array('coordinateX' => 3000, 'coordinateY' => 600));

$pptx->addComment('My comment', 'phpdocx', array('coordinateX' => 3400, 'coordinateY' => 600));

$pptx->savePptx(__DIR__ . '/example_addComment_2');