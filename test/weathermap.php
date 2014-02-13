#!/usr/bin/env php
<?php
/**
 *
 */
require_once dirname(__FILE__) . '/../library/Mic.php';
Mic::boot();

Mic_Weathermap_Cli::execute();

