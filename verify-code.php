<?php
include 'config.php';
session_start();

if (isset($_SESSION['email'])) {
  header("Location: index.php");
  exit();
}

if (!isset($_SESSION['pincode'])) {
  header('Location: forgetpassword.php');
  exit();
}

if (isset($_POST['verifyBtn'])) {
  $verifyCode = $_POST['verifyCode'];

  if ($verifyCode == $_SESSION['pincode']) {
    unset($_SESSION['pincode']);
    header("Location: newpassword.php");
    exit();
  } else {
    echo "<script>alert('Invalid verification code. Please try again.');</script>";
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
      <h2 class="text-[#121517] text-[28px] font-bold text-center pb-3 pt-5">Reset Password Code</h2>

      <p class="text-[#121517] text-base text-center px-4 pb-4">
        We've sent a code to your email address. Please enter it below to reset your password.
      </p>

      <form method="POST" action="" class="w-full">
        <!-- OTP Input -->
        <div class="flex flex-wrap items-end gap-4 px-4 py-3">
          <label class="flex flex-col w-full">
            <input
              type="text"
              name="verifyCode"
              placeholder="Enter code"
              required
              class="form-input w-full rounded-xl border border-[#dde1e4] bg-white h-14 placeholder:text-[#687782] p-4 text-base focus:outline-0 focus:ring-0"
            />
          </label>
        </div>

        <!-- Submit Button -->
        <div class="flex px-4 py-3 justify-center">
          <button
            type="submit"
            name="verifyBtn"
            class="w-full max-w-[480px] rounded-full h-10 px-4 bg-[#4e92ca] text-white text-sm font-bold tracking-[0.015em] hover:bg-[#417cb0] transition">
            Submit Code
          </button>
        </div>
      </form>

      <!-- Resend -->
      <p class="text-[#687782] text-sm text-center px-4 pt-3 underline cursor-pointer">Didn't receive the code? Resend</p>
    </div>
  </div>

</body>

</html>
