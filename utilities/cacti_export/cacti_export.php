<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 foldmethod=marker: */

/**
 * Cacti Export Utility
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
 * @filesource
 */
 
require_once dirname(__FILE__) . '/../../library/MC.php';
MC::boot();

//
define('CACTI_XPORT_SERVER', 'localhost');
//
define('CACTI_XPORT_USERNAME', 'cactiuser');
//
define('CACTI_XPORT_PASSWORD', 'cactiuser');
//
define('CACTI_XPORT_STEP', 900);
//
define('CACTI_XPORT_START', -3600);
//
define('CACTI_XPORT_END', 'now');
//
define('CACTI_XPORT_CF', 'AVERAGE');
//
define('CACTI_XPORT_FORMAT', 'csv');

main();

function build_rrd_command($data_sources, $options)
{
    
    
    $def = array();
    $xport = array();
    $used = array();
    
    $params = array("xport", "-s {$options['start']}", "-e {$options['end']}",
        "--step {$options['step']}");

    foreach ($data_sources as $ds) {
        $unique = "{$ds['name']}_{$ds['id']}";
        if (!in_array($unique, $used)) {
            array_push($def, "DEF:{$unique}={$ds['rrd']}:{$ds['name']}:" .
                $options['cf']);
            array_push($xport, "XPORT:{$unique}:{$ds['name']}");
            array_push($used, $unique);
        }
    }
    
    return join(' ', array_merge($params, $def, $xport));
}

/**
 * Connects to the cacti database
 *
 * @param array $options
 * @return resource MySQL connection    
 */
function cacti_db_connect($options)
{
    $dbh = mysql_connect($options['server'], $options['username'],
        $options['password']);
    
    if (!$dbh) {
        trigger_error("Failed to connect to MySQL server '{$options['server']}' as '{$options['username']}'",
            E_USER_ERROR);
    }
    
    mysql_select_db('cacti', $dbh);
    
    return $dbh;
}

/**
 * 
 */
function cacti_db_query($sql, $dbh)
{
    $data = array();
    
    if ($result = mysql_query($sql, $dbh)) {
        
        while ($row = mysql_fetch_assoc($result)) {
            $data[] = $row;
        }
        
        mysql_free_result($result);
    } else {
        trigger_error('Invalid query: ' . mysql_error(), E_USER_ERROR);
    }
    
    return $data;
}

/**
 * 
 */
function cacti_db_query_pair($sql, $a, $b, $dbh)
{
    $paired = array();
    
    $data = cacti_db_query($sql, $dbh);
    
    foreach ($data as $row) {
        $paired[$row[$a]] = @$row[$b];
    }
    
    return $paired;
}

/**
 * 
 */
function cacti_get_graph_data_sources($graphs, $dbh)
{
    $grouped_data_sources = array();
    
    $sql = "SELECT DISTINCT data_template_rrd.local_data_id AS id, " .
        "graph_templates_item.local_graph_id AS graph_id, " .
        "data_template_rrd.data_source_name AS name " .
        "FROM graph_templates_item LEFT JOIN data_template_rrd ON " .
        "(graph_templates_item.task_item_id = data_template_rrd.id) " .
        "WHERE graph_templates_item.local_graph_id IN (" .
        join(',', select_column("id", $graphs)) . ") " .
        "AND data_template_rrd.local_data_id IS NOT NULL " .
        "ORDER BY graph_templates_item.id";
    
    $data_sources = cacti_db_query($sql, $dbh);
    
    $sql = "SELECT data_local.id, data_template_data.data_source_path FROM " .
        "data_local LEFT JOIN data_template_data ON " .
        "(data_local.id = data_template_data.local_data_id) WHERE " .
        "data_local.id IN (" .
        join(',', select_column("id", $data_sources)) . ")";
    
    $rrd_files = cacti_db_query_pair($sql, 'id', 'data_source_path', $dbh);
    
    // group by graph id and append rrd file
    foreach ($data_sources as $index=>$ds) {
        
        $data_source_id = $ds['id'];
        $rrd = str_replace('<path_rra>/', '', $rrd_files[$data_source_id]);
        $graph_id = $ds['graph_id'];
        
        if (!isset($grouped_data_sources[$graph_id])) {
            $grouped_data_sources[$graph_id] = array();    
        }
        
        $grouped_data_sources[$graph_id][] = array_merge($ds,
            array('rrd' => $rrd));
    }
    
    return $grouped_data_sources;
}

/**
 * 
 */
function cacti_get_graph_template_graphs($options, $dbh)
{
    $sql =  "SELECT graph_local.id, graph_local.host_id, " .
        "host.hostname AS host_name, host.description AS host_description, " .
        "graph_local.snmp_query_id, graph_local.snmp_index, " .
        "graph_templates_graph.title_cache AS title, " .
        "graph_templates_graph.vertical_label AS y_axis_label " .
        "FROM (graph_templates_graph, graph_local, host) " .
        "WHERE graph_local.id = graph_templates_graph.local_graph_id " .
        "AND graph_local.host_id = host.id ";
    
    if (@$options['host_id'] > 1) {
        $sql .= "AND host.id = " .
            mysql_real_escape_string($options['host_id'], $dbh) . " ";
    }
    
    $sql .= "AND graph_templates_graph.graph_template_id = " .
        mysql_real_escape_string($options['graph_template_id'], $dbh) . " " .
        "ORDER BY host.description, graph_templates_graph.title_cache";
    return cacti_db_query($sql, $dbh);
}

/**
 * 
 */
function cacti_get_query_cache($graph, $dbh)
{
    $qc = array();
    
    if (!$graph['snmp_query_id'] || !$graph['snmp_index']) {
        return array();
    }
    
    $sql = "SELECT field_name, field_value FROM host_snmp_cache " .
           "WHERE host_id = {$graph['host_id']} AND " .
           "snmp_query_id = {$graph['snmp_query_id']} AND " .
           "snmp_index = '{$graph['snmp_index']}' " .
           "ORDER BY field_name";
    foreach (cacti_db_query_pair($sql, 'field_name', 'field_value', $dbh) as $key=>$val) {
        $qc[underscore($key)] = $val;    
    }
    
    return $qc;
}

/**
 * Returns parsed options from cli or url
 *
 * @return array
 */
function get_options()
{
    return php_sapi_name() == 'cli' ? get_options_cli() : get_options_url();
}

/**
 * Returns parsed options from cli
 *
 * @return array
 */
function get_options_cli()
{
    $options = array();
    $argv    = $_SERVER['argv'];
    
    for ($i=1; $i < count($argv); $i++) {      
        switch ($_SERVER['argv'][$i]) {
            case '--help':
            case '-h':
                usage();
                exit(0);
            case '--server':
                $options['server'] = $argv[++$i];
                break;
            case '--username':
            case '-u':
                $options['username'] = $argv[++$i];
                break;
            case '--password':
            case '-p':
                $options['password'] = $argv[++$i];
                break;
            case '--host-id':
            case '--host_id':
                $options['host_id'] = $argv[++$i];
                break;
            case '--graph-template-id':
            case '--graph_template_id':
            case '-i':
                $options['graph_template_id'] = $argv[++$i];
                break;
            case '--step':
            case '-r':
                $options['step'] = $argv[++$i];
                break;
            case '--start':
            case '-s':
                $options['start'] = $argv[++$i];
                break;
            case '--end':
            case '-e':
                $options['end'] = $argv[++$i];
                break;  
            case '--consolidation_function':
            case '--consolidation-function':
            case '--cf':
            case '-c':
                $options['cf'] = $argv[++$i];
                break;
            default:
                usage();
                trigger_error("Undefined option: {$argv[$i]}\n", E_USER_ERROR);
        }    
    }
    
    return $options;
}

/**
 * Returns parsed options from a URL
 *
 * @return array
 */
function get_options_url()
{
    parse_str($_SERVER['QUERY_STRING'], $query);
    return $query;
}

/**
 * 
 */
function main()
{   
    //
    $options = array_merge(array(
        'server'            => CACTI_XPORT_SERVER,
        'username'          => CACTI_XPORT_USERNAME,
        'password'          => CACTI_XPORT_PASSWORD,
        'step'              => CACTI_XPORT_STEP,
        'start'             => CACTI_XPORT_START,
        'end'               => CACTI_XPORT_END,
        'cf'                => CACTI_XPORT_CF,
        'graph_template_id' => null,
        'host_id'           => null,
        'format'            => CACTI_XPORT_FORMAT
    ), get_options());
    
    header("Content-type: application/{$options['format']}");
    
    if ($options['format'] == 'csv') {
        $file_name  = basename(__FILE__, ".php");
        $file_name .= $options['host_id'] ? "_{$options['host_id']}" : "";
        $file_name .= "_{$options['graph_template_id']}.{$options['format']}";
        header("Content-Disposition: attachment; filename=$file_name");
    }
    
    header("Pragma: no-cache");
    header("Expires: 0");    
    
    $dbh    = cacti_db_connect($options);
    
    $rrdsrv = rrdsrv_open($options['server']);
    $graphs = cacti_get_graph_template_graphs($options, $dbh);
    
    if (empty($graphs)) {
        trigger_error("No graphs found for id {$options['graph_template_id']}\n", E_USER_ERROR);
    }
    
    $data_sources = cacti_get_graph_data_sources($graphs, $dbh);
    
    $metadata_fields = array();
    $data_fields     = array();
    $data            = array();
    
    foreach ($graphs as $graph) {
        
        $record = array();
        
        $graph_data_sources = $data_sources[$graph['id']];
        
        $query_cache = cacti_get_query_cache($graph, $dbh);
        
        $command     = build_rrd_command($graph_data_sources, $options);
        
        if ($xport = rrdsrv_execute($command, $rrdsrv)) {    
            $parsed      = rrdsrv_parse_xport($xport);
        
            $metadata = array_merge(array(
                'host_name' => @$graph['host_name'],
                'host_description' => @$graph['host_description'],
                'title' => @$graph['title']
            ), $query_cache);
            
            $metadata_fields = array_unique(
                array_merge($metadata_fields, array_keys($metadata)));
            
            // grab the keys from the first record
            if (is_array(@$parsed['data'][0])) {
                $data_fields = array_unique(
                    array_merge($data_fields, array_keys($parsed['data'][0])));    
            }   
                
            $record = array('metadata' => $metadata, 'data' => @$parsed['data']);
        }
        
        array_push($data, $record);
    }
    
    $fields = array_merge($metadata_fields, $data_fields);
    
    call_user_func('output_' . $options['format'], $data, $fields);
}

function output_csv($data, $fields) {
    $delimeter = ',';
    
    $row_template = join($delimeter,
        array_map(function ($v) { return "{" . $v . "}"; }, $fields));
    
    echo join($delimeter, $fields) . "\n";
    foreach ($data as $record) {
        $metadata = $record['metadata'];
        
        foreach ($record['data'] as $row) {
            $quoted = array();
            $row = array_merge($metadata, $row);
            
            foreach ($fields as $field) {
                $quoted[$field] = quote(@$row[$field]);
            }
            echo MC_Template2::process($row_template, $quoted) . "\n";
        }
    }
}

function output_json($data, $fields) {
    echo json_encode($data);
}

function rrdsrv_execute($command, $handle)
{
    $response = array();
    $command = is_array($command) ? join(' ', $command) : $command;
    
    fwrite($handle, "$command\r\n");
    
    while (!feof($handle)) {
        $line = trim(fgets($handle, 4096));
        
        // OK singals the command has completed
        if (preg_match('/^OK\s.*/', $line)) {
            break;
        }
        
        // eject if an error occurs
        if (preg_match('/^ERROR\:\s+(.*)/', $line, $matches)) {
            trigger_error("RRD Error: {$matches[1]} in '$command'");
            break;
        }
        
        $response[] = $line;
    }
    
    return $response;
}

function rrdsrv_open($host, $port=13900, $timeout=5)
{
    $handle = @fsockopen($host, $port, $errno, $errstr);
    
    if (!$handle) {
        trigger_error("Failed to open conection to rrdsrv\n", E_USER_WARNING);
    }
    
    return $handle;
}

function rrdsrv_parse_xport($response)
{
    $parsed = array();
    
    $xml = new SimpleXMLElement(join('', $response));
    
    $legend = $xml->meta->legend->children();
    
    foreach ($xml->data->row as $row) {
        
        $dup_keys = array();

        $_row = array();
        $timestamp = (int) $row->t;
        $i=0;
        foreach ($row->v as $value) {

            $key = "{$legend->entry[$i]}";
            
            if (array_key_exists($key, $_row)) {
              
                $dup_keys[$key] = array_key_exists($key, $dup_keys)
                    ?  ++$dup_keys[$key] : 1;
                
                $key .= '_' . $dup_keys[$key];
            }
            
            if ((string) $value === "NaN") {
                $_row[$key] = null;
            } else {
                $_row[$key] = (double) $value;
            }
            
            $i++;
        }
        
        // don't append an array of all nulls to the results
        if (array_diff($_row, array(null))) {
            array_push($parsed, array_merge(array('timestamp' => $timestamp), $_row));
        }
    }
    
    return array('metadata' => (array) $xml->meta, 'data' => $parsed);
}

function quote($data)
{
    if (!is_scalar($data)) {
        return $data;
    } elseif (is_numeric($data)) {
        return $data;
    } elseif (is_string($data)) {
        return "\"{$data}\"";
    }
}

/**
 * 
 */
function select_column($column, $data)
{
    return array_map(function ($row) use ($column) { return @$row[$column]; },
        $data);       
}

function underscore($string)
{
    $tmp = preg_match('/[a-z]/', $string)
        ? preg_replace('~(?<=\\w)([A-Z])~', '_$1', $string)
        : $string;
        
    return strtolower(preg_replace('/\W+/', '_', $tmp));
}

/**
 * 
 */
function usage()
{
    print "Usage: {$_SERVER['argv'][0]} [--help|h] [--server <string>] " .
        "[--host-id <integer>] [--username <string>] [--password <string>] " .
        "[-i|--graph-template-id <integer>] [-r|--step <integer>] " .
        "[-s|--start <string> [-e|--end <string>] " .
        "[-c|--consolidation-function <MIN|AVERAGE|MAX|LAST>\n";        
}