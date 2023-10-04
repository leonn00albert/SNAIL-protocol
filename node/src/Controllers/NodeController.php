<?php
namespace Snail\Controllers;

use Snail\Node\Node;
use Snail\Packet\Packet;
use Snail\Packet\Request;
use Snail\Packet\Resource;

class NodeController {
    public static function handleNewRequests(array $requests)
    {
        foreach($requests as $request){
            $packet = new Packet($request);
            $packet->destination = $request->destination;
            $packet->createPacketJson();
            $resource = new Resource($request->resource_id, $request->resource_type);
            $packet->addResource($resource);
            $packet->addRequest(new Request($packet,$resource, ['method' => "GET"]) );
        }
        $node = new Node;
        $node->processInbox();
    }
}
