<?php

use Snail\Node\Node;

require_once("vendor/autoload.php");
$GLOBALS['base_path'] = __DIR__;

$node = new Node();
$node->processInbox();