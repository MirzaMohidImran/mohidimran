<?php
// Process contact form submissions.
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    header("Content-Type: application/json");
    echo json_encode(["success" => false, "message" => "Please submit the form using POST."]);
    exit;
}
header("Content-Type: application/json");
$name = trim($_POST["name"] ?? "");
$email = filter_var(trim($_POST["email"] ?? ""), FILTER_SANITIZE_EMAIL);
$phone = trim($_POST["phone"] ?? ($_POST["number"] ?? ""));
$subject = trim($_POST["subject"] ?? "New website inquiry from mohidimran.com");
$message = trim($_POST["message"] ?? "");
$errors = [];
if ($name === "") { $errors[] = "Name is required."; }
if ($email === "" || !filter_var($email, FILTER_VALIDATE_EMAIL)) { $errors[] = "A valid email address is required."; }
if ($message === "") { $errors[] = "Message is required."; }
if (!empty($errors)) {
    http_response_code(422);
    echo json_encode(["success" => false, "message" => implode(" ", $errors)]);
    exit;
}
$name = str_replace(["\r", "\n"], " ", strip_tags($name));
$phone = str_replace(["\r", "\n"], " ", strip_tags($phone));
$subject = str_replace(["\r", "\n"], " ", strip_tags($subject));
$message = strip_tags($message);
$recipient = "mirzamohidimran@gmail.com";
$email_subject = "Website inquiry: " . $subject;
$email_body = "Name: {$name}\n";
$email_body .= "Email: {$email}\n";
if ($phone !== "") { $email_body .= "Phone/Number: {$phone}\n"; }
if ($subject !== "") { $email_body .= "Subject: {$subject}\n"; }
$email_body .= "Message:\n{$message}\n";
$email_headers = "From: Mohid Imran Website <no-reply@mohidimran.com>\r\n";
$email_headers .= "Reply-To: {$name} <{$email}>\r\n";
$email_headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
if (mail($recipient, $email_subject, $email_body, $email_headers)) {
    http_response_code(200);
    echo json_encode(["success" => true, "message" => "Thank you. Your message has been sent."]);
} else {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Sorry, your message could not be sent. Please email mirzamohidimran@gmail.com directly."]);
}
?>
