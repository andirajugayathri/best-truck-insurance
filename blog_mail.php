<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars(trim($_POST["name"]));
    $email = htmlspecialchars(trim($_POST["email"]));
    $website = htmlspecialchars(trim($_POST["website"]));
    $comment = htmlspecialchars(trim($_POST["comment"]));

    $to = "madhkunchaka@gmail.com"; // 👉 Replace this with your actual email
    $subject = "New Contact Form Submission";
    $message = "
    Name: $name\n
    Email: $email\n
    Website: $website\n
    Comment:\n$comment
    ";
    $headers = "MIME-Version: 1.0\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8\r\n";
            $headers .= "From: mail@besttruckinsurance.com.au\r\n";
            $headers .= "Reply-To: $userEmail\r\n";

    if (mail($to, $subject, $message, $headers)) {
        // Redirect to thank you page
        header("Location: https://besttruckinsurance.com.au/thankyou.html");
        exit();
    } else {
        echo "<script>alert('Message could not be sent. Try again later.'); history.back();</script>";
    }
} else {
    echo "Invalid request method.";
}
?>
