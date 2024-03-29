<?php

class ClientController extends Controller {
    
    private $login;
    private $register;
    
    private $vLogin;
    private $vCLientDates;
    
    public function __construct() {
        $this->login = new Client("", "", "", "", "", "", "", "", "", "");
        $this->register = new Client("", "", "", "", "", "", "", "", "", "");
        $this->vCLientDates = new ClientDatesView();
        $this->vLogin = new ClientView();
    }
    
    public function formLogin() {
        if (isset($_COOKIE["lang"])) {
            $lang = $_COOKIE["lang"];
        } else {
            $lang = "ca";
        }
        
        ClientView::showLogin($this->login, $lang);
    }
    
    public function formRegister() {
        if (isset($_COOKIE["lang"])) {
            $lang = $_COOKIE["lang"];
        } else {
            $lang = "ca";
        }
        
        ClientView::showRegister($this->register, $lang);
    }
    
    public function showDatesClient() {
        if (isset($_COOKIE["lang"])) {
            $lang = $_COOKIE["lang"];
        } else {
            $lang = "ca";
        }
        
        $vClientDates = new ClientDatesView();
        $vClientDates->show($lang);
    }
    
    public function changePassword($idSent) {
        if (isset($_COOKIE["lang"])) {
            $lang = $_COOKIE["lang"];
        } else {
            $lang = "ca";
        }
        
        if ($_SERVER["REQUEST_METHOD"] == "POST" && (isset($_POST["changePassword"]))) {
            
            $mClient = new ClientModel();
            
            $currentPassword = $this->sanitize($_POST['currentPassword']);
            $newPassword = $this->sanitize($_POST['newPassword']);
            $confirmPassword = $this->sanitize($_POST['confirmPassword']);
            
            $id = $idSent[0];
            $auxObj = new Client($id, null, null, null, null, null, null, null, null, null);
            $result = $mClient->getById($auxObj);
            
            if ($result->password !== $currentPassword) {
                $errors = "La contraseña actual es incorrecta.";
            }
            
            if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/', $newPassword)) {
                $errors = "La contraseña no teiene el formato correcto.";
            }
            
            if ($newPassword !== $confirmPassword) {
                $errors = "La contrasenya nueva no coincide con la contraseña de confirmacion.";
            }
            
            if (!isset($errors)) {
                
                $result->__set("password", $confirmPassword);
                $consulta = $mClient->updatePassword($result);
                
                if (count($consulta) === 0) {
                    session_destroy();
                    header("Location: index.php?client/formLogin");
                } else {
                    $this->vCLientDates->show($lang, $consulta);
                }
            } else {
                $this->vCLientDates->show($lang, $errors);
            }
        }
    }
    
    public function validateLogin() {
        if (isset($_COOKIE["lang"])) {
            $lang = $_COOKIE["lang"];
        } else {
            $lang = "ca";
        }
        
        if ($_SERVER["REQUEST_METHOD"] == "POST" && (isset($_POST["submit"]))) {
            
            $usuari = $this->sanitize($_POST['username']);
            $contrasenya = $this->sanitize($_POST['password']);
            
            if (strlen($usuari) == 0) {
                $errors = "El email es obligatorio.";
            }
            else if (!filter_var($usuari, FILTER_VALIDATE_EMAIL)) {
                $errors = "El formato del email es invalido.";
            }
            
            if (strlen($contrasenya) == 0) {
                $errors = "El campo 'contrasenya' es obligatorio.";
            }
            
            $this->login = new Client(
                null,
                null,
                $contrasenya,
                null,
                null,
                $usuari,
                null,
                null,
                null,
                null
            );
            
            if (!isset($errors)) {
                $cLogin = new ClientModel();
                $consulta = $cLogin->getByEmailPassword($this->login);
                if ($consulta != "El email o la contrasenya no son correctos.") {
                    // session_regenerate_id();
                    $_SESSION['usuari'] = $consulta;
                    ($consulta->__get("type") == 1) ? $_SESSION['userAdmin'] = true : $_SESSION['userAdmin'] = false;
                    header("Location: index.php");
                }
                else {
                    $this->vLogin->showLogin($this->login, $lang, $consulta);
                }
            } else {
                $this->vLogin->showLogin($this->login, $lang, $errors);
            }
        }
    }
    
    public function updateDatesClient() {
        if (isset($_COOKIE["lang"])) {
            $lang = $_COOKIE["lang"];
        } else {
            $lang = "ca";
        }
        
        if ($_SERVER["REQUEST_METHOD"] == "POST" && (isset($_POST["submit"]))) {
            
            $name = $this->sanitize($_POST['name']);
            $apellidos = $this->sanitize($_POST['surnames']);
            $usuari = $this->sanitize($_POST['username']);
            $contrasenya = $this->sanitize($_POST['password']);
            $nacimiento = $this->sanitize($_POST['born']);
            $telefono = $this->sanitize($_POST['phone']);
            $poblacion = $this->sanitize($_POST['poblation']);
            $direccion = $this->sanitize($_POST['address']);
            $sexo = $this->sanitize($_POST['sex']);
            
            if (strlen($name) == 0) {
                $errors = "El nombre es obligatorio.";
            }
            
            if (strlen($apellidos) == 0) {
                $errors = "Los apellidos son obligatorio.";
            }
            
            if (strlen($usuari) == 0) {
                $errors = "El email es obligatorio.";
            } else if (!filter_var($usuari, FILTER_VALIDATE_EMAIL)) {
                $errors = "El formato del email es invalido.";
            }
            
            if (strlen($contrasenya) == 0) {
                $errors = "La contrasenya es obligatorio.";
            }
            
            if (strlen($nacimiento) > 0) {
                $objFecha = DateTime::createFromFormat('Y-m-d', $nacimiento);
                if (!$objFecha || $objFecha->format('Y-m-d') !== $nacimiento) {
                    $errors = "El formato de la fecha de nacimiento esta mal.";
                }
            }
            
            if (strlen($poblacion) == 0) {
                $errors = "La poblacion es obligatorio.";
            }
            
            if (strlen($direccion) < 0) {
                $errors = "La direccion es obligatorio.";
            } else if (!str_contains($direccion, ",")) {
                $errors = "La direccion esta mal, debe de contener 'direccion', 'numero'.";
            }
            
            $this->register = new Client(
                null,
                $name,
                $contrasenya,
                $apellidos,
                $nacimiento,
                $usuari,
                $telefono,
                $sexo,
                $poblacion,
                $direccion
                );
                        
            if (!isset($errors)) {
                $cLogin = new ClientModel();
                $consulta = $cLogin->create($this->register);
                if ($consulta === "La consulta se ha realizado con existo") {
                    header("index.php");
                }
                else {
                    $errors = "El registro es incorrecto";
                    $this->vLogin->showRegister($this->register, $lang, $errors);
                }
            }
            else {
                $this->vLogin->showRegister($this->register, $lang, $errors);
            }
        }
    }
    
    public function validateRegister() {
        if (isset($_COOKIE["lang"])) {
            $lang = $_COOKIE["lang"];
        } else {
            $lang = "ca";
        }
        
        if ($_SERVER["REQUEST_METHOD"] == "POST" && (isset($_POST["submit"]))) {
            
            $name = $this->sanitize($_POST['name']);
            $apellidos = $this->sanitize($_POST['surnames']);
            $usuari = $this->sanitize($_POST['username']);
            $contrasenya = $this->sanitize($_POST['password']);
            $nacimiento = $this->sanitize($_POST['born']);
            $telefono = $this->sanitize($_POST['phone']);
            $poblacion = $this->sanitize($_POST['poblation']);
            $direccion = $this->sanitize($_POST['address']);
            $sexo = $this->sanitize($_POST['sex']);
            
            if (strlen($name) == 0) {
                $errors = "El nombre es obligatorio.";
            }
            
            if (strlen($apellidos) == 0) {
                $errors = "Los apellidos son obligatorio.";
            }
            
            if (strlen($usuari) == 0) {
                $errors = "El email es obligatorio.";
            } else if (!filter_var($usuari, FILTER_VALIDATE_EMAIL)) {
                $errors = "El formato del email es invalido.";
            }
            
            if (strlen($contrasenya) == 0) {
                $errors = "La contrasenya es obligatorio.";
            }
            
            if (strlen($nacimiento) > 0) {
                $objFecha = DateTime::createFromFormat('Y-m-d', $nacimiento);
                if (!$objFecha || $objFecha->format('Y-m-d') !== $nacimiento) {
                    $errors = "El formato de la fecha de nacimiento esta mal.";
                }
            }
            
            if (strlen($poblacion) == 0) {
                $errors = "La poblacion es obligatorio.";
            }
            
            if (strlen($direccion) < 0) {
                $errors = "La direccion es obligatorio.";
            } else if (!str_contains($direccion, ",")) {
                $errors = "La direccion esta mal, debe de contener 'direccion', 'numero'.";
            }
            
            $this->register = new Client(
                null,
                $name,
                $contrasenya,
                $apellidos,
                $nacimiento,
                $usuari,
                $telefono,
                $sexo,
                $poblacion,
                $direccion
            );
                        
            if (!isset($errors)) {
                $cLogin = new ClientModel();
                $consulta = $cLogin->create($this->register);
                if ($consulta === "La consulta se ha realizado con existo") {
                    header("index.php");
                }
                else {
                    $errors = "El registro es incorrecto";
                    $this->vLogin->showRegister($this->register, $lang, $errors);
                }
            }
            else {
                $this->vLogin->showRegister($this->register, $lang, $errors);
            }
        }
    }
    
    public function logOut() {
        session_destroy();
        header("Location: index.php");
        
    }
    
    public function modificarDatos() {
        header("Location: index.php");
    }
    
}