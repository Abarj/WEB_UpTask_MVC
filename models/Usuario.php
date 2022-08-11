<?php

namespace Model;

class Usuario extends ActiveRecord {

    // Base de datos
    protected static $tabla = 'usuarios';
    protected static $columnasDB = ['id', 'nombre', 'email', 'password', 'token', 'confirmado'];

    public $id;
    public $nombre;
    public $email;
    public $password;
    public $token;
    public $confirmado;
    

    public function __construct($args = []) {

        $this->id = $args['id'] ?? null;
        $this->nombre = $args['nombre'] ?? '';
        $this->email = $args['email'] ?? '';
        $this->password = $args['password'] ?? '';
        $this->password2 = $args['password2'] ?? '';
        $this->password_actual = $args['password_actual'] ?? '';
        $this->password_nuevo = $args['password_nuevo'] ?? '';
        $this->token = $args['token'] ?? '';
        $this->confirmado = $args['confirmado'] ?? 0;
    }

    public function validarLogin() {

        if(!$this->email) {
            self::$alertas['error'][] = 'El email es obligatorio';
        }

        if(!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            self::$alertas['error'][] = 'El email no es válido';
        }

        if(!$this->password) {
            self::$alertas['error'][] = 'La contraseña es obligatoria';
        }

        return self::$alertas;
    }

    // Mensajes de validación para la creación de una cuenta
    public function validarNuevaCuenta() {
        
        if(!$this->nombre) {
            self::$alertas['error'][] = 'El nombre es obligatorio';
        }

        if(!$this->email) {
            self::$alertas['error'][] = 'El Email es obligatorio';
        }

        if(!$this->password) {
            self::$alertas['error'][] = 'La contraseña es obligatoria';
        }

        if(strlen($this->password) < 6) {
            self::$alertas['error'][] = 'La contraseña debe contener al menos 6 carácteres';
        }

        if($this->password !== $this->password2) {
            self::$alertas['error'][] = 'La contraseña ha de coincidir en ambos campos';
        }

        return self::$alertas;
    }
    
    public function validarEmail() {

        if(!$this->email) {
            self::$alertas['error'][] = 'El email es obligatorio';
        }

        if(!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            self::$alertas['error'][] = 'El email no es válido';
        }

        return self::$alertas;
    }

    public function validar_perfil() {
        
        if(!$this->nombre) {
            self::$alertas['error'][] = 'El nombre es obligatorio';
        }

        if(!$this->email) {
            self::$alertas['error'][] = 'El Email es obligatorio';
        }

        return self::$alertas;
    }

    public function nuevo_password() : array {
        
        if(!$this->password_actual) {
            self::$alertas['error'][] = 'Debes escribir tu contraseña actual';
        }

        if(!$this->password_nuevo) {
            self::$alertas['error'][] = 'Debes escribir tu nueva contraseña';
        }

        if(strlen($this->password_nuevo) < 6) {
            self::$alertas['error'][] = 'La nueva contraseña debe tener al menos 6 carácteres';
        }

        return self::$alertas;
    }

    public function comprobar_password() : bool {
        
        return password_verify($this->password_actual, $this->password);
    }

    public function hashPassword() : void {

        $this->password = password_hash($this->password, PASSWORD_BCRYPT);
    }

    public function crearToken() : void {

        $this->token = uniqid();
    }

    public function validarPassword() {

        if(!$this->password) {
            self::$alertas['error'][] = 'La contraseña es obligatoria';
        }

        if(strlen($this->password) < 6) {
            self::$alertas['error'][] = 'La contraseña debe tener al menos 6 carácteres';
        }

        return self::$alertas;
    }

    public function comprobarPasswordAndVerificado($password) {

        $resultado = password_verify($password, $this->password);

        if(!$resultado || !$this->confirmado) {
            self::$alertas['error'][] = 'Contraseña incorrecta o cuenta sin confirmar';
        }
        else {
            return true;
        }
    }
}