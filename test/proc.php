<?php
require_once dirname(__FILE__) . '/../library/MC.php';
MC::boot();

$proc = MC_Proc::open($_SERVER['argv'][1]); //, array('stderr' => array('file', '/tmp/wtf.log', 'a')));

//echo "PROC: "; print_r($proc);

//echo $proc->stdout;
//echo MC_Proc::exec($_SERVER['argv'][1]);

//print_r($proc->stdout);

//echo "{$proc->stdout}";

//print_r($proc->stdout);
if (isset($_SERVER['argv'][2])) {
    $proc->stdin->send("{$_SERVER['argv'][2]}\r\n");    
}


foreach ($proc->stdout as $line) {
    
    // OK singals the command has completed
//    if (preg_match('/^OK\s.*/', $line)) {
//        break;
//    }
    
    // eject if an error occurs
//    if (preg_match('/^ERROR\:\s+(.*)/', $line, $matches)) {
//        echo $line . "\n";
//        break;
//    }
    echo $line . "\n";
}

print_r($proc->status());