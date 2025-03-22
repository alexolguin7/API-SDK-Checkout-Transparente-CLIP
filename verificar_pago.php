<?php
require_once "api.php";
if (!isset($_GET["payment_id"])) {
    die("Error: No se encontró el Payment ID.");
}

$paymentId = htmlspecialchars($_GET["payment_id"]);

$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => "https://api.payclip.com/payments/" . $paymentId,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        "Content-Type: application/json",
        "Authorization: Bearer " . $APIK
    ],
]);

$response = curl_exec($curl);
$err = curl_error($curl);
curl_close($curl);

if ($err) {
    die("Error al verificar el pago: " . $err);
}

$result = json_decode($response, true);

// Evaluar el resultado del pago
if ($result["status"] === "approved") {
    $receiptNo = isset($result['receipt_no']) ? urlencode($result['receipt_no']) : 'No disponible';
    header("Location: gracias.php?recibo=$receiptNo");
    exit();
} else {
    echo "<h2>El pago no fue aprobado</h2>";
    echo "<p>Motivo: " . htmlspecialchars($result["status_detail"]["message"]) . "</p>";
}
?>
