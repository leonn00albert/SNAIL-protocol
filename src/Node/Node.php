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

            $requestData = json_decode($jsonData, true);

            if ($requestData !== null) {
                $header_options = [
                    "method" => "GET"
                ];
                $packet = new Packet();
                $resource = new Resource($requestData['resource_id'], $requestData['resource_type']);
                $packet->destination = $requestData["resource_destination"];
                $packet->createPacket();
                $request = new Request($packet, $resource, $header_options);
                $request->createFile();

                if (in_array($requestData["resource_type"], ["page", "pages"])) {
                    $resource_address = $GLOBALS['base_path'] . "/pages/" . $requestData["resource_destination"];
                    if (is_dir($resource_address)) {
                        $resource->resource_path = $resource_address;
                        $response = new Response($packet, $resource, ["status" => 200]);
                        $response->createFile();
                        Folder::copy($resource_address, $packet->packet_file_path . "/data");
                        $packet->zip();
                        $packet->clear();
                    }
                
                }
        
            }
        }
    }
}
