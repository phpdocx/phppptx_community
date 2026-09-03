<?php
// add date properties in a PPTX created from scratch

require_once __DIR__ . '/../../../Classes/Phppptx/Create/CreatePptx.php';

$pptx = new Phppptx\Create\CreatePptx();

$properties = array(
    'created' => '2016-11-21T09:00:00Z', // force a value
    'modified' => substr(date(DATE_W3C), 0, 19) . 'Z', // dynamic value
    'lastModifiedBy' => 'phppptxuser',
);
$pptx->addProperties($properties);

$pptx->savePptx(__DIR__ . '/example_addProperties_2');