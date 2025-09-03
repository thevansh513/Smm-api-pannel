<?php
// smm_order_index.php for Wasmer

// Enable debug for Wasmer
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Configuration
$API_URL = 'https://biggestsmmpanel.com/api/v2';
$API_KEY = '32b3d02ce682fac87c1cd2fc5455e48b';
$DEFAULT_SERVICE = 4676;
$LOG_DIR = __DIR__ . '/orders_logs';
$LOG_FILE = $LOG_DIR . '/orders.json';

// Ensure log directory exists
if (!is_dir($LOG_DIR)) {
    if (!mkdir($LOG_DIR, 0755, true)) {
        die(json_encode(['success'=>false,'message'=>'Cannot create logs folder']));
    }
}

// Read & sanitize input
$video = isset($_GET['video']) ? trim($_GET['video']) : null;
$serviceid = isset($_GET['serviceid']) && is_numeric($_GET['serviceid']) ? (int)$_GET['serviceid'] : $DEFAULT_SERVICE;
$quantity = isset($_GET['quantity']) && is_numeric($_GET['quantity']) ? (int)$_GET['quantity'] : 0;

// Validate input
$errors = [];
if (empty($video)) $errors[] = 'Missing parameter: video';
if ($quantity <= 0) $errors[] = 'Quantity must be > 0';

// Create local order record
$local_order = [
    'local_order_id' => uniqid('local_'),
    'video' => $video,
    'serviceid' => $serviceid,
    'quantity' => $quantity,
    'received_at' => date('c'),
    'status' => empty($errors) ? 'processing' : 'error',
    'errors' => $errors
];

// Append to local log
$existing = [];
if (file_exists($LOG_FILE)) {
    $json = file_get_contents($LOG_FILE);
    if ($json) $existing = json_decode($json, true) ?: [];
}
$existing[] = $local_order;
file_put_contents($LOG_FILE, json_encode($existing, JSON_PRETTY_PRINT));

// Return error if validation failed
header('Content-Type: application/json');
if (!empty($errors)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid parameters',
        'errors' => $errors,
        'local_order' => $local_order
    ], JSON_PRETTY_PRINT);
    exit;
}

// Build API request
$query_params = http_build_query([
    'key' => $API_KEY,
    'action' => 'add',
    'service' => $serviceid,
    'link' => $video,
    'quantity' => $quantity
]);
$call_url = rtrim($API_URL, '/') . '/?' . $query_params;

// Make API call
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $call_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // avoid SSL issues in Wasmer
$api_response = curl_exec($ch);
$curl_err = curl_error($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Prepare response
$response_payload = [
    'success' => false,
    'message' => $curl_err ? 'cURL error: ' . $curl_err : 'No response from API',
    'local_order' => $local_order,
    'api_call' => $call_url,
    'api_http_code' => $http_code,
    'api_raw_response' => $api_response,
    'api_decoded' => $api_response ? json_decode($api_response, true) : null
];

// Check API response for order ID
$decoded = $response_payload['api_decoded'];
if (is_array($decoded) && (isset($decoded['order']) || isset($decoded['order_id']) || isset($decoded['id']))) {
    $order_from_api = $decoded['order'] ?? ($decoded['order_id'] ?? $decoded['id']);
    $response_payload['success'] = true;
    $response_payload['message'] = 'Order placed successfully';
    $response_payload['api_order_id'] = $order_from_api;

    // Update local log
    $local_order['api_order_id'] = $order_from_api;
    $local_order['status'] = 'placed';
    $local_order['api_response'] = $decoded;
    $existing[count($existing)-1] = $local_order;
    file_put_contents($LOG_FILE, json_encode($existing, JSON_PRETTY_PRINT));
} elseif (!$curl_err) {
    $response_payload['success'] = true;
    $response_payload['message'] = 'API returned response (inspect api_raw_response/api_decoded).';
}

// Return JSON response
echo json_encode($response_payload, JSON_PRETTY_PRINT);
exit;
