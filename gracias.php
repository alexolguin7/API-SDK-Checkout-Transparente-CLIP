<?php
$receiptNo = isset($_GET["recibo"]) ? htmlspecialchars($_GET["recibo"]) : "No disponible";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gracias por su compra</title>
</head>
<body>
    <h1>¡Pago Exitoso!</h1>
    <p>Gracias por su compra, su número de recibo es: <strong><?php echo $receiptNo; ?></strong></p>
    <a href="index.php">Volver a la tienda</a>
</body>
</html>
