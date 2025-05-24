<?php
// Check if plan and price are passed in the URL, and provide default values if not
$plan = isset($_GET['plan']) ? $_GET['plan'] : 'N/A';
$price = isset($_GET['price']) ? $_GET['price'] : 0.00;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Plan Checkout</title>
  <style>
    /* Your existing CSS here */
    :root {
      --base-clr: #ffffff;
      --text-clr: #333333;
      --accent-clr: #5e63ff;
      --secondary-text-clr: #6c6f80;
      --line-clr: #ddd;
      --hover-clr: #f4f4f4;
    }

    body {
      font-family: 'Segoe UI', sans-serif;
      margin: 0;
      padding: 0;
      background: #f9f9f9;
    }

    .container {
      max-width: 900px;
      margin: 40px auto;
      padding: 20px;
      text-align: center;
    }

    .pricing-cards {
      display: flex;
      flex-wrap: wrap;
      gap: 30px;
      justify-content: center;
    }

    .card {
      background: #fff;
      border: 1px solid var(--line-clr);
      border-radius: 12px;
      padding: 30px;
      width: 280px;
      text-align: center;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
      transition: transform 0.3s ease;
    }

    .card:hover {
      transform: translateY(-5px);
    }

    .card.popular {
      border: 2px solid var(--accent-clr);
      box-shadow: 0 6px 12px rgba(94, 99, 255, 0.3);
    }

    .card.vip-card {
      border: 2px solid #d4af37;
      background-color: #fffbea;
      box-shadow: 0 6px 12px rgba(212, 175, 55, 0.3);
    }

    .card h2 {
      font-size: 20px;
      margin-bottom: 10px;
      color: var(--accent-clr);
    }

    .card.vip-card h2 {
      color: #c49b28;
    }

    .price {
      font-size: 24px;
      font-weight: 600;
      margin: 10px 0 20px;
    }

    .price span {
      font-size: 14px;
      color: var(--secondary-text-clr);
    }

    .features {
      margin-top: 15px;
      font-size: 14px;
      color: var(--secondary-text-clr);
      line-height: 1.6;
    }

    .card button {
      padding: 12px 20px;
      font-size: 14px;
      font-weight: 600;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      transition: background 0.3s ease;
    }

    .free-btn {
      background: transparent;
      color: var(--accent-clr);
      border: 1px solid var(--accent-clr);
    }

    .free-btn:hover {
      background: var(--accent-clr);
      color: white;
    }

    .premium-btn {
      background: var(--accent-clr);
      color: white;
    }

    .premium-btn:hover {
      background: #4a4eff;
    }

    .vip-btn {
      background: #d4af37;
      color: white;
    }

    .vip-btn:hover {
      background: #c49b28;
    }

    /* Checkout Modal */
    #checkoutModal {
      display: none;
      max-width: 400px;
      margin: 40px auto;
      padding: 50px;
      background: white;
      border-radius: 10px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.1);
      position: fixed;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      z-index: 999;
    }

    #checkoutModal.active {
      display: block;
    }

    .checkout-field {
      margin-bottom: 15px;
      text-align: left;
    }

    .checkout-field label {
      display: block;
      margin-bottom: 5px;
    }

    .checkout-field input {
      width: 100%;
      padding: 10px;
      border-radius: 5px;
      border: 1px solid #ccc;
    }

    .total {
      font-size: 18px;
      font-weight: bold;
      margin-top: 15px;
    }

    .checkout-submit {
      margin-top: 20px;
      background: #22c55e;
      color: white;
      padding: 10px 25px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }

    /* Form Page Styles */
    form {
      max-width: 400px;
      margin: 0 auto;
      background-color: #fff;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    }

    form h2 {
      text-align: center;
      color: var(--accent-clr);
      margin-bottom: 20px;
    }

    form input {
      width: 100%;
      padding: 12px;
      border: 1px solid var(--line-clr);
      border-radius: 8px;
      margin-bottom: 15px;
      font-size: 14px;
    }

    form button {
      width: 100%;
      padding: 12px;
      background-color: var(--accent-clr);
      color: white;
      border: none;
      border-radius: 8px;
      font-size: 16px;
      cursor: pointer;
      font-weight: 600;
      transition: background-color 0.3s ease;
    }

    form button:hover {
      background-color: #4a4eff;
    }

    /* Background blur when modal is active */
    #overlay {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.5);
      backdrop-filter: blur(5px);
      display: none;
      z-index: 998;
    }

    #overlay.active {
      display: block;
    }
  </style>
</head>
<body>
<!-- Registration Form -->
  <div class="container">
    <h1>Complete Your Registration</h1>
    <p>You have selected the <strong id="selectedPlan"><?php echo htmlspecialchars($plan); ?></strong> plan.</p>
    <p>Total: <strong id="selectedPrice">₱<?php echo number_format($price, 2); ?></strong></p>

    <!-- Registration Form -->
    <form id="registerForm" action="backend/registerBack.php" method="POST" onsubmit="return false;">
      <input type="email" name="email" placeholder="Email" required />
      <input type="password" name="password" placeholder="Password" required />
      <input type="text" name="name" placeholder="Full Name" required />

      <!-- Hidden Plan ID Field (it will be set based on plan selection) -->
      <input type="hidden" name="plan_id" id="plan_id" value="1" /> <!-- Default to Free plan -->

      <button type="submit" id="submitButton" disabled>Register and Pay</button>
    </form>

  </div>

  <!-- Pricing Cards for Plan Selection -->
  <div class="container">
    <h1>Join the Haven Library!</h1>
    <p>Choose a plan to continue</p>
  </div>

  <div class="pricing-cards">
    <!-- Free Plan -->
    <div class="card">
      <h2>Explorer</h2>
      <div class="price">Free <span>forever</span></div>
      <button class="free-btn" onclick="selectPlan('Explorer', 0)">Start for free</button>
      <div class="features">
        ✓ Access to first few chapters in many courses<br/>
        ✓ Access to community and events
      </div>
    </div>

    <!-- Premium Plan -->
    <div class="card popular">
      <h2>CLUB</h2>
      <div class="price">₱199 / month</div>
      <button class="premium-btn" onclick="selectPlan('Club', 199)">Join Club</button>
      <div class="features">
        ✓ Everything in Explorer, plus...<br/>
        ✓ Full access to Club-only content<br/>
        ✓ One-on-one support from experts
      </div>
    </div>

    <!-- VIP Plan -->
    <div class="card vip-card">
      <h2>VIP</h2>
      <div class="price">₱1,599 one-time</div>
      <button class="vip-btn" onclick="selectPlan('VIP', 1599)">Join VIP</button>
      <div class="features">
        ✓ Everything in CLUB, plus...<br/>
        ✓ Exclusive workshops & mentorship<br/>
        ✓ Personalized career roadmap
      </div>
    </div>
  </div>


  <!-- Overlay for blur effect -->
  <div id="overlay"></div>

<!-- Checkout Modal -->
<div id="checkoutModal">
  <h2 id="checkoutTitle">Checkout</h2>
  <div class="checkout-field">
    <label for="name">Full Name</label>
    <input type="text" id="name" placeholder="Your name" required />
  </div>
  <div class="checkout-field">
    <label for="card">Card Number</label>
    <input type="text" id="card" placeholder="1234 5678 9012 3456" required />
  </div>
  <div class="checkout-field">
    <label for="email">Email</label>
    <input type="email" id="email" placeholder="your@email.com" required />
  </div>
  <div class="total" id="checkoutTotal">Total: ₱0</div>
  <button class="checkout-submit" onclick="submitCheckout()">Pay Now</button>
</div>


<script>
  function selectPlan(plan, price) {
  // Update the plan and price when the user selects a plan
  document.getElementById('selectedPlan').innerText = plan;
  document.getElementById('selectedPrice').innerText = `₱${price.toLocaleString()}`;

  // Set the hidden plan_id in the form
  const planId = (plan === 'Explorer') ? 1 : (plan === 'Club') ? 2 : 3; // Assign proper plan ID
  document.getElementById('plan_id').value = planId;

  // If price > 0, show the modal when clicking Register and Pay
  if (price > 0) {
    openCheckoutModal(plan, price);
  } else {
    // If it's a free plan, just allow registration
    document.getElementById('submitButton').disabled = false;
  }
}

function openCheckoutModal(plan, price) {
  // Open the modal when the user selects a plan with price > 0
  document.getElementById('checkoutModal').classList.add('active');
  document.getElementById('overlay').classList.add('active');

  // Update the checkout modal with the selected plan and price
  document.getElementById('checkoutTitle').innerText = `Checkout - ${plan} Plan`;
  document.getElementById('checkoutTotal').innerText = `Total: ₱${price.toLocaleString()}`;
}

function submitCheckout() {
  const name = document.getElementById('name').value;
  const card = document.getElementById('card').value;
  const email = document.getElementById('email').value;

  // Validate that all fields are filled
  if (!name || !card || !email) {
    alert("All fields are required!");
    return; // Don't proceed if fields are empty
  }

  // Simulate a successful payment process
  alert("Payment Successful!");

  // Change the registration form to show the successful payment
  document.getElementById('selectedPrice').style.color = 'green';  // Make the total green after success

  // Close the modal and proceed to registration
  document.getElementById('checkoutModal').classList.remove('active');
  document.getElementById('overlay').classList.remove('active');

  // Enable registration form submission
  document.getElementById('submitButton').disabled = false; // Allow registration and payment

  // Automatically submit the form after payment success
  document.getElementById('registerForm').submit();
}

// Close modal and background blur when clicked outside
document.getElementById('overlay').onclick = function() {
  document.getElementById('checkoutModal').classList.remove('active');
  document.getElementById('overlay').classList.remove('active');
}
</script>

</body>
</html>
