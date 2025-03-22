<?php require_once "api.php"; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pago con Tarjeta</title>
    <script src="https://sdk.clip.mx/js/clip-sdk.js"></script>
</head>
<body>

    <h1>Pago con Tarjeta</h1>
    
    <form id="payment-form">
        <div id="checkout"></div>
        <input type="hidden" id="product_id" value="12345">
        <button type="submit">Pagar</button>
    </form>

    <script>
        const API_KEY = "<?php echo $APIK; ?>";
        const clip = new ClipSDK(API_KEY);

        const card = clip.element.create("Card", {
            theme: "light",
            locale: "es",
        });
        card.mount("checkout");

        document.querySelector("#payment-form").addEventListener("submit", async (event) => {
            event.preventDefault();

            try {
                const cardToken = await card.cardToken();
                const cardTokenID = cardToken.id;
                const productID = document.getElementById("product_id").value;

                // Redirigir a pagar.php con los datos
                const form = document.createElement("form");
                form.method = "POST";
                form.action = "pagar.php";

                // Crear inputs ocultos con los datos
                const inputToken = document.createElement("input");
                inputToken.type = "hidden";
                inputToken.name = "cardTokenID";
                inputToken.value = cardTokenID;

                const inputProduct = document.createElement("input");
                inputProduct.type = "hidden";
                inputProduct.name = "productID";
                inputProduct.value = productID;

                form.appendChild(inputToken);
                form.appendChild(inputProduct);
                document.body.appendChild(form);
                form.submit();

            } catch (error) {
                alert("Error al procesar la tarjeta. Intente de nuevo.");
                console.error("Error:", error);
            }
        });



    </script>

</body>
</html>
