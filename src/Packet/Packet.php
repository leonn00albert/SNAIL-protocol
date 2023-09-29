<?php
namespace Snail\Packet;

use Exception;
use Snail\User\User;
use Snail\Zip\Zip;

class Packet {
    private string $id;
    public string $destination;
    private string $source;
    private string $data;
    private string $checksum;
    public string $packet_file_path;
    public string $packet_json_file_path;
    public string $destination_folder;
    const  PACKET_FILE = 'packet.json';
    const  HEADER_FILE = 'header.json';
    public function __construct(?object $data = null)
    {

        $user = new User();
        $this->source = $data->source ??$user->readUser()->getId();
        $this->destination = $data->destination ?? "aaaaaa";
        $this->id = $data->id ?? uniqid();
        $this->destination_folder = $GLOBALS['base_path'] . "/packets/outbox/" . $this->destination;
        $this->packet_file_path  = $this->destination_folder ."/" . $this->id;
        $this->packet_json_file_path =  $data->packet_json  ?? $this->source . "_" . $this->destination . "_" .$this->id. "_". Packet::PACKET_FILE;


    }
    public function createPacket(){
        if(!is_dir($this->destination_folder)) {
            mkdir($this->destination_folder);
        }
        if(!is_dir($this->packet_file_path)) {
            mkdir($this->packet_file_path);
        }
        $header = $this->buildHeader();
        file_put_contents($this->packet_file_path . "/" . Packet::HEADER_FILE,json_encode($header));
    }

    public function createPacketJson(){
        $header = $this->buildHeader();
        file_put_contents($GLOBALS['base_path'] . "/packets/inbox/" . $this->packet_json_file_path,json_encode($header));
    }
    public function addResource(Resource $resource){
        $packet = $this->getJsonFile();
        $packet->resource = $resource->toArray();
        $this->setJsonFile($packet);    }

    public function addRequest(Request $request){
        $packet = $this->getJsonFile();
        $packet->request = $request->buildHeader();
        $this->setJsonFile($packet);
    }

    public function addResponse(Response $response){
        $packet = $this->getJsonFile();
        $packet->response = $response->buildHeader();
        $this->setJsonFile($packet);

    }

    private function getJsonFile():object 
    {
        $packet_json = file_get_contents($GLOBALS['base_path'] . "/packets/inbox/" . $this->packet_json_file_path);
        $packet = json_decode($packet_json);
        return $packet;
    }
    private function setJsonFile(object $packet)
    {
        file_put_contents($GLOBALS['base_path'] . "/packets/inbox/" . $this->packet_json_file_path,json_encode($packet));

    }
    public function writeFiles(){
        $packet_json = file_get_contents($GLOBALS['base_path'] . "/packets/inbox/" . $this->packet_json_file_path);
        $packet = json_decode($packet_json);

        Response::createFile((array) $packet->response, $this->packet_file_path);
        Request::createFile((array) $packet->request, $this->packet_file_path);
        file_put_contents($GLOBALS['base_path'] . "/packets/inbox/" . $this->packet_json_file_path,json_encode($packet));
        file_put_contents($this->packet_file_path . "/" . Packet::PACKET_FILE,json_encode($packet));

    }

    public function buildHeader():array{
        return [
        "id" => $this->id,
        "destination" => $this->destination,
        "source" => $this->source,
        "packet_json" => $this->packet_json_file_path,
        "timestamp" => time(),
        ];
    }

    public function zip():bool
    {
        try { 
            Zip::zipFolder($this);
            return true;
        }
        catch(Exception $e) {
            return false;
        }
    }

    public function clear():bool{
        try { 
            $this->deleteDirectory($this->packet_file_path);
            return true;
        }
        catch(Exception $e) {
            return false;
        }
    }
 
    private function deleteDirectory($dir){
        if (!is_dir($dir)) {
            return false;
        }
        $handle = opendir($dir);
    
        if ($handle) {
            while (false !== ($entry = readdir($handle))) {
                if ($entry != "." && $entry != "..") {
                    $entryPath = $dir . DIRECTORY_SEPARATOR . $entry;
                    if (is_dir($entryPath)) {
                        $this->deleteDirectory($entryPath);
                    } else {
                        unlink($entryPath);
                    }
                }
            }
            closedir($handle);
            rmdir($dir);
            return true;
        } else {
            return false;
        }
    }
}