<?php
require_once dirname(__FILE__) . '/../library/Mic.php';
Mic::boot();

$ct = new Mic_Cache_Time('sqlite', array('path' => '/tmp'));

//$ct->set('some_key', array(mt_rand(), mt_rand()));

//print_r($ct->get('some_key', '2 hours ago', false));

//var_dump($ct->get('some_key', array(time() - 3600, time())));

//if (!$response = $ct->get('some_key', '-1 minute')) {
//    $response = $ct->set('some_key', array(mt_rand(), mt_rand()));
//}

$obj = new TestCb;

$response = $ct->get('some_key', 'now', 'now', 'cb', array('function'));

print_r($response);

sleep(1);

$obj->testCache();


function cb($context)
{
    return array('context' => $context, mt_rand(), mt_rand());
}


class TestCb
{
    public static function cbs($context)
    {
        return array('context' => $context, mt_rand(), mt_rand());
    }
    public function testCache()
    {
        $ct = new Mic_Cache_Time('sqlite', array('path' => '/tmp'));
        print_r($ct->get('some_key', 'now', 'now', array($this, 'cb'), array('Method')));
        sleep(1);
        print_r($ct->get('some_key', 'now', 'now', array(__CLASS__, 'cbs'), array('Static')));
    }
    
    public function cb($context)
    {
        return array('context' => $context, mt_rand(), mt_rand());
    }
}
