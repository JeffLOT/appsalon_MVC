<?php

namespace Controllers;

use MVC\Router;
use Classes\Email;
use Model\Usuario;

class LoginController {
    public static function login(Router $router) {
        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $auth = new Usuario($_POST);

            $alertas = $auth->validarLogin();

            if(empty($alertas)) {
                $usuario = Usuario::where('email', $auth->email);

                if($usuario) {
                    // Verificar password
                    if($usuario->comprobarPasswordAndVerificado($auth->password)) {
                        // Autenticar usuario
                        if(!isset($_SESSION)) {
                            session_start();   
                        }
                        $_SESSION['id'] = $usuario->id;
                        $_SESSION['nombre'] = $usuario->nombre . ' ' . $usuario->apellido;
                        $_SESSION['email'] = $usuario->email;
                        $_SESSION['login'] = true;

                        // Redireccionamiento
                        if($usuario->admin === '1') {
                            $_SESSION['admin'] = $usuario->admin ?? null;

                            header('location: /admin');
                        } else {
                            header('location: /cita');
                        }
                    }
                } else {
                    Usuario::setAlerta('error', 'Usuario no encontrado');
                }
                $alertas = Usuario::getAlertas();  
            }
        }
        $router->render('auth/login', [
            'alertas' => $alertas,
        ]);
    }

    public static function logout() {
        if(!isset($_SESSION)) {
            session_start();
        }
        $_SESSION = [];
        header('Location: /');
    }

    public static function olvide(Router $router) {
        $alertas = [];
       
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $auth = new Usuario($_POST);
            $alertas = $auth->validarEmail();

            if(empty($alertas)) {
                $usuario = Usuario::where('email', $auth->email);

                if($usuario && $usuario->confirmado === "1") {

                    // Generar un token
                    $usuario->crearToken();
                    $usuario->guardar();

                    // Enviar email
                    $email = new Email($usuario->nombre, $usuario->email, $usuario->token);
                    $email->enviarInstrucciones();

                    // Enviar alerta
                    Usuario::setAlerta('exito', 'Revisa tu email');

                } else {
                    Usuario::setAlerta('error', 'El usuario no existe o no está confirmado');
                }
            }
        }
        $alertas = Usuario::getAlertas();

        $router->render('auth/olvide-password', [
            'alertas' => $alertas
        ]);
    }

    public static function recuperar(Router $router) {

        $alertas = [];
        $token = s($_GET['token']);
        $usuario = Usuario::where('token', $token);
        $error = false;

        if(empty($usuario)) {
            Usuario::setAlerta('error', 'Token no válido');
            $error = true;
        }

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Leer el nuevo password y guardarlo
            $password = new Usuario($_POST);
            $alertas = $password->validarPassword();

            if(empty($alertas)) {
                $usuario->password = null;

                $usuario->password = $password->password;
                $usuario->hashPassword();
                $usuario->token = null;

                $resultado = $usuario->guardar();
                if($resultado) {
                    header('location: /');
                }
            }
        }

        // debuguear($usuario);
        $alertas = Usuario::getAlertas();
        $router->render('auth/recuperar-password', [
            'alertas' => $alertas,
            'error' => $error
        ]);
    }

    public static function crear(Router $router) {

        $usuario = new Usuario;
        // Alertas vacías
        $alertas = [];
        
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            $usuario->sincronizar($_POST);
            $alertas = $usuario->validarNuevaCuenta();

            if(empty($alertas)) {
                // Verificar que el email no esté registrados
                $resultado = $usuario->existeUsuario();

                if($resultado->num_rows) {
                    $alertas = Usuario::getAlertas();
                } else {
                    // Hashear password
                    $usuario->hashPassword();

                    $usuario->crearToken();

                    // Enviar email
                    $email = new Email($usuario->nombre, $usuario->email, $usuario->token);
                    $email->enviarConfirmacion();
                    
                    // Crear usuario
                    $resultado = $usuario->guardar();
                    
                    if($resultado) {
                        header('location: /mensaje');
                    }

                    // debuguear($usuario);
                }
            }
        }

        $router->render('auth/crear-cuenta', [
            'usuario' => $usuario,
            'alertas' => $alertas
        ]);
    }

    public static function mensaje(Router $router) {
        $router->render('auth/mensaje');
    }

    public static function confirmar(Router $router) {
        $alertas = [];
        $token = s($_GET['token']);
        $usuario = Usuario::where('token', $token);
      
        if(empty($usuario)) {
            // Mostrar mensaje de error
            Usuario::setAlerta('error', 'Token no válido');
        } else {
            // Modificar a usuario confirmado
            $usuario->confirmado = '1';
            $usuario->token = '';
            $usuario->guardar();
            Usuario::setAlerta('exito', 'Cuenta Confirmada Correctamente');
        }

        // Obtener alertas
        $alertas = Usuario::getAlertas();
        
        // Renderizar la vista
        $router->render('auth/confirmar-cuenta', [
            'alertas'=> $alertas
        ]);
    }
}