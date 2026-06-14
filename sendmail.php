<?php
// Secure sendmail.php for Daniel Jesusegun's Portfolio

// 1. Spam Honeypot Protection: If the hidden honeypot field is filled, reject the submission
if (!empty($_POST['website_trap'])) {
    http_response_code(400);
    echo "Spam detected.";
    exit;
}

// 2. Validate inputs exist
if (empty($_POST['name']) || empty($_POST['email']) || empty($_POST['subject']) || empty($_POST['message'])) {
    http_response_code(400);
    echo "Please fill in all fields.";
    exit;
}

// 3. Retrieve and sanitize input fields (prevent XSS / HTML Injection in email clients)
$name = htmlspecialchars(strip_tags(trim($_POST['name'])), ENT_QUOTES, 'UTF-8');
$email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
$subject = htmlspecialchars(strip_tags(trim($_POST['subject'])), ENT_QUOTES, 'UTF-8');
$message = htmlspecialchars(strip_tags(trim($_POST['message'])), ENT_QUOTES, 'UTF-8');

// 4. Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo "Invalid email format.";
    exit;
}

// 5. Avoid Email Header Injection / Mail Hijacking
$pattern = "/(content-type|bcc:|cc:|to:)/i";
if (preg_match($pattern, $name) || preg_match($pattern, $email) || preg_match($pattern, $message)) {
    http_response_code(400);
    echo "Injection detected.";
    exit;
}

// 6. Email Configuration
$to = "danieljesusegun@gmail.com";
$mailSubject = "Portfolio Contact: " . $subject;

// HTML Body
$body = "
<html>
<head>
    <title>" . $subject . "</title>
</head>
<body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
    <h2>New Portfolio Message</h2>
    <p><strong>Name:</strong> " . $name . "</p>
    <p><strong>Email:</strong> <a href='mailto:" . $email . "'>" . $email . "</a></p>
    <p><strong>Subject:</strong> " . $subject . "</p>
    <hr style='border: 0; border-top: 1px solid #eee;' />
    <p><strong>Message:</strong></p>
    <p style='background-color: #f9f9f9; padding: 15px; border-left: 4px solid #00e5ff; white-space: pre-wrap;'>" . nl2br($message) . "</p>
</body>
</html>
";

// 7. Secure Headers (using Daniel's domain to ensure inbox delivery, and setting Reply-To so he can click reply)
$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type: text/html; charset=UTF-8" . "\r\n";
$headers .= "From: Daniel Portfolio <no-reply@reem.media>" . "\r\n";
$headers .= "Reply-To: " . $name . " <" . $email . ">" . "\r\n";
$headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";

// PHP email sender
if (mail($to, $mailSubject, $body, $headers)) {
    echo "success";
} else {
    http_response_code(500);
    echo "Mailer failed.";
}
?>