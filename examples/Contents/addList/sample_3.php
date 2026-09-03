<?php
// add list contents using PptxFragments in layout placeholders in a PPTX created from scratch

require_once __DIR__ . '/../../../Classes/Phppptx/Create/CreatePptx.php';

$pptx = new Phppptx\Create\CreatePptx(array('layout' => 'Title and Content'));

$paragraphStyles = array(
    'noBullet' => false,
    'listStyles' => array(
        'type' => 'decimal',
        'indent' => -500000,
    ),
);

$contentA = array(
    'text' => 'List item 1',
    'bold' => true,
    'underline' => 'single',
);
$listFragmentA = new Phppptx\Elements\PptxFragment();
$listFragmentA->addText($contentA, array(), $paragraphStyles);

$contentB = array(
    array(
        'text' => 'Sublist',
        'highlight' => '628A54',
    ),
    array(
        'text' => ' item 1.1',
        'bold' => true,
        'italic' => true,
        'font' => 'Times New Roman',
    ),
);
$listFragmentB = new Phppptx\Elements\PptxFragment();
$listFragmentB->addText($contentB, array(), $paragraphStyles);

$contentC = array(
    array(
        'text' => 'List item ',
        'bold' => true,
        'font' => 'Arial',
        'fontSize' => 30,
    ), array(
        'text' => '3',
        'color' => '406094',
        'fontSize' => 40,
    )
);
$listFragmentC = new Phppptx\Elements\PptxFragment();
$listFragmentC->addText($contentC, array(), $paragraphStyles);

$contentLink = array(
    'text' => 'My link',
    'hyperlink' => 'https://www.phppptx.com'
);
$linkFragment = new Phppptx\Elements\PptxFragment();
$linkFragment->addText($contentLink, array(), $paragraphStyles);

$content = array(
    $listFragmentA,
    array(
        $listFragmentB,
    ),
    $listFragmentC,
    $linkFragment,
);
$pptx->addList($content, array('placeholder' => array('name' => 'Content Placeholder 2')));

$pptx->savePptx(__DIR__ . '/example_addList_3');