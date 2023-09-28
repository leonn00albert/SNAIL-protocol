<?php
namespace Snail\Controllers;
use Snail\Packet\Resource;

class ResourceController {
    public static function create(array $data): Resource
    {
        $id = $data['resource_id'];
        $type = $data['resource_type'];
        $resource = new Resource($id, $type);
        return $resource;
    }
}