<?php

namespace Snail\Node;

use Snail\Packet\Packet;
use Snail\Packet\Request;
use Snail\Packet\Resource;
use Snail\Packet\Response;
use Snail\Utils\Folder;
use Exception;

class Node
{
    private string $id;
    private string $inbox_folder = __DIR__ . "/../../packets/inbox";
    private string $outbox_folder = __DIR__ . "/../../packets/outbox";
    const NODE_FILE = 'node.json';
    const ROUTING_FILE = 'routing.json';
    const CONNECTED_FILE = 'connected.json';
    const LOG_FILE = 'log.json';
    public static function create() {
        if(!is_file($GLOBALS["base_path"] ."/config/" . self::NODE_FILE)){
            $data = [
                'id' => uniqid(),
                'type' => 'individual',
                'sync_rate' => 24,
                'sync_unit' => 'hour',
                'created_at' => time(),
                'disconnect_time' => (24  * 7),
                'last_sync' => "",
            ];
            file_put_contents($GLOBALS["base_path"] ."/config/" . self::NODE_FILE, json_encode($data));
        }
        if(!is_file($GLOBALS["base_path"] ."/config/" . self::ROUTING_FILE)){
            $data = [
                "connected_nodes" =>[],
                "connected_clients" => [],

            ];
            file_put_contents($GLOBALS["base_path"] ."/config/" . self::ROUTING_FILE, json_encode($data));
        }
        if(!is_file($GLOBALS["base_path"] ."/config/" . self::CONNECTED_FILE)){
            $data = [
                ["destination" => "x" ,"next_hop" => "x_node", "mode" => "ip" ,"mode_data" =>[]] 
            ];
            file_put_contents($GLOBALS["base_path"] ."/config/" . self::CONNECTED_FILE, json_encode($data));
        }
        if(!is_file($GLOBALS["base_path"] ."/config/" . self::LOG_FILE)){
            $data = [
                ["event" => "INSTALL", "data" => "Installing node", "severity" => "info", "time_stamp" => time()]
            ];
            file_put_contents($GLOBALS["base_path"] ."/config/" . self::LOG_FILE, json_encode($data));
        }
    }
    public function read(): Node
    {
        $nodeData = json_decode(file_get_contents($GLOBALS["base_path"] ."/config/" . self::NODE_FILE), true);
        if ($nodeData === null) {
            throw new Exception("Error reading user data from JSON file.");
        }
        $this->id = $nodeData['id'];

        return $this;
    }
    public function getId() :string
    {
        return $this->id;
    }
    public function processInbox()
    {
        $jsonFiles = glob($this->inbox_folder . '/*.json');

        foreach ($jsonFiles as $jsonFile) {
            $jsonData = file_get_contents($jsonFile);

            $requestData = json_decode($jsonData);

            if ($requestData !== null) {
                $header_options = [
                    "method" => "GET"
                ];
            
                if (in_array($requestData->resource->resource_type, ["page", "pages"])) {
                    $resource_address = $GLOBALS['base_path'] . "/pages/" . $requestData->destination;
                    if (is_dir($resource_address)) {
                        $header = [
                            'status' => 200,
                            'destination' => $requestData->source,
                            'source' => 'node',

                        ];
                        $requestData->destination = $header['destination'];
                        $resource = new Resource($requestData->resource->resource_id, $requestData->resource->resource_type,);
                        $resource->resource_path = $resource_address;
                        $packet = new Packet($requestData);
                        $packet->createPacket();
                        $packet->addResponse(new Response($packet,$resource,  $header) );
                        $packet->writeFiles();
                        $packet->deleteInboxPacketJson();
                        Folder::copy($resource_address, $packet->packet_file_path . "/" . end(explode("/",$resource->resource_path)));
                  
                    }else{
                        $header = [
                            'status' => 300,
                            'destination' => $requestData->destination,
                            'source' => 'node',

                        ];
                        $packet = new Packet($requestData);
                        $packet->createPacket();
                        $resource = new Resource($requestData->resource->resource_id, $requestData->resource->resource_type,);
                        $resource->resource_path = $requestData->destination;
                        $packet->addResponse(new Response($packet,$resource, $header) );
                        $packet->deleteInboxPacketJson();

                        $packet->writeFiles();
                    }

                }
        
            }
        }
    }
}
