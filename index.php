<?php
session_start();
if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Health Chatbot</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container py-5">
    <h2 class="text-center mb-4">Welcome, <?php echo $_SESSION['user']; ?> | <a href="logout.php">Logout</a></h2>
    <div class="card">
      <div class="card-body" id="chat-box" style="height: 300px; overflow-y: scroll;">
        <div class="text-muted">Chat started...</div>
      </div>
      <form id="chat-form" class="mt-3 d-flex">
        <input type="text" id="user-input" class="form-control me-2" placeholder="Type your message..." required>
        <button type="submit" class="btn btn-primary">Send</button>
      </form>
    </div>
  </div>
  <script>
    document.getElementById("chat-form").addEventListener("submit", function(event) {
      event.preventDefault();
      const input = document.getElementById("user-input");
      const userText = input.value;
      input.value = "";
      const chatBox = document.getElementById("chat-box");
      chatBox.innerHTML += `<div><strong>You:</strong> ${userText}</div>`;
      fetch("chat.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "message=" + encodeURIComponent(userText)
      })
      .then(response => response.text())
      .then(data => {
        chatBox.innerHTML += `<div><strong>Bot:</strong> ${data}</div>`;
        chatBox.scrollTop = chatBox.scrollHeight;
      });
    });
  </script>
</body>
</html>
