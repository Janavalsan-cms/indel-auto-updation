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

// BRAND
$allowed_brands = [
    "Kerala Volvo",
    "Kairali Toyota",
    "Kairali Ford",
    "Indel Honda",
    "Indel Suzuki",
    "Indel Yamaha",
    "Indel Mobility – Ashok Leyland (Service Only)",
    "Indel Wheels – River",
    "Motory"
];
$brand = clean_input($_POST["brand"] ?? '');
if ($brand == "") {
    $errorMSG .= "Please select a Brand. ";
} elseif (!in_array($brand, $allowed_brands)) {
    $errorMSG .= "Invalid brand selected. ";
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

    // DATE & TIME
    $datetime = date("d M Y, h:i A");

    //  SEND EMAIL
    $EmailTo = "info@indelauto.com";
    $subject = "New Contact Enquiry – Indel Automotives";

    // HTML EMAIL BODY
    $Body = '
    <!DOCTYPE html>
    <html lang="en">
    <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>New Contact Enquiry</title>
    </head>
    <body style="margin:0; padding:0; background-color:#f4f4f4; font-family: Arial, sans-serif;">

      <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#f4f4f4; padding: 30px 0;">
        <tr>
          <td align="center">
            <table width="620" cellpadding="0" cellspacing="0" border="0" style="background-color:#ffffff; border-radius:8px; overflow:hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">

              <!-- HEADER -->
              <tr>
                <td style="background-color:#17479E; padding: 24px 40px;">
                  <table width="100%" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                      <td>
                        <h1 style="margin:0; color:#ffffff; font-size:22px; font-weight:700; letter-spacing:1px;">INDEL</h1>
                        <p style="margin:2px 0 0; color:#aac4f0; font-size:11px; letter-spacing:2px; text-transform:uppercase;">AUTOMOTIVES</p>
                      </td>
                      <td align="right">
                        <p style="margin:0; color:#aac4f0; font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:1px;">Contact Enquiry</p>
                        <p style="margin:4px 0 0; color:#ffffff; font-size:12px;">'. $datetime .'</p>
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>

              <!-- RED ACCENT LINE -->
              <tr>
                <td style="background-color:#EE3824; height:4px; font-size:0; line-height:0;">&nbsp;</td>
              </tr>

              <!-- BODY -->
              <tr>
                <td style="padding: 32px 40px;">

                  <p style="margin:0 0 24px; color:#555555; font-size:14px; line-height:1.6;">
                    A new enquiry has been submitted via the Indel Automotives website. Please find the details below.
                  </p>

                  <!-- DETAILS TABLE -->
                  <table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-radius:6px; overflow:hidden; border: 1px solid #e8e8e8;">

                    <tr>
                      <td style="background-color:#f9f9f9; padding:14px 20px; width:35%; border-bottom:1px solid #e8e8e8;">
                        <span style="color:#888888; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.5px;">Full Name</span>
                      </td>
                      <td style="background-color:#ffffff; padding:14px 20px; border-bottom:1px solid #e8e8e8;">
                        <span style="color:#1a1a1a; font-size:14px; font-weight:600;">'. $fname .' '. $lname .'</span>
                      </td>
                    </tr>

                    <tr>
                      <td style="background-color:#f9f9f9; padding:14px 20px; border-bottom:1px solid #e8e8e8;">
                        <span style="color:#888888; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.5px;">Email</span>
                      </td>
                      <td style="background-color:#ffffff; padding:14px 20px; border-bottom:1px solid #e8e8e8;">
                        <a href="mailto:'. $email .'" style="color:#17479E; font-size:14px; text-decoration:none;">'. $email .'</a>
                      </td>
                    </tr>

                    <tr>
                      <td style="background-color:#f9f9f9; padding:14px 20px; border-bottom:1px solid #e8e8e8;">
                        <span style="color:#888888; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.5px;">Phone</span>
                      </td>
                      <td style="background-color:#ffffff; padding:14px 20px; border-bottom:1px solid #e8e8e8;">
                        <a href="tel:'. $phone .'" style="color:#17479E; font-size:14px; text-decoration:none;">'. $phone .'</a>
                      </td>
                    </tr>

                    <tr>
                      <td style="background-color:#f9f9f9; padding:14px 20px; border-bottom:1px solid #e8e8e8;">
                        <span style="color:#888888; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.5px;">Brand</span>
                      </td>
                      <td style="background-color:#ffffff; padding:14px 20px; border-bottom:1px solid #e8e8e8;">
                        <span style="color:#1a1a1a; font-size:14px;">'. $brand .'</span>
                      </td>
                    </tr>

                    <tr>
                      <td style="background-color:#f9f9f9; padding:14px 20px; vertical-align:top;">
                        <span style="color:#888888; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.5px;">Message</span>
                      </td>
                      <td style="background-color:#ffffff; padding:14px 20px;">
                        <p style="margin:0; color:#333333; font-size:14px; line-height:1.7;">'. nl2br($message) .'</p>
                      </td>
                    </tr>

                  </table>

                </td>
              </tr>

              <!-- FOOTER -->
              <tr>
                <td style="background-color:#f9f9f9; padding:18px 40px; text-align:center; border-top:1px solid #e8e8e8;">
                  <p style="margin:0 0 4px; color:#aaaaaa; font-size:12px;">This is an automated message from Indel Automotives website.</p>
                  <p style="margin:0;">
                    <a href="https://indelauto.com" style="color:#17479E; font-size:12px; text-decoration:none;">indelauto.com</a>
                  </p>
                </td>
              </tr>

            </table>
          </td>
        </tr>
      </table>

    </body>
    </html>
    ';

    // EMAIL HEADERS FOR HTML
    $headers  = "From: Indel Automotives <noreply@indelauto.com>\r\n";
    $headers .= "Reply-To: $email\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

    if(mail($EmailTo, $subject, $Body, $headers)){
        echo "success";
    } else {
        echo "Something went wrong. Please try again.";
    }

} else {
    echo $errorMSG;
}

?>