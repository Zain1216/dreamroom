<?php
session_start();
include "config.php";

if (isset($_POST['submit'])) {
  $name = mysqli_real_escape_string($connect, $_POST['user_name']);
  $email = mysqli_real_escape_string($connect, $_POST['email']);
  $password = mysqli_real_escape_string($connect, md5($_POST['password']));

  $check = "SELECT email FROM users WHERE email = '{$email}'";
  $result = mysqli_query($connect, $check);

  if (mysqli_num_rows($result) > 0) {
    echo "<div class='text-red-600 text-center mt-4'>Email already exists.</div>";
  } else {
    $insert = "INSERT INTO users(user_name, email, password) VALUES ('{$name}','{$email}','{$password}')";
    if (mysqli_query($connect, $insert)) {
      $_SESSION['user_id'] = mysqli_insert_id($connect);
      header('Location: login.php');
      exit;
    } else {
      echo "<div class='text-red-600 text-center mt-4'>Registration failed.</div>";
    }
  }
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <title>Casahue</title>
  <link rel="icon" type="image/x-icon" href="data:image/x-icon;base64," />
  <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin />
  <link rel="stylesheet" as="style" onload="this.rel='stylesheet'"
    href="https://fonts.googleapis.com/css2?display=swap&family=Manrope:wght@400;500;700;800&family=Noto+Sans:wght@400;500;700;900" />
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
</head>

<body class="bg-white font-sans">
  <div class="px-6 sm:px-10 lg:px-40 flex justify-center py-10">
    <div class="layout-content-container flex flex-col w-full max-w-[512px]">
      <h2 class="text-[#121517] text-[28px] font-bold text-center pb-6 pt-2">Create your account</h2>

      <form action="<?= $_SERVER['PHP_SELF']; ?>" method="post" class="w-full space-y-5">

        <!-- Name Field -->
        <div class="px-2">
          <label class="block text-[#121517] text-base font-medium pb-2">Full Name</label>
          <input type="text" name="user_name" placeholder="Enter your full name" required
            class="form-input w-full rounded-xl bg-[#f1f2f4] border-none h-14 placeholder:text-[#687782] p-4 text-base" />
        </div>

        <!-- Email Field -->
        <div class="px-2">
          <label class="block text-[#121517] text-base font-medium pb-2">Email Address</label>
          <input type="email" name="email" placeholder="Enter your email" required
            class="form-input w-full rounded-xl bg-[#f1f2f4] border-none h-14 placeholder:text-[#687782] p-4 text-base" />
        </div>

        <!-- Password Field -->
        <div class="px-2">
          <label class="block text-[#121517] text-base font-medium pb-2">Password</label>
          <input type="password" name="password" placeholder="Enter your password" required
            class="form-input w-full rounded-xl bg-[#f1f2f4] border-none h-14 placeholder:text-[#687782] p-4 text-base" />
        </div>

        <!-- Submit Button -->
        <div class="px-2">
          <button type="submit" name="submit"
            class="w-full h-12 bg-[#4990ca] text-white rounded-full font-bold text-base hover:bg-[#357bb0] transition">
            Create Account
          </button>
        </div>

      </form>

      <!-- Login Redirect -->
      <p class="text-[#687782] text-sm text-center pt-4 px-4">
        Already have an account?
        <a href="login.php" class="underline text-blue-600 hover:text-blue-800">Log in</a>
      </p>
    </div>
  </div>
</body>

</html>
