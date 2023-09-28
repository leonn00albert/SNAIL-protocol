<?php
namespace Snail\Packet;

use Snail\Packet\Packet;
use Snail\Packet\Resource;

class Request
{
    private string $id;
    private string $method;
    private array $accepted_file_types = ['zip'];
    private string $request_file = 'request.json';

    public function __construct(private Packet $packet, private Resource $resource , array $header_options)
    {
        $this->id = uniqid();
        $this->resource = $resource;
        $this->packet = $packet;
        $this->method = $header_options["method"];
    }
    public function createFile()
    {
        $header = $this->buildHeader();
        file_put_contents($this->packet->packet_file_path . "/" . $this->request_file,json_encode($header));
        
    }

    private function buildHeader():array{
        return [
        "id" => $this->id,
        "resource_type" => $this->resource->resource_type,
        "resource_id" => $this->resource->resource_id,
        "accepted_file_types" => $this->accepted_file_types,
        "method" => $this->method,
        ];
    }
}
