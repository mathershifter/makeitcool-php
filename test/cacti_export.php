<?php
/**
 *
 */
require_once dirname(__FILE__) . '/../projects/tmo_php/library/MC.php';

MC::boot();

set_time_limit(300);

$console = php_sapi_name() == 'cli' ? true : false;
$opts    = false;
if ($console) {
    require_once 'Zend/Console/Getopt.php';
    try {
        $opts = new Zend_Console_Getopt(
            array(
                'help|h' => 'Display usage information',
                'server=s' => 'Cacti server ip adddress or hostname',
                'host_id=s' => 'Host ID',
                'directory|d=s' => 'Server subdirectory containing rrd files',
                'username|u=w' => '',
                'password|p=s' => '',
                'graph_template_id|i=s' => 'Cacti graph template ID',
                'graph_template_name|n=s' => 'Cacti graph template name',
                'step|r=i' => 'Data resolution (seconds)',
                'start|s=s' => 'Data start time',
                'end|e=s' => 'Data end time',
                'cf|c=s' => 'Consolidation function (AVERAGE, MAX, MIN, or LAST)'
            )
        );
        
        //$opts->step  = 900;
        //$opts->start = -3600;
        //$opts->end   = 'now';
        
        $opts->parse();
    } catch (Zend_Console_Getopt_Exception $e) {
        exit($e->getMessage() ."\n\n". $e->getUsageMessage());
    }
    
    if(isset($opts->h)) {
        echo $opts->getUsageMessage();
        exit;
    }
} else {
    $opts = A(array(
      'step'  => 900,
      'start' => -3600,
      'end'   => 'now'
    ))->merge($_GET);
        
    $filename = 'cacti_export_' . $opts->graph_template_id . '.csv';
    
    header('Content-type: text/csv');
    header("Content-Disposition: attachment; filename=\"$filename\"");
}

$first_row  = true;
$first_row  = true;
$cacti      = null;
$rrd        = false;
#$first_row = true;
$data       = A();
$keys       = A(array(
    'host_name'        => true,
    'host_description' => true,
    'title'            => true
));

$extended_keys = A();
$data_keys     = A(array(
    'timestamp'        => true
));

try {
    $cacti = MC_Cacti::connect($opts->server, array('host' => $opts->server));
} catch (MC_Cacti_Exception $e) {
    echo json_encode("ERROR: " . $e->getMessage());
    exit(1);
}

$graphs = $cacti->getOfType('graph_template', $opts->{'graph_template_id'})
  ->getGraphs($opts->host_id ? $cacti->getOfType('host', $opts->host_id) : false);
    


foreach ($graphs as $graph) {
    $def      = A();
    $xport    = A();
    $response_data = null;
    $extended_data = A();
    $local_data = A(array(
        'host_name'        => quote($graph->host_name),
        'host_description' => quote($graph->host_description),
        'title'            => quote($graph->title_cache)
    ));
    
    // add host_snmp_cache to metadata
    foreach ($cacti->getOfType('host_snmp_cache', array($graph->host_id,
                                                        $graph->snmp_query_id,
                                                        $graph->snmp_index))
            as $cache_entry
    ) {
        $extended_data->{S($cache_entry->field_name)->underscorize()} = 
            quote($cache_entry->field_value);
    }
    
    foreach ($graph->getDataSources() as $ds) {
        
        $_ds_unique = "{$ds->name}_{$ds->id}";
        
        # collect data sources
        $def->push(A(array(
          'DEF',
          "{$_ds_unique}={$ds->rrd}",
          $ds->name,
          $opts->cf ? $opts->cf : $ds->cf
        ))->join(':'));
        
        # build export CDEF
        $xport->push(A(array(
          'XPORT',
          $_ds_unique,
          S($ds->name)->underscorize()
        ))->join(':'));
    }
    
    try {
        $rrd = new MC_Rrd('tcp://' . $opts->server);
        
        if ($opts->directory) {
            $rrd->cd($opts->directory);
        } elseif ($opts->server) {
			// do nothing
        }
        
        $response_data = $rrd->xport(
            '--start ' . $opts->start,
            '--end ' . $opts->end,
            '--step ' . $opts->step,
            $def->merge($xport)->join(' ')
        )->response();
        
        
        $rrd->close();
        
    } catch (MC_Rrd_Exception $e) {
        $error = $e->getMessage();
    }
    
    if (!empty($response_data)) {
        foreach ($response_data as $row) {
            $_row = $local_data->merge($extended_data)->merge($row);
            $extended_keys = $extended_keys->merge($extended_data);
            $data_keys     = $data_keys->merge($row);
            
            $data->push($_row);
        }
    }
}

$keys = $keys->merge($extended_keys)
    ->merge($data_keys)
    ->fillKeys(null);
    
echo $keys->keys()->join(',') . "\n";

foreach ($data as $record) {
    echo $keys->merge($record)->join(',') . "\n";
}

/**
 * 
 */
function quote($data)
{
    if (!is_scalar($data))
        return $data;
    elseif (is_numeric($data))
        return $data;
    elseif (is_string($data))
        return "\"{$data}\"";
}