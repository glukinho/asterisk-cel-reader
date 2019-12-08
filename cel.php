<?php

// execute like this:
// php cel.php | tee calls-info.csv

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

// prepare calls.txt file with linkedids
// for example, using SQL:
// select linkedid from cel where eventtime >= '2019-09-01 00:00:00' and eventtype='LINKEDID_END' into outfile 'calls.txt';
$calls_arr = file('calls.txt', FILE_IGNORE_NEW_LINES);

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
	
	
	// you can extend CEL_Call or MPA_CEL_Call classes with your own business logic 
	// (for example, you can check call direction based on DID length or work time with your own schedule)
	// MPA_CEL_Call is just one of many possible logic implementations, special for each PBX installation
	$call = new MPA_CEL_Call($linkedid, $db_options);
	
	// loads CEL events of given linkedid from DB
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
