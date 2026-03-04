<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $to = "madhkunchala@gmail.com"; // 🔁 Change to your email
    $subject = "New Form Submission";

    $first_name = htmlspecialchars(trim($_POST["first_name"]));
    $last_name = htmlspecialchars(trim($_POST["last_name"]));
    $email = htmlspecialchars(trim($_POST["email"]));
    $phone = htmlspecialchars(trim($_POST["phone"]));
    $message = htmlspecialchars(trim($_POST["message"]));

    $body = "You have received a new message from your website form:\n\n";
    $body .= "Name: $first_name $last_name\n";
    $body .= "Email: $email\n";
    $body .= "Phone: $phone\n";
    $body .= "Message:\n$message\n";

    $headers .= "Content-type:text/html;charset=UTF-8\r\n";
            $headers .= "From: mail@besttruckinsurance.com.au\r\n";
            $headers .= "Reply-To: $userEmail\r\n";

    // Send email and redirect
    if (mail($to, $subject, $body, $headers)) {
        header("Location: https://besttruckinsurance.com.au/thankyou.html");
        exit;
    } else {
        echo "Sorry, something went wrong. Please try again later.";
    }
} else {
    echo "Invalid request.";
}
?>
