<?php
// Assuming you have created a ZIP file named "starter_pack.zip" on the server
$zipFilePath = './snail_starter_pack.zip';

if (file_exists($zipFilePath)) {
    header('Content-Type: application/zip');
    header('Content-Disposition: attachment; filename="starter_pack.zip"');
    header('Content-Length: ' . filesize($zipFilePath));

    readfile($zipFilePath);
} else {
    echo 'Starter pack ZIP file not found.';
}