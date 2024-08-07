<?php

/**
 * Clase ProducteView
 *
 * Esta clase se utiliza para mostrar las vistas relacionadas con los productos.
 */
class ProducteView extends View {

    /**
     * @var CommentModel Objeto de modelo de comentarios.
     */
    private $mComment;
    
    /**
     * Constructor de la clase.
     */
    public function __construct() {
        $this->mComment = new CommentModel();
    }
    
    /**
     * Muestra la vista del producto.
     *
     * @param mixed $producte Información del producto.
     * @param mixed $tallas Información de las tallas disponibles para el producto.
     * @param mixed $enFavoritos Indica si el producto está en la lista de favoritos.
     * @param string $errors Mensajes de error.
     * @param string $errorsComment Mensajes de error para los comentarios.
     */
    public function show($producte, $tallas, $enFavoritos, $errors = '', $errorsComment = '') {
        
        $getComments = $this->mComment->getById($producte);   
        $generateAddComment = $this->generateAddComment($producte, $errorsComment);
        
        echo "<!DOCTYPE html><html class=\"light\" lang=\"es\">";
        include "templates/Head.tmp.php";
        echo "<body id=\"producto_page\">";
        include "templates/Header.tmp.php";
        include "templates/Producte.tmp.php";
        include "templates/Footer.tmp.php";
        echo "</body></html>";

    }
    
    /**
     * Genera el formulario para añadir un comentario.
     *
     * @param mixed $producte Información del producto.
     * @param string $errorsComment Mensajes de error para los comentarios.
     * @return string HTML del formulario para añadir un comentario.
     */
    public function generateAddComment($producte, $errorsComment = '')
    {        
        $html = '<div>
            <div id="myModal" class="modal">
            <div class="modal-content">
                <div class="modal-header"><h2>Añadir Comentario</h2><span id="closeModalBtn" class="close">&times;</span></div>
                <div class="modal-body">
                    <div>
                    <form id="addComment" action="?comment/addComment/' . $producte->id . '" method="post">
                        <div class="section-modal">
                            <label for="titleComment">Titulo</label>
                            <input type="text" placeholder="Titulo" id="titleComment" name="titleComment" required>
                        </div>
                        <div class="section-modal">
                            <label for="mensajeComment">Mensaje</label>
                            <input type="text" placeholder="Mensaje" id="mensajeComment" name="mensajeComment" required>
                        </div>
                        <div class="section-modal">
                            <select name="estrellas" class="select" style="min-width: 200px;">
                                <option value="">Selecciona las estrellas</option>
                                <option value="0">Ninguna estrella</option>
                                <option value="1">1 estrella</option>
                                <option value="2">2 estrellas</option>
                                <option value="3">3 estrellas</option>
                                <option value="4">4 estrellas</option>
                                <option value="5">5 estrellas</option>
                            </select>
                        </div>';
        
        if ($errorsComment !== '') {
            $html .= "<div class=\"errorMessage\"><p>" . $errorsComment . "</p></div>";
        }
        
        $html .= '<div>
                            <button class="btn" name="addNewComent" type="submit">Añadir Comentario</button>
                        </div>
                    </form>
                </div>
            </div></div>';
        
        return $html;
    }

}
