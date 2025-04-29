<?php
include 'db.php'; // Connect to your database

// Hash the default password "123"
$default_hashed_password = password_hash("123", PASSWORD_DEFAULT);

// Update all users' passwords in the database
$sql = "UPDATE users SET password = '$default_hashed_password'";

if ($conn->query($sql) === TRUE) {
    echo "All users' passwords have been updated!";
} else {
    echo "Error updating passwords: " . $conn->error;
}

$conn->close();
?>
