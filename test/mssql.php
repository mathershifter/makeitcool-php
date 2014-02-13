<?php

$link = mssql_connect('DWSIPP_PROD', 'tdw_ssis_user', 'tdw4tw');
mssql_select_db('DWSIPP', $link);

$proc = mssql_init('sp_help', $link);

$result = mssql_execute($proc);
		 
mssql_free_statement($proc);
		 
var_dump($result);