<?php

namespace Snail\Utils;

use Exception;

class Folder
{
    static function copy(string $source, string $destination): void
    {
        if (is_dir($source)) {
            if (!is_dir($destination)) {
                mkdir($destination, 0777, true);
            }
            $items = scandir($source);
            foreach ($items as $item) {
                if ($item != '.' && $item != '..') {
                    $sourcePath = $source . '/' . $item;
                    $destinationPath = $destination . '/' . $item;

                    if (is_dir($sourcePath)) {
                        Folder::copy($sourcePath, $destinationPath);
                    } else {
                        copy($sourcePath, $destinationPath);
                    }
                }
            }
        } else {
            copy($source, $destination);
        }
    }
    static function destroy($dir): bool
    {
        if (!is_dir($dir)) {
            return false;
        }
        $handle = opendir($dir);

        if ($handle) {
            while (false !== ($entry = readdir($handle))) {
                if ($entry != "." && $entry != "..") {
                    $entryPath = $dir . DIRECTORY_SEPARATOR . $entry;
                    if (is_dir($entryPath)) {
                        self::destroy($entryPath);
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

    public static function isEmpty(string $directoryPath): bool
    {
        if (is_dir($directoryPath)) {
            $items = scandir($directoryPath);

            $itemCount = count($items) - 2;

            if ($itemCount <= 0) {
                return true;
            } else {
                return false;
            }
        } else {
            throw new Exception("The directory does not exist. ");
        }
    }

    public static function getFileNames(string $directoryPath): array
    {
        if (is_dir($directoryPath)) {
            $items = scandir($directoryPath);
            $files = array_filter($items, function ($item) use ($directoryPath) {
                return is_file($directoryPath . '/' . $item);
            });
            return $files;
        } else {
            throw new Exception("The directory does not exist. ");
        }
    }
}
