<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <title>Project Title</title>

    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="Skelet." property="Sēlekkt. Studio" content="https://selekkt.dk/skelet/v3/">

    <meta name="theme-color" content="#FFFFFF">

    <meta name="description" content="">
    <meta name="robots" content="index,follow,max-snippet:-1,max-image-preview:large,max-video-preview:-1">

    <!-- Facebook OG Tags -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="">
    <meta property="og:site_name" content="">
    <meta property="og:title" content="">
    <meta property="og:description" content="">
    <meta property="og:image" content="">

    <!-- Twitter Card Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="">
    <meta name="twitter:description" content="">

    <link rel="canonical" href="">

    <!-- Target iOS browsers -->
    <link rel="apple-touch-icon" sizes="180x180" href="img/apple-touch-icon.png">
    <!-- Target Safari on macOS -->
    <link rel="icon" type="image/png" sizes="32x32" href="img/favicon-32x32.png">
    <!-- The classic favicon displayed in tabs -->
    <link rel="icon" type="image/png" sizes="16x16" href="img/favicon-16x16.png">
    <!-- Used for Safari pinned tabs -->
    <link rel="mask-icon" href="img/safari-pinned-tab.svg" color="#24d05a">

    <script>
        document.documentElement.className = 'js'
    </script>

    <link rel="stylesheet" href="https://rsms.me/inter/inter.css">

    <link rel="stylesheet" href="css/skelet.css"> <!-- include Skelet -->
    <!-- <link rel="stylesheet" href="css/skelet-tooltips.css"> -->
    <link rel="stylesheet" href="css/app.css"> <!-- write all of your CSS styles here -->



    <style></style>
</head>

<body>
    <a href="#mainContent" class="button is-primary visually-hidden-focus skipTo">Skip to content</a>

    <div id="app">


        <main id="mainContent">
            <!-- Remove this -->
            <article>
                <x-grid columns="2" style="max-width: 750px; margin:0 auto; padding:2rem">
                    <x-col span-s="row">
                        <h1>Snail Net Pages</h1>
                        <ul>
                            <li>
                                <a href="/pages/0/welcome.html">First user welcome page</a>
                            </li>
                        </ul>

                    </x-col>
                    <x-col span="2+1" span-s="row">
                        <h3>Requested Pages & content</h3>
                        <form method="POST">
                        <div id="requestedItems">

                        </div>
                        <button>Create Packets</button>
                        </form>
                
                    </x-col>
                </x-grid>
            </article>
        </main>
    </div>
<!-- Important needed for functioning do not remove or alter -->
    <script src="/js/modules.js"></script>
    <script src="/js/app.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
                    function displayRequestedResources(requestedResources) {
                        const requestedResourceList= document.getElementById('requestedItems');
                        requestedResourceList.innerHTML = '';

                        JSON.parse(requestedResources).forEach(resource => {
                            const inputHiddenId = document.createElement('input');
                            const inputHiddenType = document.createElement('input');
                            const inputHiddenDestination= document.createElement('input');
                            inputHiddenDestination.type = "hidden";
                            inputHiddenDestination.name = "resource_destination";
                            inputHiddenDestination.value = resource.destination

                            inputHiddenId.type = "hidden";
                            inputHiddenId.name = "resource_id";
                            inputHiddenId.value = resource.id

                            inputHiddenType.type = "hidden";
                            inputHiddenType.name = "resource_type";
                            inputHiddenType.value = resource.type;
                            const listItem = document.createElement('li');
                            listItem.textContent = resource.name;
                            listItem.appendChild(inputHiddenId);
                            listItem.appendChild(inputHiddenDestination);
                            listItem.appendChild(inputHiddenType);

                           
                          
                            requestedResourceList.appendChild(listItem);
                        });
                    }

                    window.addEventListener('load', function() {
                        const existingCookie = document.cookie.replace(/(?:(?:^|.*;\s*)requestedResources\s*\=\s*([^;]*).*$)|^.*$/, "$1");
		                const requestedResources = existingCookie ? JSON.parse(existingCookie) : [];
                        displayRequestedResources(requestedResources);
                    });
                });
    </script>

</body>

</html>

<?php
require_once("vendor/autoload.php");

$GLOBALS['basePath'] = __DIR__;

use Snail\Controllers\ResourceController;
use Snail\Packet\Packet;
use Snail\Packet\Request;

if(isset($_POST)){

    if(isset($_POST['resource_id'])){
        $header_options = [
            "method" => "GET"
        ];
        $packet = new Packet();
        $packet->destination = $_POST["resource_destination"];
        $packet->createPacket();
        $request = new Request($packet,ResourceController::create($_POST), $header_options);
        $request->createFile();
        $packet->zip();
        $packet->clear();
    }
}

?>