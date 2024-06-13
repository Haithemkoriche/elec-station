<?php
include 'db.php'; // Database connection file

// Fetch categories from the database
function getCategories()
{
    global $conn;
    $sql = "SELECT * FROM categories";
    $result = $conn->query($sql);
    $categories = [];
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }
    return $categories;
}

$categories = getCategories();

// Fetch locations linked to categories
function getLocations()
{
    global $conn;
    $sql = "SELECT locations.*, categories.name AS category_name FROM locations 
            INNER JOIN categories ON locations.category_id = categories.id";
    $result = $conn->query($sql);
    $locations = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $locations[] = $row;
        }
    } else {
        error_log("SQL error: " . $conn->error);  // Log error to PHP error log
    }
    return $locations;
}


$locations = getLocations();

?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="css/bootstrap.min.css" />
    <link rel="stylesheet" href="style.css" />
    <script src="js/bootstrap.min.js"></script>
    <title>Voltway</title>
</head>

<body>
    <?php include 'session_management.php'; // Include your session management script 
    ?>
    <nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #a6490c">
        <div class="container-fluid container">
            <a class="navbar-brand" href="#"><img src="img/fav-icon-white.png" width="50" height="50" alt=""
                    srcset="" /><span class="bold">V O L T W A Y</span></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="#">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="about.html">About Us</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contact.html">Contact Us</a>
                    </li>
                    <?php if (isAuthenticated()) : ?>
                    <li class="nav-item"><a class="nav-link" href="logout.php">
                            <div class="btn btn-outline-light">Logout</div>
                        </a></li>
                    <?php else : ?>
                    <li class="nav-item"><a class="nav-link" href="signup.php">
                            <div class="btn btn-light">Sign Up</div>
                        </a></li>
                    <li class="nav-item"><a class="nav-link" href="signin.php">
                            <div class="btn btn-outline-light">Sign In</div>
                        </a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    <div class="" style="height: calc(100% - 56px);">
        <div id="sidebar" class="bg-light p-4" style="width: 250px; position: absolute; z-index: 10;">
            <img src="img/fav-icon.png" width="70" height="70" alt="" class="img img-fluid me-auto ms-auto d-block" />
            <h4>Filtres</h4>
            <?php foreach ($categories as $category) : ?>
            <div class="form-check">
                <input class="form-check-input" type="checkbox"
                    value="<?php echo htmlspecialchars($category['name']); ?>"
                    id="category-<?php echo $category['id']; ?>" />
                <label class="form-check-label" for="category-<?php echo $category['id']; ?>">
                    <?php echo htmlspecialchars($category['name']); ?>
                </label>
            </div>
            <?php endforeach; ?>
        </div>
        <div id="map" class="flex-grow-1" style="position: relative"></div>
    </div>

    <script>
    let map;
    let infowindow;
    const locations = <?php echo json_encode($locations); ?>;
    let markers = [];

    function initMap() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(position => {
                const pos = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude,
                };
                initializeMap(pos);
            }, () => {
                // Fallback if geolocation fails or is not allowed
                initializeMap({
                    lat: 36.7525,
                    lng: 3.04197
                }); // Default coordinates for Algiers, Algeria
            });
        } else {
            // Geolocation is not supported by this browser
            initializeMap({
                lat: 36.7525,
                lng: 3.04197
            }); // Default coordinates for Algiers, Algeria
        }
    }

    function initializeMap(pos) {
        map = new google.maps.Map(document.getElementById("map"), {
            center: pos,
            zoom: 14,
        });
        infowindow = new google.maps.InfoWindow();
        addMarkers();
        setupCategoryFilter();
    }

    function addMarkers() {
        locations.forEach(location => {
            const pos = {
                lat: parseFloat(location.latitude),
                lng: parseFloat(location.longitude)
            };
            const marker = new google.maps.Marker({
                position: pos,
                map: null, // Initially do not display the marker
                icon: {
                    url: 'img/fav-icon.png',
                    scaledSize: new google.maps.Size(50, 50) // Adjust the size as needed
                },
                title: location.name,
                category: location.category_name
            });


            google.maps.event.addListener(marker, 'click', () => {
                infowindow.setContent(location.name + "<br/>" + location.category_name);
                infowindow.open(map, marker);
            });

            markers.push(marker);
        });
    }

    function setupCategoryFilter() {
        document.querySelectorAll(".form-check-input").forEach(input => {
            input.addEventListener("change", filterMarkers); // Bind filtering function to checkbox changes
        });
        filterMarkers(); // Call once to apply filters based on the initial checkbox state
    }

    function filterMarkers() {
        const selectedCategories = Array.from(document.querySelectorAll('input[type="checkbox"]:checked')).map(input =>
            input.value);
        markers.forEach(marker => {
            if (selectedCategories.length === 0) {
                marker.setMap(null); // Hide all markers if no category is selected
            } else if (selectedCategories.includes(marker.category)) {
                marker.setMap(map); // Show marker if its category is selected
            } else {
                marker.setMap(null); // Hide marker if its category is not selected
            }
        });
    }
    </script>


    <script async defer
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDveHsMkraRmza124qxTK0s0HDls3klYrw&libraries=places&callback=initMap">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>