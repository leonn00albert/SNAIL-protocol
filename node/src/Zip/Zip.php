<?php
namespace Snail\Zip;
use ZipArchive;
use Exception;
use DirectoryIterator;
use Snail\Packet\Packet;

class Zip {
    public static function zipFolder($dir)
    {
        $zip = new ZipArchive();
      
            if ($zip->open($dir. ".zip", ZipArchive::CREATE) === TRUE) {
                self::addFolderToZip($dir, $zip, $GLOBALS["base_path"] ."/packets/outbox/");
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