<?php

//  BLOCK DIRECT ACCESS
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    die("Access denied.");
}

$errorMSG = "";

// SANITIZE FUNCTION
function clean_input($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

// FIRST NAME
$fname = clean_input($_POST["fname"] ?? '');
if ($fname == "") {
    $errorMSG .= "First Name is required. ";
}

// LAST NAME
$lname = clean_input($_POST["lname"] ?? '');
if ($lname == "") {
    $errorMSG .= "Last Name is required. ";
}

// EMAIL
$email = clean_input($_POST["email"] ?? '');
if ($email == "") {
    $errorMSG .= "Email is required. ";
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errorMSG .= "Enter a valid email address. ";
}

// PHONE
$phone = clean_input($_POST["phone"] ?? '');
if ($phone == "") {
    $errorMSG .= "Phone is required. ";
}

// MESSAGE
$message = clean_input($_POST["message"] ?? '');
if ($message == "") {
    $errorMSG .= "Message is required. ";
}


//  ONLY CONTINUE IF NO ERRORS
if ($errorMSG == "") {

    // GOOGLE RECAPTCHA
    $secretKey = "6Leff2IsAAAAALKcRL0RCmjYpbAJv7v15h1gs-5p ";

    if(empty($_POST['g-recaptcha-response'])){
        die("Please complete the CAPTCHA.");
    }

    //  cURL verification
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
        'secret' => $secretKey,
        'response' => $_POST['g-recaptcha-response']
    ]));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    curl_close($ch);

    $responseData = json_decode($response);

    if(!$responseData->success){
        die("Captcha verification failed. Try again.");
    }


    //  SEND EMAIL
    $EmailTo = "info@indelauto.com";
    $subject = "New contact inquiry - Indel Automotives Website";

    $Body  = "New contact inquiry from Indel Automotives Website\n";
    $Body .= "--------------------------------------------------\n\n";

    $Body .= "First Name : $fname\n";
    $Body .= "Last Name  : $lname\n";
    $Body .= "Email      : $email\n";
    $Body .= "Phone      : $phone\n\n";

    $Body .= "Message:\n$message\n";


    //  PROFESSIONAL HEADERS
    $headers  = "From: Indel Automotives <noreply@indelauto.com>\r\n";
    $headers .= "Reply-To: $email\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";


    if(mail($EmailTo, $subject, $Body, $headers)){
        echo "success";
    } else {
        echo "Something went wrong. Please try again.";
    }

} else {
    echo $errorMSG;
}

?>
