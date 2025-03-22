<?php
require_once "API.php";
// Habilitar reporte de errores para depuración
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    die("Acceso no autorizado");
}

header("Content-Type: application/json");

$cardTokenID = $_POST["cardTokenID"] ?? null;

$amount = '1'; // PRECIO DE TU PRODUCTO, EVIDENTEMENTE ESTO LO DEBERAS MANEJAR DINAMICAMENTE HACIENDO USO DE BASES DE DATOS.

if (!$cardTokenID || !$amount) {
    echo json_encode(["status" => "error", "message" => "Faltan datos"]);
    exit;
}

$curl = curl_init();

curl_setopt_array($curl, [
  CURLOPT_URL => "https://api.payclip.com/payments",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS => json_encode([
    'amount' => $amount,
    'currency' => 'MXN',
    'description' => 'Pago de producto',
    'payment_method' => [
        'token' => $cardTokenID
    ],
    'customer' => [
        'email' => 'correo@ejemplo.com',
        'phone' => '5555555555'
    ]
  ]),
  CURLOPT_HTTPHEADER => [
    "Authorization: Bearer " . $APIK, // Tu API Key
    "accept: application/json",
    "content-type: application/json"
  ],
]);

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl); 

if ($err) {
    echo json_encode(["status" => "error", "message" => "cURL Error: " . $err]);
} else {
    $result = json_decode($response, true);

    if ($result) {
        // Extraer receipt_no y status si existen
        $receiptNo = isset($result['receipt_no']) ? urlencode($result['receipt_no']) : 'No disponible';
        $status = isset($result['status']) ? $result['status'] : 'No disponible';


        if ($result["status"] === "approved") {
        header("Location: gracias.php?recibo=$receiptNo");

        } elseif ($result["status"] === "rejected") {
            header ("location: error.php");

        } elseif($result["status"] === "pending") {
            $paymentId = urlencode($result["id"]);
            $pendingActionUrl = urlencode($result["pending_action"]["url"]);
            header("Location: 3D.php?payment_id=$paymentId&url=$pendingActionUrl");
            exit();
        } else {
            echo "Error en el pago, intentar de nuevo mas tarde o contactar a soporte.";
        }


        // Devolver respuesta filtrada en JSON
        echo json_encode([
            "status" => $status,
            "receipt_no" => $receiptNo
        ]);
    } else {
        echo json_encode(["status" => "error", "message" => "Respuesta no válida de la API"]);
    }
}
?>
