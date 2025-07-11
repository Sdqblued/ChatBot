<?php
session_start();
require 'db.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';
require 'PHPMailer/Exception.php';

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];

    // Check if the user exists and is not verified
    $stmt = $conn->prepare("SELECT full_name FROM users WHERE email = ? AND is_verified = 0");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($name);
        $stmt->fetch();

        // Generate new code and update
        $new_code = md5(rand());
        $update = $conn->prepare("UPDATE users SET verification_code = ? WHERE email = ?");
        $update->bind_param("ss", $new_code, $email);
        $update->execute();

        // Send email again
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'salisubalarabea@gmail.com';
            $mail->Password   = 'tkcfngrmwtqerhjh';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            $mail->setFrom('salisubalarabea@gmail.com', 'Health Chatbot');
            $mail->addAddress($email, $name);
            $mail->isHTML(true);
            $mail->Subject = 'Resend Verification Code';
            $mail->Body = "Your new verification code is: <strong>$new_code</strong><br>Enter it at <a href='http://localhost/verify.php'>http://localhost/verify.php</a>";
            $mail->send();

            $_SESSION['verify_email'] = $email;
            $message = "Verification code resent. Please check your email.";
        } catch (Exception $e) {
            $message = "Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        $message = "Email not found or already verified.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Resend Verification</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="card shadow">
        <div class="card-body">
          <h4 class="mb-3 text-center">Resend Verification Code</h4>
          <?php if ($message): ?>
            <div class="alert alert-info"><?= $message ?></div>
          <?php endif; ?>
          <form method="POST">
            <div class="mb-3">
              <label class="form-label">Email</label>
              <input type="email" name="email" class="form-control" required placeholder="Enter your registered email">
            </div>
            <button type="submit" class="btn btn-primary w-100">Resend Code</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
</body>
</html>
