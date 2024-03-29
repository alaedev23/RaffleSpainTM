<?php

class ClientView extends View {
    
    public function __construct() {
        parent::__construct();
    }
    
    public static function showLogin($login, $lang, $errors=null) {
        $fitxerDeTraduccions = "languages/{$lang}_traduccio.php";
        
        echo "<!DOCTYPE html><html lang=\"en\">";
        include "templates/Head.tmp.php";
        echo "<body>";
        include "templates/Header.tmp.php";
        include "templates/Login.tmp.php";
        include "templates/Footer.tmp.php";
        echo "</body></html>";
    }
    
    public static function showRegister($login, $lang, $errors=null) {
        $fitxerDeTraduccions = "languages/{$lang}_traduccio.php";
        
        echo "<!DOCTYPE html><html lang=\"en\">";
        include "templates/Head.tmp.php";
        echo "<body>";
        include "templates/Header.tmp.php";
        include "templates/Register.tmp.php";
        include "templates/Footer.tmp.php";
        echo "</body></html>";
    }
    
}

?>