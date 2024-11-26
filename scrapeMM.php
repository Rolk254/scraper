<?php
// Establece la URL del producto en MediaMarkt
$product_url = "https://www.mediamarkt.es/es/product/_raton-inalambrico-logitech-pro-x-superlight-2-dex-44000-ppp-lightspeed-interruptores-lightforce-95h-bateria-sensor-hero-2-60-gramos-negro-1581350.html";

// Inicializa cURL para obtener la página
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $product_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36');
$html = curl_exec($ch);
curl_close($ch);

// Procesa el HTML con DOMDocument
$doc = new DOMDocument();
libxml_use_internal_errors(true); // Suprime los errores de HTML mal formado
$doc->loadHTML($html);
libxml_clear_errors();

// Usamos XPath para buscar el precio
$xpath = new DOMXPath($doc);
$price = $xpath->query('//*[contains(@data-test, "branded-price-whole-value")]')->item(0);

// Devuelve el precio si lo encontramos
if ($price) {
    return json_encode(['price' => trim($price->nodeValue)]);
} else {
    return json_encode(['error' => 'Precio no encontrado']);
}
?>
