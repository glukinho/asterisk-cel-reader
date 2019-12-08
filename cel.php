<?php

include 'classes/CEL_Call.php';
include 'classes/CEL_Event.php';
include 'classes/CEL_Events_Collection.php';
include 'classes/MPA_CEL_Call.php';
include 'classes/safemysql.class.php';

$db_options = array(
	'user'    => 'root',
	'pass'    => '',
	'db'      => 'asteriskcdrdb',
	'charset' => 'latin1'
);

// $linkedid = '1575633306.274662';
// $linkedid = '1575719830.275368';
// $linkedid = '1575644057.275354';



// $linkedid = '1567403575.132993';

// $call = new MPA_CEL_Call($linkedid, $db_options);
// $call->load();
// var_dump($call->getDirection());
// var_dump($call->isAnswered());
// var_dump($call->getStartTimestamp());
// var_dump($call->isWorkTime());
// die;



$calls_arr = file('calls-sep.txt', FILE_IGNORE_NEW_LINES);
// print_r ($calls_arr);

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





// print_r($call->getEvents()->filter('eventtype', 'CHAN_START'));

// print_r($call);
// print_r($call->getEvents()->filter('eventtype', 'BRIDGE_ENTER')->last()->get('extra'));
// $first_channel = $call->getEvents()->filter('eventtype', 'CHAN_START')->first()->get('channame');
// echo $first_channel;
// echo ( begins($first_channel, 'SIP/mastertel') ? 'incoming' : 'outgoing' );

// var_dump($call->getDirection());
// var_dump($call->isAnswered());
// var_dump($call->getStartTimestamp());
// var_dump($call->isWorkTime());











