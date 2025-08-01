<?php


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['user_id'])) {
        header("Location: useradmin.html");
        exit();
    }

    $userId = intval($_POST['user_id']);

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

    // Prepare and execute delete statement
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            // Successful delete
            $stmt->close();
            $conn->close();
            header("Location: usersadmin.php");
            exit();
        } else {
            // No rows deleted - user id may not exist
            $stmt->close();
            $conn->close();
            die("No user found with the specified ID.");
        }
    } else {
        // Error executing statement
        $error = $stmt->error;
        $stmt->close();
        $conn->close();
        die("Error deleting user: " . $error);
    }


    exit();
}
?>
