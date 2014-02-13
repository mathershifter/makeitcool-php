<?php
require_once dirname(__FILE__) . '/../library/Mic.php';
Mic::boot();

$cacti  = Mic_Cacti::connect('cacti', array('host' => 'cacti-server-ip'));

$host = $cacti->getOfType('host', 1143);
var_dump($host->id);

$graphs = $cacti->getOfType('graph_template', 2)->getGraphs($host);
print_r($graphs);

//print_r($cacti);

//$host   = Mic_Cacti_Host::findByDescription('<hostname>');

#print_r($host);

//$graphs = Mic_Cacti_Graph::all($host, false, 'Traffic - Gi1/2 ');

//print_r($graphs);
#print_r($graphs->toArray());

//$graph = Mic_Cacti_Graph::find(5875);

//var_dump($graph);

//print_r($graph->getItems()->toArray());
//print_r($graph->getDataSources()->toArray());
//$graphs = Mic_Cacti_Graph::all($host);
