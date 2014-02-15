<?php
require_once dirname(__FILE__) . '/../../library/MC.php';
MC::boot();

//
set_time_limit(3600);

//
if (!isset($_GET['debug']) || !$_GET['debug']) {
    error_reporting(ini_get('error_reporting') ^ E_USER_NOTICE);
}

//
$cacti_servers = array(
    'cacti1'        => array('localhost',  'cactiuser', 'cactiuser')
);

//
$resolution             = isset($_GET['resolution']) ? $_GET['resolution'] : 900;

//
$maxrows                = isset($_GET['maxrows']) ? $_GET['maxrows'] : 8640;

//
$consolidation_function = false;

//
$start_time             = isset($_GET['start_time']) ? MC_Time::parse($_GET['start_time']) : MC_Time::parse('1 hour ago')->floor('h');

//
$end_time               = isset($_GET['end_time'])   ? MC_Time::parse($_GET['end_time'])   : MC_Time::now()->floor('h')->offset(-1);

//
$include                = isset($_GET['host_filter']) ? $_GET['host_filter'] : '.*';
#$include                = isset($_GET['include']) ? $_GET['include'] : '.*';


//
$exclude                = isset($_GET['exclude']) ? $_GET['exclude'] : '^$';

//
if (isset($_GET['server']) && $_GET['server']) {
    $cacti_servers = array_intersect_key($cacti_servers, array($_GET['server'] => 1));
}

//
$memcache = new Memcache;
$memcache->connect('localhost', 11211);

//
$dbh                    = false;

//
$current_server         = false;

//
$csv_file_name          = basename(__FILE__, '.php') . "_" . strftime("%Y%m%d%H%M00", $start_time->toI()) . ".csv";

//
header("Content-type: application/csv");
header("Content-Disposition: attachment; filename=$csv_file_name");
header("Pragma: no-cache");
header("Expires: 0");

// print heading
echo 'hostname,host_addr,if_name,if_descr,if_alias,if_index,if_ip,if_oper_status,if_speed,resolution,timestamp,timestring,traffic_in,traffic_out,unicast_in,unicast_out,errors_in,errors_out,discards_in,discards_out' . "\n";

foreach ($cacti_servers as $current_server=>$config) {
    //
    $server                 = $config[0];
    
    //
    $user                   = $config[1];
    
    //
    $password               = $config[2];
    
    //
    $dbh = mysql_connect($server, $user, $password);
    
    if (!$dbh) {
        trigger_error("Failed to connect to MySQL server '$server' as $user", E_USER_WARNING);
        continue;
    }
    
    mysql_select_db('cacti', $dbh);
    
    //echo "$current_server > Getting SNMP query ID...\n";
    $snmp_query_id          = mysql_query_cell("SELECT id FROM snmp_query WHERE name = 'SNMP - Interface Statistics'");
    
    //echo "$current_server > Getting graph templates...\n";
    $graph_templates        = mysql_query_column("SELECT DISTINCT graph_template_id FROM graph_local WHERE snmp_query_id = $snmp_query_id");
    
    
    //echo "$current_server > Getting hosts...\n";
    $hosts                  = mysql_query_assoc("SELECT DISTINCT 
            h.id, h.hostname, h.description
        FROM host_snmp_cache hsc, host h
        WHERE
            hsc.host_id = h.id 
            AND h.disabled != 'on'
            AND h.status = 3
            AND (
                (h.description REGEXP  '$include' OR h.hostname REGEXP  '$include')
                AND (h.description NOT REGEXP  '$exclude' OR h.hostname NOT REGEXP  '$exclude')
            )
            AND hsc.snmp_query_id = $snmp_query_id");
    
    foreach ($hosts as $host) {
        
        $host_id = $host['id'];
        
        $snmp_indexes = mysql_query_column("SELECT DISTINCT snmp_index FROM host_snmp_cache WHERE snmp_query_id = $snmp_query_id AND host_id = $host_id AND snmp_index > 0");
        
        foreach ($snmp_indexes as $snmp_index) {
            $graph_ids = mysql_query_column("SELECT id FROM graph_local WHERE host_id = $host_id AND snmp_query_id = $snmp_query_id AND snmp_index = '$snmp_index'");
            
            if (empty($graph_ids)) {
                // No graphs, skip
                continue;
            }
            
            $snmp_cache = mysql_query_pair("SELECT field_name, field_value FROM host_snmp_cache WHERE snmp_index = '$snmp_index' AND snmp_query_id = $snmp_query_id AND host_id = $host_id", 'field_name', 'field_value');

            $response = xport_graphs($graph_ids);
            
            $step = @$response['metadata']['step'] > 0 ? $response['metadata']['step'] : null;
            
            $ds_series_data = array();
            
            $ds_base_data   = array(
                'hostname'       => $host['description'],
                'host_addr'      => $host['hostname'],
                'if_name'        => isset($snmp_cache['ifName'])       ? $snmp_cache['ifName']                : null,
                'if_descr'       => isset($snmp_cache['ifDescr'])      ? $snmp_cache['ifDescr']               : null,
                'if_alias'       => isset($snmp_cache['ifAlias'])      ? $snmp_cache['ifAlias']               : null,
                'if_hw_addr'     => isset($snmp_cache['ifHwAddr'])     ? $snmp_cache['ifHwAddr']              : null,
                'if_index'       => isset($snmp_cache['ifIndex'])      ? $snmp_cache['ifIndex']               : null,
                'if_ip'          => isset($snmp_cache['ifIP'])         ? $snmp_cache['ifIP']                  : null,
                'if_oper_status' => isset($snmp_cache['ifOperStatus']) ? $snmp_cache['ifOperStatus']          : null,
                'if_speed'       => isset($snmp_cache['ifHighSpeed'])  ? ($snmp_cache['ifHighSpeed'] * 1000000) : (isset($snmp_cache['ifSpeed']) ? $snmp_cache['ifSpeed'] : null),
                'if_type'        => isset($snmp_cache['ifType'])       ? $snmp_cache['ifType']                : null,
                'resolution'     => $step,
                'timestamp'      => null,
                'time'           => null,
                'traffic_in'     => null,
                'traffic_out'    => null,
                'unicast_in'     => null,
                'unicast_out'    => null,
                'discards_in'    => null,
                'discards_out'   => null,
                'errors_in'      => null,
                'errors_out'     => null
            );
            
            if (@$response['data']) {
                foreach ($response['data'] as $row) {
                    $ds_row_data = array_merge($ds_base_data, array(
                        'timestamp'      => $row['timestamp'],
                        'time'           => MC_Time::at($row['timestamp'])->toS()
                    ));
                    
                    if ($step > 0) {
                        $ds_row_data = array_merge($ds_row_data, array(
                            'traffic_in'     => isset($row['traffic_in'])   ? round($row['traffic_in']   * $resolution) : null,
                            'traffic_out'    => isset($row['traffic_out'])  ? round($row['traffic_out']  * $resolution) : null,
                            'unicast_in'     => isset($row['unicast_in'])   ? round($row['unicast_in']   * $resolution) : null,
                            'unicast_out'    => isset($row['unicast_out'])  ? round($row['unicast_out']  * $resolution) : null,
                            'discards_in'    => isset($row['discards_in'])  ? round($row['discards_in']  * $resolution) : null,
                            'discards_out'   => isset($row['discards_out']) ? round($row['discards_out'] * $resolution) : null,
                            'errors_in'      => isset($row['errors_in'])    ? round($row['errors_in']    * $resolution) : null,
                            'errors_out'     => isset($row['errors_out'])   ? round($row['errors_out']   * $resolution) : null
                        ));
                    }
                    
                    $ds_series_data[] = $ds_row_data;
                }
                
                foreach ($ds_series_data as $ds_series_row) {
                    echo MC_Template2::process('"{hostname}","{host_addr}","{if_name}","{if_descr}","{if_alias}",{if_index},"{if_ip}","{if_oper_status}",{if_speed},{resolution},{timestamp},"{time}",{traffic_in},{traffic_out},{unicast_in},{unicast_out},{errors_in},{errors_out},{discards_in},{discards_out}' . "\n", $ds_series_row);
                }
                
                flush();
            }
        }
    }

}

function xport_graphs($graph_ids)
{
    global $start_time, $end_time, $resolution, $maxrows, $memcache;
    
    $data = array();
    
    if ($rrd = rrd_connect()) {
        try {
            
            $xport = $memcache->get(memcache_key(join(':', $graph_ids)));
            if (!$xport) {
                trigger_error('Memcache miss in ' . __FUNCTION__, E_USER_NOTICE);
                $xport = build_xport($graph_ids);
                $memcache->set(memcache_key(join(':', $graph_ids)), $xport, 0, memcache_expire());
            }
            
            $xport = "--maxrows $maxrows --start " . $start_time->toI() . " --end " . $end_time->toI() . " --step $resolution {$xport}";
            
            trigger_error("DEBUG: Running: 'rrdtool xport $xport'", E_USER_NOTICE);
            
            $data = $rrd->xport($xport)->getResponse()->toArray();
        } catch (MC_Rrd_Exception $e) {
            trigger_error('RRD Xport failed for graphs (' . join(', ', $graph_ids) . ') with: ' . $e->getMessage(), E_USER_NOTICE);
        }
        
        $rrd->close();
    }
    return $data;
}

function rrd_connect()
{
    global $server, $directory;
    
    $rrd = false;
    try {
        $rrd = new MC_Rrd('tcp://' . $server);
        
        if ($directory) {
            $rrd->cd($directory);    
        } else {
            // try to change directories to "cacti",
            // but continue if unsuccessful
            try {
                $rrd->cd('cacti');    
            } catch (MC_Rrd_Exception $e) {
                /* ignore */
            }
        }
    } catch (MC_Rrd_Exception $e) {
        trigger_error("Failed to connect to rrdsrv: " . $e->getMessage(), E_USER_WARNING);
    }
    
    return $rrd;
}


function build_xport($graph_ids)
{
    $graph_ids = is_int($graph_ids) ? array($graph_ids) : $graph_ids; 
    
    $def   = A();
    $xport = A();
    
    $used_data_sources = A();
    
    $graph_data_sources = mysql_query_assoc("SELECT
    dl.id
    , gti.consolidation_function_id AS cf_id
    , dtr.data_source_name AS name
    , dtd.data_source_path
FROM
    graph_templates_item AS gti
    , data_template_rrd AS dtr
    , data_local AS dl
    , data_template_data AS dtd
WHERE 
    gti.task_item_id = dtr.id
    AND dtr.local_data_id = dl.id
    AND dl.id = dtd.local_data_id
    AND dtd.active = 'on'
    AND gti.local_graph_id IN (" . join(', ', $graph_ids) . ")");
    
    foreach ($graph_data_sources as $ds) {
        
        $_ds_unique = "{$ds['name']}_{$ds['id']}";
        
        $rrd = str_replace('<path_rra>/', '', $ds['data_source_path']);
        
        if ($used_data_sources->contains($_ds_unique)) {
            continue;
        } else {
            $used_data_sources->push($_ds_unique);    
        }
        
        // collect data sources
        $def->push("DEF:{$_ds_unique}={$rrd}:{$ds['name']}:AVERAGE");

        $xport->push("XPORT:{$_ds_unique}:" . S($ds['name'])->underscorize());
    }
    
    return $def->merge($xport)->join(' ');   
}


function mysql_query_one($sql)
{
    global $dbh, $memcache;
    
    $key = memcache_key($sql);

    if (($data = unserialize($memcache->get($key))) === FALSE) {
        
        $data = array();
        
        trigger_error('Memcache miss in ' . __FUNCTION__, E_USER_NOTICE);
        if ($result = mysql_query($sql)) {
            $data = mysql_fetch_assoc($result);
            mysql_free_result($result);
        } else {
            trigger_error('Invalid query: ' . mysql_error(), E_USER_ERROR);
        }
        $memcache->set($key, serialize($data), 0, memcache_expire());
    }
    return $data;
}

function mysql_query_cell($sql, $field=false)
{
    global $dbh, $memcache;
    
    $key = memcache_key($sql);

    if (($data = unserialize($memcache->get($key))) === FALSE) {
        
        $data = '';
        
        trigger_error('Memcache miss in ' . __FUNCTION__, E_USER_NOTICE);
        if ($result = mysql_query($sql)) {
            $data = mysql_fetch_assoc($result);
            mysql_free_result($result);
        } else {
            trigger_error('Invalid query: ' . mysql_error(), E_USER_ERROR);
        }
                
        $memcache->set($key, serialize($data), 0, memcache_expire());
    }
    return $field ? (isset($data[$field]) ? $data[$field] : false) : array_shift($data);
}

function mysql_query_column($sql, $field=false)
{
    global $dbh, $memcache;

    $key = memcache_key($sql);

    if (($data = unserialize($memcache->get($key))) === FALSE) {
        
        $data = array();
        
        trigger_error("Memcache miss in " . __FUNCTION__, E_USER_NOTICE);
        
        trigger_error("Running query: " . $sql, E_USER_NOTICE);
        
        if ($result = mysql_query($sql)) {
            while ($row = mysql_fetch_assoc($result)) {
                $data[] = $field ? (isset($row[$field]) ? $row[$field] : false) : array_shift($row);;
            }
            mysql_free_result($result);
        } else {
            trigger_error('Invalid query: ' . mysql_error(), E_USER_WARNING);
        }
        
        trigger_error("Setting memcache $key with " . serialize($data), E_USER_NOTICE);
        
        $memcache->set($key, serialize($data), 0, memcache_expire());
    }
    return $data;
}

function mysql_query_pair($sql, $a, $b)
{
    global $dbh, $memcache;
    
    $key = memcache_key($sql);

    if (($data = unserialize($memcache->get($key))) === FALSE) {
        
        $data = array();
    
        trigger_error('Memcache miss in ' . __FUNCTION__, E_USER_NOTICE);
        if ($result = mysql_query($sql)) {
            while ($row = mysql_fetch_assoc($result)) {
                $data[$row[$a]] = $row[$b];
            }
            mysql_free_result($result);
        } else {
            trigger_error('Invalid query: ' . mysql_error(), E_USER_ERROR);
        }
        
        $memcache->set($key, serialize($data), 0, memcache_expire());
    }
    return $data;
}

function mysql_query_assoc($sql)
{
    global $dbh, $memcache;
    
    $key = memcache_key($sql);

    if (($data = unserialize($memcache->get($key))) === FALSE) {
        
        $data = array();
    
        trigger_error('Memcache miss in ' . __FUNCTION__, E_USER_NOTICE);
        if ($result = mysql_query($sql)) {
            while ($row = mysql_fetch_assoc($result)) {
                $data[] = $row;
            }
            mysql_free_result($result);
        } else {
            trigger_error('Invalid query: ' . mysql_error(), E_USER_ERROR);
        }
        
        $memcache->set($key, serialize($data), 0, memcache_expire());
    }
    
    return $data;
}

function memcache_expire()
{
    return (int) ((86400/2) + rand(0, 86400));        
}

function memcache_key($string) {
    global $current_server;
    $h = hash("md5", serialize(array($current_server, $string)));
    return $h;
}

function parse_time_param($time)
{
    if (is_int($time)) {
    }
}