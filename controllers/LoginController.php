<?php

namespace Controllers;

use Classes\Email;
use Model\Usuario;
use MVC\Router;

class LoginController {

    public static function login(Router $router) {

        $alertas = [];
        
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            $auth = new Usuario($_POST);

            $alertas = $auth->validarLogin();

            if(empty($alertas)) {
                // Comprobar que exista el usuario
                $usuario = Usuario::where('email', $auth->email);

                if($usuario) {
                    // Verificar el password
                    if($usuario->comprobarPasswordAndVerificado($auth->password)) {
                        // Autenticar el usuario
                        session_start();

                        $_SESSION['id'] = $usuario->id;
                        $_SESSION['nombre'] = $usuario->nombre;
                        $_SESSION['email'] = $usuario->email;
                        $_SESSION['login'] = true;

                        // Redireccionar
                        if($usuario->admin === "1") {
                            $_SESSION['admin'] = $usuario->admin ?? null;
                            header('Location: /admin');
                        }
                        else {
                            header('Location: /dashboard');
                        }
                    }
                }
                else{
                    Usuario::setAlerta('error', 'Usuario no encontrado');
                }
            }
        }

        $alertas = Usuario::getAlertas();

        // Renderizar la vista
        $router->render('auth/login', [
            'titulo' => 'Iniciar Sesión',
            'alertas' => $alertas
        ]);
    }

    public static function logout() {

        session_start();

        $_SESSION = [];

        header('Location: /');
    }

    public static function crear(Router $router) {

        $alertas = [];

        $usuario = new Usuario;

        if($_SERVER['REQUEST_METHOD'] === 'POST') {

            $usuario->sincronizar($_POST);
            $alertas = $usuario->validarNuevaCuenta();
            
            if(empty($alertas)) {
                $existeUsuario = Usuario::where('email', $usuario->email);

                if($existeUsuario) {
                    Usuario::setAlerta('error', 'El usuario ya está registrado');
                    $alertas = Usuario::getAlertas();
                }
                else {
                    // Hashear el password
                    $usuario->hashPassword();

                    // Eliminar password2 (no se requiere ya que no está en la BD)
                    unset($usuario->password2);

                    // Generar el Token
                    $usuario->crearToken();

                    // Crear Nuevo Usuario
                    $resultado = $usuario->guardar();

                    // Enviar Email
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                    $email->enviarConfirmacion();
                    
                    if($resultado) {
                        header('Location: /mensaje');
                    }
                }
            }
        }

        // Renderizar la vista
        $router->render('auth/crear', [
            'titulo' => 'Crea tu cuenta en UpTask',
            'usuario' => $usuario,
            'alertas' => $alertas
        ]);
    }

    public static function olvide(Router $router) {

        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST') {

            $usuario = new Usuario($_POST);
            $alertas = $usuario->validarEmail();

            if(empty($alertas)) {

                $usuario = Usuario::where('email', $usuario->email);

                if($usuario && $usuario->confirmado === "1") {
                    // Generar un token
                    $usuario->crearToken();
                    unset($usuario->password2);

                    // Actualizar el usuario
                    $usuario->guardar();

                    // Enviar el email
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                    $email->enviarInstrucciones();

                    // Alerta de exito
                    Usuario::setAlerta('exito', 'Revisa tu email');
                }
                else {
                    Usuario::setAlerta('error', 'El Usuario no existe o no está confirmado');
                }
            }
        }
        
        $alertas = Usuario::getAlertas();

        // Renderizar la vista
        $router->render('auth/olvide', [
            'titulo' => 'Restablecer contraseña',
            'alertas' => $alertas
        ]);
    }

    public static function restablecer(Router $router) {

        $alertas = [];
        $error = false;
        $mostrar = true;
        $token = s($_GET['token']);

        if(!$token) {
            header('Location: /');
        }

        // Buscar usuario por su token
        $usuario = Usuario::where('token', $token);

        if(empty($usuario)) {
            Usuario::setAlerta('error', 'Token no válido');
            $error = true;
            $mostrar = false;
        }

        if($_SERVER['REQUEST_METHOD'] === 'POST') {

            // Leer el nuevo password y guardarlo
            $password = new Usuario($_POST);
            $alertas = $password->validarPassword();

            if(empty($alertas)) {

                $usuario->password = null;

                $usuario->password = $password->password;
                
                // Hashear Password
                $usuario->hashPassword();

                // Eliminar el Token
                $usuario->token = null;

                // Guardar en DB
                $resultado = $usuario->guardar();
                
                // Redireccionar
                if($resultado) {
                    header('Location: /');
                }
            }
        }

        $alertas = Usuario::getAlertas();

        // Renderizar la vista
        $router->render('auth/restablecer', [
            'titulo' => 'Restablecer contraseña',
            'alertas' => $alertas,
            'error' => $error,
            'mostrar' => $mostrar
        ]);
    }

    public static function mensaje(Router $router) {

        // Renderizar la vista
        $router->render('auth/mensaje', [
            'titulo' => 'Cuenta creada correctamente'
        ]);
    }

    public static function confirmar(Router $router) {

        $alertas = [];
        $token = s($_GET['token']);
        $usuario = Usuario::where('token', $token);

        if(!$token) header('Location: /');

        if(empty($usuario)) {
            // Mostrar mensaje de error
            Usuario::setAlerta('error', 'Token no válido');
        }
        else {
            // Modificar a usuario confirmado
            $usuario->confirmado = 1;
            unset($usuario->password2);
            $usuario->token = "";

            // Guardar en la BD
            $usuario->guardar();

            Usuario::setAlerta('exito', 'Cuenta confirmada correctamente');
        }
        
        $alertas = Usuario::getAlertas();

        Usuario::getAlertas();
        
        // Renderizar la vista
        $router->render('auth/confirmar', [
            'titulo' => 'Confirmar tu cuenta UpTask',
            'alertas' => $alertas
        ]);
    }
}