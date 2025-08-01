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

function sanitize_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (!isset($_GET['id'])) {
        header("Location: usersadmin.php");
        exit();
    }
    $userId = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT id, user_name, email FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows == 0) {
        $stmt->close();
        $conn->close();
        header("Location: usersadmin.php");
        exit();
    }
    $user = $result->fetch_assoc();
    $stmt->close();
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Edit User</title>
        <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin="" />
        <link
          rel="stylesheet"
          as="style"
          onload="this.rel='stylesheet'"
          href="https://fonts.googleapis.com/css2?display=swap&amp;family=Manrope%3Awght%40400%3B500%3B700%3B800&amp;family=Noto+Sans%3Awght%40400%3B500%3B700%3A900"
        />
        <link rel="icon" type="image/x-icon" href="data:image/x-icon;base64," />
        <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    </head>
    <body>
    <div id="navbar-container"></div>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
      $(function () {
        $("#navbar-container").load("navbar.html");
      });
    </script>

    <form method="POST" action="edit_user.php" class="px-40 flex flex-1 justify-center py-5">
      <div class="layout-content-container flex flex-col w-[512px] max-w-[512px] py-5 max-w-[960px] flex-1">
        <h2 class="text-[#121517] tracking-light text-[28px] font-bold leading-tight px-4 text-center pb-3 pt-5">Edit User</h2>

        <?php if (isset($_SESSION['error'])): ?>
          <div class="text-red-600 text-center font-semibold mb-4 px-4">
            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
          </div>
        <?php endif; ?>
        <?php if (isset($_SESSION['success'])): ?>
          <div class="text-green-600 text-center font-semibold mb-4 px-4">
            <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
          </div>
        <?php endif; ?>

        <input type="hidden" name="id" value="<?php echo $user['id']; ?>" />
        <div class="flex max-w-[480px] flex-wrap items-end gap-4 px-4 py-3">
          <label for="user_name" class="flex flex-col min-w-40 flex-1">
            <p class="text-[#121517] text-base font-medium leading-normal pb-2">Full Name</p>
            <input
              id="user_name"
              name="user_name"
              type="text"
              placeholder="Full name"
              class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-xl text-[#121517] focus:outline-0 focus:ring-0 border border-[#dde1e4] bg-white focus:border-[#dde1e4] h-14 placeholder:text-[#687782] p-[15px] text-base font-normal leading-normal"
              value="<?php echo htmlspecialchars($user['user_name']); ?>"
              required
            />
          </label>
        </div>
        <div class="flex max-w-[480px] flex-wrap items-end gap-4 px-4 py-3">
          <label for="email" class="flex flex-col min-w-40 flex-1">
            <p class="text-[#121517] text-base font-medium leading-normal pb-2">Email</p>
            <input
              id="email"
              name="email"
              type="email"
              placeholder="Email address"
              class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-xl text-[#121517] focus:outline-0 focus:ring-0 border border-[#dde1e4] bg-white focus:border-[#dde1e4] h-14 placeholder:text-[#687782] p-[15px] text-base font-normal leading-normal"
              value="<?php echo htmlspecialchars($user['email']); ?>"
              required
            />
          </label>
        </div>
        <div class="flex max-w-[480px] flex-wrap items-end gap-4 px-4 py-3">
          <label for="password" class="flex flex-col min-w-40 flex-1">
            <p class="text-[#121517] text-base font-medium leading-normal pb-2">Password <small>(Leave blank to keep current password)</small></p>
            <input
              id="password"
              name="password"
              type="password"
              placeholder="Password"
              class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-xl text-[#121517] focus:outline-0 focus:ring-0 border border-[#dde1e4] bg-white focus:border-[#dde1e4] h-14 placeholder:text-[#687782] p-[15px] text-base font-normal leading-normal"
            />
          </label>
        </div>
        <div class="flex max-w-[480px] flex-wrap items-end gap-4 px-4 py-3">
        </div>
        <div class="flex px-4 py-3">
          <button
            type="submit"
            class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-xl h-10 px-4 flex-1 bg-[#4990ca] text-white text-sm font-bold leading-normal tracking-[0.015em]"
          >
            <span class="truncate">Update User</span>
          </button>
        </div>
        <a href="usersadmin.php" class="text-[#687782] text-sm font-normal leading-normal pb-3 pt-1 px-4 underline">Cancel</a>
      </div>
    </form>
  </body>
  </html>
  <?php
} elseif ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userId = intval($_POST['id']);
    $user_name = sanitize_input($_POST['user_name']);
    $email = sanitize_input($_POST['email']);
    $password = $_POST['password'];
    if (empty($user_name) || empty($email)) {
        $_SESSION['error'] = "Full name and email are required.";
        header("Location: edit_user.php?id=" . $userId);
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Invalid email format.";
        header("Location: edit_user.php?id=" . $userId);
        exit();
    }

    // Check if email is used by another user
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
    $stmt->bind_param("si", $email, $userId);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $_SESSION['error'] = "Email already registered by another user.";
        $stmt->close();
        $conn->close();
        header("Location: edit_user.php?id=" . $userId);
        exit();
    }
    $stmt->close();

        if (!empty($password)) {
            // Hash new password
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET user_name = ?, email = ?, password = ? WHERE id = ?");
            $stmt->bind_param("sssi", $user_name, $email, $passwordHash, $userId);
        } else {
            // Update without changing password
            $stmt = $conn->prepare("UPDATE users SET user_name = ?, email = ? WHERE id = ?");
            $stmt->bind_param("ssi", $user_name, $email, $userId);
        }

    if ($stmt->execute()) {
        if ($stmt->affected_rows === 0) {
            $_SESSION['error'] = "No changes were made to the user.";
        } else {
            $_SESSION['success'] = "User updated successfully.";
        }
        $stmt->close();
        $conn->close();
        header("Location: usersadmin.php");
        exit();
    } else {
        $_SESSION['error'] = "Error: " . $stmt->error;
        $stmt->close();
        $conn->close();
        header("Location: edit_user.php?id=" . $userId);
        exit();
    }
} else {
    header("Location: usersadmin.php");
    exit();
}
?>
