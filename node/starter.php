<?php
$zipFilePath = './snail_starter_pack.zip';

if (file_exists($zipFilePath)) {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Content-Disposition,Content-Length' );
    header('Content-Type: application/zip');
    header('Content-Disposition: attachment; filename="starter_pack.zip"');
    header("Content-Length: " .  filesize($zipFilePath));

    readfile($zipFilePath);
} else {
    echo 'Starter pack ZIP file not found.';
}