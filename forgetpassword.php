<?php
include 'config.php';

session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['email'])) {
    $email = $_POST['email'];

    $pincode = rand(1001,9999);

    $_SESSION['pincode'] = $pincode;
    $_SESSION['forgotPasswordEmail'] = $email;

    // TODO: Validate that this email exists in your DB before proceeding

    $mail = new PHPMailer(true);
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';            // Set the SMTP server
        $mail->SMTPAuth   = true;
        $mail->Username   = 'free4talk13@gmail.com';      // Your Gmail address
        $mail->Password   = 'eykz iohz gynl qfcx';       // App password or Gmail password (use app password if 2FA is on)
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Recipients
        $mail->setFrom('itsumernadeem@gmail.com', 'Casahue');
        $mail->addAddress($email);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Password Reset Request';
        $mail->Body    = "Hi,<br><br> Your verification code is $pincode " . $email . "' </a><br><br>Kindly, ignore this email,If you didn't request.";

        $mail->send();
        echo "<script>alert('Reset password code has been sent to your email');</script>";
        echo "<script>window.location.href = 'verify-code.php';</script>";
    } catch (Exception $e) {
        echo "<script>alert('Message could not be sent. Mailer Error: {$mail->ErrorInfo}');</script>";
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
      <h2 class="text-[#121517] text-[28px] font-bold text-center pb-3 pt-5">Forgot your password?</h2>

      <p class="text-[#121517] text-base font-normal text-center px-4 pb-4">
        Enter the email address associated with your account, and we'll send you a link to reset your password.
      </p>

      <form action="<?= $_SERVER['PHP_SELF']; ?>" method="post" class="w-full">

        <!-- Email Field -->
        <div class="flex flex-wrap items-end gap-4 px-4 py-3">
          <label for="email" class="flex flex-col w-full">
            <input
              type="email"
              id="email"
              name="email"
              placeholder="Email address"
              required
              class="form-input w-full rounded-xl bg-[#f1f2f4] border-none h-14 placeholder:text-[#687782] p-4 text-base focus:outline-0 focus:ring-0"
            />
          </label>
        </div>

        <!-- Submit Button -->
        <div class="flex px-4 py-3 justify-center">
          <button
            type="submit"
            class="w-full max-w-[480px] rounded-full h-10 px-4 bg-[#4e92ca] text-white text-sm font-bold tracking-[0.015em] hover:bg-[#417cb0] transition">
            Continue
          </button>
        </div>
      </form>

      <!-- Back to login -->
      <p class="text-[#687782] text-sm text-center px-4 pt-3">
        Remember your password?
        <a href="login.php" class="text-blue-600 hover:underline">Back to login</a>
      </p>
    </div>
  </div>

</body>

</html>
