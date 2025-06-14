<?php
// ❌ WARNING: This script is intentionally vulnerable. Do NOT deploy on a live system.

$conn = new mysqli("localhost", "root", "", "vulnerable_app");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 🆕 Vulnerable file upload feature
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_pic'])) {
    $upload_dir = "uploads/";
    $filename = basename($_FILES["profile_pic"]["name"]);
    $target_file = $upload_dir . $filename;

    // 🚨 No file type or content check!
    if (move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $target_file)) {
        echo "Profile picture uploaded successfully: <a href='$target_file'>$filename</a><br>";
    } else {
        echo "Upload failed.<br>";
    }
}

// 🆕 Display upload form
echo <<<HTML
<h2>Upload Profile Picture</h2>
<form method="POST" enctype="multipart/form-data">
    <input type="file" name="profile_pic"><br>
    <button type="submit">Upload</button>
</form>
HTML;

// 🔁 Existing functionality continues below...
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

$conn->close();
?>
