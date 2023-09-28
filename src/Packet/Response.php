<?php 
namespace Snail\Packet;

class Response {
    private string $id;
    private string $status;
    private string $request_file = 'response.json';

    public function __construct(private Packet $packet, private Resource $resource , array $header_options)
    {
        $this->id = uniqid();
        $this->resource = $resource;
        $this->packet = $packet;
        $this->status = $header_options["status"];
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
        "resource_path" => $this->resource->resource_path,
        "status" => $this->status,
        ];
    }
}