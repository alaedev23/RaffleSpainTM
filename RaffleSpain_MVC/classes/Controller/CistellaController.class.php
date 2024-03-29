<?php
class CistellaController extends Controller
{

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

    public static function cargarProductos() {

        if (isset($_SESSION['usuari'])) {
            $cistellaList = new CistellaProduct();
            $cistellaModel = new CistellaProductModel();

            $cistellaList->client_id = $_SESSION['usuari']->id;
            $cistella = $cistellaModel->read($cistellaList);

            return $cistella;

        }
    }

    public static function addProduct($id)
    {
        $prd = new Product($id[0]);
        $productModel = new ProductModel();
        $newProduct = $productModel->getById($prd);

        if (isset($_SESSION['usuari'])) {

            $cistellaList = new CistellaProduct();
            $cistellaModel = new CistellaProductModel();

            $cistellaList->client_id = $_SESSION['usuari']->id;
            $cistellaList->product = $newProduct;

            $dbProductCarreto = $cistellaModel->readByClientAndProduct($cistellaList);

            if ($dbProductCarreto === null) {
                $cistellaListdbProduct = new CistellaProduct();
                $cistellaListdbProduct->client_id = $_SESSION['usuari']->id;
                $cistellaListdbProduct->product = $cistellaList->product;
                $cistellaModel->create($cistellaListdbProduct);
            } else {
                $cistellaListdbProduct = new CistellaProduct();
                $cistellaListdbProduct->client_id = $_SESSION['usuari']->id;
                $cistellaListdbProduct->product = $newProduct;
                $cistellaListdbProduct->quantity = $dbProductCarreto->quantity + 1;
                $cistellaModel->update($cistellaListdbProduct);
            }

        } else {
            $loginController = new ClientController();
            $loginController->formLogin();
        }

        header('Location: ?Producte/mostrarProducte/' . $id[0]);
        exit();

    }

    public static function emptyCart() {

        if (isset($_SESSION['usuari'])) {
            $cistellaModel = new CistellaProductModel();
            $client = new Client($_SESSION['usuari']->id);
            $cistellaModel->deleteByClientId($client);
        }

        header('Location: .');
        exit();
    }
    

    public static function removeProductById($productId) {

        if (isset($_SESSION['usuari'])) {

            $cistellaListModel = new CistellaProductModel();
            $product = new Product($productId[0]);

            $cistellaListModel->deleteById($product);

        }

        header('Location: ?Cistella/show');
        exit();
    }

    public static function updateCantidad($params) {

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

}
