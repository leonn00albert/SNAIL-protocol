<?php 
namespace Snail\Packet;

class Response {
    private string $id;
    private string $status;
    private string $source;
    private string $destination;
    const  FILE_NAME = 'response.json';

    public function __construct(private Packet $packet, private Resource $resource , array $header_options)
    {
        $this->id = uniqid();
        $this->resource = $resource;
        $this->packet = $packet;
        $this->status = $header_options["status"];
        $this->source = $header_options["source"];
        $this->destination = $header_options["destination"];
    }

    public static function createFile(array $data, string $path)
    {
    
        file_put_contents($path. "/" . Response::FILE_NAME,json_encode($data));
        
    }

    public function buildHeader():array{
        return [
        "id" => $this->id,
        "resource_type" => $this->resource->resource_type,
        "resource_id" => $this->resource->resource_id,
        "resource_path" => $this->resource->resource_path,
        "destination" => $this->destination,
        "source" => $this->source,
        "status" => $this->status,
        ];
    }
}