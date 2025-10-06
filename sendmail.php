<?php include 'header.php'; ?>
<hr>

<style>
    .site-content {
        max-width: 800px;
        margin: 20px auto;
        padding: 0 20px;
        text-align: center;
    }
    .site-content p {
        font-size: 18px;
        line-height: 1.8;
        margin-bottom: 20px;
        color: #333;
    }
    .site-footer {
        max-width: 800px;
        margin: 20px auto;
        padding: 20px 0;
        text-align: center;
        color: #666;
        font-size: 14px;
    }
    a.back-link {
        display: inline-block;
        margin-top: 15px;
        padding: 8px 15px;
        background-color: #000;
        color: #fff;
        text-decoration: none;
        border-radius: 5px;
        transition: background-color 0.3s;
    }
    a.back-link:hover { background-color: #444; }
</style>

<div class="site-content">
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars(trim($_POST['name'] ?? ''));
    $email = htmlspecialchars(trim($_POST['email'] ?? ''));
    $message = htmlspecialchars(trim($_POST['message'] ?? ''));

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<p>Invalid email address.</p>";
        echo '<a href="contact.php" class="back-link">Go back to Contact Page</a>';
        exit;
    }

    $to = "ashary@yahoo.com"; // <-- replace with your email
    $subject = "Contact Form Message from $name";
    $body = "Name: $name\nEmail: $email\n\nMessage:\n$message";
    $headers = "From: $email\r\nReply-To: $email";

    if (mail($to, $subject, $body, $headers)) {
        echo "<p>Thank you, <strong>$name</strong>! Your message has been sent successfully.</p>";
        echo '<a href="contact.php" class="back-link">Back to Contact Page</a>';
    } else {
        echo "<p>Sorry, there was an error sending your message. Please try again later.</p>";
        echo '<a href="contact.php" class="back-link">Back to Contact Page</a>';
    }
} else {
    header("Location: contact.php");
    exit;
}
?>
</div>

<footer class="site-footer">
    <p>© 2025 Maranaw Tafsir. All rights reserved.</p>
</footer>
