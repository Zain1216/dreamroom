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

$search = "";
if (isset($_GET['search'])) {
    $search = $conn->real_escape_string($_GET['search']);
}

// Fetch users from database
if ($search !== "") {
    $sql = "SELECT id, user_name, email FROM users WHERE user_name LIKE '%$search%' OR email LIKE '%$search%'";
} else {
    $sql = "SELECT id, user_name, email FROM users";
}
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html>
  <head>
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin="" />
    <link
      rel="stylesheet"
      as="style"
      onload="this.rel='stylesheet'"
      href="https://fonts.googleapis.com/css2?display=swap&amp;family=Manrope%3Awght%40400%3B500%3B700%3B800&amp;family=Noto+Sans%3Awght%40400%3B500%3B700%3B900"
    />
    <title>Stitch Design</title>
    <link rel="icon" type="image/x-icon" href="data:image/x-icon;base64," />
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
  </head>
  <body>
    <div class="relative flex size-full min-h-screen flex-col bg-white group/design-root overflow-x-hidden" style='font-family: Manrope, "Noto Sans", sans-serif;'>
      <div class="layout-container flex h-full grow flex-col">
        <div class="gap-1 px-6 flex flex-1 justify-center py-5">
          <div class="layout-content-container flex flex-col w-80">
            <div class="flex h-full min-h-[700px] flex-col justify-between bg-white p-4">
              <div class="flex flex-col gap-4">
                <div class="flex flex-col">
                  <h1 class="text-[#121517] text-base font-medium leading-normal">Dream Room 3D</h1>
                  <p class="text-[#687782] text-sm font-normal leading-normal">Admin</p>
                </div>
                <div class="flex flex-col gap-2">
                  <div class="flex items-center gap-3 px-3 py-2">
                    <div class="text-[#121517]" data-icon="House" data-size="24px" data-weight="regular">
                      <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" fill="currentColor" viewBox="0 0 256 256">
                        <path
                          d="M218.83,103.77l-80-75.48a1.14,1.14,0,0,1-.11-.11,16,16,0,0,0-21.53,0l-.11.11L37.17,103.77A16,16,0,0,0,32,115.55V208a16,16,0,0,0,16,16H96a16,16,0,0,0,16-16V160h32v48a16,16,0,0,0,16,16h48a16,16,0,0,0,16-16V115.55A16,16,0,0,0,218.83,103.77ZM208,208H160V160a16,16,0,0,0-16-16H112a16,16,0,0,0-16,16v48H48V115.55l.11-.1L128,40l79.9,75.43.11.1Z"
                        ></path>
                      </svg>
                    </div>
                    <a class="text-[#121517] text-sm font-medium leading-normal" href="admin.php">Dashboard</a>
                  </div>
                  
                 
                 
                  <div class="flex items-center gap-3 px-3 py-2 rounded-full bg-[#f1f2f4]">
                    <div class="text-[#121517]" data-icon="Users" data-size="24px" data-weight="fill">
                      <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" fill="currentColor" viewBox="0 0 256 256">
                        <path
                          d="M164.47,195.63a8,8,0,0,1-6.7,12.37H10.23a8,8,0,0,1-6.7-12.37,95.83,95.83,0,0,1,47.22-37.71,60,60,0,1,1,66.5,0A95.83,95.83,0,0,1,164.47,195.63Zm87.91-.15a95.87,95.87,0,0,0-47.13-37.56A60,60,0,0,0,144.7,54.59a4,4,0,0,0-1.33,6A75.83,75.83,0,0,1,147,150.53a4,4,0,0,0,1.07,5.53,112.32,112.32,0,0,1,29.85,30.83,23.92,23.92,0,0,1,3.65,16.47,4,4,0,0,0,3.95,4.64h60.3a8,8,0,0,0,7.73-5.93A8.22,8.22,0,0,0,252.38,195.48Z"
                        ></path>
                      </svg>
                    </div>
                    <p class="text-[#121517] text-sm font-medium leading-normal">Users</p>
                  </div>
                  
                </div>
              </div>
              <button
                onclick='document.getElementById("addUserModal").style.display="block"'
                class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-full h-10 px-4 bg-[#4990ca] text-white text-sm font-bold leading-normal tracking-[0.015em]"
              >
                <span class="truncate">Add New User</span>
              </button>
            </div>
          </div>
          <div class="layout-content-container flex flex-col max-w-[960px] flex-1">
            <div class="flex flex-wrap justify-between gap-3 p-4">
              <div class="flex min-w-72 flex-col gap-3">
                <p class="text-[#121517] tracking-light text-[32px] font-bold leading-tight">Users</p>
                <p class="text-[#687782] text-sm font-normal leading-normal">Manage all users of Dream Room 3D</p>
              </div>
            </div>
            <div class="px-4 py-3">
                  <form method="GET" action="usersadmin.php" class="flex flex-col min-w-40 h-12 w-full">
                    <div class="flex w-full flex-1 items-stretch rounded-xl h-full">
                      <div
                        class="text-[#687782] flex border-none bg-[#f1f2f4] items-center justify-center pl-4 rounded-l-xl border-r-0"
                        data-icon="MagnifyingGlass"
                        data-size="24px"
                        data-weight="regular"
                      >
                        <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" fill="currentColor" viewBox="0 0 256 256">
                          <path
                            d="M229.66,218.34l-50.07-50.06a88.11,88.11,0,1,0-11.31,11.31l50.06,50.07a8,8,0,0,0,11.32-11.32ZM40,112a72,72,0,1,1,72,72A72.08,72.08,0,0,1,40,112Z"
                          ></path>
                        </svg>
                      </div>
                      <input
                        name="search"
                        placeholder="Search users"
                        class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-xl text-[#121517] focus:outline-0 focus:ring-0 border-none bg-[#f1f2f4] focus:border-none h-full placeholder:text-[#687782] px-4 rounded-l-none border-l-0 pl-2 text-base font-normal leading-normal"
                        value="<?php echo htmlspecialchars($search); ?>"
                      />
                    </div>
                  </form>
            </div>
            <div class="px-4 py-3 @container">
              <div class="flex overflow-hidden rounded-xl border border-[#dde1e4] bg-white">
                <table class="flex-1">
                  <thead>
                    <tr class="bg-white">
                      <th class="px-4 py-3 text-left text-[#121517] w-[400px] text-sm font-medium leading-normal">
                        Username
                      </th>
                      <th class="px-4 py-3 text-left text-[#121517] w-[400px] text-sm font-medium leading-normal">Email</th>
                      <th class="px-4 py-3 text-left text-[#687782] w-60 text-sm font-bold leading-normal tracking-[0.015em]">
                        Actions
                      </th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr class='border-t border-t-[#dde1e4]'>";
                            echo "<td class='h-[72px] px-4 py-2 w-[400px] text-[#121517] text-sm font-normal leading-normal'>" . htmlspecialchars($row['user_name']) . "</td>";
                            echo "<td class='h-[72px] px-4 py-2 w-[400px] text-[#687782] text-sm font-normal leading-normal'>" . htmlspecialchars($row['email']) . "</td>";
                            echo "<td class='h-[72px] px-4 py-2 w-60 text-[#687782] text-sm font-bold leading-normal tracking-[0.015em]'>";
                            echo "<a href='view_user.php?id=" . $row['id'] . "'>View</a> | ";
                            echo "<a href='edit_user.php?id=" . $row['id'] . "'>Edit</a> | ";
                            echo "<form action='delete_user.php' method='POST' style='display:inline;' onsubmit='return confirm(\"Are you sure you want to delete this user?\");'>";
                            echo "<input type='hidden' name='user_id' value='" . $row['id'] . "'>";
                            echo "<button type='submit' style='background:none;border:none;color:#687782;cursor:pointer;padding:0;'>Delete</button>";
                            echo "</form>";
                            echo "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='3'>No users found.</td></tr>";
                    }
                    $conn->close();
                    ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Add User Modal -->
    <div id="addUserModal" style="display:none; position:fixed; top:10%; left:50%; transform:translateX(-50%); background:white; padding:20px; border-radius:8px; box-shadow:0 2px 10px rgba(0,0,0,0.1); z-index:1000;">
      <h2 class="text-lg font-bold mb-4">Add New User</h2>
      <form action="create_user.php" method="POST">
        <label for="fullname">Full Name:</label><br />
        <input type="text" id="fullname" name="fullname" required class="border rounded px-2 py-1 w-full mb-2" /><br />
        <label for="email">Email:</label><br />
        <input type="email" id="email" name="email" required class="border rounded px-2 py-1 w-full mb-2" /><br />
        <label for="password">Password:</label><br />
        <input type="password" id="password" name="password" required class="border rounded px-2 py-1 w-full mb-2" /><br />
        <label for="status">Status:</label><br />
        <select id="status" name="status" class="border rounded px-2 py-1 w-full mb-2">
          <option value="active">Active</option>
          <option value="inactive">Inactive</option>
        </select><br />
        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Add User</button>
        <button type="button" onclick='document.getElementById("addUserModal").style.display="none"' class="ml-2 px-4 py-2 rounded border">Cancel</button>
      </form>
    </div>
  </body>
</html>
