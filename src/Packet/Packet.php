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
    private string $header_file = 'header.json';
    public function __construct()
    {
        $user = new User();
        
        $this->source = $user->readUser()->getId();
        $this->id = uniqid();
        $this->packet_file_path = $GLOBALS['base_path'] . "/packets/outbox/" . $this->id;
    }
    public function createPacket(){
        mkdir($this->packet_file_path);
        $header = $this->buildHeader();
        file_put_contents($this->packet_file_path . "/" . $this->header_file,json_encode($header));
    }
    

    public function buildHeader():array{
        return [
        "id" => $this->id,
        "destination" => $this->destination,
        "source" => $this->source,
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