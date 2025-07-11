<?php
session_start();
require 'db.php';

$message = '';
if (!isset($_SESSION['verify_email'])) {
    $message = "Session expired. Please register again.";
} elseif ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_SESSION['verify_email'];
    $code = $_POST['verification_code'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND verification_code = ?");
    $stmt->bind_param("ss", $email, $code);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $update = $conn->prepare("UPDATE users SET is_verified = 1, verification_code = '' WHERE email = ?");
        $update->bind_param("s", $email);
        $update->execute();
        $message = "Email verified successfully! <a href='login.php'>Login</a>";
        unset($_SESSION['verify_email']);
    } else {
        $message = "Invalid verification code.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Verify Email</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="card shadow">
        <div class="card-body">
          <h4 class="mb-3 text-center">Enter Verification Code</h4>
          <?php if ($message): ?>
            <div class="alert alert-info"><?= $message ?></div>
          <?php endif; ?>
          <?php if (isset($_SESSION['verify_email'])): ?>
          <form method="POST">
            <div class="mb-3">
              <label class="form-label">Verification Code</label>
              <input type="text" name="verification_code" class="form-control" required>
            </div>
            <div class="mt-3 text-center">
  Didn't get the code? <a href="resend_verification.php">Resend verification email</a>
</div>

            <button type="submit" class="btn btn-success w-100">Verify</button>
          </form>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>
</body>
</html>
