<?php
include 'config.php';

session_start();
if (isset($_SESSION['email'])) {
    header("Location: index.php");
}

$forgotPasswordEmail = $_SESSION['forgotPasswordEmail'];

if (!isset($_SESSION['forgotPasswordEmail'])) {
    header('location:forgot-password.php');
}

if (isset($_POST['updatePasswordBtn'])) {

    $password = $_POST['password'];

    $hash_password = md5($_POST['password']);


    $insert = "UPDATE `users` SET `password`='$hash_password' WHERE `email`='$forgotPasswordEmail'";

    if (mysqli_query($connect, $insert)) {
        header('location:login.php');


    } else {
        echo "<div class='alert alert-danger text-center'>Password Updation failed.</div>";
    }
}

?>


<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Casahue - New Password</title>

    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      rel="stylesheet"
      href="https://fonts.googleapis.com/css2?display=swap&family=Manrope:wght@400;500;700;800&family=Noto+Sans:wght@400;500;700;900"
    />
    <link rel="icon" type="image/x-icon" href="data:image/x-icon;base64," />

    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
  </head>

  <body class="bg-gray-50 font-sans text-gray-800">
    <div class="flex justify-center items-center min-h-screen px-4">
      <div class="w-full max-w-md bg-white shadow-md rounded-xl p-6">
        <h2 class="text-2xl font-bold text-center text-[#121517] mb-2">Create a New Password</h2>
        <p class="text-center text-sm text-gray-600 mb-6">
          Your password must be at least 8 characters long and include a number, an uppercase, and a lowercase letter.
        </p>

        <form action="<?= $_SERVER['PHP_SELF']; ?>" method="POST" class="space-y-5">
          <div>
            <label class="block text-sm font-medium text-[#121517] mb-1">New Password</label>
            <input
              type="password"
              name="password"
              placeholder="Enter new password"
              required
              class="w-full h-12 px-4 rounded-xl border border-gray-300 focus:outline-none focus:border-blue-400 text-base placeholder:text-gray-500"
            />
          </div>

          <div class="text-center pt-2">
            <button
            name="updatePasswordBtn"
              type="submit"
              class="w-full h-10 rounded-full bg-[#4990ca] text-white text-sm font-semibold tracking-wide hover:bg-[#397cb0] transition"
            >
              Update Password
            </button>
          </div>
        </form>
      </div>
    </div>
  </body>
</html>
