<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Plan Checkout</title>
  <style>
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

    #checkoutModal {
      display: none;
      max-width: 400px;
      margin: 40px auto;
      padding: 50px;
      background: white;
      border-radius: 10px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.1);
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
  </style>
</head>
<body>
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
      <div class="price">
        <span id="club-price">₱370</span><span id="price-unit"> / month</span>
      </div>
      <button class="premium-btn" onclick="selectPlan('Club', 370)">Join Club</button>
      <div class="features">
        ✓ Everything in Explorer, plus...<br/>
        ✓ Full access to Club-only content<br/>
        ✓ One-on-one support from experts<br/>
        ✓ and more!
      </div>
    </div>

    <!-- VIP Plan -->
    <div class="card vip-card">
      <h2>VIP</h2>
      <div class="price">
        <span id="vip-price">₱19,999</span><span id="vip-unit"> one-time</span>
      </div>
      <button class="vip-btn" onclick="selectPlan('VIP', 19999)">Join VIP</button>
      <div class="features">
        ✓ Everything in CLUB, plus...<br/>
        ✓ Exclusive workshops & mentorship<br/>
        ✓ Personalized career roadmap<br/>
        ✓ VIP badge & early access to features
      </div>
    </div>
  </div>

  <!-- Checkout Modal -->
  <div id="checkoutModal">
    <h2 id="checkoutTitle">Checkout</h2>
    <div class="checkout-field">
      <label for="name">Full Name</label>
      <input type="text" id="name" placeholder="Your name" />
    </div>
    <div class="checkout-field">
      <label for="card">Card Number</label>
      <input type="text" id="card" placeholder="1234 5678 9012 3456" />
    </div>
    <div class="checkout-field">
      <label for="email">Email</label>
      <input type="email" id="email" placeholder="your@email.com" />
    </div>
    <div class="total" id="checkoutTotal">Total: ₱0</div>
    <button class="checkout-submit" onclick="submitCheckout()">Pay Now</button>
  </div>

  <script>
    function selectPlan(plan, price) {
      if (price === 0) {
        alert("Registered with Free Plan!");
        return;
      }

      document.getElementById("checkoutModal").classList.add("active");
      document.getElementById("checkoutTitle").innerText = `Checkout - ${plan} Plan`;
      document.getElementById("checkoutTotal").innerText = `Total: ₱${price.toLocaleString()}`;
    }

    function submitCheckout() {
      alert("Payment submitted!");
    }
  </script>
</body>
</html>
