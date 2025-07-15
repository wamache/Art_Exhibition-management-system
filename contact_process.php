<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);
    $message = htmlspecialchars($_POST["message"]);

    // Option A: Email
    $to = "your@email.com";
    $subject = "New Contact Form Submission";
    $headers = "From: $email";

    if (mail($to, $subject, $message, $headers)) {
        echo "Message sent!";
    } else {
        echo "Failed to send.";
    }

    // Option B: Store in Database
    // $conn = new mysqli('localhost', 'username', 'password', 'database');
    // $stmt = $conn->prepare("INSERT INTO contact_messages (email, message) VALUES (?, ?)");
    // $stmt->bind_param("ss", $email, $message);
    // $stmt->execute();
    // echo "Message stored!";
}
?>
