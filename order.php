<?php
session_start();
if(!isset($_SESSION['payment_done']) || $_SESSION['payment_done'] !== true){
    die("Access denied. Payment not done.");
}

$api_key = "32b3d02ce682fac87c1cd2fc5455e48b";
$api_url = "https://biggestsmmpanel.com/api/v2";

$orderid = $_POST['orderid'] ?? null;
$video_link = $_POST['video_link'] ?? null;

if(!$orderid || !$video_link){
    die("Missing parameters.");
}

$service_map = [
    "0639" => ["service_id" => 4676, "quantity" => 1000],
    "0000" => ["service_id" => 10000, "quantity" => 10000]
];

if(!isset($service_map[$orderid])){
    die("Invalid order ID.");
}

$service_id = $service_map[$orderid]['service_id'];
$quantity = $service_map[$orderid]['quantity'];

$post_data = [
    "key" => $api_key,
    "action" => "add",
    "service" => $service_id,
    "link" => $video_link,
    "quantity" => $quantity
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $api_url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
$response = curl_exec($ch);

if(curl_errno($ch)){
    $error_msg = curl_error($ch);
}
curl_close($ch);

if(isset($error_msg)){
    die("API request failed: " . htmlspecialchars($error_msg));
}

$res = json_decode($response, true);
$api_order_id = $res['order'] ?? null;

if(!$api_order_id){
    die("Failed to place order. Response: " . htmlspecialchars($response));
}

$_SESSION['payment_done'] = false;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Order Success - ViewBoost Pro</title>
<style>
  @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap');

  :root {
    --color-primary: #4f46e5;
    --color-primary-light: #6366f1;
    --color-success: #10b981;
    --color-bg: #f9fafb;
    --color-card-bg: #ffffff;
    --color-text-primary: #111827;
    --color-text-secondary: #6b7280;
    --shadow-light: 0 8px 24px rgba(79, 70, 229, 0.15);
    --border-radius: 20px;
    --transition: all 0.3s ease;
  }

  * {
    box-sizing: border-box;
  }

  body {
    margin: 0;
    font-family: 'Poppins', sans-serif;
    background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%);
    color: var(--color-text-primary);
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    padding: 1rem;
  }

  .card {
    background: var(--color-card-bg);
    border-radius: var(--border-radius);
    box-shadow: var(--shadow-light);
    padding: 3rem 3.5rem;
    max-width: 480px;
    width: 100%;
    text-align: center;
    position: relative;
    overflow: hidden;
    animation: fadeInUp 0.8s ease forwards;
  }

  @keyframes fadeInUp {
    from {
      opacity: 0;
      transform: translateY(30px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  .icon-wrapper {
    background: var(--color-primary-light);
    width: 90px;
    height: 90px;
    border-radius: 50%;
    margin: 0 auto 1.5rem;
    display: flex;
    justify-content: center;
    align-items: center;
    box-shadow: 0 0 15px var(--color-primary-light);
    animation: pulse 2s infinite;
  }

  @keyframes pulse {
    0%, 100% {
      box-shadow: 0 0 15px var(--color-primary-light);
    }
    50% {
      box-shadow: 0 0 30px var(--color-primary);
    }
  }

  .icon-wrapper svg {
    width: 48px;
    height: 48px;
    fill: white;
  }

  h1 {
    font-weight: 600;
    font-size: 2.25rem;
    margin-bottom: 0.5rem;
    color: var(--color-primary);
  }

  p.subtitle {
    font-size: 1.1rem;
    color: var(--color-text-secondary);
    margin-bottom: 2rem;
  }

  .info {
    background: #f3f4f6;
    border-radius: 12px;
    padding: 1.25rem 1.5rem;
    margin-bottom: 2rem;
    box-shadow: inset 0 0 8px rgba(0,0,0,0.05);
    font-weight: 500;
    color: var(--color-text-primary);
    word-break: break-word;
  }

  .info strong {
    color: var(--color-primary);
  }

  .loader {
    margin: 0 auto 2rem;
    width: 60px;
    height: 60px;
    border: 6px solid #e0e7ff;
    border-top: 6px solid var(--color-primary);
    border-radius: 50%;
    animation: spin 1.2s linear infinite;
  }

  @keyframes spin {
    0% {transform: rotate(0deg);}
    100% {transform: rotate(360deg);}
  }

  button.btn-home {
    background: var(--color-primary);
    color: white;
    border: none;
    padding: 0.85rem 2.5rem;
    font-size: 1.1rem;
    font-weight: 600;
    border-radius: 12px;
    cursor: pointer;
    box-shadow: 0 8px 20px rgba(79, 70, 229, 0.3);
    transition: var(--transition);
    user-select: none;
  }

  button.btn-home:hover,
  button.btn-home:focus {
    background: var(--color-primary-light);
    box-shadow: 0 12px 30px rgba(99, 102, 241, 0.5);
    outline: none;
  }

  @media (max-width: 480px) {
    .card {
      padding: 2rem 2rem;
    }
    h1 {
      font-size: 1.8rem;
    }
  }
</style>
</head>
<body>
  <main class="card" role="alert" aria-live="polite" aria-atomic="true">
    <div class="icon-wrapper" aria-hidden="true">
      <!-- Checkmark SVG icon -->
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" >
        <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
      </svg>
    </div>
    <h1>Order Placed Successfully!</h1>
    <p class="subtitle">Thank you for your purchase. Your order is being processed.</p>
    <div class="loader" aria-label="Loading animation"></div>
    <div class="info">
      <p><strong>Order ID:</strong> <?= htmlspecialchars($api_order_id) ?></p>
      <p><strong>Video Link:</strong> <a href="<?= htmlspecialchars($video_link) ?>" target="_blank" rel="noopener noreferrer"><?= htmlspecialchars($video_link) ?></a></p>
    </div>
    <button class="btn-home" onclick="goHome()" aria-label="Go back to home page">Back to Home</button>
  </main>

  <script>
    function goHome() {
      window.location.href = "index.php";
    }

    // Auto redirect after 5 seconds with gentle fade out
    setTimeout(() => {
      document.querySelector('.card').style.opacity = '0';
      setTimeout(() => {
        goHome();
      },