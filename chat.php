<?php
session_start();
header("Content-Type: text/plain");

// Check if user is logged in
if (!isset($_SESSION["user"])) {
    echo "Unauthorized access.";
    exit();
}

// Fix: Check if message is provided
if (!isset($_POST['message']) || empty(trim($_POST['message']))) {
    echo "Please enter a message.";
    exit();
}

$userMessage = trim($_POST['message']);
$botReply = "";

// Simple AI logic – you can expand or replace with OpenAI API
$lowerMsg = strtolower($userMessage);
if (strpos($lowerMsg, 'hello') !== false || strpos($lowerMsg, 'hi') !== false) {
    $botReply = "Hello! How can I assist your health concern today?";
} elseif (strpos($lowerMsg, 'fever') !== false) {
    $botReply = "For fever, drink plenty of fluids, rest, and monitor your temperature. If it persists, see a doctor.";
} elseif (strpos($lowerMsg, 'headache') !== false) {
    $botReply = "For headaches, try to rest, stay hydrated, and avoid screen time. Use paracetamol if needed.";
} elseif (strpos($lowerMsg, 'covid') !== false) {
    $botReply = "COVID-19 symptoms include fever, cough, and difficulty breathing. Get tested if symptoms appear.";
} elseif (strpos($lowerMsg, 'bye') !== false) {
    $botReply = "Goodbye! Take care and stay healthy.";
} else {
    $botReply = "I'm sorry, I didn't quite understand that. Could you please rephrase or ask about a health topic?";
}

echo $botReply;
?>
