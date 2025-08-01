<?php
session_start();


// Database configuration
$host = "localhost";
$dbUsername = "root";
$dbPassword = "";
$dbName = "dream-house";

// Create connection
$conn = new mysqli($host, $dbUsername, $dbPassword, $dbName);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to sanitize input data
function sanitize_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_name = sanitize_input($_POST['fullname']);
    $email = sanitize_input($_POST['email']);
    $password = $_POST['password'];
    // Basic validation
    if (empty($user_name) || empty($email) || empty($password)) {
        $_SESSION['error'] = "All fields are required.";
        header("Location: usersadmin.php");
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Invalid email format.";
        header("Location: usersadmin.php");
        exit();
    }

    // Check if email already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $_SESSION['error'] = "Email already registered.";
        $stmt->close();
        $conn->close();
        header("Location: usersadmin.php");
        exit();
    }
    $stmt->close();

    // Hash the password
    // Insert user into database
    $stmt = $conn->prepare("INSERT INTO users (user_name, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $user_name, $email, $password);

    if ($stmt->execute()) {
        $_SESSION['success'] = "User added successfully.";
        $stmt->close();
        $conn->close();
        header("Location: usersadmin.php");
        exit();
    } else {
        $_SESSION['error'] = "Error: " . $stmt->error;
        $stmt->close();
        $conn->close();
        header("Location: usersadmin.php");
        exit();
    }
} else {
    header("Location: usersadmin.php");
    exit();
}
?>
