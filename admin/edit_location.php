<?php
session_start();
include '../db.php'; // Ensure your database connection file is correctly included

// Check if the user is logged in
function isAuthenticated() {
    return isset($_SESSION['admin_email']); // Adjust this according to your session setup
}

if (!isAuthenticated()) {
    header("Location: login.php"); // Redirect to the login page if not authenticated
    exit;
}

// Fetch the location data
$locationId = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($locationId === 0) {
    header("Location: index.php"); // Redirect if no ID provided
    exit;
}

$sql = "SELECT * FROM locations WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $locationId);
$stmt->execute();
$result = $stmt->get_result();
$location = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    $name = $conn->real_escape_string($_POST['name']);
    $latitude = $conn->real_escape_string($_POST['latitude']);
    $longitude = $conn->real_escape_string($_POST['longitude']);

    $updateSql = "UPDATE locations SET name = ?, latitude = ?, longitude = ? WHERE id = ?";
    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->bind_param("sssi", $name, $latitude, $longitude, $locationId);
    if ($updateStmt->execute()) {
        header("Location: index.php"); // Redirect 
        exit;
    } else {
        echo "<p class='alert alert-danger'>Error updating location: " . $conn->error . "</p>";
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <title>Edit Location</title>
    <style>
    #map {
        height: 300px;
        /* Set the height of the map */
        width: 100%;
        /* Optional: you can set the width as well */
    }
    </style>
</head>

<body>
    <div class="container mt-5">
        <h2>Edit Location</h2>
        <form method="POST" action="">
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" class="form-control" id="name" name="name"
                    value="<?php echo htmlspecialchars($location['name']); ?>" required>
            </div>
            <div class="form-group">
                <label for="map">Map (Drag marker to set coordinates):</label>
                <div id="map"></div> <!-- Map will display here -->
            </div>
            <div class="form-group">
                <label for="latitude">Latitude:</label>
                <input type="text" class="form-control" id="latitude" name="latitude"
                    value="<?php echo htmlspecialchars($location['latitude']); ?>" required>
            </div>
            <div class="form-group">
                <label for="longitude">Longitude:</label>
                <input type="text" class="form-control" id="longitude" name="longitude"
                    value="<?php echo htmlspecialchars($location['longitude']); ?>" required>
            </div>
            <button type="submit" class="btn btn-primary" name="update">Update Location</button>
        </form>
    </div>

    <script>
    function initMap() {
        const currentPos = {
            lat: parseFloat(document.getElementById('latitude').value),
            lng: parseFloat(document.getElementById('longitude').value)
        };

        const map = new google.maps.Map(document.getElementById('map'), {
            zoom: 12,
            center: currentPos
        });

        const marker = new google.maps.Marker({
            position: currentPos,
            map: map,
            draggable: true
        });

        google.maps.event.addListener(marker, 'dragend', function() {
            document.getElementById('latitude').value = marker.getPosition().lat();
            document.getElementById('longitude').value = marker.getPosition().lng();
        });

        map.addListener('click', function(e) {
            marker.setPosition(e.latLng);
            document.getElementById('latitude').value = e.latLng.lat();
            document.getElementById('longitude').value = e.latLng.lng();
        });
    }
    </script>
    <script async defer
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDveHsMkraRmza124qxTK0s0HDls3klYrw&callback=initMap">
    </script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>