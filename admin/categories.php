<?php
session_start();
include '../db.php'; // Ensure your database connection file is correctly included

// Check if the user is logged in
function isAuthenticated() {
    return isset($_SESSION['admin_email']);
}

if (!isAuthenticated()) {
    header("Location: login.php"); // Redirect to the login page if not authenticated
    exit;
}

// Function to fetch all categories
function getCategories() {
    global $conn;
    $sql = "SELECT * FROM categories";
    $result = $conn->query($sql);
    $categories = [];
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }
    return $categories;
}

// Handle add category
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add'])) {
    $name = $conn->real_escape_string($_POST['name']);
    $insertSql = "INSERT INTO categories (name) VALUES (?)";
    $stmt = $conn->prepare($insertSql);
    $stmt->bind_param("s", $name);
    if ($stmt->execute()) {
        echo "<p class='alert alert-success'>Category added successfully.</p>";
    } else {
        echo "<p class='alert alert-danger'>Error adding category: " . $stmt->error . "</p>";
    }
}

// Handle delete category
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $deleteSql = "DELETE FROM categories WHERE id = ?";
    $stmt = $conn->prepare($deleteSql);
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        echo "<p class='alert alert-success'>Category deleted successfully.</p>";
    } else {
        echo "<p class='alert alert-danger'>Error deleting category: " . $stmt->error . "</p>";
    }
}

// Handle update category
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    $name = $conn->real_escape_string($_POST['name']);
    $id = intval($_POST['id']);
    $updateSql = "UPDATE categories SET name = ? WHERE id = ?";
    $stmt = $conn->prepare($updateSql);
    $stmt->bind_param("si", $name, $id);
    if ($stmt->execute()) {
        echo "<p class='alert alert-success'>Category updated successfully.</p>";
    } else {
        echo "<p class='alert alert-danger'>Error updating category: " . $stmt->error . "</p>";
    }
}

$categories = getCategories();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="../css/bootstrap.min.css" />
    <link rel="stylesheet" href="../style.css" />
    <script src="../js/bootstrap.min.js"></script>
    <title>Category Management</title>
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
        <h2>Category Management</h2>
        <div>
            <h4>Add Category</h4>
            <form method="POST" action="">
                <div class="form-group">
                    <label for="name">Category Name:</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                    <button type="submit" class="btn btn-primary mt-2" name="add">Add Category</button>
                </div>
            </form>
        </div>
        <div>
            <h4>Existing Categories</h4>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $category): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($category['id']); ?></td>
                        <td><?php echo htmlspecialchars($category['name']); ?></td>
                        <td>
                            <form method="POST" action="">
                                <input type="hidden" name="id" value="<?php echo $category['id']; ?>">
                                <input type="text" class="form-control mb-2" name="name"
                                    value="<?php echo htmlspecialchars($category['name']); ?>" required>
                                <button type="submit" class="btn btn-warning" name="update">Update</button>
                                <a href="?delete=<?php echo $category['id']; ?>" class="btn btn-danger"
                                    onclick="return confirm('Are you sure you want to delete this category?');">Delete</a>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>