<?php
// add default and custom properties in a PPTX created from scratch

require_once __DIR__ . '/../../../Classes/Phppptx/Create/CreatePptx.php';

$pptx = new Phppptx\Create\CreatePptx();

$properties = array(
    'title' => 'My title',
    'subject' => 'My subject',
    'creator' => 'The creator',
    'keywords' => 'keyword 1, keyword 2, keyword 3',
    'description' => 'The description could be much longer than this',
    'category' => 'My category',
    'contentStatus' => 'Draft',
    'Manager' => 'The boss',
    'Company' => 'My company',
    'custom' => array(
	    'My custom text' => array('text' => 'This is a reasonably large text'),
	    'My custom number' => array('number' => '4567'),
	    'My custom date' => array('date' => '1962-01-27T23:00:00Z'),
	    'My custom boolean' => array('boolean' => true)
	)
);
$pptx->addProperties($properties);

$pptx->savePptx(__DIR__ . '/example_addProperties_1');