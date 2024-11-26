<?php
// Establece la URL del producto en Amazon
$product_url = "https://www.amazon.es/Logitech-SUPERLIGHT-Inal%C3%A1mbrico-interruptores-programables/dp/B07W5JKP66";

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

// Usamos XPath para obtener la parte entera, decimal y símbolo del precio
$xpath = new DOMXPath($doc);

// Obtener la parte entera del precio
$whole_price = $xpath->query('//span[@class="a-price-whole"]')->item(0)?->nodeValue;

// Obtener la parte decimal (centavos)
$fraction_price = $xpath->query('//span[@class="a-price-fraction"]')->item(0)?->nodeValue;

// Aseguramos que el símbolo de moneda sea correcto
$currency_symbol = "€";

// Si se encuentran las partes del precio, se formatea
if ($whole_price && $fraction_price) {
    $price = "<div class='price'>" . $currency_symbol . " " . trim($whole_price) . trim($fraction_price) . "</div>";
    echo $price; // Muestra el precio en formato "10,00€"
} else {
    echo "Precio no encontrado";
}
?>
