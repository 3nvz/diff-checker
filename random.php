<?php
// ❌ WARNING: This script is intentionally vulnerable. DO NOT deploy this on a real server.

$conn = new mysqli("localhost", "root", "", "vulnerable_app");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 🆕 Vulnerable file upload feature
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_pic'])) {
    $upload_dir = "uploads/";
    $filename = basename($_FILES["profile_pic"]["name"]);
    $target_file = $upload_dir . $filename;

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

// 🆕 Public comment feature (intentionally vulnerable)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment'])) {
    $comment = $_POST['comment'];
    file_put_contents("comments.txt", $comment . "\n", FILE_APPEND);
    echo "<p><strong>Comment saved!</strong></p>";
}

echo <<<HTML
<h2>Leave a Comment</h2>
<form method="POST">
    <textarea name="comment" rows="4" cols="50" placeholder="Write something..."></textarea><br>
    <button type="submit">Post Comment</button>
</form>
HTML;

// 🆕 Display stored comments (no sanitization - XSS risk)
echo "<h3>Public Comments</h3><div style='background:#f9f9f9;padding:10px;border:1px solid #ccc;'>";
if (file_exists("comments.txt")) {
    echo nl2br(file_get_contents("comments.txt"));  // 🚨 XSS risk
}
echo "</div>";

// 🆕 Vulnerable Ping feature (Command Injection)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['host'])) {
    $host = $_POST['host'];
    $output = shell_exec("ping -c 2 $host");  // 🚨 Command injection risk
    echo "<pre>$output</pre>";
}

echo <<<HTML
<h2>Ping a Host</h2>
<form method="POST">
    <input type="text" name="host" placeholder="e.g., 8.8.8.8 or google.com"><br>
    <button type="submit">Ping</button>
</form>
HTML;

// 🆕 LFI Download feature (no sanitization)
if (isset($_GET['file'])) {
    $file = $_GET['file'];
    if (file_exists($file)) {
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($file) . '"');
        readfile($file);  // 🚨 LFI / file disclosure
        exit;
    } else {
        echo "<p style='color:red;'>File not found: $file</p>";
    }
}

echo <<<HTML
<h2>Download a File</h2>
<form method="GET">
    <input type="text" name="file" placeholder="Enter file path"><br>
    <button type="submit">Download</button>
</form>
HTML;

// 🔁 Original user lookup continues (vulnerable to SQLi if unchecked)
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
