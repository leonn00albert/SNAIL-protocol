<?php

function readDirContent($folderPath, $fileType = null)
{
    $fileNames = [];
    if (is_dir($folderPath)) {
        if ($dh = opendir($folderPath)) {
            while (($file = readdir($dh)) !== false) {
                if ($file != "." && $file != "..") {
                    if ($fileType === null || pathinfo($file, PATHINFO_EXTENSION) === $fileType) {
                        $fileNames[] = pathinfo($file, PATHINFO_FILENAME);
                    }
                }
            }
            closedir($dh);
        }
    } else {
        echo "Invalid directory path.";
    }
    return $fileNames;
}

$folders = [
    'packets' => [
        'inbox' => [],
        'outbox' => readDirContent("packets/outbox", 'zip')
    ]
];

?>
<!DOCTYPE html>
<html>

<head>
    <title>Packets</title>
    <link rel="stylesheet" href="css/skelet.css">
    <link rel="stylesheet" href="css/app.css">

</head>

<body>
    <main>

        <article>
        <h1 style="text-align: center;">Packets</h1>
            <x-grid columns="2" style="max-width: 750px; margin:0 auto; padding:2rem">
            

                <x-col span-s="row">
                    <h3>Inbox</h3>
                    <table>
                        <thead>

                            <th>
                                Id
                            </th>
                            <th>
                                Destination
                            </th>
                        </thead>
                        <tbody>
                            <?php foreach (readDirContent("packets/inbox/") as $packet) :; ?>
                                <?= $packet_details = explode("_", $packet); ?>

                                <tr>
                                    <td>
                                        <?= $packet_details[0] ?>
                                    </td>
                                    <td>
                                        <?= $packet_details[1] ?>
                                    </td>
                                </tr>
                            <?php endforeach ?>

                        </tbody>

                    </table>
                </x-col>
                <x-col span-s="row">
                    <h3>Outbox</h3>
                    <table>
                        <thead>

                            <th>
                                Id
                            </th>
                            <th>
                                Destination
                            </th>
                        </thead>
                        <tbody>
                            <?php foreach (readDirContent("packets/outbox/") as $packet) :; ?>
                                <?= $packet_details = explode("_", $packet); ?>

                                <tr>
                                    <td>
                                        <?= $packet_details[0] ?>
                                    </td>
                                    <td>
                                        <?= $packet_details[1] ?>
                                    </td>
                                </tr>
                            <?php endforeach ?>

                        </tbody>
                    </table>
                </x-col>
            </x-grid>
        </article>


    </main>



</body>

</html>