<?php
require_once dirname(__FILE__) . '/../library/MC.php';
MC::boot();

/* die early if expression is empty */
if ($argc < 3) {
    echo "Invalid arguments\n";
    echo "Usage: " . basename($argv[0]) . " <host> <options>\n";
    die();
}

$host = $argv[1];

$arguments = preg_replace('/,\s*/', ' ', 
    join(' ',
        array_slice($argv, 2)
    )
);

$rrd = new MC_Rrd($host);
$rrd->fetch($arguments);

$xml = new XMLWriter();
$xml->openURI('php://output');
$xml->startDocument('1.0');
$xml->setIndent(4); 
$xml->startElement('fetch');
foreach ($rrd->response() as $row) {
    $xml->startElement('row');
    foreach ($row as $key=>$val) {
        if ($key == 'timestamp') {
            $val = date('Y-m-d H:i:s', $val->c());
        }
        $xml->writeElement($key, $val); 
    }
    $xml->endElement(/* 'row' */); 
}
$xml->endElement(/* $command */); 