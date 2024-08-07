<?php

class PayPalController extends Controller
{

        /**
     * Obtiene el token de acceso de PayPal.
     *
     * @throws Exception Si hay errores en la solicitud cURL.
     * @return string|null El token de acceso de PayPal.
     */
    public static function getToken()
    {
        $clientId = PayPalData::getClientId();
        $secret = PayPalData::getSecret();
        $token_endpoint = PayPalData::getTokenEndpoint();

        $bodyParams = http_build_query(array('grant_type' => 'client_credentials'));

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $token_endpoint);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_USERPWD, $clientId . ":" . $secret);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $bodyParams);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            echo "Error en la solicitud cURL: " . $err;
        } else {
            $data = json_decode($response);

            $access_token = $data->access_token;

            return $access_token;
        }
    }

        /**
     * Crea una orden de pago en PayPal para los productos en la cesta.
     *
     * @throws Exception Si hay errores en la solicitud cURL.
     * @return void
     */
    public static function createOrder()
    {

        $totalPrice = null;
        $productsIds = null;
        $quantities = null;

        if (isset($_SESSION['usuari'])) {
            $cistellaList = new CistellaProduct();
            $cistellaModel = new CistellaProductModel();
            $cistellShow = new CistellaView();
            $raffleModel = new RaffleModel();
            $productModel = new ProductModel();

            $cistellaList->client_id = $_SESSION['usuari']->id;
            $cistella = $cistellaModel->read($cistellaList);

            foreach ($cistella as $producteCistella) {

                $stock = $productModel->getQuantity($producteCistella->product);

                $totalPrice += $producteCistella->product->price * $producteCistella->quantity;
                $productsIds[] = $producteCistella->product->id;
                $quantities[] = $producteCistella->quantity;

                $raffle = $raffleModel->getRaffleByProductId($producteCistella);

                if ($raffle != null) {
                    if ($_SESSION['usuari']->id != $raffle->winner && $stock <= 1) {
                        $cistellShow->show($cistella, 'No hay stock del producto ' . Functions::replaceHyphenForSpace($producteCistella->product->brand) . ' ' . str_replace('-', ' ', $producteCistella->product->name) . ' en la cantidad solicitada. Por favor, revise su cesta de la compra.');
                    }
                }

            }

        }

        $tokenArray = array(
            'userId' => $_SESSION['usuari']->id,
            'productIds' => $productsIds,
            'quantities' => $quantities,
            'date' => date('Y-m-d H:i:s')
        );

        $paymentToken = Crypto::encrypt_hash(json_encode($tokenArray));
        $sendToken = urlencode($paymentToken);

        $accessToken = self::getToken();

        $createOrderUrl = 'https://api.sandbox.paypal.com/v2/checkout/orders';
        $returnUrl = 'http://localhost/M12/RaffleSpainTM/RaffleSpain_MVC/?PayPal/confirmPaymentCapture/' . $sendToken;
        $cancelUrl = 'http://localhost/M12/RaffleSpainTM/RaffleSpain_MVC/?cistella/show';

        $bodyParams = json_encode(
            array(
                'intent' => 'CAPTURE',
                'purchase_units' => array(
                    array(
                        'amount' => array(
                            'currency_code' => 'EUR',
                            'value' => $totalPrice,
                            'description' => $cistella[0]->product->id
                        )
                    )
                ),
                'payment_source' => array(
                    'paypal' => array(
                        'experience_context' => array(
                            'payment_method_preference' => 'IMMEDIATE_PAYMENT_REQUIRED',
                            'brand_name' => 'RAFFLESPAIN TM',
                            'locale' => 'es-ES',
                            'user_action' => 'PAY_NOW',
                            'return_url' => $returnUrl,
                            'cancel_url' => $cancelUrl
                        )
                    )
                )
            )
        );

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $createOrderUrl);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt(
            $curl,
            CURLOPT_HTTPHEADER,
            array(
                'Authorization: Bearer ' . $accessToken,
                'Content-Type: application/json'
            )
        );
        curl_setopt($curl, CURLOPT_POSTFIELDS, $bodyParams);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            echo "Error en la solicitud cURL: " . $err;
        } else {
            $orderData = json_decode($response);

            if (isset($orderData->links[1])) {
                header('Location: ' . $orderData->links[1]->href);
                exit();
            } else {
                echo "No se pudo obtener el enlace de redirección de la respuesta de PayPal.";
            }
        }
    }
        /**
     * Confirma el pago y captura los fondos en PayPal.
     *
     * @param string $paymentToken El token de pago.
     * @return void
     */
    public static function confirmPaymentCapture($paymentToken)
    {

        $token = $_GET['token'] ?? '';
        $payerID = $_GET['PayerID'] ?? '';

        if (empty($token) || empty($payerID) || empty($paymentToken)) {
            echo "Error: Falta token, PayerID o paymentToken en la solicitud.";
            return;
        }

        $accessToken = self::getToken();

        $captureUrl = 'https://api.sandbox.paypal.com/v2/checkout/orders/' . $token . '/capture';

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $captureUrl);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt(
            $curl,
            CURLOPT_HTTPHEADER,
            array(
                'Authorization: Bearer ' . $accessToken,
                'Content-Type: application/json'
            )
        );

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        $pView = new PayPalView();

        if ($err) {
            $pView->showError();
        } else {

            // $captureData = json_decode($response);
            $implodedhash = implode("/", $paymentToken);
            $data = Crypto::decrypt_hash($implodedhash);
            $array = json_decode($data, true);

            $deliverModel = new DeliverModel();
            $productModel = new ProductModel();
            $cistellaModel = new CistellaProductModel();

            if (isset($_SESSION['usuari'])) {

                if ($_SESSION['usuari']->id == $array['userId']) {

                    foreach ($array['productIds'] as $index => $productId) {

                        $deliver = new Deliver();
                        $product = new Product($productId);

                        $deliver->client_id = $array['userId'];
                        $deliver->product = $product;
                        $deliver->quantity = $array['quantities'][$index];
                        $deliver->date = $array['date'];

                        $stockProduct = $productModel->getQuantity($deliver->product);

                        $deliver->date_deliver = date('Y-m-d H:i:s', strtotime('+5 days', strtotime($deliver->date)));

                        $existantDeliver = $deliverModel->getDeliver($deliver->id);

                        if ($existantDeliver != null) {
                            $pView->showError();
                            die;
                        }

                        $productModel->updateQuantity($deliver->product, $stockProduct - $deliver->quantity);

                        $createdDeliverId = $deliverModel->createDeliver($deliver);
                        $deliver->id = $createdDeliverId;

                        $emailController = new EmailController();
                        $emailController->sendMail($_SESSION['usuari'], $deliver);

                    }
                }
            }

            $client = new Client($_SESSION['usuari']->id);
            $cistellaModel->deleteByClientId($client);

            Header('Location: ?Deliver/showDelivers');

            // $pView->showCorrect();
        }
    }

}

