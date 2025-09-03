<?php
session_start();

// ===== CONFIG =====
$packages = [
    "0639"=>["name"=>"1000 Views","price"=>5,"paymentlink"=>"https://rzp.io/rzp/AlJPYN3"],
    "0000"=>["name"=>"10000 Views","price"=>25,"paymentlink"=>"https://rzp.io/rzp/qikDbHel"]
];

// URL PARAMETER CHECK
$orderid = $_GET['orderid'] ?? null;
$true_flag = isset($_GET['true']) || isset($_SESSION['payment_done']);

// SHOW FORM CONTROL
$show_form = false;

if($orderid && $true_flag && isset($packages[$orderid])){
    $show_form = true;
    $_SESSION['payment_done'] = true; // Payment done flag
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>ViewBoost Pro - Premium Video Views</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
<style>
  :root {
    --primary: #2563eb;
    --success: #10b981;
    --shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    --transition: all 0.3s ease;
    --light-bg: #f8fafc;
    --dark-text: #1f2937;
    --gray-text: #6b7280;
    --gradient-primary: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    --border-radius: 16px;
  }

  /* Reset */
  *, *::before, *::after {
    box-sizing: border-box;
  }

  body {
    margin: 0;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: linear-gradient(135deg, var(--light-bg) 0%, #e0e7ff 100%);
    color: var(--dark-text);
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 1rem;
  }

  header {
    background: var(--gradient-primary);
    color: white;
    width: 100%;
    max-width: 520px;
    border-radius: var(--border-radius) var(--border-radius) 0 0;
    padding: 2rem 1.5rem;
    text-align: center;
    box-shadow: var(--shadow);
    margin-bottom: 2rem;
  }

  .logo {
    font-size: 2.5rem;
    font-weight: 700;
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 0.5rem;
  }

  .tagline {
    font-size: 1.1rem;
    opacity: 0.85;
    margin-top: 0.25rem;
  }

  .container {
    width: 100%;
    max-width: 520px;
  }

  .card {
    background: white;
    border-radius: var(--border-radius);
    padding: 2rem 2.5rem;
    box-shadow: var(--shadow);
    transition: var(--transition);
  }

  .card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
  }

  .card-title {
    color: var(--primary);
    font-size: 1.75rem;
    font-weight: 700;
    margin-bottom: 1.75rem;
    text-align: center;
  }

  .service-options {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 1.25rem;
    margin-bottom: 2rem;
  }

  .service-option {
    border: 2px solid #e5e7eb;
    border-radius: var(--border-radius);
    padding: 1.5rem 1rem;
    cursor: pointer;
    text-align: center;
    position: relative;
    transition: var(--transition);
    user-select: none;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
  }

  .service-option:hover {
    border-color: var(--primary);
    transform: translateY(-3px);
  }

  .service-option.selected {
    border-color: var(--primary);
    background: linear-gradient(135deg, var(--light-bg) 0%, #e0e7ff 100%);
    box-shadow: 0 5px 15px rgba(37, 99, 235, 0.15);
  }

  .service-badge {
    position: absolute;
    top: -10px;
    right: -10px;
    background: var(--success);
    color: white;
    padding: 5px 10px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    box-shadow: 0 2px 6px rgba(0,0,0,0.15);
  }

  .service-icon {
    font-size: 2.5rem;
    color: var(--primary);
  }

  .service-name {
    font-weight: 600;
    font-size: 1.1rem;
    color: var(--dark-text);
  }

  .service-price {
    color: var(--success);
    font-weight: 700;
    font-size: 1.3rem;
  }

  .btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 1rem 2rem;
    border: none;
    border-radius: var(--border-radius);
    font-size: 1.15rem;
    font-weight: 700;
    cursor: pointer;
    transition: var(--transition);
    text-decoration: none;
    width: 100%;
    background: var(--gradient-primary);
    color: white;
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
  }

  .btn:hover:not(:disabled) {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.5);
  }

  .btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none;
  }

  form {
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
  }

  label.form-label {
    font-weight: 600;
    font-size: 1rem;
    color: var(--dark-text);
  }

  input.form-control {
    padding: 0.75rem 1rem;
    font-size: 1rem;
    border: 2px solid #d1d5db;
    border-radius: var(--border-radius);
    transition: border-color 0.3s ease;
  }

  input.form-control:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 5px var(--primary);
  }

  /* Fade-in animation */
  .fade-in {
    animation: fadeIn 0.6s ease-out forwards;
    opacity: 0;
  }

  @keyframes fadeIn {
    to {
      opacity: 1;
      transform: translateY(0);
    }
    from {
      opacity: 0;
      transform: translateY(20px);
    }
  }

  /* Responsive */
  @media (max-width: 600px) {
    .card {
      padding: 1.5rem 1.5rem;
    }

    .logo {
      font-size: 2rem;
    }

    .service-options {
      grid-template-columns: 1fr;
    }
  }
</style>
</head>
<body>
<header class="fade-in" style="animation-delay: 0.1s;">
  <div class="logo" aria-label="ViewBoost Pro Logo">
    <i class="fas fa-eye" aria-hidden="true"></i> ViewBoost Pro
  </div>
  <div class="tagline">Get Instant Video Views - High Quality Guaranteed</div>
</header>

<div class="container">
<?php if(!$show_form): ?>
  <section class="card fade-in" id="packageCard" style="animation-delay: 0.2s;" aria-label="Select your package">
    <h2 class="card-title">Select Your Package</h2>
    <div class="service-options" role="list">
      <?php foreach($packages as $id=>$pkg): ?>
      <div class="service-option" role="listitem" tabindex="0" aria-pressed="false" data-orderid="<?= htmlspecialchars($id) ?>" data-paymentlink="<?= htmlspecialchars($pkg['paymentlink']) ?>">
        <span class="service-badge"><?= $id=="0639"?"POPULAR":"PREMIUM" ?></span>
        <div class="service-icon" aria-hidden="true"><?= $id=="0639"?"<i class='fas fa-fire'></i>":"<i class='fas fa-crown'></i>" ?></div>
        <div class="service-name"><?= htmlspecialchars($pkg['name']) ?></div>
        <div class="service-price">₹<?= htmlspecialchars($pkg['price']) ?></div>
      </div>
      <?php endforeach; ?>
    </div>
    <button class="btn" id="payButton" disabled aria-disabled="true" aria-label="Proceed to payment">Proceed to Payment</button>
  </section>
<?php else: ?>
  <section class="card fade-in" style="animation-delay: 0.2s;" aria-label="Payment verified form">
    <h2 class="card-title">Payment Verified! Enter Video Link</h2>
    <form action="order.php" method="POST" novalidate>
      <input type="hidden" name="orderid" value="<?= htmlspecialchars($orderid) ?>">
      <div class="form-group">
        <label for="video_link" class="form-label">Video Link</label>
        <input type="url" id="video_link" name="video_link" class="form-control" required placeholder="https://youtube.com/..." aria-required="true" />
      </div>
      <button class="btn" type="submit" aria-label="Submit order">Submit Order</button>
    </form>
  </section>
<?php endif; ?>
</div>

<script>
  let selectedPackage = null;
  const options = document.querySelectorAll('.service-option');
  const payButton = document.getElementById('payButton');

  options.forEach(el => {
    el.addEventListener('click', () => {
      options.forEach(e => {
        e.classList.remove('selected');
        e.setAttribute('aria-pressed', 'false');
      });
      el.classList.add('selected');
      el.setAttribute('aria-pressed', 'true');
      selectedPackage = el.getAttribute('data-orderid');
      payButton.disabled = false;
      payButton.setAttribute('aria-disabled', 'false');
    });

    // Keyboard accessibility: allow selection with Enter or Space
    el.addEventListener('keydown', e => {
      if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        el.click();
      }
    });
  });

  payButton?.addEventListener('click', () => {
    if (!selectedPackage) return;
    const selectedOption = document.querySelector('.service-option.selected');
    const payLink = selectedOption.getAttribute('data-paymentlink');
    const redirectUrl = encodeURIComponent(`https://${window.location.host}/index.php?orderid=${selectedPackage}&true`);
    window.location.href = `${payLink}?redirect=${redirectUrl}`;
  });
</script>
</body>
</html>