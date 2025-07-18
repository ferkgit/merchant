<?php
// KONFIGURACIJA
$api_key = 'qpu2DCnJpMUy2q4vCi6FLNifg';
$api_secret = '5ed8df919c46aaaf86fb67c525feb8fd';
$shop_url = 'https://opremazanavodnjavanje.rs';

$auth = base64_encode("$api_key:$api_secret");

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "$shop_url/api/v2/orders?include=products");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Basic $auth",
    "Accept: application/json"
]);

$response = curl_exec($ch);
curl_close($ch);

$data = json_decode($response, true);
$formatted_orders = [];

foreach ($data['data'] as $order) {
    $formatted = [
        "id" => $order["id"],
        "order_status" => $order["status"],
        "order_date" => substr($order["created_at"], 0, 10),
        "last_name" => $order["shipping_address"]["last_name"] ?? '',
        "shipping_method" => $order["shipping_method_name"] ?? "Standard",
        "order_total" => floatval($order["total"] ?? 0),
        "products" => []
    ];

    foreach ($order["products"] as $item) {
        $formatted["products"][] = [
            "product_id" => $item["product_id"],
            "sku" => $item["sku"] ?? "",
            "name" => $item["name"],
            "description" => $item["description"] ?? "",
            "price" => floatval($item["price"]),
            "quantity" => intval($item["quantity"]),
            "stock_quantity" => 0,
            "image_url" => $item["image"] ?? "",
            "categories" => []
        ];
    }

    $formatted_orders[] = $formatted;
}

header('Content-Type: application/json');
echo json_encode($formatted_orders, JSON_PRETTY_PRINT);
?>
