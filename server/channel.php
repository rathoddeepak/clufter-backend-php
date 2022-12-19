<?php 
use Workerman\Worker;
require_once join(DIRECTORY_SEPARATOR, array(__DIR__, "..", "server", "main.php"));
$channel = new Channel\Server("192.168.43.250", 2021);

if (!defined('GLOBAL_START')) {
    Worker::runAll();
}
?>