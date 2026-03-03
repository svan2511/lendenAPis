<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Lenden – Smart Billing App</title>

<!-- Favicon -->
<link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon-16x16.png') }}">
<link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon-32x32.png') }}">
<link rel="icon" type="image/png" sizes="48x48" href="{{ asset('images/favicon-48x48.png') }}">
<link rel="shortcut icon" href="{{ asset('images/favicon.ico') }}">
<link rel="icon" href="data:image/svg+xml,
<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'>
<rect width='100' height='100' fill='%230EA5A4'/>
<text x='50%' y='78%' font-size='85' font-weight='900' text-anchor='middle' fill='white'>L</text>
</svg>">

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
*{
  margin:0;
  padding:0;
  box-sizing:border-box;
  font-family:'Poppins',sans-serif;
}

body{
  background:#F1F5F9;
  color:#0F172A;
}

/* ===== HERO ===== */

.hero{
  padding:110px 0;
  background:linear-gradient(135deg,#2563EB,#0EA5A4);
  color:#fff;
}

.container{
  max-width:1150px;
  margin:auto;
  padding:0 20px;
}

.hero-grid{
  display:grid;
  grid-template-columns:1.1fr 0.9fr;
  gap:70px;
  align-items:center;
}

/* ===== TEXT ===== */

.hero h1{
  font-size:44px;
  margin-bottom:18px;
  line-height:1.2;
}

.hero p{
  font-size:17px;
  margin-bottom:22px;
  opacity:.95;
}

.hero ul{
  list-style:none;
  margin-bottom:30px;
}

.hero ul li{
  margin-bottom:10px;
  font-size:15px;
}

.hero ul li::before{
  content:"✔";
  margin-right:10px;
  color:#BBF7D0;
}

.btn{
  display:inline-block;
  padding:14px 30px;
  background:#fff;
  color:#2563EB;
  font-weight:600;
  border-radius:14px;
  text-decoration:none;
  transition:.3s;
  box-shadow:0 12px 30px rgba(0,0,0,.18);
}

.btn:hover{
  transform:translateY(-4px);
}

/* ===== PHONE FRAME ===== */

.phone-frame{
  width:320px;
  height:560px;
  background:#000;
  border-radius:34px;
  padding:12px;
  margin:auto;
  box-shadow:0 35px 70px rgba(0,0,0,.4);
}

.slider{
  width:100%;
  height:100%;
  border-radius:26px;
  overflow:hidden;
  position:relative;
  background:#000;
}

/* ===== SLIDES ===== */

.slide{
  position:absolute;
  inset:0;
  opacity:0;
  display:flex;
  align-items:center;
  justify-content:center;
}

/* FLOATING EFFECT */
.slide.active{
  opacity:1;
  animation: floatUpDown 5s ease-in-out infinite;
}

@keyframes floatUpDown{
  0%{ transform:translateY(0); }
  50%{ transform:translateY(-12px); }
  100%{ transform:translateY(0); }
}

.slide img{
  max-width:100%;
  max-height:100%;
  object-fit:contain;   /* 🔥 NO CROP */
  display:block;
}

/* ===== DOTS ===== */

.dots{
  text-align:center;
  margin-top:22px;
}

.dot{
  display:inline-block;
  width:11px;
  height:11px;
  background:#cbd5e1;
  border-radius:50%;
  margin:0 6px;
  cursor:pointer;
  transition:.3s;
}

.dot.active{
  background:#0EA5A4;
  transform:scale(1.25);
}

/* SweetAlert mobile stability */
.swal2-container {
  padding: 16px !important;
}

/* Prevent body jump on mobile */
body.swal2-shown {
  overflow: auto !important;
}

/* ===== GLASSMORPHISM ALERT ===== */
.swal2-popup.glass {
  background: rgba(255,255,255,0.12);
  backdrop-filter: blur(12px);
  border-radius: 20px;
  border: 1px solid rgba(255,255,255,0.3);
  color: #fff;
}

/* ===== RESPONSIVE ===== */

@media(max-width:768px){
  .hero{
    padding:80px 0;
  }

  .hero-grid{
    grid-template-columns:1fr;
    text-align:center;
  }

  .hero h1{
    font-size:36px;
  }
}
</style>
</head>
<body>

<section class="hero">
<div class="container hero-grid">

<!-- LEFT CONTENT -->
<div>
<h1>Billing & Inventory for Indian Small Businesses</h1>

<p>
Lenden helps shop owners, traders and small businesses manage
billing, products, customers and payments — all from one simple app.
</p>

<ul>
  <li>Create GST-ready invoices instantly</li>
  <li>Manage products & stock easily</li>
  <li>Track customers & daily sales</li>
  <li>Fast, secure & made for India 🇮🇳</li>
</ul>

<a href="#" class="btn" onclick="confirmDownload()">Download the App for test</a>
</div>

<!-- RIGHT PHONE -->
<div>
<div class="phone-frame">
<div class="slider">

<div class="slide active">
  <img src="{{ asset('images/1st.jpeg') }}" alt="Login Screen">
</div>

<div class="slide">
  <img src="{{ asset('images/second.jpg') }}" alt="Signup Screen">
</div>

</div>
</div>

<div class="dots">
  <span class="dot active"></span>
  <span class="dot"></span>
</div>

</div>
</div>
</section>

<script>
const slides = document.querySelectorAll('.slide');
const dots = document.querySelectorAll('.dot');
let index = 0;

function showSlide(i){
  slides.forEach(s => s.classList.remove('active'));
  dots.forEach(d => d.classList.remove('active'));
  slides[i].classList.add('active');
  dots[i].classList.add('active');
}

setInterval(() => {
  index = (index + 1) % slides.length;
  showSlide(index);
}, 4500);

dots.forEach((dot, i) => {
  dot.addEventListener('click', () => {
    index = i;
    showSlide(index);
  });
});
</script>

<script>
function confirmDownload() {
  Swal.fire({
    title: 'Download Lenden App?',
    html: `
      <svg width="50" height="50" viewBox="0 0 512 512" style="display:block;margin:auto;">
        <circle cx="256" cy="256" r="256" fill="#0EA5A4"/>
        <path d="M256 128v192m0 0l64-64m-64 64l-64-64" stroke="#fff" stroke-width="32" stroke-linecap="round"/>
      </svg>
      <p style="font-size:14px;line-height:1.6">
        APK will be downloaded directly to your phone.<br>
        <b>Allow installation from unknown sources</b> if asked.
      </p>
    `,
    iconHtml: '🐱', // custom emoji
    showCancelButton: true,
    confirmButtonText: 'Download Now',
    cancelButtonText: 'Cancel',
    allowOutsideClick: false,
    allowEscapeKey: true,
    heightAuto: false,
    position: 'center',
    backdrop: true,
    customClass: {
      popup: 'glass'
    },
    showClass: {
      popup: 'swal2-show swal2-animate-bottom'
    },
    hideClass: {
      popup: 'swal2-hide swal2-animate-top'
    }
  }).then((result) => {
    if (result.isConfirmed) {
      // Example download link
      window.location.href = "{{ asset('storage/apks/lenden-app-latest.apk') }}"
    }
  });
}
</script>

</body>
</html>