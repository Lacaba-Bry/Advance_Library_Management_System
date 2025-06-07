<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Join the Club</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
  <style>
    :root {
      --base-clr: #ffffff;
      --text-clr: #333333;
      --accent-clr: #5e63ff;
      --secondary-text-clr: #6c6f80;
      --line-clr: #ddd;
      --hover-clr: #f4f4f4;
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Poppins', sans-serif;
      background: var(--base-clr);
      color: var(--text-clr);
      padding: 40px 20px;
      display: flex;
      flex-direction: column;
      align-items: center;
    }

    h1 {
      margin-top: 100px;
      font-size: 28px;
      font-weight: 600;
      margin-bottom: 10px;
    }

    p.subtitle {
      font-size: 16px;
      color: var(--secondary-text-clr);
      margin-bottom: 30px;
      text-align: center;
      max-width: 600px;
    }

    .toggle-buttons {
      display: flex;
      gap: 10px;
      margin-bottom: 30px;
    }

    .toggle-buttons button {
      background: var(--hover-clr);
      color: var(--text-clr);
      border: 1px solid var(--line-clr);
      padding: 10px 20px;
      border-radius: 6px;
      font-size: 14px;
      cursor: pointer;
      transition: background 0.3s ease;
    }

    .toggle-buttons button:hover {
      background: var(--accent-clr);
      color: white;
    }

    .active-toggle {
      background: var(--accent-clr);
      color: white;
      border-color: var(--accent-clr);
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

    .permanent {
      display: inline-block;
      font-size: 12px;
      color: #999;
      margin-left: 5px;
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

    .features {
      text-align: left;
      font-size: 14px;
      margin-top: 20px;
      color: var(--secondary-text-clr);
      line-height: 1.6;
    }

    .trusted {
      margin-top: 60px;
      text-align: center;
      font-size: 14px;
      color: var(--secondary-text-clr);
    }

    .logos {
      display: flex;
      gap: 40px;
      margin-top: 20px;
      flex-wrap: wrap;
      justify-content: center;
    }

    .logos img {
      max-height: 30px;
      filter: grayscale(1) brightness(1.6);
      opacity: 0.8;
    }

    @media (max-width: 768px) {
      .pricing-cards {
        flex-direction: column;
        align-items: center;
      }

      .card {
        width: 90%;
      }
    }

    .close-link {
      position: absolute;
      top: 30px;
      right: 80px;
      font-size: 50px;
      font-weight: bold;
      color: var(--secondary-text-clr);
      text-decoration: none;
      z-index: 1001;
      transition: color 0.2s ease;
    }

    .close-link:hover {
      color: var(--accent-clr);
    }
  </style>
</head>
<body>
<a href="index.php" class="close-link">×</a>
<h1>Join the Haven Library!</h1>
<p class="subtitle">Unlock your full potential with our tailored plans designed for every stage of your coding journey.</p>

<div class="toggle-buttons">
  <button id="monthly-btn" class="active-toggle">Monthly</button>
  <button id="yearly-btn">Yearly</button>
</div>

<div class="pricing-cards">
  <!-- Free Plan -->
  <div class="card">
    <h2>Explorer</h2>
    <div class="price">Free <span>forever</span></div>
    <button class="free-btn">Start for free</button>
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
    <button class="premium-btn">Join Club</button>
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
    <button class="vip-btn">Join VIP</button>
    <div class="features">
      ✓ Everything in CLUB, plus...<br/>
      ✓ Exclusive workshops & mentorship<br/>
      ✓ Personalized career roadmap<br/>
      ✓ VIP badge & early access to features
    </div>
  </div>
</div>

<div class="trusted">
  Trusted by learners from
  <div class="logos">
    <img src="https://upload.wikimedia.org/wikipedia/commons/3/3f/Logo_HackNYU_2019.png" alt="HackNYU"/>
    <img src="https://cdn.boces.org/images/logo_sullivan-boces.png" alt="Sullivan BOCES"/>
    <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Uber_Eats_2020_logo.svg" alt="Uber Eats"/>
    <img src="https://upload.wikimedia.org/wikipedia/commons/8/84/Spotify_icon.svg" alt="Spotify"/>
  </div>
</div>

<script>
  const monthlyBtn = document.getElementById('monthly-btn');
  const yearlyBtn = document.getElementById('yearly-btn');
  const priceElement = document.getElementById('club-price');
  const priceUnit = document.getElementById('price-unit');
  const vipPrice = document.getElementById('vip-price');
  const vipUnit = document.getElementById('vip-unit');

  // Set VIP plan as permanent by default
  vipPrice.textContent = '₱19,999';
  vipUnit.textContent = ' one-time';

  function clearActive() {
    monthlyBtn.classList.remove('active-toggle');
    yearlyBtn.classList.remove('active-toggle');
  }

  monthlyBtn.addEventListener('click', () => {
    clearActive();
    monthlyBtn.classList.add('active-toggle');
    priceElement.textContent = '₱370';
    priceUnit.textContent = ' / month';
  });

  yearlyBtn.addEventListener('click', () => {
    clearActive();
    yearlyBtn.classList.add('active-toggle');
    priceElement.textContent = '₱3,700';
    priceUnit.textContent = ' / year';
  });
</script>

</body>
</html>
