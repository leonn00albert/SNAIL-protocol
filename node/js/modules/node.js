class Node {
    #addressInput = document.getElementById("nodeAddress");
    #requestDisplay = document.getElementById("requestDisplay");
    #responseDisplay = document.getElementById("responseDisplay");
    #requestButton = document.getElementById("sendRequestButton");
    #createStaterPack = document.getElementById("createStarterPack");
    #downloadResponseButton = document.getElementById("downloadResponseButton");

    constructor() {
        const url = new URL(window.location.href);
        console.log(url)
        this.#addressInput.value = url.protocol + "//" + url.hostname + ":" + url.port;
        this.address = this.#addressInput.value;

        this.setupEventListeners();

    }
    setupEventListeners() {
        this.#requestButton?.addEventListener("click", () => this.sendRequests());
        this.#createStaterPack?.addEventListener("click", () => this.generateStarterPack());
        this.#addressInput?.addEventListener("change", (event) => this.address = event.target.value);
        this.#downloadResponseButton?.addEventListener("click", (event) => this.downloadResponse());

    }

    downloadResponse() {
        fetch(this.address)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                return response.blob();
            })
            .then(blob => {
                const url = window.URL.createObjectURL(blob);

                const a = document.createElement('a');
                a.href = url;
                a.download = 'response_pack.zip';
                a.click();

                window.URL.revokeObjectURL(url);
            })
            .catch(error => {
                console.error("Fetch error:", error);
            });

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

    createAndDisplayResponseTable(data) {
        if (data.length > 0) {
            const table = document.createElement("table");
            table.border = "1";

            const thead = document.createElement("thead");
            const headerRow = document.createElement("tr");
            const headers = ["Response ID", "Response Status", "Resource ID", "Resource Type"];

            headers.forEach(headerText => {
                const headerCell = document.createElement("th");
                headerCell.textContent = headerText;
                headerRow.appendChild(headerCell);
            });

            thead.appendChild(headerRow);
            table.appendChild(thead);

            const tbody = document.createElement("tbody");

            data.forEach(item => {
                const row = document.createElement("tr");

                const responseIdCell = document.createElement("td");
                responseIdCell.textContent = item.response_id;

                const responseStatusCell = document.createElement("td");
                responseStatusCell.textContent = item.response_status;

                const resourceIdCell = document.createElement("td");
                resourceIdCell.textContent = item.resource_id;

                const resourceTypeCell = document.createElement("td");
                resourceTypeCell.textContent = item.resource_type;

                row.appendChild(responseIdCell);
                row.appendChild(responseStatusCell);
                row.appendChild(resourceIdCell);
                row.appendChild(resourceTypeCell);

                tbody.appendChild(row);
            });

            table.appendChild(tbody);
            responseDisplay.appendChild(table);
        }
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

        fetch(this.address + "/starter.php", requestOptions)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                return response.blob();
            })
            .then(blob => {
                const url = window.URL.createObjectURL(blob);

                const a = document.createElement('a');
                a.href = url;
                a.download = 'starter_pack.zip';
                a.click();

                window.URL.revokeObjectURL(url);
            })
            .catch(error => {
                console.error("Fetch error:", error);
            });
    }





    displayResponses(data) {
        const respondedResourceList = this.#responseDisplay;
        respondedResourceList.innerHTML = '';
        this.createAndDisplayResponseTable(data);
        this.#downloadResponseButton.style.display = 'block';

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

window.addEventListener('load', function () {
    const node = new Node()
    node.getRequests();
    node.displayRequestedResources();

});