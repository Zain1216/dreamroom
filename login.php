<?php
session_start();
include "config.php";
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Casahue</title>
  <link rel="icon" type="image/x-icon" href="data:image/x-icon;base64,">
  <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin />
  <link rel="stylesheet" as="style" onload="this.rel='stylesheet'" href="https://fonts.googleapis.com/css2?display=swap&amp;family=Manrope%3Awght%40400%3B500%3B700%3B800&amp;family=Noto+Sans%3Awght%40400%3B500%3B700%3B900" />
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
</head>

<body>

  <div class="px-40 flex flex-1 justify-center py-5">
    <div class="layout-content-container flex flex-col w-full max-w-[512px] py-5">
      <h2 class="text-[#121517] text-[28px] font-bold leading-tight text-center px-4 pb-3 pt-5">Welcome back</h2>

      <form action="<?= $_SERVER['PHP_SELF']; ?>" method="POST" class="w-full">

        <!-- Username Field -->
        <div class="flex flex-wrap items-end gap-4 px-4 py-3">
          <label class="flex flex-col min-w-40 flex-1">
            <p class="text-[#121517] text-base font-medium pb-2">Email</p>
            <input type="email" name="email" placeholder="Enter your email" required
              class="form-input w-full rounded-xl text-[#121517] border border-[#dde1e4] bg-white h-14 placeholder:text-[#687782] p-[15px] text-base font-normal focus:outline-0 focus:ring-0 focus:border-[#dde1e4]" />
          </label>
        </div>

        <!-- Password Field -->
        <div class="flex flex-wrap items-end gap-4 px-4 py-3">
          <label class="flex flex-col min-w-40 flex-1">
            <p class="text-[#121517] text-base font-medium pb-2">Password</p>
            <input type="password" name="password" placeholder="Enter your password" required
              class="form-input w-full rounded-xl text-[#121517] border border-[#dde1e4] bg-white h-14 placeholder:text-[#687782] p-[15px] text-base font-normal focus:outline-0 focus:ring-0 focus:border-[#dde1e4]" />
          </label>
        </div>



        <!-- Login Button -->
        <div class="flex px-4 py-3">
          <button type="submit"
          name="login"
            class="flex w-full justify-center items-center rounded-xl h-10 px-4 bg-[#4990ca] text-white text-sm font-bold tracking-[0.015em]">
            <span class="truncate">Login</span>
          </button>
        </div>

      </form>

      <!-- Sign Up Link -->
      <div class="px-4 text-center">
        <a href="createaccount.php" class="text-[#687782] text-sm underline">Don't have an account? Sign Up</a>
      </div>
      <!-- Forgot Password -->
      <div class="px-4 text-center">
        <a href="forgetpassword.php" class="text-[#687782] text-sm underline">Forgot password?</a>
      </div>

    </div>
  </div>

   <?php

        if (isset($_POST['login'])) {
          $email = mysqli_real_escape_string($connect, $_POST['email']);
          $password = mysqli_real_escape_string($connect, md5($_POST['password']));

          $query = "SELECT * FROM users WHERE email = '{$email}' AND password = '{$password}'";
          $result = mysqli_query($connect, $query);

          if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $_SESSION["user_id"] = $row['id'];
            $_SESSION["user_name"] = $row['user_name'];
            $_SESSION["email"] = $row['email'];

            header('Location:sample.php');
            exit;
          } else {
            echo "<div>Invalid login credentials.</div>";
          }
        }

        ?>

</body>

</html>