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
use Snail\Zip\Zip;

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Access-Control-Allow-Origin');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $header_options = [
        "method" => "GET"
    ];
    $requests = json_decode(file_get_contents("php://input"));
    NodeController::handleNewRequests((array) $requests);
    $user = new Node; // change later to get source id
    $source = $user->read()->getId();
    $dir = $GLOBALS['base_path'] . "/packets/outbox/" . $source ;

    header('Content-Type: application/json');
    if (is_dir($dir) && !Folder::isEmpty($dir)) {
        $folders = glob($dir ."/" . "*", GLOB_ONLYDIR);
        $data = [];
        foreach ($folders as $folder) {
            $jsonFilePath = $folder . "/response.json";
        if (file_exists($jsonFilePath)) {
            $jsonContents = file_get_contents($jsonFilePath);
            $jsonData = json_decode($jsonContents);
                $data[] = ["response_id" => $jsonData->id,"response_status" =>  $jsonData->status,  "resource_id" => $jsonData->resource_id,"resource_type" => $jsonData->resource_type];
            }
        }
        $jsonData = json_encode($data);
        echo $jsonData;
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'GET') {

        $user = new Node;
        $source = $user->read()->getId();
        $dir = $GLOBALS['base_path'] . "/packets/outbox/" . $source;
        Zip::zipFolder($dir);
        $zip_file = $GLOBALS['base_path'] . "/packets/outbox/" . $source . ".zip";
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Content-Disposition, Content-Length' );
        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="starter_pack.zip"');
        header('Content-Length: ' . filesize($zip_file)); 
        readfile($zip_file); 
        Folder::destroy($dir);
        unlink($zip_file);
}   

