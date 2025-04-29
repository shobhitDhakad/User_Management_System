<?php
session_start();
require_once 'db.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user data
$sql = "SELECT firstName, lastName, email, gender, mobile, city, language, images FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("SQL Error: " . $conn->error);
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    echo "User not found!";
    exit();
}

// Parse language field (JSON array or comma-separated)
$languages = [];
if (!empty($user['language'])) {
    $languages = json_decode($user['language'], true) ?? explode(',', $user['language']);
}

// Handle profile image
$profileImage = !empty($user['images']) ? "uploads/" . htmlspecialchars($user['images']) : "logo img/default-user.jpg";

// Update user profile
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstName = trim($_POST['firstName']);
    $lastName = trim($_POST['lastName']);
    $gender = trim($_POST['gender']);
    $mobile = trim($_POST['mobile']);
    $city = trim($_POST['city']);
    $language = isset($_POST['language']) ? json_encode($_POST['language']) : '';

    // Handle image upload
    if (!empty($_FILES['profile_image']['name'])) {
        $uploadDir = "uploads/";
        $imageName = time() . "_" . basename($_FILES['profile_image']['name']);
        $targetFile = $uploadDir . $imageName;
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        // Check image type
        if (in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
            if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $targetFile)) {
                // Delete old image if it exists
                if (!empty($user['images']) && file_exists("uploads/" . $user['images'])) {
                    unlink("uploads/" . $user['images']);
                }
                $profileImage = $imageName;
            } else {
                die("Error uploading image!");
            }
        } else {
            die("Invalid image format! Only JPG, JPEG, PNG, and GIF allowed.");
        }
    } else {
        $profileImage = $user['images']; // Keep old image if no new image uploaded
    }

    // Update database
    $updateSql = "UPDATE users SET firstName = ?, lastName = ?, gender = ?, mobile = ?, city = ?, language = ?, images = ? WHERE id = ?";
    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->bind_param("sssssssi", $firstName, $lastName, $gender, $mobile, $city, $language, $profileImage, $user_id);

    if ($updateStmt->execute()) {
        header("Location: profile.php?success=Profile updated");
        exit();
    } else {
        die("Error updating profile: " . $conn->error);
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="styles.css"> <!-- Add your CSS file -->
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .form-container {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            width: 400px;
            text-align: center;
        }

        .form-group {
            margin-bottom: 15px;
            text-align: left;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
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

        .profile-pic img {
            border-radius: 50%;
            width: 120px;
            height: 120px;
            object-fit: cover;
            border: 3px solid #007bff;
        }
    </style>
</head>

<body>
    <div class="form-container">
        <h2>Edit Profile</h2>

        <form action="edit_profile.php" method="POST" enctype="multipart/form-data">

            <!-- Profile Image -->
            <div class="profile-pic">
                <img src="<?= $profileImage ?>" alt="Profile Image">
            </div>
            <div class="form-group">
                <label for="profile_image">Change Profile Image:</label>
                <input type="file" name="profile_image" id="profile_image">
            </div>

            <!-- First Name -->
            <div class="form-group">
                <label for="firstName">First Name:</label>
                <input type="text" name="firstName" value="<?= htmlspecialchars($user['firstName']) ?>" required>
            </div>

            <!-- Last Name -->
            <div class="form-group">
                <label for="lastName">Last Name:</label>
                <input type="text" name="lastName" value="<?= htmlspecialchars($user['lastName']) ?>" required>
            </div>

            <!-- Gender -->
            <div class="form-group">
                <label for="gender">Gender:</label>
                <select name="gender" required>
                    <option value="Male" <?= $user['gender'] == 'Male' ? 'selected' : '' ?>>Male</option>
                    <option value="Female" <?= $user['gender'] == 'Female' ? 'selected' : '' ?>>Female</option>
                </select>
            </div>

            <!-- Mobile -->
            <div class="form-group">
                <label for="mobile">Mobile:</label>
                <input type="text" name="mobile" value="<?= htmlspecialchars($user['mobile']) ?>" required>
            </div>

            <!-- City -->
            <div class="form-group">
                <label for="city">City:</label>
                <input type="text" name="city" value="<?= htmlspecialchars($user['city']) ?>" required>
            </div>

            <!-- Languages -->
            <div class="form-group">
                <label>Languages:</label>
                <input type="text" name="language[]" value="<?= implode(", ", $languages) ?>" required>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn">Save Changes</button>
            <a href="profile.php" class="btn">Cancel</a>

        </form>
    </div>
</body>

</html>
