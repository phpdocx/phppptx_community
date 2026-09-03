<?php
// add shapes in a PPTX created from scratch

require_once __DIR__ . '/../../../Classes/Phppptx/Create/CreatePptx.php';

$pptx = new Phppptx\Create\CreatePptx();

$position = array(
    'coordinateX' => 1470000,
    'coordinateY' => 600000,
    'sizeX' => 500000,
    'sizeY' => 450000,
);
$options = array(
    'fillColor' => '#FF0000',
    'outlineColor' => '#0000FF',
);
$pptx->addShape('triangle', $position, $options);

$position = array(
    'coordinateX' => 970000,
    'coordinateY' => 2250000,
    'sizeX' => 3800000,
    'sizeY' => 1450000,
);
$options = array(
    'outlineColor' => '#0000FF',
    'tailEnd' => 'triangle',
);
$pptx->addShape('straightConnector1', $position, $options);

$position = array(
    'coordinateX' => 970000,
    'coordinateY' => 2250000,
    'sizeX' => 3800000,
    'sizeY' => 1450000,
);
$options = array(
    'flipV' => true,
    'outlineColor' => '#0000FF',
    'tailEnd' => 'triangle',
);
$pptx->addShape('straightConnector1', $position, $options);

$pptx->savePptx(__DIR__ . '/example_addShape_1');