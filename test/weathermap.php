#!/usr/bin/env php
<?php
/**
 *
 */
require_once dirname(__FILE__) . '/../library/MC.php';
MC::boot();

MC_Weathermap_Cli::execute();

