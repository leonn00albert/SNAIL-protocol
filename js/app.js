// app.js
document.addEventListener("DOMContentLoaded", () => {
	function setCookie(name, value, days) {
		const expires = new Date();
		expires.setTime(expires.getTime() + days * 24 * 60 * 60 * 1000);
		document.cookie = `${name}=${JSON.stringify(value)};expires=${expires.toUTCString()};path=/`;	  
	}
	function handleRequestButtonClick(event) {
		
		const button = event.target;
		button.style.display = "none";
		const resource = {};
		resource.name = button.getAttribute("resource-name");
		resource.id  = button.getAttribute("resource-id");
		resource.type = button.getAttribute("resource-type");
		resource.destination = button.getAttribute("resource-destination");
		
		const existingCookie = document.cookie.replace(/(?:(?:^|.*;\s*)requestedResources\s*\=\s*([^;]*).*$)|^.*$/, "$1");
		const requestedResources = existingCookie ? JSON.parse(existingCookie) : [];
	  
		if (resource) {
			if (!requestedResources.some(res => res.id === resource.id && res.type === resource.type)) {
				requestedResources.push(resource);
				setCookie("requestedResources", JSON.stringify(requestedResources), 365);
			}
	  
		}
	}

	const requestButtons = document.querySelectorAll(".request-button");
	requestButtons.forEach(button => {
		const resource = {};
		resource.id  = button.getAttribute("resource-id");
		resource.type = button.getAttribute("resource-type");
		const existingCookie = document.cookie.replace(/(?:(?:^|.*;\s*)requestedResources\s*\=\s*([^;]*).*$)|^.*$/, "$1");
		const requestedResources = existingCookie ? JSON.parse(existingCookie) : [];
	  
		if (requestedResources.some(res => res.id === resource.id && res.type === resource.type)) {
			button.style.display = "none";
		}
		
		button.addEventListener("click", handleRequestButtonClick);
	});


});

