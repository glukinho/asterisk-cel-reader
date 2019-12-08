# asterisk-cel-reader
Class is used to get business-oriented information (reports) over tech-oriented Asterisk CEL events.

Only events stored in database are supported, no realtime mode.

Tested on FreePBX 14.0.5.25 (Asterisk 13.19.1).

See cel.php and classes files for examples.

## Asterisk channels and calls
It is important to understand difference between channel and call, uniqueid and linkedid in Asterisk logic. Many **channels** may take part in one **call**, each having its own **uniqueid** and one **linkedid** shared between all channels:

For example, a user calls to external number. We have channels all assciated with one call with linkedid 1234567890.123:

| Channel name | Uniqueid | Linkedid | Description |
| --- | --- | --- | --- |
| SIP/user | 1234567890.123 | **1234567890.123** | channel from user to Asterisk |
| SIP/provider | 1234567890.456 | **1234567890.123** | channel from Asterisk to SIP provider |

## Basic usage
As for this class, a call is a set of CEL events associated with one linkedid. CEL_Call class represents one single call. It has many CEL_Event subclasses represented by CEL_Events_Collection class.

Usage:
```
$linkedid = '1234567890.123456';
  
$call = new MPA_CEL_Class($linkedid, $db_options)
$call->load();  // Loads CEL events from database.

// You can filter events collection:

$call->getEvents()->filter('eventtype', 'BRIDGE_ENTER');   // returns collection of BRIDGE_ENTER events


// You can pipe several filters to apply them in a row (this is killer feature :)
// This will return collection of events with eventtype = BRIDGE_ENTER and channame begins from 'SIP/':

$call->getEvents()
  ->filter('eventtype', 'BRIDGE_ENTER')
  ->filter('channame', 'SIP/', 'begins');


// Get last BRIDGE_ENTER event:

$call->getEvents()->filter('eventtype', 'BRIDGE_ENTER')->last();
```

See methods of CEL_Events_Collection class to see how events can be manipulated.

## Business logic
MPA_CEL_Call (extends CEL_Call) class is a particular adaptation to some PBX I needed to have reports from. It implements some business logic (work time, direction of a call, etc) that suited my situation. You can extend classes as you wish to have your own logic.

Examples:
```
// Seconds before a human actually answered the call 
// (technically, this is number of seconds from first event 
// to the first BRIDGE_ENTER event, or LINKEDID_END event, 
// if none BRIDGE_ENTER found):

echo $call->getTimeBeforeHumanAnswer();


// returns true if the call was inside work hours schedule. See week-based schedule inside MPA_CEL_Call class:

var_dump($call->isWorkTime());
```
