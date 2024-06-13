<?php
session_start();
include '../db.php'; // Ensure your database connection file is correctly included

// Function to check if user is logged in
function isAuthenticated()
{
    return isset($_SESSION['admin_email']); // Adjust as necessary for your session variable
}

if (!isAuthenticated()) {
    header("Location: login.php"); // Redirect to the login page if not authenticated
    exit;
}

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

// Fetch locations from the database
function getLocations()
{
    global $conn;
    $sql = "SELECT locations.*, categories.name AS category_name FROM locations 
            LEFT JOIN categories ON locations.category_id = categories.id";
    $result = $conn->query($sql);
    $locations = [];
    while ($row = $result->fetch_assoc()) {
        $locations[] = $row;
    }
    return $locations;
}

$categories = getCategories();
$locations = getLocations();

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add'])) {
        $latitude = $conn->real_escape_string($_POST['latitude']);
        $longitude = $conn->real_escape_string($_POST['longitude']);
        $name = $conn->real_escape_string($_POST['name']);
        $category_id = intval($_POST['category_id']);

        $insertSql = "INSERT INTO locations (latitude, longitude, name, category_id) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($insertSql);
        $stmt->bind_param("sssi", $latitude, $longitude, $name, $category_id);
        if ($stmt->execute()) {
            echo "<p class='alert alert-success'>New location added successfully.</p>";
            header("location: index.php");
        } else {
            echo "<p class='alert alert-danger'>Error adding location: " . $conn->error . "</p>";
        }
    }
    // Check for delete request
}
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $deleteSql = "DELETE FROM locations WHERE id = $id";
    if ($conn->query($deleteSql) === TRUE) {
        echo "<p class='alert alert-success'>Location deleted successfully.</p>";
        header("location: index.php");
    } else {
        echo "<p class='alert alert-danger'>Error deleting record: " . $conn->error . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="../css/bootstrap.min.css" />
    <link rel="stylesheet" href="../style.css" />
    <script src="../js/bootstrap.min.js"></script>
    <title>Admin Dashboard</title>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #a6490c">
        <div class="container-fluid container">
            <a class="navbar-brand" href="#"><img src="../img/fav-icon-white.png" width="50" height="50" alt=""
                    srcset="" /><span class="bold">V O L T W A Y</span></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item">
                        <a class="nav-link " href="../index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="index.php">Locations</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="categories.php">Categories</a>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">
                            <div class="btn btn-outline-light">Logout</div>
                        </a></li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container mt-5">
        <h1>Welcome to the Admin Dashboard</h1>
        <div class="row">
            <div class="col-md-4">
                <h2>Add New Location</h2>
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="name">Name:</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="latitude">Latitude:</label>
                        <input type="text" class="form-control" id="latitude" name="latitude" required>
                    </div>
                    <div class="form-group">
                        <label for="longitude">Longitude:</label>
                        <input type="text" class="form-control" id="longitude" name="longitude" required>
                    </div>
                    <div class="form-group">
                        <label for="category_id">Category:</label>
                        <select class="form-control" id="category_id" name="category_id">
                            <?php foreach ($categories as $category) : ?>
                            <option value="<?php echo $category['id']; ?>">
                                <?php echo htmlspecialchars($category['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <br>
                    <button type="submit" class="btn btn-primary" name="add">Add Location</button>
                </form>
            </div>
            <div class="col-md-8">
                <h2>Manage Locations</h2>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Latitude</th>
                            <th>Longitude</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($locations as $location) : ?>
                        <tr>
                            <td><?php echo htmlspecialchars($location['id']); ?></td>
                            <td><?php echo htmlspecialchars($location['latitude']); ?></td>
                            <td><?php echo htmlspecialchars($location['longitude']); ?></td>
                            <td><?php echo htmlspecialchars($location['name']); ?></td>
                            <td><?php echo htmlspecialchars($location['category_name']); ?></td>
                            <td>
                                <a href="edit_location.php?id=<?php echo $location['id']; ?>"
                                    class="btn btn-primary">Edit</a>
                                <a href="?delete=<?php echo $location['id']; ?>" class="btn btn-danger"
                                    onclick="return confirm('Are you sure you want to delete this location?');">Delete</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>