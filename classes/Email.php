<?php
namespace Classes;

use PHPMailer\PHPMailer\PHPMailer;
use Dotenv\Dotenv as Dotenv;
$dotenv = Dotenv::createImmutable('../includes/.env');
$dotenv->safeLoad();

class Email {

    public $nombre;
    public $email;
    public $token;

    public function __construct($nombre, $email, $token)
    {
        $this->nombre = $nombre;
        $this->email = $email;
        $this->token = $token;
    }

    public function enviarConfirmacion() {
        // Crear el objeto de email
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = 'smtp.mailtrap.io';
        $mail->SMTPAuth = true;
        $mail->Port = 2525;
        $mail->Username = '5641d202f040ae';
        $mail->Password = '5db62fcfca46b1';

        $mail->setFrom('cuentas@appsalon.com');
        $mail->addAddress('cuentas@appsalon.com', 'AppSalon.com');
        $mail->Subject = 'Confirma tu cuenta';

        $mail->isHTML(TRUE);
        $mail->CharSet = 'UTF-8';

        $contenido = "<html>";
        $contenido.= "<p><strong>Hola " . $this->nombre . "</strong> Has creado una cuenta en Appsalon, solo debes confirmarla presionando el siguiente enlace</p>";
        $contenido.= "<p>Presiona aquí: <a href='https://warm-ocean-68863.herokuapp.com/confirmar-cuenta?token=" . $this->token . "'>Confirmar cuenta</a></p>";
        $contenido.= "<p>Si no solicitaste esta cuenta, puedes ignorar este mensaje</p>";
        $contenido.= "</html>";

        $mail->Body = $contenido;

        $mail->send();
    }

    public function enviarInstrucciones() {
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = 'smtp.mailtrap.io';
        $mail->SMTPAuth = true;
        $mail->Port = 2525;
        $mail->Username = '5641d202f040ae';
        $mail->Password = '5db62fcfca46b1';

        $mail->setFrom('cuentas@appsalon.com');
        $mail->addAddress('cuentas@appsalon.com', 'AppSalon.com');
        $mail->Subject = 'Reestablece tu password';

        $mail->isHTML(TRUE);
        $mail->CharSet = 'UTF-8';

        $contenido = "<html>";
        $contenido.= "<p><strong>Hola " . $this->nombre . "</strong> Has solicitado reestablecer tu password, presiona el siguiente enlace para hacerlo</p>";
        $contenido.= "<p>Presiona aquí: <a href='https://warm-ocean-68863.herokuapp.com/recuperar?token=" . $this->token . "'>Reestablece password</a></p>";
        $contenido.= "<p>Si no solicitaste este cambio, puedes ignorar este mensaje</p>";
        $contenido.= "</html>";

        $mail->Body = $contenido;

        $mail->send();
    }
}