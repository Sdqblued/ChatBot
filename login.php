<?php
session_start();
require 'db.php';

$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT full_name, password FROM users WHERE email = ? AND is_verified = 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($name, $hash);

    if ($stmt->num_rows > 0 && $stmt->fetch()) {
        if (password_verify($password, $hash)) {
            $_SESSION["user"] = $name;
            header("Location: index.php");
            exit();
        } else {
            $message = "Incorrect password.";
        }
    } else {
        $message = "Invalid credentials or email not verified.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login - Health Chatbot</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container py-5">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card shadow">
          <div class="card-body">
            <h3 class="text-center mb-4">Login</h3>

            <?php if (!empty($message)): ?>
              <div class="alert alert-danger text-center"><?= $message ?></div>
            <?php endif; ?>

            <form method="POST" action="">
              <div class="mb-3">
                <label for="email" class="form-label">Email address</label>
                <input type="email" class="form-control" name="email" id="email" required placeholder="Enter your registered email">
              </div>

              <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" name="password" id="password" required placeholder="Enter your password">
              </div>

              <div class="d-grid">
                <button type="submit" class="btn btn-primary">Login</button>
              </div>
            </form>

            <div class="mt-3 text-center">
              Don't have an account? <a href="register.php">Register here</a><br>
              <a href="forgot-password.php">Forgot Password?</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
