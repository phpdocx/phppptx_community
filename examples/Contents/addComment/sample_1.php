<?php
// add a comment in a PPTX created from scratch

require_once __DIR__ . '/../../../Classes/Phppptx/Create/CreatePptx.php';

$pptx = new Phppptx\Create\CreatePptx();

// at least one author must be added
$pptx->addCommentAuthor('phppptx');

$pptx->addComment('New comment', 'phppptx');

$pptx->savePptx(__DIR__ . '/example_addComment_1');