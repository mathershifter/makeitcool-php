<?php
require_once dirname(__FILE__) . '/../library/Mic.php';
Mic::boot();

$conn  = Mic_Cacti2_Db::connect('cacti-server', 'cacti', 'c4ct1');

//print_r($conn);

$device = Mic_Cacti2_Db_Table_Device::find(31);

print_r($device);

print_r($device->graph());

#$graph = Mic_Cacti2_Db_Table_Graph::find(30);
#
#print_r($graph);