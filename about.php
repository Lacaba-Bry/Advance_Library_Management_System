<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Combined Design</title>
  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
      font-family: 'Segoe UI', sans-serif;
    }

    body {
      background: linear-gradient(to bottom, #f5f5f5, #e0e0ff);
      display: flex;
      flex-direction: column;
      align-items: center;
      padding: 40px 20px;
    }

    .wrapper {
      width: 85%;
      max-width: 1400px;
    }

    /* --- Services Card --- */
    .services-card {
      width: 100%;
      background-color: #6a0dad;
      border-top-left-radius: 40px;
      border-bottom-right-radius: 40px;
      color: white;
      padding: 40px;
      position: relative;
      overflow: hidden;
      margin-bottom: 40px;
    }

    .services-card h2 {
      font-size: 24px;
      margin-bottom: 10px;
    }

    .services-card p {
      font-size: 14px;
      opacity: 0.9;
    }

    .curve-design {
      position: absolute;
      right: 20px;
      bottom: 20px;
      width: 150px;
      height: 150px;
      background: radial-gradient(circle at center, rgba(255,255,255,0.2) 40%, transparent 41%);
      border-radius: 50%;
      box-shadow: 0 0 0 10px rgba(255,255,255,0.15),
                  0 0 0 25px rgba(255,255,255,0.1);
    }

    /* --- Hero Section --- */
    .hero {
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      background: linear-gradient(to right, #f6f2ff, #ffffff);
      padding: 60px;
      border-radius: 30px;
      margin-bottom: 40px;
    }

    .hero-text {
      flex: 1;
    }

    .badge {
      background-color: #e6dbff;
      color: #6a0dad;
      padding: 6px 12px;
      border-radius: 16px;
      font-size: 14px;
      display: inline-block;
      margin-bottom: 16px;
    }

    .hero-text h1 {
      font-size: 42px;
      color: #222;
      margin-bottom: 16px;
    }

    .hero-text p {
      font-size: 16px;
      color: #555;
      margin-bottom: 24px;
    }

    .btn {
      background-color: #6a0dad;
      color: white;
      padding: 12px 24px;
      border-radius: 30px;
      text-decoration: none;
      font-weight: bold;
      display: inline-block;
      margin-top: 16px;
    }

    .btn:hover {
      background-color: #4b0082;
    }

    .stats {
      display: flex;
      gap: 40px;
      font-size: 16px;
      margin-top: 24px;
    }

    .stats span {
      font-weight: bold;
      font-size: 24px;
      color: #6a0dad;
    }

    .hero-media {
      flex: 1;
      display: flex;
      flex-direction: column;
      align-items: flex-end;
      gap: 20px;
    }

    .video-card {
      position: relative;
      width: 200px;
      height: 120px;
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .video-card video {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .play-btn {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      background: white;
      color: #6a0dad;
      border-radius: 50%;
      width: 36px;
      height: 36px;
      text-align: center;
      line-height: 36px;
      font-size: 18px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.2);
      cursor: pointer;
    }

    .doctor-card {
      position: relative;
      background-color: #6a0dad;
      border-radius: 20px;
      border-bottom-right-radius: 100px;
      padding: 20px;
      overflow: hidden;
    }

    .doctor-card img {
      width: 280px;
      border-radius: 12px;
      z-index: 2;
      position: relative;
    }

    .curves {
      position: absolute;
      right: 10px;
      bottom: 10px;
      width: 100px;
      height: 100px;
      border-radius: 50%;
      background: radial-gradient(circle, rgba(255,255,255,0.2) 40%, transparent 41%);
      box-shadow: 0 0 0 10px rgba(255,255,255,0.15),
                  0 0 0 25px rgba(255,255,255,0.1);
      z-index: 1;
    }

    /* --- Library Section --- */
    .library-container {
      display: flex;
      flex-wrap: wrap;
      background: white;
      border-radius: 30px;
      box-shadow: 0 0 30px rgba(0,0,0,0.08);
      overflow: hidden;
      margin-top: 40px;
    }

    .image-side, .text-side {
      flex: 1;
      min-width: 300px;
    }

    .image-side {
      background: linear-gradient(to bottom right, #d8cfff, #eae6ff);
      display: flex;
      align-items: center;
      justify-content: center;
      position: relative;
      padding: 40px;
    }

    .image-side img {
      width: 100%;
      max-width: 330px;
      border-radius: 20px;
    }

    .image-side .badge {
      position: absolute;
      bottom: 30px;
      left: 30px;
      background: white;
      padding: 12px 20px;
      border-radius: 15px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      font-weight: 600;
      font-size: 14px;
      color: #4b0082;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .text-side {
      padding: 60px 40px;
    }

    .text-side small {
      color: #4b0082;
      text-transform: uppercase;
      font-weight: bold;
      font-size: 13px;
    }

    .text-side h1 {
      font-size: 28px;
      color: #1e1e2f;
      margin-top: 10px;
    }

    .text-side p {
      color: #555;
      margin: 15px 0;
      line-height: 1.6;
    }

    .features {
      display: flex;
      flex-wrap: wrap;
      gap: 15px;
      margin-top: 20px;
      font-size: 14px;
      color: #4b0082;
    }

    .features div {
      display: flex;
      align-items: center;
      gap: 5px;
    }

    @media (max-width: 768px) {
      .hero {
        flex-direction: column;
        padding: 40px 20px;
      }

      .hero-media {
        align-items: center;
      }

      .doctor-card img {
        width: 100%;
      }

      .library-container {
        flex-direction: column;
      }

      .text-side, .image-side {
        padding: 20px;
        text-align: center;
      }
    }
  </style>
</head>
<body>

  <div class="wrapper">

    <!-- Services Section -->
    <div class="services-card">
      <h2>Services</h2>
      <p>Dedicated and consulting communication<br>design and brand innovation.</p>
      <div class="curve-design"></div>
    </div>

    <!-- Hero Hospital Section -->
    <section class="hero">
      <div class="hero-text">
        <div class="badge">We are #1 Hospital</div>
        <h1>Healing for Everyone!</h1>
        <p>Dedicated to providing compassionate, high-quality healthcare.</p>
        <a href="#" class="btn">FINDING A DOCTOR →</a>
        <div class="stats">
          <div><span>45+</span><br>Years of Care</div>
          <div><span>200K+</span><br>Patient Served</div>
        </div>
      </div>
      <div class="hero-media">
        <div class="video-card">
          <video src="video.mp4" autoplay muted loop></video>
          <div class="play-btn">&#9658;</div>
        </div>
        <div class="doctor-card">
          <img src="hero.jpg" alt="Doctor" />
          <div class="curves"></div>
        </div>
      </div>
    </section>

    <!-- Library Section -->
    <div class="library-container">
      <div class="image-side">
        <img src="bb.jpg" alt="Library Illustration" />
        <div class="badge">
          📚 Accomplishments: Over 1M books issued
        </div>
      </div>
      <div class="text-side">
        <small>About Us</small>
        <h1>For over 45+ years, we’ve been the cornerstone of knowledge</h1>
        <p>We provide a state-of-the-art library management system, enabling seamless book access, inventory control, and a smooth reader experience. From digital archives to modern reading lounges, we make learning accessible and enjoyable.</p>
        <div class="features">
          <div>🗂 Digital Catalog</div>
          <div>📖 Reading Zones</div>
          <div>💻 Online Reservations</div>
          <div>👩‍💼 Friendly Staff</div>
        </div>
        <a href="#" class="btn">Learn More →</a>
      </div>
    </div>

  </div>

  <script>
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if(entry.isIntersecting){
          entry.target.classList.add('animate');
        }
      });
    });
    document.querySelectorAll('.hero-text, .hero-media, .text-side, .image-side').forEach(el => observer.observe(el));
  </script>
</body>
</html>
