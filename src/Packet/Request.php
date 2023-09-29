<?php
namespace Snail\Packet;

use Snail\Packet\Packet;
use Snail\Packet\Resource;

class Request
{
    private string $id;
    private string $method;
    private array $accepted_file_types = ['zip'];
    const FILE_NAME = 'request.json';

    public function __construct(private Packet $packet, private Resource $resource , array $header_options)
    {
        $this->id = uniqid();
        $this->resource = $resource;
        $this->packet = $packet;
        $this->method = $header_options["method"];
    }
    public static function createFile(array $data, string $path)
    {
    
        file_put_contents($path. "/" . Request::FILE_NAME,json_encode($data));
        
    }
    public function buildHeader():array{
        return [
        "id" => $this->id,
        "resource_type" => $this->resource->resource_type,
        "resource_id" => $this->resource->resource_id,
        "accepted_file_types" => $this->accepted_file_types,
        "method" => $this->method,
        ];
    }
}
