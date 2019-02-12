<?php

function ___email($datos)
{
    if(class_exists('_PHPMailer') == false)
    {
        try {
            // Se incluye PHPMailer
            require_once __DIR__ . '/phpmailer/src/PHPMailer.php';

            // Se crea el objeto PHPMailer
            $mail = new _PHPMailer();

            // Se configura el servidor
            $mail->isSMTP();
            $mail->SMTPDebug = 0;
            $mail->Host = $datos['host'];
            $mail->Port = $datos['puerto'];

            // Se aceptan certificados auto-firmados
            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );

            //Set the encryption system to use - ssl (deprecated) or tls
            $mail->SMTPSecure = isset($datos['SMTPSecure']) ? $datos['SMTPSecure'] : 'tls';
            //Whether to use SMTP authentication
            $mail->SMTPAuth = isset($datos['SMTPAuth']) ? $datos['SMTPAuth'] : true;

            //Username to use for SMTP authentication - use full email address for gmail
            $mail->Username = $datos['usuario'];

            //Password to use for SMTP authentication
            $mail->Password = $datos['clave'];

            //Set who the message is to be sent from
            $mail->setFrom($datos['remitente']);

            if(isset($datos['receptores']))
            {
                $receptores = explode(',', $datos['receptores']);
                foreach ($receptores as $idx => $receptor) {
                    //Set who the message is to be sent to
                    $mail->addAddress(trim($receptor));
                }
            }
            /*foreach ($datos['receptores'] as $idx => $receptor) {
                //Set who the message is to be sent to
                $mail->addAddress($receptor);
            }*/

            //Set the subject line
            $mail->Subject = $datos['asunto'];
            //Read an HTML message body from an external file, convert referenced images to embedded,
            //convert HTML into a basic plain-text alternative body
            //$mail->msgHTML(file_get_contents('contents.html'), __DIR__);

            // Se agregan los adjuntos
            foreach ($datos['adjuntos'] as $idx => $adjunto) {
                $mail->addAttachment($adjunto);
                //echo "se adjunto: $adjunto\r\n";
            }

            $mail->msgHTML($datos['cuerpo_html']);
            //Replace the plain text body with one created manually
            $mail->AltBody = $datos['cuerpo_plano'];
            //Attach an image file
            //$mail->addAttachment('images/phpmailer_mini.png');
            //send the message, check for errors
            $r = $mail->send();
            return array (
                'codigo_mf_numero' => ($r == true) ? 0 : 1,
                'codigo_mf_texto' => ($r == true) ? 'OK' : $mail->ErrorInfo
            );

            /*if (!$r) {
                echo "Mailer Error: " . $mail->ErrorInfo;
            } else {
                echo "Message sent!";
                //Section 2: IMAP
                //Uncomment these to save your message in the 'Sent Mail' folder.
                #if (save_mail($mail)) {
                #    echo "Message saved!";
                #}
            }
            //Section 2: IMAP
            //IMAP commands requires the PHP IMAP Extension, found at: https://php.net/manual/en/imap.setup.php
            //Function to call which uses the PHP imap_*() functions to save messages: https://php.net/manual/en/book.imap.php
            //You can use imap_getmailboxes($imapStream, '/imap/ssl') to get a list of available folders or labels, this can
            //be useful if you are trying to get this working on a non-Gmail IMAP server.
            function save_mail($mail)
            {
                //You can change 'Sent Mail' to any other folder or tag
                $path = "{imap.gmail.com:993/imap/ssl}[Gmail]/Sent Mail";
                //Tell your server to open an IMAP connection using the same username and password as you used for SMTP
                $imapStream = imap_open($path, $mail->Username, $mail->Password);
                $result = imap_append($imapStream, $path, $mail->getSentMIMEMessage());
                imap_close($imapStream);
                return $result;
            }*/
        }
        catch (Exception $ex)
        {
            return array(
                'codigo_mf_numero' => 2,
                'codigo_mf_texto' => $ex->getMessage()
            );
        }
    }
}

/*function ___email($datos)
{
    if(class_exists('_KMail') !== false)
    {
        require_once __DIR__ . '/kmail.php';
    }

    $mailer = new _KMail();
    $mailer->debug();

    // Se asigna la direccion del receptor
    $mailer->from($datos['emisor']);

    // Se agregan los receptores
    $mailer->to($datos['receptores']);

    // Se agregan los adjuntos
    if(isset($datos['adjuntos']) && is_array($datos['adjuntos']) && !empty($datos['adjuntos']))
    {
        $mailer->attach($datos['adjuntos']);
    }

    // Se asigna el mensaje
    $mailer->message($datos['mensaje']);

    // Se envia el mensaje
    $mailer->send();
}*/