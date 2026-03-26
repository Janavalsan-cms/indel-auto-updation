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
    $EmailTo = "janavalsan@mindstory.in";
    // $EmailTo = "info@indelauto.com";
    $subject = "New Contact Inquiry - Indel Automotives";

    // HTML EMAIL BODY
    $Body = '
    <!DOCTYPE html>
    <html lang="en">
    <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>New Contact Inquiry</title>
    </head>
    <body style="margin:0; padding:0; background-color:#f4f4f4; font-family: Arial, sans-serif;">

      <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#f4f4f4; padding: 30px 0;">
        <tr>
          <td align="center">
            <table width="620" cellpadding="0" cellspacing="0" border="0" style="background-color:#ffffff; border-radius:8px; overflow:hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">

              <!-- HEADER -->
              <tr>
                <td style="background-color:#1a1a2e; padding: 30px 40px; text-align:center;">
                  <h1 style="margin:0; color:#ffffff; font-size:22px; font-weight:700; letter-spacing:1px;">INDEL AUTOMOTIVES</h1>
                  <p style="margin:6px 0 0; color:#aaaacc; font-size:13px; letter-spacing:2px; text-transform:uppercase;">Contact Form Inquiry</p>
                </td>
              </tr>

              <!-- ALERT BANNER -->
              <tr>
                <td style="background-color:#c8a951; padding: 12px 40px; text-align:center;">
                  <p style="margin:0; color:#1a1a2e; font-size:13px; font-weight:600; letter-spacing:0.5px;">
                    &#128276; You have received a new message from your website
                  </p>
                </td>
              </tr>

              <!-- BODY -->
              <tr>
                <td style="padding: 36px 40px;">

                  <p style="margin:0 0 24px; color:#555555; font-size:14px; line-height:1.6;">
                    Hello Team, a new inquiry has been submitted through the Indel Automotives website contact form. Details are below:
                  </p>

                  <!-- DETAILS TABLE -->
                  <table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-radius:6px; overflow:hidden; border: 1px solid #e8e8e8;">

                    <tr>
                      <td style="background-color:#f9f9f9; padding:14px 20px; width:35%; border-bottom:1px solid #e8e8e8;">
                        <span style="color:#888888; font-size:12px; font-weight:600; text-transform:uppercase; letter-spacing:0.5px;">Full Name</span>
                      </td>
                      <td style="background-color:#ffffff; padding:14px 20px; border-bottom:1px solid #e8e8e8;">
                        <span style="color:#1a1a2e; font-size:14px; font-weight:600;">'. $fname .' '. $lname .'</span>
                      </td>
                    </tr>

                    <tr>
                      <td style="background-color:#f9f9f9; padding:14px 20px; border-bottom:1px solid #e8e8e8;">
                        <span style="color:#888888; font-size:12px; font-weight:600; text-transform:uppercase; letter-spacing:0.5px;">Email</span>
                      </td>
                      <td style="background-color:#ffffff; padding:14px 20px; border-bottom:1px solid #e8e8e8;">
                        <a href="mailto:'. $email .'" style="color:#c8a951; font-size:14px; text-decoration:none;">'. $email .'</a>
                      </td>
                    </tr>

                    <tr>
                      <td style="background-color:#f9f9f9; padding:14px 20px; border-bottom:1px solid #e8e8e8;">
                        <span style="color:#888888; font-size:12px; font-weight:600; text-transform:uppercase; letter-spacing:0.5px;">Phone</span>
                      </td>
                      <td style="background-color:#ffffff; padding:14px 20px; border-bottom:1px solid #e8e8e8;">
                        <a href="tel:'. $phone .'" style="color:#c8a951; font-size:14px; text-decoration:none;">'. $phone .'</a>
                      </td>
                    </tr>

                    <tr>
                      <td style="background-color:#f9f9f9; padding:14px 20px; border-bottom:1px solid #e8e8e8;">
                        <span style="color:#888888; font-size:12px; font-weight:600; text-transform:uppercase; letter-spacing:0.5px;">Brand</span>
                      </td>
                      <td style="background-color:#ffffff; padding:14px 20px; border-bottom:1px solid #e8e8e8;">
                        <span style="display:inline-block; background-color:#1a1a2e; color:#c8a951; font-size:12px; font-weight:700; padding:4px 12px; border-radius:20px; letter-spacing:0.5px;">'. $brand .'</span>
                      </td>
                    </tr>

                    <tr>
                      <td style="background-color:#f9f9f9; padding:14px 20px; vertical-align:top;">
                        <span style="color:#888888; font-size:12px; font-weight:600; text-transform:uppercase; letter-spacing:0.5px;">Message</span>
                      </td>
                      <td style="background-color:#ffffff; padding:14px 20px;">
                        <p style="margin:0; color:#333333; font-size:14px; line-height:1.7;">'. nl2br($message) .'</p>
                      </td>
                    </tr>

                  </table>

                  <!-- REPLY BUTTON -->
                  <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-top:28px;">
                    <tr>
                      <td align="center">
                        <a href="mailto:'. $email .'?subject=Re: Your Inquiry - Indel Automotives"
                           style="display:inline-block; background-color:#c8a951; color:#1a1a2e; font-size:14px; font-weight:700; padding:13px 32px; border-radius:4px; text-decoration:none; letter-spacing:0.5px;">
                          &#9993;&nbsp; Reply to '. $fname .'
                        </a>
                      </td>
                    </tr>
                  </table>

                </td>
              </tr>

              <!-- FOOTER -->
              <tr>
                <td style="background-color:#f4f4f4; padding:20px 40px; text-align:center; border-top:1px solid #e8e8e8;">
                  <p style="margin:0 0 4px; color:#aaaaaa; font-size:12px;">This email was generated automatically from the Indel Automotives website.</p>
                  <p style="margin:0; color:#bbbbbb; font-size:11px;">Received on '. $datetime .' &nbsp;|&nbsp; <a href="https://indelauto.com" style="color:#c8a951; text-decoration:none;">indelauto.com</a></p>
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