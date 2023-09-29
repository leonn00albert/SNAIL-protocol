<?php

namespace Snail\Node;

use Snail\Packet\Packet;
use Snail\Packet\Request;
use Snail\Packet\Resource;
use Snail\Packet\Response;
use Snail\Utils\Folder;

class Node
{

    private string $inbox_folder = __DIR__ . "/../../packets/inbox";
    private string $outbox_folder = __DIR__ . "/../../packets/outbox";
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
                        Folder::copy($resource_address, $packet->packet_file_path . "/data");
                        $packet->zip();
                        $packet->clear();
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
                        $packet->writeFiles();
                        //$packet->zip();
                        //$packet->clear();
                    }

                }
        
            }
        }
    }
}
