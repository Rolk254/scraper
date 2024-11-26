<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Precio del Raton - MediaMarkt, Amazon y PCC</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
        }

        h1 {
            color: #333;
            text-align: center;
        }

        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 25%;
            margin: 0 auto;
            margin-bottom: 20px;
            padding: 15px;
        }

        .price {
            font-size: 2em;
            font-weight: bold;
            color: #e74c3c;
            text-align: center;
        }

        .error {
            color: #e74c3c;
            font-weight: bold;
            text-align: center;
        }

        .button {
            display: block;
            width: 100%;
            padding: 10px;
            background-color: #3498db;
            color: white;
            text-align: center;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 20px;
        }

        .button:hover {
            background-color: #2980b9;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function updatePrice(source) {
            var url;
            switch (source) {
                case 'MM':
                    url = 'scrapeMM.php';
                    break;
                case 'AZ':
                    url = 'scrapeAZ.php';
                    break;
                case 'PCC':
                    url = 'scrapePCC.php';
                    break;
                default:
                    return;
            }

            $.ajax({
                url: url,
                method: 'GET',
                success: function (data) {
                    var priceData = JSON.parse(data);
                    if (priceData.price) {
                        var price = parseFloat(priceData.price.replace(',', ''));
                        $('#' + source + '-price').html('€ ' + price.toFixed(2));
                    } else {
                        $('#' + source + '-price').html('<div class="error">Error: ' + priceData.error + '</div>');
                    }
                },
                error: function () {
                    $('#' + source + '-price').html('<div class="error">Error al obtener el precio</div>');
                }
            });
        }
    </script>
</head>

<body>
    <div class="container">
        <a target=”_blank” href="https://www.mediamarkt.es/es/product/_raton-inalambrico-logitech-pro-x-superlight-2-dex-44000-ppp-lightspeed-interruptores-lightforce-95h-bateria-sensor-hero-2-60-gramos-negro-1581350.html"><h1>Logitech Superlight 2 - MediaMarkt</h1></a>
        <div id="MM-price" class="price-container">
            <?php
            $price_data = include('scrapeMM.php');
            $price_data = json_decode($price_data, true);
            if (isset($price_data['price'])) {
                $price = trim($price_data['price']);
                $price = str_replace(',', '', $price);
                $price = (float) $price;
                echo "<div class='price'>€ " . number_format($price, 2) . "</div>";
            } else {
                echo "<div class='error'>Error: " . $price_data['error'] . "</div>";
            }
            ?>
        </div>
        <button class="button" onclick="updatePrice('MM');">Actualizar Precio</button>
    </div>
    <div class="container">
        <a target=”_blank” href="https://www.amazon.es/Logitech-SUPERLIGHT-Inal%C3%A1mbrico-interruptores-programables/dp/B07W5JKP66"><h1>Logitech Superlight 2 - Amazon</h1></a>
        <div id="AZ-price" class="price-container">
            <?php
            $price_data = include('scrapeAZ.php');
            $price_data = json_decode($price_data, true);
            if (isset($price_data['price'])) {
                $price = trim($price_data['price']);
                $price = str_replace(',', '', $price);
                $price = (float) $price;
                echo "<div class='price'>€ " . number_format($price, 2) . "</div>";
            }
            ?>
        </div>
        <button class="button" onclick="updatePrice('AZ');">Actualizar Precio</button>
    </div>
    <div class="container">
        <a target=”_blank” href="https://www.pccomponentes.com/logitech-g-pro-x-superlight-2-lightspeed-raton-inalambrico-gaming-negro-32000-dpi"><h1>Logitech Superlight 2 - PCC</h1></a>
        <div id="PCC-price" class="price-container">
            <?php
            $price_data = include('scrapePCC.php');
            $price_data = json_decode($price_data, true);
            if (isset($price_data['price'])) {
                $price = trim($price_data['price']);
                $price = str_replace(',', '', $price);
                $price = (float) $price;
                echo "<div class='price'>€ " . number_format($price, 2) . "</div>";
            }
            ?>
        </div>
        <button class="button" onclick="updatePrice('PCC');">Actualizar Precio</button>
    </div>
</body>

</html>