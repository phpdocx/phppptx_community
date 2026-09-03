<?php
// add comment authors in a PPTX created from scratch

require_once __DIR__ . '/../../../Classes/Phppptx/Create/CreatePptx.php';

$pptx = new Phppptx\Create\CreatePptx();

$pptx->addCommentAuthor('phppptx');
$pptx->addCommentAuthor('phpdocx');

$pptx->savePptx(__DIR__ . '/example_addCommentAuthor_1');