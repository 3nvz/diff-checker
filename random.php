<?php
// ❌ WARNING: This script is intentionally vulnerable. Do NOT deploy on a live system.

$conn = new mysqli("localhost", "root", "", "vulnerable_app");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 🆕 Feature: Search by username
if (isset($_GET['search'])) {
    $search = $_GET['search'];

    // 🚨 SQL Injection possible here
    $sql = "SELECT * FROM users WHERE username LIKE '%$search%'";
    $result = $conn->query($sql);

    echo "<h2>Search Results for '$search'</h2>";
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            // 🚨 Stored/Reflected XSS risk
            echo "User: " . $row["username"] . " - Email: " . $row["email"] . "<br>";
        }
    } else {
        echo "No results found.";
    }
} else {
    // Original ID-based user lookup
    $id = $_GET['id'] ?? 1;
    $sql = "SELECT * FROM users WHERE id = $id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            echo "User: " . $row["username"] . "<br>";
            echo "Email: " . $row["email"] . "<br>";
        }
    } else {
        echo "No user found.";
    }
}

$conn->close();
?>
