<?php
$conn = new mysqli("localhost", "root", "", "form");

if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
} else {
    echo "Database Connected Successfully!";
}
?>
