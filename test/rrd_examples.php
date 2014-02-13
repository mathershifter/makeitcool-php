<?php
error_reporting(E_ALL | E_STRICT);

require_once dirname(__FILE__) . '/../library/Mic.php';
Mic::boot();

$rrd = new Mic_Rrd('tcp://rrd-server:13900');


$rrd->xport('--start 1281046500 --end 1281047400 --step 3600 DEF:traffic_in_22531=fwxbtl02_traffic_in_22531.rrd:traffic_in:AVERAGE DEF:traffic_out_22531=fwxbtl02_traffic_in_22531.rrd:traffic_out:AVERAGE XPORT:traffic_in_22531:traffic_in XPORT:traffic_out_22531:traffic_out');

print_r($rrd->response());
//$rrd->graph('/dev/null', '--start -7200', '--end now',
//    'DEF:rtt=svmdal01_avg_20707.rrd:avg:AVERAGE',
//    'DEF:google="svmdal01_google_20706.rrd":google:AVERAGE',
//    'DEF:msn="svmdal01_google_20706.rrd":msn:AVERAGE',
//    'DEF:weather="svmdal01_google_20706.rrd":weather:AVERAGE',
//    'VDEF:v_rtt_avg=rtt,AVERAGE',
//    'VDEF:v_google_avg=google,AVERAGE',
//    'VDEF:v_msn_avg=msn,AVERAGE',
//    'VDEF:v_weather_avg=weather,AVERAGE',
//    'CDEF:c_all_avg=google,msn,+,weather,+,3,/',
//    'VDEF:v_all_avg=c_all_avg,AVERAGE',
//    'PRINT:v_rtt_avg:"rtt\:%3.2lf"',
//    'PRINT:v_google_avg:"google\:%3.2lf"',
//    'PRINT:v_msn_avg:"msn\:%3.2lf"',
//    'PRINT:v_weather_avg:"weather\:%3.2lf"',
//    'PRINT:v_all_avg:"all_dns_avg\:%3.2lf"'
//);
//print_r($rrd->response()->toArray());

//$rrd->graph('/dev/null', '--start -7200', '--end now',
//    'DEF:rtt=svmdal01_avg_20707.rrd:avg:AVERAGE',
//    'DEF:google="svmdal01_google_20706.rrd":google:AVERAGE',
//    'DEF:msn="svmdal01_google_20706.rrd":msn:AVERAGE',
//    'DEF:weather="svmdal01_google_20706.rrd":weather:AVERAGE',
//    'VDEF:v_rtt_avg=rtt,AVERAGE',
//    'VDEF:v_google_avg=google,AVERAGE',
//    'VDEF:v_msn_avg=msn,AVERAGE',
//    'VDEF:v_weather_avg=weather,AVERAGE',
//    'CDEF:c_all_avg=google,msn,+,weather,+,3,/',
//    'VDEF:v_all_avg=c_all_avg,AVERAGE',
//    'PRINT:v_rtt_avg:"rtt\:%3.2lf"',
//    'PRINT:v_google_avg:"google\:%3.2lf"',
//    'PRINT:v_msn_avg:"msn\:%3.2lf"',
//    'PRINT:v_weather_avg:"weather\:%3.2lf"',
//    'PRINT:v_all_avg:"all_dns_avg\:%3.2lf"'
//);
//print_r($rrd->response()->toArray());
#echo $rrd->response();

#$rrd->graph('-', '--start -7200', '--end now',
    #'DEF:rtt=svmdal01_avg_20707.rrd:avg:AVERAGE',
#    'DEF:google="svmdal01_google_20706.rrd":google:AVERAGE',
    #'DEF:msn="svmdal01_google_20706.rrd":msn:AVERAGE',
    #'DEF:weather="svmdal01_google_20706.rrd":weather:AVERAGE',
    #'CDEF:c_all_avg=google,msn,+,weather,+,3,/',
    #'LINE1:google#0000ff:google'
#);
#print_r($rrd->response()->toArray());
#echo $rrd->response();

//$rrd->xport('--start -900', '--end -300',
//    'DEF:a=svmdal01_avg_20707.rrd:avg:AVERAGE',
//    'DEF:b="svmphi01_google_20880.rrd":google:AVERAGE',
//    'DEF:c="svmphi01_google_20880.rrd":msn:AVERAGE',
//    'DEF:d="svmphi01_google_20880.rrd":weather:AVERAGE',
//    'CDEF:ca=a,b,+,c,+,3,/',
//    'XPORT:a:"rtt"',
//    'XPORT:ca:"average"'
//);
//print_r($rrd->response()->last()->toArray());

//
//$rrd->xport('--start -1800', '--end -300',
//    'DEF:sys="ibxatl01_cpu_system_18535.rrd":cpu_system:AVERAGE',
//    'DEF:user="ibxatl01_cpu_user_18536.rrd":cpu_user:AVERAGE',
//    'DEF:nice="ibxatl01_cpu_nice_18534.rrd":cpu_nice:AVERAGE',
//    'CDEF:total=sys,user,+,nice,+,0,*,10,+',
//    'CDEF:status=total,SQRT',
    #'XPORT:sys:sys',
    #'XPORT:user:user',
    #'XPORT:nice:nice',
//    'XPORT:total:total',
//    'XPORT:status:status'
//);
//print_r($rrd->response()->last()->toArray());
//

//$rrd->info('svmphi01_google_20880.rrd');
//print_r($rrd->response()->toArray());

#print_r($rrd->fetch('tc4phi0115_traffic_in_5435.rrd', 'MAX', '--start -7200', '--end now')->response());
#$rrd = null; // close the connection
#try {
#    $rrd2 = new Mic_Rrd();
#} catch (Exception $e) {
#    echo $e->getMessage() . "\n";
#}
#$rrd3 = new Rrd('ssh://cacti@cacti/usr/bin/rrdtool'); //, array('ssh' => '-i /users/jmather5/.ssh/id_rsa_rrd_test'));
#$rrd3->pwd();
#echo $rrd3->response() . "\n";
#
#try {
#    $rrd3->mkdir('/tmp/_rra');
#} catch (Exception $e) {
#    echo $e->getMessage();
#}
#
#$rrd3->cd('/tmp/_rra');
#print_r($rrd3->ls()->response());
#
#// bad command
#$rrd3->ls('bogus');
#
#$rrd3->quit();
