<?php

namespace Controllers;

use Classes\Email;
use Model\Usuario;
use MVC\Router;

class LoginController {    
    
    public static function login( Router $router) {
        $alertas = [];                 

        if($_SERVER['REQUEST_METHOD'] === "POST") {
            $auth = new Usuario($_POST);              
            
            $alertas = $auth->validarLogin();
          
            if(empty($alertas)) {
                // comprobar que exista el usuario
                $usuario = Usuario::where('email', $auth->email);        
                
                if($usuario) {
                    // veriricar el password
                    if($usuario->ComprobarPasswordAndVerificado($auth->password)) {
                        // autenticando al usuario
                        session_start();

                        $_SESSION['id'] = $usuario->id;
                        $_SESSION['nombre'] = $usuario->nombre ." " . $usuario->apellido;
                        $_SESSION['email'] = $usuario->email;
                        $_SESSION['login'] = true;

                        // redireccionamiento

                        if($usuario->admin === "1") {
                            $_SESSION['admin'] = $usuario->admin ?? null;
                            header('Location: /admin');
                        } else {
                            header('Location: /cita');
                        }                       

                    }
                } else {
                    Usuario::setAlerta('error', 'Usuario no encontrado');
                }                
            }
        }

        $alertas = Usuario::getAlertas();
        
        $router->render('auth/login', [
            'alertas' => $alertas
        ]);
        
    }

    public static function logout() {
        session_start();

        $_SESSION = [];

        header('Location: /');
    }

    public static function olvide(Router $router) {
        $alertas = [];
            if($_SERVER['REQUEST_METHOD'] === "POST"){
                $auth = new Usuario($_POST);
                $alertas = $auth->validarEmail();

                if(empty($alertas)) {
                    $usuario = Usuario::where('email', $auth->email);

                    if($usuario && $usuario->confirmado === "1"){

                        //generar un token de un solo uso
                        $usuario->crearToken();
                        $usuario->guardar();

                        //TODO: Enviar el email
                        $email = new Email($usuario->email, $usuario->nombre, $usuario->token );
                        $email->enviarInstrucciones();

                        // Alerta de Exito
                        Usuario::setAlerta('exito', 'Revisa Tu Email');                      
                } else {
                    Usuario::setAlerta('error', 'No existe ususario o No esta Confirmado');                    
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
        $error = false;

        $token = s($_GET['token']);
        //buscar usuario por su token
        $usuario = Usuario::where('token', $token);

        if(empty($usuario)){
            Usuario::setAlerta('error', 'Token No Valido');
            $error = true;
        }

        if($_SERVER['REQUEST_METHOD'] === "POST") {
            // Leer el nuevo Password y Guardarlo

            $password = new Usuario($_POST); 
            $alertas = $password->validarPassword();

            if(empty($alertas)) {
                $usuario->password = null;

                $usuario->password = $password->password;
                $usuario->hashPassword();
                $usuario->token = null;

                $resultado = $usuario->guardar();
                if($resultado) {
                    header('Location: /');
                }
            }
        }

        $alertas = Usuario::getAlertas();
        $router->render('auth/recuperar-password', [
            'alertas' => $alertas,
            'error' => $error
        
        ]);     
    }

    public static function crear(Router $router) {
        $usuario = new Usuario;

        // Alertas Vacias
        $alertas = [];
        

        if($_SERVER['REQUEST_METHOD'] === 'POST') {                   
            
            
            $alertas = $usuario->validarNuevaCuenta();

            //revisar que alertas este vacio
            if(empty($alertas)){
                //verificar que el usuario no este registrado
                $resultado = $usuario->existeUsuario();

                if($resultado->num_rows) {
                    $alertas = Usuario::getAlertas();
                } else {
                    // hashear el password
                    $usuario->hashPassword();

                    // generar token unico
                    $usuario->crearToken();

                    // enviar el email
                    $email = new Email($usuario->nombre, $usuario->email, $usuario->token);

                    $email->enviarConfirmacion();

                    // crear el usuario
                    $resultado = $usuario->guardar();

                    // debuguear($usuario);

                    if($resultado) {
                        header('Location: /mensaje');
                    }                   

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
            Usuario::setAlerta('error', 'Token No Válido');
        } else {
            // Modificar a usuario confirmado
            $usuario->confirmado = "1";
            $usuario->token = null;
            $usuario->guardar();
            Usuario::setAlerta('exito', 'Cuenta Comprobada Correctamente');
        }
       
        // Obtener alertas
        $alertas = Usuario::getAlertas();

        // Renderizar la vista
        $router->render('auth/confirmar-cuenta', [
            'alertas' => $alertas
        ]);
    }
}