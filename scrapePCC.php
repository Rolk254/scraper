<?php
// Establece la URL del producto en PCComponentes
$product_url = "https://www.pccomponentes.com/logitech-g-pro-x-superlight-2-lightspeed-raton-inalambrico-gaming-negro-32000-dpi";

// Tu clave de API de ScraperAPI
$api_key = 'a8a40f2e41242cb321e7669fbaff056b';  // Reemplázala con tu API Key de ScraperAPI

// Crea la URL para la API de ScraperAPI
$scraperapi_url = "http://api.scraperapi.com?api_key=$api_key&url=" . urlencode($product_url);

// Inicializa cURL para obtener la página a través de ScraperAPI
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $scraperapi_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$html = curl_exec($ch);
curl_close($ch);

// Procesa el HTML con DOMDocument
$doc = new DOMDocument();
libxml_use_internal_errors(true);
$doc->loadHTML($html);
libxml_clear_errors();

// Usamos XPath para obtener la parte entera y decimal del precio
$xpath = new DOMXPath($doc);

// Obtener la parte entera del precio y eliminar el símbolo de moneda si está presente
$whole_price = $xpath->query('//span[@id="pdp-price-current-integer"]')->item(0)?->nodeValue;
$whole_price = str_replace('€', '', $whole_price);

// Aseguramos que el símbolo de moneda sea correcto
$currency_symbol = "€";

// Si se encuentran las partes del precio, se formatea
if ($whole_price) {
    $price = "<div class='price'>" . $currency_symbol . " " . trim($whole_price)."</div>";
    echo $price; // Muestra el precio en formato "10,00€"
} else {
    echo "Precio no encontrado";
}
?>
