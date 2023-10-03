<?php
require_once("vendor/autoload.php");

$GLOBALS['base_path'] = __DIR__;

use Snail\Controllers\NodeController;
use Snail\Controllers\ResourceController;
use Snail\Node\Node;
use Snail\Packet\Packet;
use Snail\Packet\Request;
use Snail\User\User;
use Snail\Utils\Folder;
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Access-Control-Allow-Origin');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $header_options = [
        "method" => "GET"
    ];
    $requests = json_decode(file_get_contents("php://input"));
    NodeController::handleNewRequests((array) $requests);
    $user = new User;
    $source = $user->readUser()->getId();
    $dir = $GLOBALS['base_path'] . "/packets/outbox/" . $source;

    header('Content-Type: application/json');
    if (is_dir($dir) && !Folder::isEmpty($dir)) {

        $jsonData = json_encode(Folder::getFileNames($dir));


        echo $jsonData;
    }
}
