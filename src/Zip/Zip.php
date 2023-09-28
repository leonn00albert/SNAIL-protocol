<?php
namespace Snail\Zip;
use ZipArchive;
use Exception;
use DirectoryIterator;
use Snail\Packet\Packet;

class Zip {
    public static function zipFolder(Packet $packet)
    {
        $zip = new ZipArchive();
        if ($zip->open($packet->packet_file_path . "_" . $packet->destination . ".zip", ZipArchive::CREATE) === TRUE) {
            self::addFolderToZip($packet->packet_file_path, $zip, $GLOBALS["base_path"] ."/packets/outbox/");
            $zip->close();
           
        } else {
            throw new Exception("Failed to create zip file");
        }
    }

    private static function addFolderToZip($folder, $zip, $basePath = '') {
        $folderContents = new DirectoryIterator($folder);
        foreach ($folderContents as $item) {
            if (!$item->isDot()) {
                $path = $item->getPathname();
                $localPath = ltrim(str_replace($basePath, '', $path), DIRECTORY_SEPARATOR);
                if ($item->isFile()) {
                    $zip->addFile($path, $localPath);
                } elseif ($item->isDir()) {
                    self::addFolderToZip($path, $zip, $basePath);
                }
            }
        }
    }
}