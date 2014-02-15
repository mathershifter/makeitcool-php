<?php
require_once dirname(__FILE__) . '/../library/MC.php';
MC::boot();

$conn  = MC_Cacti2_Db::connect('cacti-server', 'cacti', 'c4ct1');

//print_r($conn);

$device = MC_Cacti2_Db_Table_Device::find(31);

print_r($device);

print_r($device->graph());

#$graph = MC_Cacti2_Db_Table_Graph::find(30);
#
#print_r($graph);