<?php

class EmailController extends Controller
{

    public function sendMail($client)
    {
        $to = $client->email;

        $from = 'rafflespaintm@gmail.com';
        $fromName = 'rafflespainTM';

        $subject = 'Activa tu cuenta de RaffleSpain';
        $hash = Crypto::encrypt_hash($client->email);
        $encodedEmail = urlencode($hash);
        $domain = "192.168.119.18";
        $activationLink = "http://{$domain}/M12/RaffleSpainTM/RaffleSpain_MVC/?Email/validate/{$encodedEmail}";

        $htmlContent = "
        <body style='font-family: Verdana, Geneva, sans-serif; background-color: #e8e8e8; margin: 0; padding: 0;'>
            <div style='background-color: #f8f8f8; padding: 30px; border-radius: 10px; box-shadow: 0 0 15px rgba(0, 0, 0, 0.1); position: relative;'>
                <h2 style='color: #444444; margin-bottom: 15px;'>¡Saludos de RaffleSpain!</h2>
                <p style='color: #666666; line-height: 1.6; margin-bottom: 25px;'>Para activar tu cuenta, por favor sigue el enlace a continuación:</p>
                <p style='margin: 20px 0;'><a href='{$activationLink}' style='color: #008cff; text-decoration: none; font-weight: bold;'>Activar Cuenta</a></p>
                <p style='color: #666666; line-height: 1.6;'>Agradecemos tu participación.</p>
                <p style='color: #444444; line-height: 1.6; margin-top: 25px;'>Cordialmente,<br>El Equipo de Soporte</p>
            </div>
        </body>";       

        $mail = new PHPMailer\PHPMailer\PHPMailer;

        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = $from;
        $mail->Password = 'hpdwbbgulzkimkqh';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom($from, $fromName);
        $mail->addAddress($to);

        $mail->isHTML(true);

        $mail->Subject = $subject;
        $mail->Body = $htmlContent;

        if (!$mail->send()) {
            throw new Exception("Email sending failed.");
        }
    }
    
    public function sendMailContactUs($contact)
    {
//         var_dump($contact);
//         die;
        
        $to = 'rafflespaintm@gmail.com';
        $from = 'rafflespaintm@gmail.com';
        $fromName = 'rafflespainTM';
        
        $subject = 'Contact Us Soporte';
        $hash = Crypto::encrypt_hash($contact['email']);
        $encodedEmail = urlencode($hash);
        $domain = "192.168.119.18";
        // $activationLink = "http://{$domain}/M12/RaffleSpainTM/RaffleSpain_MVC/?Email/validate/{$encodedEmail}";
        // <p style='margin: 20px 0;'><a href='{$activationLink}' style='color: #008cff; text-decoration: none; font-weight: bold;'>Activar Cuenta</a></p>
        
        $htmlContent = "
        <body style='font-family: Verdana, Geneva, sans-serif; background-color: #e8e8e8; margin: 0; padding: 0;'>
            <div style='background-color: #f8f8f8; padding: 30px; border-radius: 10px; box-shadow: 0 0 15px rgba(0, 0, 0, 0.1); position: relative;'>
                <h2 style='color: #444444; margin-bottom: 15px;'>Contact Us Soporte</h2>
                <p style='color: #666666; line-height: 1.6; margin-bottom: 25px;'>Titulo" . $contact['titulo'] . "</p>
                <p style='color: #666666; line-height: 1.6; margin-bottom: 25px;'>Email" . $contact['email'] . "</p>
                <p style='color: #666666; line-height: 1.6; margin-bottom: 25px;'>Mensaje</p>
                <p style='color: #666666; line-height: 1.6; margin-bottom: 25px;'>" . $contact['mensaje'] . "</p>
            </div>
        </body>";
        
        $mail = new PHPMailer\PHPMailer\PHPMailer;
        
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = $from;
        $mail->Password = 'hpdwbbgulzkimkqh';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;
        
        $mail->setFrom($from, $fromName);
        $mail->addAddress($to);
        
        $mail->isHTML(true);
        
        $mail->Subject = $subject;
        $mail->Body = $htmlContent;
        
        if (!$mail->send()) {
            throw new Exception("Email sending failed.");
        }
    }

    public function validate($hash) {
        $implodedhash = implode("/", $hash);
        $email = Crypto::decrypt_hash($implodedhash);
        if ($email === false ) {
            throw new Exception("Invalid hash.");
        }
        $mClient = new ClientModel();
        $client = $mClient->getByEmail($email);
        $mClient->validateType($client);
        ValidateController::showCorrectValidate($email);
    }

}