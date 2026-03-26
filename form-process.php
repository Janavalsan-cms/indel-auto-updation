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
if ($fname == "") { $errorMSG .= "First Name is required. "; }

// LAST NAME
$lname = clean_input($_POST["lname"] ?? '');
if ($lname == "") { $errorMSG .= "Last Name is required. "; }

// EMAIL
$email = clean_input($_POST["email"] ?? '');
if ($email == "") {
    $errorMSG .= "Email is required. ";
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errorMSG .= "Enter a valid email address. ";
}

// PHONE
$phone = clean_input($_POST["phone"] ?? '');
if ($phone == "") { $errorMSG .= "Phone is required. "; }

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
if ($message == "") { $errorMSG .= "Message is required. "; }


if ($errorMSG == "") {

    // GOOGLE RECAPTCHA
    $secretKey = "6Leff2IsAAAAALKcRL0RCmjYpbAJv7v15h1gs-5p";

    if(empty($_POST['g-recaptcha-response'])){
        die("Please complete the CAPTCHA.");
    }

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
        'secret'   => $secretKey,
        'response' => $_POST['g-recaptcha-response']
    ]));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response     = curl_exec($ch);
    curl_close($ch);
    $responseData = json_decode($response);

    if(!$responseData->success){
        die("Captcha verification failed. Try again.");
    }

    $datetime = date("d M Y, h:i A");

    $EmailTo = "janavalsan@mindstory.in";
    // $EmailTo = "info@indelauto.com";
    $subject  = "New Contact Inquiry - Indel Automotives";

    $Body = '
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>New Contact Inquiry</title>
</head>
<body style="margin:0; padding:0; background-color:#f0f0f0; font-family:Arial, sans-serif;">

  <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#f0f0f0; padding:30px 0;">
    <tr>
      <td align="center">
        <table width="600" cellpadding="0" cellspacing="0" border="0" style="background-color:#ffffff;">

          <!-- HEADER -->
          <tr>
            <td style="background-color:#003366; padding:28px 36px;">
              <table width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr>
                  <td>
                    <p style="margin:0; color:#ffffff; font-size:20px; font-weight:700; letter-spacing:1px;">INDEL</p>
                    <p style="margin:2px 0 0; color:#aec6e8; font-size:11px; letter-spacing:2px; text-transform:uppercase;">Automotives</p>
                  </td>
                  <td align="right">
                    <p style="margin:0; color:#aec6e8; font-size:11px; text-transform:uppercase; letter-spacing:1px;">Contact Inquiry</p>
                    <p style="margin:4px 0 0; color:#ffffff; font-size:11px;">'. $datetime .'</p>
                  </td>
                </tr>
              </table>
            </td>
          </tr>

          <!-- RED ACCENT LINE -->
          <tr>
            <td style="background-color:#e8413b; height:3px; font-size:0; line-height:0;">&nbsp;</td>
          </tr>

          <!-- INTRO -->
          <tr>
            <td style="padding:28px 36px 20px;">
              <p style="margin:0; color:#333333; font-size:14px; line-height:1.7;">
                A new inquiry has been submitted via the Indel Automotives website. Please find the details below.
              </p>
            </td>
          </tr>

          <!-- DETAILS TABLE -->
          <tr>
            <td style="padding:0 36px 28px;">
              <table width="100%" cellpadding="0" cellspacing="0" border="0" style="border:1px solid #e0e0e0;">

                <tr>
                  <td style="background-color:#f5f8fc; padding:13px 18px; width:32%; border-bottom:1px solid #e0e0e0;">
                    <p style="margin:0; color:#666666; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.5px;">Full Name</p>
                  </td>
                  <td style="background-color:#ffffff; padding:13px 18px; border-bottom:1px solid #e0e0e0;">
                    <p style="margin:0; color:#111111; font-size:14px;">'. $fname .' '. $lname .'</p>
                  </td>
                </tr>

                <tr>
                  <td style="background-color:#f5f8fc; padding:13px 18px; border-bottom:1px solid #e0e0e0;">
                    <p style="margin:0; color:#666666; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.5px;">Email</p>
                  </td>
                  <td style="background-color:#ffffff; padding:13px 18px; border-bottom:1px solid #e0e0e0;">
                    <a href="mailto:'. $email .'" style="color:#003366; font-size:14px; text-decoration:none;">'. $email .'</a>
                  </td>
                </tr>

                <tr>
                  <td style="background-color:#f5f8fc; padding:13px 18px; border-bottom:1px solid #e0e0e0;">
                    <p style="margin:0; color:#666666; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.5px;">Phone</p>
                  </td>
                  <td style="background-color:#ffffff; padding:13px 18px; border-bottom:1px solid #e0e0e0;">
                    <a href="tel:'. $phone .'" style="color:#003366; font-size:14px; text-decoration:none;">'. $phone .'</a>
                  </td>
                </tr>

                <tr>
                  <td style="background-color:#f5f8fc; padding:13px 18px; border-bottom:1px solid #e0e0e0;">
                    <p style="margin:0; color:#666666; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.5px;">Brand</p>
                  </td>
                  <td style="background-color:#ffffff; padding:13px 18px; border-bottom:1px solid #e0e0e0;">
                    <span style="background-color:#003366; color:#ffffff; font-size:12px; font-weight:700; padding:4px 14px; letter-spacing:0.5px;">'. $brand .'</span>
                  </td>
                </tr>

                <tr>
                  <td style="background-color:#f5f8fc; padding:13px 18px; vertical-align:top;">
                    <p style="margin:0; color:#666666; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.5px;">Message</p>
                  </td>
                  <td style="background-color:#ffffff; padding:13px 18px;">
                    <p style="margin:0; color:#333333; font-size:14px; line-height:1.7;">'. nl2br($message) .'</p>
                  </td>
                </tr>

              </table>
            </td>
          </tr>

          <!-- REPLY BUTTON -->
          <tr>
            <td style="padding:0 36px 32px;">
              <a href="mailto:'. $email .'?subject=Re: Your Inquiry - Indel Automotives"
                 style="display:inline-block; background-color:#e8413b; color:#ffffff; font-size:13px; font-weight:700; padding:12px 28px; text-decoration:none; letter-spacing:0.5px;">
                Reply to '. $fname .'
              </a>
            </td>
          </tr>

          <!-- DIVIDER -->
          <tr>
            <td style="background-color:#e0e0e0; height:1px; font-size:0; line-height:0;">&nbsp;</td>
          </tr>

          <!-- FOOTER -->
          <tr>
            <td style="background-color:#f5f8fc; padding:18px 36px;">
              <table width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr>
                  <td>
                    <p style="margin:0; color:#999999; font-size:11px;">This is an automated message from Indel Automotives website.</p>
                  </td>
                  <td align="right">
                    <a href="https://indelauto.com" style="color:#003366; font-size:11px; text-decoration:none;">indelauto.com</a>
                  </td>
                </tr>
              </table>
            </td>
          </tr>

        </table>
      </td>
    </tr>
  </table>

</body>
</html>
    ';

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