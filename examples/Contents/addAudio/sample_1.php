<?php
// add audios in a PPTX created from scratch

require_once __DIR__ . '/../../../Classes/Phppptx/Create/CreatePptx.php';

$pptx = new Phppptx\Create\CreatePptx();

$position = array(
    'coordinateX' => 1400000,
    'coordinateY' => 4500000,
    'sizeX' => 2000000,
    'sizeY' => 2000000,
);
$pptx->addAudio(__DIR__ . '/../../files/audio.mp3', $position);

$pptx->addSlide(array('layout' => 'Blank', 'active' => true));

$position = array(
    'coordinateX' => 1500000,
    'coordinateY' => 2000000,
    'sizeX' => 2000000,
    'sizeY' => 2000000,
);
$pptx->addAudio(__DIR__ . '/../../files/audio.flac', $position);

$position = array(
    'coordinateX' => 4000000,
    'coordinateY' => 2000000,
    'sizeX' => 2000000,
    'sizeY' => 2000000,
);
$pptx->addAudio(__DIR__ . '/../../files/audio.wav', $position);

$position = array(
    'coordinateX' => 6500000,
    'coordinateY' => 2000000,
    'sizeX' => 2000000,
    'sizeY' => 2000000,
);
$pptx->addAudio(__DIR__ . '/../../files/audio.wma', $position);

$pptx->savePptx(__DIR__ . '/example_addAudio_1');