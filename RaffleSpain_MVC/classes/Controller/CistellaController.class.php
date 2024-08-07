<?php

class CistellaController extends Controller
{

        /**
     * Muestra la cesta de la compra del usuario.
     *
     * @throws Exception Si el usuario no está autenticado.
     */
    public static function show()
    {
        if (isset($_SESSION['usuari'])) {
            $carretoProductos = self::cargarProductos();
            CistellaView::show($carretoProductos);
        } else {
            $loginController = new ClientController();
            $loginController->formLogin();
        }
    }

        /**
     * Carga los productos en la cesta del usuario.
     *
     * @return mixed|array|null Los productos en la cesta o null si el usuario no está autenticado.
     */
    public static function cargarProductos()
    {
        if (isset($_SESSION['usuari'])) {
            $cistellaList = new CistellaProduct();
            $cistellaModel = new CistellaProductModel();

            $cistellaList->client_id = $_SESSION['usuari']->id;
            $cistella = $cistellaModel->read($cistellaList);

            return $cistella;
        }
    }

        /**
     * Añade un producto a la cesta de la compra del usuario.
     *
     * @param int $id El ID del producto a añadir.
     * @throws Exception Si el usuario no está autenticado o no se proporciona la talla del producto.
     */
    public function addProduct($id)
    {
        if (isset($_SESSION['usuari'])) {
            if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["cesta"])) {
                
                $sizeSelect = intval($this->sanitize($_POST["talla"]));
                
                if (!is_numeric($sizeSelect) || $sizeSelect <= 0) {
                    $error = "Debes de seleccionar una talla.";
                }

                $vProduct = new ProducteView();
                $prd = new Product($id[0]);
                $productModel = new ProductModel();
                $newProduct = $productModel->getById($prd);
                $tallas = $productModel->getTallas($newProduct);
                
                if (!isset($error)) {
                    
                    $cistellaList = new CistellaProduct();
                    $cistellaModel = new CistellaProductModel();
                    
                    $cistellaList->client_id = $_SESSION['usuari']->id;
                    $cistellaList->product = $newProduct;
                    $cistellaList->size = $sizeSelect;
                    
                    $dbProductCarreto = $cistellaModel->readByClientAndProduct($cistellaList);
                    
                    if ($dbProductCarreto === null) {
                        $cistellaListdbProduct = new CistellaProduct();
                        $cistellaListdbProduct->client_id = $_SESSION['usuari']->id;
                        $cistellaListdbProduct->product = $cistellaList->product;
                        $cistellaListdbProduct->size = $cistellaList->size;
                        $cistellaModel->create($cistellaListdbProduct);
                    } else {
                        $cistellaListdbProduct = new CistellaProduct();
                        $cistellaListdbProduct->client_id = $_SESSION['usuari']->id;
                        $cistellaListdbProduct->product = $newProduct;
                        $cistellaListdbProduct->quantity = $dbProductCarreto->quantity + 1;
                        $cistellaListdbProduct->size = $cistellaList->size;
                        $cistellaModel->update($cistellaListdbProduct);
                    }
                } else {
                    $vProduct->show($newProduct, $tallas, false, $error);
                }
            } else {
                header('Location: ?Producte/mostrarProducte/' . $id[0]);
            }
        } else {
            $loginController = new ClientController();
            $loginController->formLogin();
        }

        header('Location: ?Producte/mostrarProducte/' . $id[0]);
        exit();
    }

    /**
     * Vacía la cesta de la compra del usuario.
     *
     * @throws Exception Si el usuario no está autenticado.
     */
    public static function emptyCart()
    {
        if (isset($_SESSION['usuari'])) {
            $cistellaModel = new CistellaProductModel();
            $client = new Client($_SESSION['usuari']->id);
            $cistellaModel->deleteByClientId($client);
        }

        header('Location: .');
        exit();
    }

    
    public static function removeProductById($productId)
    {
        
        if (isset($_SESSION['usuari'])) {

            $cistellaListModel = new CistellaProductModel();
            $product = new Product($productId[0]);

            $cistellaListModel->deleteById($product);
        }

        header('Location: ?Cistella/show');
        exit();
    }

        /**
     * Actualiza la cantidad de un producto en la cesta de la compra del usuario.
     *
     * @param array $params Un array que contiene el ID del producto y la nueva cantidad.
     * @throws Exception Si el usuario no está autenticado.
     */

    public static function updateCantidad($params)
    {
        $productId = $params[0];
        $newQuantity = $params[1];

        if (isset($_SESSION['usuari'])) {

            $cistellaListModel = new CistellaProductModel();
            $product = new Product($productId);
            $cistellaList = new CistellaProduct();
            $cistellaList->client_id = $_SESSION['usuari']->id;
            $cistellaList->product = $product;
            $cistellaList->quantity = $newQuantity;

            $cistellaListModel->update($cistellaList);
        }

        header('Location: ?Cistella/show');
        exit();
    }

        /**
     * Actualiza la cantidad y la talla de un producto en la cesta de la compra del usuario.
     *
     * @param array $params Un array que contiene el ID del producto, la nueva cantidad y la nueva talla.
     * @throws Exception Si el usuario no está autenticado.
     */
    public static function updateCantidadTalla($params)
    {
        $productId = $params[0];
        $newQuantity = $params[1];
        $newSize = $params[2];

        if (isset($_SESSION['usuari'])) {

            $cistellaListModel = new CistellaProductModel();
            $product = new Product($productId);
            $cistellaList = new CistellaProduct();
            $cistellaList->client_id = $_SESSION['usuari']->id;
            $cistellaList->product = $product;
            $cistellaList->quantity = $newQuantity;
            $cistellaList->size = $newSize;

            $cistellaListModel->updateQuantityAndSize($cistellaList);
        }

        header('Location: ?Cistella/show');
        exit();
    }

}