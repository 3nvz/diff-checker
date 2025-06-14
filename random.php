<?php
// ❌ WARNING: This script is intentionally vulnerable. Do NOT deploy on a live system.

// Connect to database (hardcoded credentials)
$conn = new mysqli("localhost", "root", "", "vulnerable_app");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get `id` parameter from user input
$id = $_GET['id'];

// 🚨 SQL Injection vulnerability
$sql = "SELECT * FROM users WHERE id = $id";
$result = $conn->query($sql);

// Output user info
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        // 🚨 XSS vulnerability
        echo "User: " . $row["username"] . "<br>";
        echo "Email: " . $row["email"] . "<br>";
    }
} else {
    echo "No user found.";
}

$conn->close();
?>
