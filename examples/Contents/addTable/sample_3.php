<?php
// add tables using PptxFragments and text styles in a PPTX created from scratch

require_once __DIR__ . '/../../../Classes/Phppptx/Create/CreatePptx.php';

$pptx = new Phppptx\Create\CreatePptx(array('layout' => 'Blank'));

$content = array(
    array(
        array(
            'text' => 'Title A',
            'bold' => true,
            'align' => 'center',
        ),
        array(
            'text' => 'Title B',
            'bold' => true,
            'align' => 'center',
        ),
        array(
            'text' => 'Title C',
            'bold' => true,
            'align' => 'center',
        ),
        array(
            'text' => 'Title D',
            'bold' => true,
            'align' => 'center',
        ),
    ),
    array(
        array(
            'text' => 'Cell 2.1',
        ),
        'Cell 2.2',
        'Cell 2.3',
        array(
            'text' => 'Cell 2.4',
        ),
    ),
    array(
        array(
            'text' => 'Cell 3.1',
        ),
        'Cell 3.2',
        'Cell 3.3',
        array(
            'text' => 'Cell 3.4',
            'align' => 'right',
            'backgroundColor' => '679EB5',
            'textDirection' => 'vert',
            'verticalAlign' => 'middle',
        ),
    ),
);
$position = array(
    'coordinateX' => 850000,
    'coordinateY' => 1800000,
    'sizeX' => 10500000,
    'sizeY' => 750000,
);
$rowStyles = array(
    array(),
    array(),
    array(
        'height' => 1000000,
    ),
);
$pptx->addTable($content, $position, array(), $rowStyles);

$pptx->addSlide(array('active' => true));

$contentTextA = array(
    array(
        'text' => 'Title ',
        'bold' => true,
        'font' => 'Arial',
        'fontSize' => 30,
    ), array(
        'text' => 'A',
        'color' => '406094',
        'fontSize' => 40,
    )
);
$textFragmentA = new Phppptx\Elements\PptxFragment();
$textFragmentA->addText($contentTextA, array());

$contentTextB = array(
    array(
        'text' => 'Title ',
        'bold' => true,
        'font' => 'Arial',
        'fontSize' => 30,
    ), array(
        'text' => 'B',
        'color' => '406094',
        'fontSize' => 40,
    )
);
$textFragmentB = new Phppptx\Elements\PptxFragment();
$textFragmentB->addText($contentTextB, array());

$contentTextC= array(
    array(
        'text' => 'Title ',
        'bold' => true,
        'font' => 'Arial',
        'fontSize' => 30,
    ), array(
        'text' => 'C',
        'color' => '406094',
        'fontSize' => 40,
    )
);
$textFragmentC = new Phppptx\Elements\PptxFragment();
$textFragmentC->addText($contentTextC, array());

$contentTextD = array(
    array(
        'text' => 'Title ',
        'bold' => true,
        'font' => 'Arial',
        'fontSize' => 30,
    ), array(
        'text' => 'D',
        'color' => '406094',
        'fontSize' => 40,
    )
);
$paragraphStyles = array(
    'align' => 'center',
);
$textFragmentD = new Phppptx\Elements\PptxFragment();
$textFragmentD->addText($contentTextD, array(), $paragraphStyles);

$contentLink = array(
    'text' => 'My link',
    'hyperlink' => 'https://www.phppptx.com'
);
$linkFragment = new Phppptx\Elements\PptxFragment();
$linkFragment->addText($contentLink, array());

$content = array(
    array(
        $textFragmentA,
        $textFragmentB,
        $textFragmentC,
        array(
            'text' => $textFragmentD,
            'backgroundColor' => '7FAAF0',
        )
    ),
    array(
        '',
        'Cell 2.2',
        'Cell 2.3',
        array(
            'text' => 'Cell 2.4',
        ),
    ),
    array(
        'Cell 3.1',
        'Cell 3.2',
        'Cell 3.3',
        $linkFragment,
    ),
);

$position = array(
    'coordinateX' => 850000,
    'coordinateY' => 1800000,
    'sizeX' => 10500000,
    'sizeY' => 750000,
);
$pptx->addTable($content, $position);

$pptx->savePptx(__DIR__ . '/example_addTable_3');