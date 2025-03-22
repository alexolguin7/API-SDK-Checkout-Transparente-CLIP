<?php
if (!isset($_GET["payment_id"]) || !isset($_GET["url"])) {
    die("Error: No se encontraron los datos de autenticación.");
}

$paymentId = htmlspecialchars($_GET["payment_id"]); 
$authUrl = htmlspecialchars($_GET["url"]);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Autenticación 3D Secure</title>
</head>
<body>
    <h2>Por favor, completa la autenticación 3DS</h2>

    <iframe src="<?= $authUrl ?>" style="width: 100vw; height: 100vh; border: none;"></iframe>

    <script>
        window.addEventListener("message", function(event) {
            // Validar el origen de la respuesta (evita ataques)
            if (event.origin !== new URL("<?= $authUrl ?>").origin) {
                return;
            }

            if (event.data?.paymentId) {
                const returnedPaymentId = event.data.paymentId;
                console.log("Returned Payment ID:", returnedPaymentId);

                if (returnedPaymentId === "<?= $paymentId ?>") {
                    window.location.href = "verificar_pago.php?payment_id=" + returnedPaymentId;
                } else {
                    alert("El Payment ID no coincide. Intenta nuevamente.");
                }
            }
        });
    </script>
</body>
</html>
