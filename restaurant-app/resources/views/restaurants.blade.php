<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurant Search</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>

<body>
    <div id="loading-overlay">
        <div class="spinner-border text-light" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    </div>
    <div class="container">
        <h1 class="mb-4 text-center">Restaurant Search</h1>
        <div class="input-group mb-3">
            <input type="text" id="keyword" class="form-control" placeholder="Enter keyword..."
                aria-label="Enter keyword" aria-describedby="button-addon2">
            <div class="input-group-append">
                <button class="btn btn-outline-secondary" type="button" id="button-addon2"
                    onclick="searchRestaurants()">Search</button>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div id="map"></div>
            </div>
            <div class="col-md-6">
                <div id="results-container">
                    <ul id="results" class="list-group"></ul>
                </div>
            </div>
        </div>
    </div>
    <script>
        var markers = [];

        function initMap() {
            showLoadingOverlay();
            var defaultLocation = { lat: 13.7563, lng: 100.5018 };
            window.map = new google.maps.Map(document.getElementById('map'), {
                center: defaultLocation, zoom: 10
            });
            hideLoadingOverlay();
            searchRestaurants();
        }

        function searchRestaurants() {
            showLoadingOverlay();
            clearMarkers();
            const keyword = document.getElementById('keyword').value || 'Bang Sue';
            if (!document.getElementById('keyword').value) { document.getElementById('keyword').value = ("Bang Sue"); }
            fetch(`/restaurants/search/${keyword}`)
                .then(response => response.json())
                .then(data => {
                    const resultsDiv = document.getElementById('results');
                    resultsDiv.innerHTML = '';
                    var bounds = new google.maps.LatLngBounds();
                    data.results.forEach((restaurant, index) => {
                        const listItem = `<li id="list-item-${index}" class="list-group-item" onclick="handleListItemClick(this, ${restaurant.geometry.location.lat}, ${restaurant.geometry.location.lng})">
                                            ${restaurant.name} - ${restaurant.formatted_address}
                                          </li>`;
                        resultsDiv.innerHTML += listItem;
                        var location = new google.maps.LatLng(restaurant.geometry.location.lat, restaurant.geometry.location.lng);
                        var marker = new google.maps.Marker({ position: location, map: window.map });
                        var infoWindow = new google.maps.InfoWindow({ content: `<div><strong>${restaurant.name}</strong><p>${restaurant.formatted_address}</p></div>` });
                        marker.addListener('mouseover', function () { infoWindow.open(window.map, marker); });
                        marker.addListener('mouseout', function () { infoWindow.close(); });
                        marker.addListener('click', function () {
                            setActiveListItem(index);
                            focusOnRestaurant(restaurant.geometry.location.lat, restaurant.geometry.location.lng);
                        });
                        markers.push(marker);
                        bounds.extend(marker.getPosition());
                    });
                    window.map.fitBounds(bounds);
                    hideLoadingOverlay();
                });
        }

        function setActiveListItem(index) {
            var listItems = document.querySelectorAll('#results .list-group-item');
            listItems.forEach(item => item.classList.remove('active'));
            var activeItem = document.getElementById(`list-item-${index}`);
            if (activeItem) activeItem.classList.add('active');
        }

        function focusOnRestaurant(lat, lng) {
            showLoadingOverlay();
            clearMarkers();
            var location = { lat: lat, lng: lng };
            window.map.setCenter(location);
            window.map.setZoom(15);
            var marker = new google.maps.Marker({ position: location, map: window.map });
            markers.push(marker);
            hideLoadingOverlay();
        }

        function clearMarkers() {
            markers.forEach(marker => marker.setMap(null));
            markers = [];
        }

        function showLoadingOverlay() { document.getElementById('loading-overlay').classList.add('active'); }
        function hideLoadingOverlay() { document.getElementById('loading-overlay').classList.remove('active'); }
        function handleListItemClick(clickedElement, lat, lng) {
            focusOnRestaurant(lat, lng);
            setActiveListItem(Array.from(clickedElement.parentNode.children).indexOf(clickedElement));
        }
    </script>
    <script async defer
        src="https://maps.googleapis.com/maps/api/js?key={{ $googleMapsApiKey }}&callback=initMap"></script>
</body>

</html>