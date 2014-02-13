<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 foldmethod=marker: */

/**
 * Toran Data Export Utility
 *
 * PHP version 5.3+
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @category  utilities
 * @package   cacti_export
 * @author    Jesse R. Mather <jrmather@gmail.com>
 * @copyright 2011 Nobody
 * @license   MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @version   SVN: $Id: $
 * @filesource
 */
 
require_once dirname(__FILE__) . '/../../library/Mic.php';
Mic::boot();

//
set_time_limit(60);

$memcache = new Memcache();
$memcache->connect('localhost', 11211); 

$request = new Mic_Http_Request;

$params     = $request->params;

$end_time   = null;
$start_time = null;

if ($params->key) {
    $_page = $memcache->get($params->key);
    
    if ($_page) {
        echo $_page;
        exit;    
    } 
}

if ($params->end_time) {
    $end_time = Mic_Time::parse($params->end_time);
} else {
    $end_time = Mic_Time::parse('midnight');
}

if ($params->start_time) {
    $start_time = Mic_Time::parse($params->start_time);
} else {
    $start_time = Mic_Time::parse($end_time->offset(-604800)->toS());
}

$resolution = $params->resolution ?: 'hourly';

if (!$params->interfaces)  {
?>

<form method="post">
    <label for="male">Start Date</label>
    <input type="text" name="start_time" id="start_time" />
    <br />
    
    <label for="male">End Date</label>
    <input type="text" name="end_time" id="start_time" />
    <br />
    
    <strong>Enter one device and interface per line: (e.g. <hostname> Te3/1)</strong>
    <br />
    
    <textarea rows="25" cols="50" name="interfaces"></textarea> <br>
    <input type="submit" value="Submit" />
</form>

<?php
} else {
    $key = md5("{$params}");

    ob_start();   
?>
    <a href="<?php echo $request->path; ?>">Reset</a> | <a href="?key=<?php  echo $key; ?>">Link</a>
<?php
    
    $lines = S($params->interfaces)->rsplit("/\r\n/");
    
    foreach ($lines as $line) {
        $_line = S($line)->rmatch('/(?<device>[\w\-\.]+)[\s\,\|\;](?<interface>[\w\-\.\/]+)+/');
        
        $device    = $_line->device;
        $interface = S($_line->interface)->replace('/[\/\.]/', '-');
        
        $chart_url = Mic_Template2::process(
            "http://some-web-service.mathershifter.com/api/v1/devices/{device}" .
            "/interfaces/{interface}/chart.png?chart_name=interface_traffic&" .
            "start_time={start_time}&end_time={end_time}&resolution={resolution}",
            array(
                'device'     => $device,
                'interface'  => $interface,
                'start_time' => $start_time->format('Y-m-d'),
                'end_time'   => $end_time->format('Y-m-d'),
                'resolution' => $resolution
            )
        );
        
        $device_url    = "http://some-web-service.mathershifter.com/devices/{$device}";
        $interface_url = "{$device_url}/interfaces/$interface";
?>

<div style="border: 2px solid gray; margin-top: 10px;">
<ul style="list-style: none;">
    <li>
        <a href="<?php echo $device_url; ?>"><?php echo $device; ?></a>
        <a href="<?php echo $interface_url; ?>"><?php echo $_line->interface; ?></a></li>
    <li>
        <img src="<?php echo $chart_url; ?>"></img>
    </li>
</ul>
</div>

<?php
    }
    
    $page = ob_get_contents();
    
    $memcache->set($key, $page, false, 86400*30);
    
    ob_end_flush();
}