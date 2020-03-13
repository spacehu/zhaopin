<?php

date_default_timezone_set('PRC');
include_once('env.php');
include_once('./mod/init.php');
include_once('./mod/autoload.php');
$config = include_once('./config/config.php');
$run = new mod\init($config);
$run->cli();
