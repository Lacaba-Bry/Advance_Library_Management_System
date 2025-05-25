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
  <link rel="stylesheet" href="css/index/register.css">
  <title>Plan Checkout</title>
</head>
<body>
<!-- Registration Form -->
  <div class="container">
    <h1>Complete Your Registration</h1>
    <p>You have selected the <strong id="selectedPlan"><?php echo htmlspecialchars($plan); ?></strong> plan.</p>
    <p>Total: <strong id="selectedPrice">₱<?php echo number_format($price, 2); ?></strong></p>

    <!-- Registration Form -->
    <form id="registerForm" action="backend/registerBack.php" method="POST">
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

    <!-- Premium Plan (was Club) -->
    <div class="card popular">
      <h2>Premium</h2>
      <div class="price">₱199 / month</div>
      <button class="premium-btn" onclick="selectPlan('Premium', 199)">Join Premium</button>
      <div class="features">
        ✓ Everything in Explorer, plus...<br/>
        ✓ Full access to Premium-only content<br/>
        ✓ One-on-one support from experts
      </div>
    </div>

    <!-- VIP Plan -->
    <div class="card vip-card">
      <h2>VIP</h2>
      <div class="price">₱1,599 one-time</div>
      <button class="vip-btn" onclick="selectPlan('VIP', 1599)">Join VIP</button>
      <div class="features">
        ✓ Everything in PREMIUM, plus...<br/>
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
  // Wait for the DOM to fully load before executing the script
  document.addEventListener("DOMContentLoaded", function() {
    // Select the free plan by default when the page loads
    selectPlan('Explorer', 0);  // Default to Explorer (free plan)
    // Ensure the submitButton is enabled for the free plan
    document.getElementById('submitButton').disabled = false;

    // Handle form submission
    document.getElementById('submitButton').addEventListener('click', function() {
      const planId = document.getElementById('plan_id').value;
      const price = parseInt(document.getElementById('selectedPrice').innerText.replace('₱', '').replace(',', ''));
      if (price > 0) {
        openCheckoutModal(getPlanName(planId), price);
      } else {
        document.getElementById('registerForm').submit();
      }
    });
  });

  // Function to get plan name from plan ID
  function getPlanName(planId) {
    switch(planId) {
      case '1':
        return 'Explorer';
      case '2':
        return 'Premium';
      case '3':
        return 'VIP';
    }
  }

  function selectPlan(plan, price) {
    console.log(`Plan Selected: ${plan}, Price: ${price}`);

    // Update the plan and price when the user selects a plan
    document.getElementById('selectedPlan').innerText = plan;
    document.getElementById('selectedPrice').innerText = `₱${price.toLocaleString()}`;

    // Set the hidden plan_id in the form
    const planId = (plan === 'Explorer') ? 1 : (plan === 'Premium') ? 2 : 3; // Assign proper plan ID
    document.getElementById('plan_id').value = planId;

    // Enable the registration button for free plan
    document.getElementById('submitButton').disabled = (price > 0); // Disable for paid plans, enable for free plan

    // If price > 0, show the modal when clicking Register and Pay
    if (price > 0) {
      console.log("Disabling register button for payment");
      // Open the payment modal for paid plans
      openCheckoutModal(plan, price);
    }
  }

  function openCheckoutModal(plan, price) {
    document.getElementById('checkoutModal').classList.add('active');
    document.getElementById('overlay').classList.add('active');

    document.getElementById('checkoutTitle').innerText = `Checkout - ${plan} Plan`;
    document.getElementById('checkoutTotal').innerText = `Total: ₱${price.toLocaleString()}`;
  }

  function submitCheckout() {
    const name = document.getElementById('name').value;
    const card = document.getElementById('card').value;
    const email = document.getElementById('email').value;

    if (!name || !card || !email) {
      alert("All fields are required!");
      return; // Don't proceed if fields are empty
    }

    // Simulate payment success
    alert("Payment Successful!");

    // Enable the registration button after successful payment
    document.getElementById('submitButton').disabled = false;

    // Close the modal after payment
    document.getElementById('checkoutModal').classList.remove('active');
    document.getElementById('overlay').classList.remove('active');

    // Submit the registration form
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
