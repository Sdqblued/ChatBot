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
    $name = $_POST['full_name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $verification_code = md5(rand());

    // Check if email already exists
    $check_stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check_stmt->bind_param("s", $email);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        $message = "Email already registered. Please use another or login.";
    } else {
        // Insert new user
        $stmt = $conn->prepare("INSERT INTO users (full_name, email, password, verification_code) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $password, $verification_code);

        if ($stmt->execute()) {
            $_SESSION['verify_email'] = $email;

            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'salisubalarabea@gmail.com'; // Your email
                $mail->Password   = 'tkcfngrmwtqerhjh';          // Your App Password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;

                $mail->setFrom('salisubalarabea@gmail.com', 'Health Chatbot');
                $mail->addAddress($email, $name);
                $mail->isHTML(true);
                $mail->Subject = 'Verify Email';
                $mail->Body = "Your verification code is: <strong>$verification_code</strong><br>Please enter this code at: <a href='http://localhost/verify.php'>http://localhost/verify.php</a>";
                $mail->send();

                // Redirect to verification page
                header("Location: verify.php");
                exit();
            } catch (Exception $e) {
                $message = "Mail error: {$mail->ErrorInfo}";
            }
        } else {
            $message = "Error: " . $stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Register - Health Chatbot</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="card shadow">
        <div class="card-body">
          <h3 class="text-center mb-4">Register</h3>

          <?php if ($message): ?>
            <div class="alert alert-info"><?= $message ?></div>
          <?php endif; ?>

          <form method="POST" action="">
            <div class="mb-3">
              <label for="full_name" class="form-label">Full Name</label>
              <input type="text" class="form-control" placeholder="Full Name" id="full_name" name="full_name" required>
            </div>

            <div class="mb-3">
              <label for="email" class="form-label">Email address</label>
              <input type="email" placeholder="email@gmail.com" class="form-control" id="email" name="email" required>
            </div>

            <div class="mb-3">
              <label for="password" class="form-label">Password</label>
              <input type="password" class="form-control" placeholder="Password" id="password" name="password" required>
            </div>

            <button type="submit" class="btn btn-primary w-100">Register</button>
          </form>

          <div class="mt-3 text-center">
            Already have an account? <a href="login.php">Login</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

</body>
</html>
