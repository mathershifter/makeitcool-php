<?php
require_once dirname(__FILE__) . '/../library/Mic.php';
Mic::boot();
?>
<pre>

<?php 
$request = new Mic_Request();

echo "REQUEST: ";
print_r($request);
echo "\n\n";

echo "URI: ";
var_dump($request->u("?test2=heaayy"));

echo "PATH(s): <br>";
var_dump($request->p('test'));
var_dump($request->p('ss=rad'));
var_dump($request->path);


?>
</pre>