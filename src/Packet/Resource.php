<?php
namespace Snail\Packet;

class Resource {
    public string $resource_id;
    public string $resource_type;
    public string $resource_path;
    public function __construct(string $resource_id, string $resource_type)
    {
        $this->resource_id = $resource_id;
        $this->resource_type = $resource_type;
    }
    public function toArray(): array
    {
        return [
            "resource_id" => $this->resource_id,
            "resource_type" => $this->resource_type,
        ];
    }
}