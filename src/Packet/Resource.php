<?php
namespace Snail\Packet;

class Resource {
    public string $resource_id;
    public string $resource_type;
    public string $resource_path;
    public function __construct(string $id, string $type)
    {
        $this->resource_id = $id;
        $this->resource_type = $type;
    }
}