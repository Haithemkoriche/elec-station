document.addEventListener('DOMContentLoaded', function() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            var currentLocation = [position.coords.latitude, position.coords.longitude];
            var map = L.map('map').setView(currentLocation, 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap'
            }).addTo(map);
        }, function() {
            alert('Position could not be determined.');
        });
    } else {
        alert('Geolocation is not supported by this browser.');
    }
});

function ajouterStation() {
    // Logique pour ajouter une station
    console.log("Ajouter une station");
}
