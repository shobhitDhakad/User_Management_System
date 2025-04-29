<?php
session_start();
require_once 'db.php'; // Ensure database connection

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT firstName, lastName, email, gender, mobile, city, language FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("SQL Error: " . $conn->error);
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
} else {
    echo "User not found!";
    exit();
}

// Handle languages (JSON array or comma-separated)
$languages = [];
if (!empty($user['language'])) {
    if (json_decode($user['language']) !== null) {
        $languages = json_decode($user['language'], true);
    } else {
        $languages = explode(',', $user['language']);
    }
}

// Handle user image (skip if empty)
$profileImage = !empty($user['img']) ? "uploads/" . htmlspecialchars($user['img']) : "dist/assets/img/default-user.png";

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="styles.css"> <!-- Add your CSS file -->
    <style>
        /* General Page Styling */
body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}

/* Profile Container */
.profile-container {
    background: #fff;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    text-align: center;
    width: 350px;
}

/* Profile Picture */
.profile-pic img {
    border-radius: 50%;
    width: 120px;
    height: 120px;
    object-fit: cover;
    border: 3px solid #007bff;
    margin-bottom: 15px;
}

/* Profile Info */
p {
    font-size: 16px;
    color: #333;
    margin: 8px 0;
}

p strong {
    color: #007bff;
}

/* Buttons */
.btn {
    display: inline-block;
    padding: 10px 15px;
    margin: 10px 5px;
    font-size: 14px;
    text-decoration: none;
    color: #fff;
    background-color: #007bff;
    border-radius: 5px;
    transition: 0.3s ease;
}

.btn:hover {
    background-color: #0056b3;
}

/* Responsive Design */
@media (max-width: 400px) {
    .profile-container {
        width: 90%;
        padding: 15px;
    }
}

    </style>
</head>
<body>
    <div class="profile-container">
        <h2>My Profile</h2>
        <div class="profile-pic">
          <!--  <img src="<?= $profileImage ?>" alt="Profile Picture" width="150"> -->
        </div>
        <p><strong>Name:</strong> <?= htmlspecialchars($user['firstName'] . " " . $user['lastName']) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
        <p><strong>Gender:</strong> <?= htmlspecialchars($user['gender']) ?></p>
        <p><strong>Mobile:</strong> <?= htmlspecialchars($user['mobile']) ?></p>
        <p><strong>City:</strong> <?= htmlspecialchars($user['city']) ?></p>
        <p><strong>Languages:</strong> <?= !empty($languages) ? htmlspecialchars(implode(", ", $languages)) : "Not specified" ?></p>
        
        <a href="edit_profile.php" class="btn">Edit Profile</a>
        <a href="logout.php" class="btn">Logout</a>
        <a href="dashboard.php" class="btn">Back</a>
    </div>
</body>
</html>
