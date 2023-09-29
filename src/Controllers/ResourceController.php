<?php
namespace Snail\Controllers;
use Snail\Packet\Resource;

class ResourceController {
    public static function create(object $data): Resource
    {
        $id = $data->id;
        $type = $data->type;
        $resource = new Resource($id, $type);
        return $resource;
    }
}