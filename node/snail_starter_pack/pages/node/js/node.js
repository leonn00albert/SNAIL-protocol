class Node {
    #addressInput = document.getElementById("nodeAddress");
    #requestDisplay = document.getElementById("requestDisplay");
    #responseDisplay = document.getElementById("responseDisplay");
    #requestButton = document.getElementById("sendRequestButton");
    #createStaterPack = document.getElementById("createStarterPack");

    constructor() {
        this.address = this.#addressInput.value;
        this.#requestButton.addEventListener("click", () => this.sendRequests());
        this.#createStaterPack.addEventListener("click", () => this.generateStarterPack());
        this.#addressInput.addEventListener("change", (event) => this.address = event.target.value);

    }

    sendRequests() {
        const requestsArray = this.getRequests();
        document.cookie = "requestedResources" + "=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
        this.#requestDisplay.innerHTML = "";
        const requestOptions = {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify(requestsArray)
        };

        fetch(this.address, requestOptions)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                this.displayResponses(Object.values(data));

            })
            .catch(error => {
                console.error("Fetch error:", error);
            });
    }

    getRequests() {
        const existingCookie = document.cookie.replace(/(?:(?:^|.*;\s*)requestedResources\s*\=\s*([^;]*).*$)|^.*$/, "$1");
        const requestedResources = existingCookie ? JSON.parse(existingCookie) : [];
        this.requestedResources = requestedResources;
        return requestedResources;
    }
    generateStarterPack() {
            const requestOptions = {
                method: "GET",
                headers: {
                    "Content-Type": "application/json"
                },
            };

            fetch(this.address + "/node/starter.php", requestOptions)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! Status: ${response.status}`);
                    }
                    return response.blob(); // Get the response as a Blob
                })
                .then(blob => {
                    const url = window.URL.createObjectURL(blob);

                    const a = document.createElement('a');
                    a.href = url;
                    a.download = 'starter_pack.zip'; // Set the filename
                    document.body.appendChild(a);
                    a.click(); // Simulate a click event to trigger the download

                    window.URL.revokeObjectURL(url);
                })
                .catch(error => {
                    console.error("Fetch error:", error);
                });
        }





    displayResponses(data) {
        const respondedResourceList = this.#responseDisplay;
        respondedResourceList.innerHTML = '';

        data.forEach(resource => {
            const listItem = document.createElement('li');
            listItem.textContent = resource
            respondedResourceList.appendChild(listItem);
        });
    }

    displayRequestedResources() {
        const requestedResourceList = this.#requestDisplay;
        requestedResourceList.innerHTML = '';

        this.requestedResources.forEach(resource => {
            const listItem = document.createElement('li');
            listItem.textContent = resource.name;
            requestedResourceList.appendChild(listItem);
        });
    }


}

window.addEventListener('load', function() {
        const node = new Node()
        node.getRequests();
        node.displayRequestedResources();

    });