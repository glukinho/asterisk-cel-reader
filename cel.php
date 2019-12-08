<?php

include 'classes/CEL_Call.php';
include 'classes/CEL_Event.php';
include 'classes/CEL_Events_Collection.php';
include 'classes/MPA_CEL_Call.php';
include 'classes/safemysql.class.php';

$db_options = array(
	'user'    => 'user',
	'pass'    => 'pass',
	'db'      => 'asteriskcdrdb',
	'charset' => 'utf8'
);


$calls_arr = file('calls-sep.txt', FILE_IGNORE_NEW_LINES);

$fields = [
	'linkedid',
	'start',
	'weekday',
	'worktime',
	'answered',
	'holdtime',
	'direction',
	'cid',
	'did',
	'source_peer',
	'bridged_peer',
];

echo implode(',', $fields) . PHP_EOL;


foreach ($calls_arr as $linkedid) {
	// echo $linkedid . PHP_EOL;
	
	$call = new MPA_CEL_Call($linkedid, $db_options);
	$call->load();
	
	$csv = [
		$linkedid,
		$call->getStartTimestamp()->format('Y-m-d H:i:s'),
		$call->getStartTimestamp()->format('D'),
		$call->isWorkTime() ? '1' : '0',
		$call->isAnswered() ? '1' : '0',
		$call->getTimeBeforeHumanAnswer(),
		$call->getDirection(),
		$call->getCID(),
		$call->getDID(),
		$call->getSourcePeer(),
		$call->getBridgedPeer(),

	];
	
	echo implode(',', $csv) . PHP_EOL;
}
