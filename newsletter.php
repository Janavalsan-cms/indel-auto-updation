<?php

if(isset($_POST['mail'])){

    $email = filter_var(trim($_POST['mail']), FILTER_SANITIZE_EMAIL);

    if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        echo "Invalid email address";
        exit;
    }

    $to = "info@indelauto.com";
    $subject = "New Newsletter Subscriber from Indel Automotives Website";

    $message = "New subscriber:\n\nEmail: $email";

    $headers = "From: IndelAutomotive wbsite <noreply@indelauto.com>\r\n";
    $headers .= "Reply-To: noreply@indelauto.com\r\n";

    if(mail($to, $subject, $message, $headers)){
        echo "success";
    }else{
        echo "Something went wrong.";
    }

}
?>
