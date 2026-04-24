<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Barangay Tabu · Smart Governance Portal</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@600;700&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= base_url('assets/adminlte/plugins/fontawesome-free/css/all.min.css') ?>">

  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    :root {
      --navy:       #020D24;
      --navy-2:     #061535;
      --navy-3:     #0A2150;
      --gold:       #C9A84C;
      --gold-2:     #e8c96e;
      --gold-3:     #f5dfa0;
      --white:      #FFFFFF;
      --muted:      rgba(255,255,255,0.38);
      --muted-2:    rgba(255,255,255,0.6);
      --danger:     #e05c5c;
    }

    html, body {
      height: 100%; width: 100%;
      overflow: hidden;
      font-family: 'Outfit', sans-serif;
      background: var(--navy);
      color: #fff;
    }

    #canvas {
      position: fixed; inset: 0; z-index: 0;
    }

    .bg-photo {
      position: fixed; inset: 0; z-index: 1;
      background-image: url("<?= base_url('assets/img/wowow.jpg') ?>");
      background-size: cover; background-position: center;
      filter: brightness(0.18) saturate(1.3);
      animation: bgBreath 20s ease-in-out infinite alternate;
    }

    @keyframes bgBreath {
      from { transform: scale(1);    filter: brightness(0.18) saturate(1.3); }
      to   { transform: scale(1.06); filter: brightness(0.22) saturate(1.5); }
    }

    .grain {
      position: fixed; inset: -50%; width: 200%; height: 200%;
      z-index: 2; opacity: 0.028;
      background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)'/%3E%3C/svg%3E");
      animation: grainShift 0.5s steps(2) infinite;
      pointer-events: none;
    }

    @keyframes grainShift {
      0%   { transform: translate(0,0); }
      25%  { transform: translate(-2%,1%); }
      50%  { transform: translate(1%,-2%); }
      75%  { transform: translate(2%,2%); }
      100% { transform: translate(-1%,-1%); }
    }

    .vignette {
      position: fixed; inset: 0; z-index: 3; pointer-events: none;
      background: radial-gradient(ellipse at center, transparent 35%, rgba(2,13,36,0.65) 70%, rgba(2,13,36,0.95) 100%);
    }

    .page {
      position: relative; z-index: 10;
      min-height: 100vh;
      display: flex; align-items: center; justify-content: center;
      padding: 20px;
    }

    .layout {
      display: flex; align-items: stretch;
      width: 100%; max-width: 1100px;
      border-radius: 28px; overflow: hidden;
      box-shadow: 0 0 0 1px rgba(201,168,76,0.18), 0 40px 100px rgba(0,0,0,0.7), 0 0 120px rgba(201,168,76,0.05) inset;
      animation: cardIn 1s cubic-bezier(0.16,1,0.3,1) both;
    }

    @keyframes cardIn {
      from { opacity:0; transform: translateY(40px) scale(0.97); }
      to   { opacity:1; transform: translateY(0) scale(1); }
    }

    /* ── LEFT ── */
    .left {
      flex: 1; position: relative;
      display: flex; flex-direction: column; justify-content: space-between;
      padding: 56px 52px;
      background: linear-gradient(145deg, rgba(201,168,76,0.07) 0%, transparent 60%), rgba(6,21,53,0.55);
      backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px);
      border-right: 1px solid rgba(201,168,76,0.14);
      overflow: hidden;
    }

    .left::before {
      content: ''; position: absolute; top:24px; left:24px;
      width:38px; height:38px;
      border-top: 2px solid var(--gold); border-left: 2px solid var(--gold);
      border-radius: 4px 0 0 0; opacity: 0.7;
    }
    .left::after {
      content: ''; position: absolute; bottom:24px; right:24px;
      width:38px; height:38px;
      border-bottom: 2px solid var(--gold); border-right: 2px solid var(--gold);
      border-radius: 0 0 4px 0; opacity: 0.4;
    }

    .orb { position: absolute; border-radius: 50%; filter: blur(70px); pointer-events: none; animation: orbFloat 12s ease-in-out infinite alternate; }
    .orb-1 { width:260px; height:260px; background: radial-gradient(circle, rgba(201,168,76,0.18), transparent 70%); top:-60px; right:-60px; }
    .orb-2 { width:180px; height:180px; background: radial-gradient(circle, rgba(6,21,53,0.8), transparent 70%); bottom:40px; left:-30px; animation-delay:-5s; }

    @keyframes orbFloat { from { transform:translate(0,0) scale(1); } to { transform:translate(20px,-20px) scale(1.15); } }

    .left-top { position: relative; z-index: 2; }

    .live-badge {
      display: inline-flex; align-items: center; gap:8px;
      background: rgba(201,168,76,0.1); border: 1px solid rgba(201,168,76,0.3);
      color: var(--gold-2); font-size:0.68rem; font-weight:600; letter-spacing:2.5px;
      text-transform: uppercase; padding: 5px 14px 5px 10px; border-radius: 100px;
      margin-bottom: 32px; animation: fadeInUp 0.8s 0.4s ease both;
    }
    .live-dot { width:7px; height:7px; border-radius:50%; background:#4adb86; box-shadow:0 0 8px #4adb86; animation: livePulse 1.8s ease infinite; }
    @keyframes livePulse { 0%,100%{box-shadow:0 0 4px #4adb86;} 50%{box-shadow:0 0 14px #4adb86;} }

    .left-headline {
      font-family: 'Cormorant Garamond', serif;
      font-size: clamp(2.6rem, 4.5vw, 4rem); font-weight:700; line-height:1.05;
      color:#fff; animation: fadeInUp 0.8s 0.55s ease both;
    }
    .left-headline .gold { color:var(--gold); }
    .left-headline .italic { font-style: italic; }

    .tagline {
      margin-top:22px; font-size:0.9rem; font-weight:300; color:var(--muted-2);
      line-height:1.8; max-width:340px; animation: fadeInUp 0.8s 0.7s ease both;
    }

    .gold-rule { display:flex; align-items:center; gap:12px; margin:36px 0; animation: fadeInUp 0.8s 0.85s ease both; }
    .gold-rule-line { height:1px; width:40px; background: linear-gradient(90deg, var(--gold), transparent); }
    .gold-rule-diamond { width:6px; height:6px; background:var(--gold); transform:rotate(45deg); }

    .left-stats { display:grid; grid-template-columns:repeat(3,1fr); gap:16px; animation: fadeInUp 0.8s 1s ease both; }
    .stat {
      padding:16px; background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.07);
      border-radius:12px; text-align:center; transition: background 0.3s, border-color 0.3s;
    }
    .stat:hover { background:rgba(201,168,76,0.06); border-color:rgba(201,168,76,0.2); }
    .stat-n { font-family:'Cormorant Garamond',serif; font-size:1.8rem; font-weight:700; color:var(--gold); line-height:1; }
    .stat-l { font-size:0.65rem; font-weight:600; letter-spacing:1.5px; text-transform:uppercase; color:var(--muted); margin-top:5px; }

    .left-bottom { position:relative; z-index:2; animation: fadeInUp 0.8s 1.15s ease both; }
    .seal-row { display:flex; align-items:center; gap:12px; }
    .seal-icon { width:32px; height:32px; border-radius:50%; background:rgba(201,168,76,0.15); border:1px solid rgba(201,168,76,0.3); display:flex; align-items:center; justify-content:center; font-size:0.8rem; color:var(--gold); }
    .seal-text { font-size:0.78rem; font-weight:300; color:var(--muted); line-height:1.5; }
    .seal-text strong { color:rgba(255,255,255,0.75); font-weight:600; }

    /* ── RIGHT ── */
    .right {
      width:440px; flex-shrink:0;
      display:flex; flex-direction:column; align-items:center; justify-content:center;
      padding:56px 44px;
      background: rgba(255,255,255,0.025);
      backdrop-filter: blur(40px) saturate(150%); -webkit-backdrop-filter: blur(40px) saturate(150%);
      position: relative; overflow: hidden;
    }

    .beam {
      position:absolute; top:0; height:1px;
      background: linear-gradient(90deg, transparent, var(--gold), transparent);
      animation: beamSlide 4s ease-in-out infinite;
    }
    @keyframes beamSlide {
      0%   { left:10%; width:20%; opacity:0; }
      20%  { opacity:1; }
      80%  { opacity:1; }
      100% { left:90%; width:20%; opacity:0; }
    }

    .right-inner { width:100%; position:relative; z-index:2; }

    .logo-wrap { text-align:center; margin-bottom:38px; animation: fadeInUp 0.8s 0.3s ease both; }
    .logo-ring-wrap { position:relative; display:inline-block; }
    .logo-ring { position:absolute; inset:-10px; border-radius:50%; border:1.5px dashed rgba(201,168,76,0.35); animation:ringRotate 20s linear infinite; }
    .logo-ring-2 { position:absolute; inset:-18px; border-radius:50%; border:1px solid rgba(201,168,76,0.12); animation:ringRotate 30s linear infinite reverse; }
    @keyframes ringRotate { from{transform:rotate(0deg);} to{transform:rotate(360deg);} }

    .logo-img { height:80px; width:80px; border-radius:50%; object-fit:cover; position:relative; z-index:2; box-shadow:0 0 0 2px rgba(201,168,76,0.4), 0 8px 30px rgba(0,0,0,0.5); animation: logoFloat 7s ease-in-out infinite; }
    @keyframes logoFloat { 0%,100%{transform:translateY(0);} 50%{transform:translateY(-5px);} }

    .portal-name { font-family:'Cormorant Garamond',serif; font-size:1.3rem; font-weight:700; color:#fff; margin-top:18px; line-height:1.2; }
    .portal-sub { font-size:0.65rem; font-weight:600; letter-spacing:3px; text-transform:uppercase; color:var(--gold); margin-top:6px; display:block; }
    .live-clock { display:flex; align-items:center; justify-content:center; gap:6px; margin-top:10px; font-size:0.72rem; font-weight:500; color:var(--muted); letter-spacing:1px; }

    .form-divider { display:flex; align-items:center; gap:12px; margin:0 0 22px; animation: fadeInUp 0.8s 0.45s ease both; }
    .form-divider-line { flex:1; height:1px; background:rgba(255,255,255,0.07); }
    .form-divider span { font-size:0.65rem; font-weight:700; letter-spacing:2.5px; text-transform:uppercase; color:var(--gold); }

    .flash-error {
      background:rgba(224,92,92,0.08); border:1px solid rgba(224,92,92,0.2); border-left:3px solid var(--danger);
      color:#f08080; border-radius:10px; padding:12px 16px; font-size:0.82rem; font-weight:500;
      margin-bottom:20px; display:flex; align-items:center; gap:10px;
      animation: shake 0.5s ease;
    }
    @keyframes shake {
      0%,100%{transform:translateX(0);} 20%{transform:translateX(-6px);} 40%{transform:translateX(6px);}
      60%{transform:translateX(-4px);} 80%{transform:translateX(4px);}
    }

    .fields { animation: fadeInUp 0.8s 0.6s ease both; }
    .field { margin-bottom:18px; }
    .field-label { font-size:0.65rem; font-weight:700; letter-spacing:2px; text-transform:uppercase; color:rgba(255,255,255,0.45); display:flex; align-items:center; gap:6px; margin-bottom:8px; }
    .field-label i { font-size:0.6rem; color:var(--gold); opacity:0.8; }

    .field-wrap {
      position:relative; border-radius:12px; border:1px solid rgba(255,255,255,0.09);
      background:rgba(255,255,255,0.04); display:flex; align-items:center; overflow:hidden;
      transition: border-color 0.3s, box-shadow 0.3s, background 0.3s;
    }
    .field-wrap::after {
      content:''; position:absolute; bottom:0; left:50%; width:0; height:2px;
      background: linear-gradient(90deg, transparent, var(--gold), transparent);
      transition: width 0.4s ease, left 0.4s ease;
    }
    .field-wrap:focus-within {
      border-color:rgba(201,168,76,0.35); background:rgba(255,255,255,0.06);
      box-shadow: 0 0 0 4px rgba(201,168,76,0.07), 0 4px 20px rgba(0,0,0,0.3);
    }
    .field-wrap:focus-within::after { width:100%; left:0; }

    .field-wrap input {
      flex:1; background:transparent; border:none; outline:none;
      padding:15px 16px; font-family:'Outfit',sans-serif; font-size:0.9rem; font-weight:400; color:#fff; letter-spacing:0.3px;
    }
    .field-wrap input::placeholder { color:rgba(255,255,255,0.2); }
    .field-wrap input:-webkit-autofill { -webkit-box-shadow:0 0 0 1000px rgba(6,21,53,0.9) inset; -webkit-text-fill-color:#fff; }

    .field-icon-btn { padding:0 16px; color:rgba(255,255,255,0.25); font-size:0.82rem; cursor:pointer; background:none; border:none; outline:none; transition:color 0.2s; flex-shrink:0; }
    .field-icon-btn:hover { color:var(--gold); }

    .remember { display:flex; align-items:center; gap:10px; margin-bottom:28px; margin-top:-2px; animation: fadeInUp 0.8s 0.75s ease both; }
    .remember input[type="checkbox"] { width:15px; height:15px; accent-color:var(--gold); cursor:pointer; }
    .remember label { font-size:0.8rem; font-weight:400; color:rgba(255,255,255,0.4); cursor:pointer; }

    .btn-auth {
      width:100%; padding:17px; border:none; border-radius:12px;
      background: linear-gradient(135deg, var(--gold) 0%, #a8782a 100%);
      color:#1a0e00; font-family:'Outfit',sans-serif; font-size:0.8rem; font-weight:700;
      letter-spacing:2.5px; text-transform:uppercase; cursor:pointer;
      position:relative; overflow:hidden; transition: transform 0.25s, box-shadow 0.25s;
      box-shadow: 0 8px 24px rgba(201,168,76,0.28), 0 2px 8px rgba(0,0,0,0.3);
      animation: fadeInUp 0.8s 0.85s ease both;
    }
    .btn-auth::before {
      content:''; position:absolute; top:0; left:-100%; width:100%; height:100%;
      background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
      transition: left 0.5s ease;
    }
    .btn-auth:hover::before { left:100%; }
    .btn-auth:hover { transform:translateY(-2px); box-shadow:0 14px 36px rgba(201,168,76,0.38), 0 4px 12px rgba(0,0,0,0.3); }
    .btn-auth:active { transform:translateY(0); }

    .ripple { position:absolute; border-radius:50%; background:rgba(255,255,255,0.35); transform:scale(0); animation:rippleOut 0.6s linear; pointer-events:none; }
    @keyframes rippleOut { to { transform:scale(4); opacity:0; } }

    .btn-auth.loading { pointer-events:none; opacity:0.8; }
    .btn-auth .btn-label { transition:opacity 0.2s; }
    .btn-auth .btn-spinner { display:none; position:absolute; inset:0; align-items:center; justify-content:center; }
    .btn-auth.loading .btn-label { opacity:0; }
    .btn-auth.loading .btn-spinner { display:flex; }
    .spinner-ring { width:20px; height:20px; border-radius:50%; border:2px solid rgba(0,0,0,0.2); border-top-color:#1a0e00; animation:spin 0.7s linear infinite; }
    @keyframes spin { to{transform:rotate(360deg);} }

    .security-badges {
      display:flex; justify-content:center; gap:20px;
      margin-top:28px; padding-top:24px; border-top:1px solid rgba(255,255,255,0.06);
      animation: fadeInUp 0.8s 1s ease both;
    }
    .badge { display:flex; flex-direction:column; align-items:center; gap:4px; color:rgba(255,255,255,0.25); transition:color 0.2s; }
    .badge:hover { color:var(--gold); }
    .badge i { font-size:1rem; }
    .badge span { font-size:0.58rem; font-weight:600; letter-spacing:1px; text-transform:uppercase; }

    .copy { text-align:center; margin-top:18px; font-size:0.68rem; color:rgba(255,255,255,0.2); animation: fadeInUp 0.8s 1.1s ease both; line-height:1.6; }
    .copy a { color:rgba(255,255,255,0.35); text-decoration:none; transition:color 0.2s; }
    .copy a:hover { color:var(--gold); }

    @keyframes fadeInUp { from{opacity:0;transform:translateY(18px);} to{opacity:1;transform:translateY(0);} }

    @media(max-width:860px) {
      .left{display:none;}
      .layout{max-width:440px;border-radius:24px;}
      .right{width:100%;}
    }
    @media(max-width:480px){.right{padding:40px 28px;}}
  </style>
</head>
<body>

  <canvas id="canvas"></canvas>
  <div class="bg-photo"></div>
  <div class="grain"></div>
  <div class="vignette"></div>

  <div class="page">
    <div class="layout">

      <!-- LEFT -->
      <div class="left">
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>

        <div class="left-top">
          <div class="live-badge"><span class="live-dot"></span>System Online</div>

          <h1 class="left-headline">
            Smart<br>
            <span class="gold italic">Governance</span><br>
            Portal
          </h1>

          <p class="tagline">
            A unified digital platform for Barangay Tabu, Ilog City — delivering transparent, efficient, and citizen-centered public service to every household.
          </p>

          <div class="gold-rule">
            <div class="gold-rule-line"></div>
            <div class="gold-rule-diamond"></div>
          </div>

          <div class="left-stats">
            <div class="stat"><div class="stat-n">24/7</div><div class="stat-l">Uptime</div></div>
            <div class="stat"><div class="stat-n">AES</div><div class="stat-l">Encrypted</div></div>
            <div class="stat"><div class="stat-n">ISO</div><div class="stat-l">Certified</div></div>
          </div>
        </div>

        <div class="left-bottom">
          <div class="seal-row">
            <div class="seal-icon"><i class="fas fa-shield-alt"></i></div>
            <div class="seal-text">
              <strong>Authorized Personnel Only</strong><br>
              All access attempts are logged and monitored.
            </div>
          </div>
        </div>
      </div>

      <!-- RIGHT -->
      <div class="right">
        <div class="beam"></div>
        <div class="right-inner">

          <div class="logo-wrap">
            <div class="logo-ring-wrap">
              <div class="logo-ring"></div>
              <div class="logo-ring-2"></div>
              <img src="<?= base_url('assets/img/tabu.jpg') ?>" alt="Barangay Tabu" class="logo-img">
            </div>
            <div class="portal-name">Barangay Tabu, Ilog City</div>
            <span class="portal-sub">Smart Governance Portal</span>
            <div class="live-clock">
              <i class="fas fa-circle" style="font-size:0.4rem;color:#4adb86;"></i>
              <span id="clock">--:-- --</span>
            </div>
          </div>

          <?php if (session()->getFlashdata('error')): ?>
            <div class="flash-error">
              <i class="fas fa-exclamation-triangle"></i>
              <?= session()->getFlashdata('error') ?>
            </div>
          <?php endif; ?>

          <form id="loginForm" action="<?= base_url('/auth') ?>" method="post" autocomplete="off">
            <?= csrf_field() ?>

            <div class="form-divider">
              <div class="form-divider-line"></div>
              <span>Secure Sign-In</span>
              <div class="form-divider-line"></div>
            </div>

            <div class="fields">
              <div class="field">
                <div class="field-label"><i class="fas fa-circle"></i> Administrator Email</div>
                <div class="field-wrap">
                  <input type="email" name="email" placeholder="admin@ilogcity.gov.ph" required autocomplete="username">
                  <button type="button" class="field-icon-btn" tabindex="-1"><i class="fas fa-user-shield"></i></button>
                </div>
              </div>

              <div class="field">
                <div class="field-label"><i class="fas fa-circle"></i> Access Key</div>
                <div class="field-wrap">
                  <input type="password" name="password" id="passwordField" placeholder="••••••••••••" required autocomplete="current-password">
                  <button type="button" class="field-icon-btn" id="togglePwd" tabindex="-1"><i class="fas fa-eye-slash" id="eyeIcon"></i></button>
                </div>
              </div>
            </div>

            <div class="remember">
              <input type="checkbox" id="remember" name="remember">
              <label for="remember">Keep me signed in on this device</label>
            </div>

            <button type="submit" class="btn-auth" id="submitBtn">
              <span class="btn-label">Authenticate &nbsp;<i class="fas fa-fingerprint"></i></span>
              <span class="btn-spinner"><span class="spinner-ring"></span></span>
            </button>
          </form>

          <div class="security-badges">
            <div class="badge"><i class="fas fa-lock"></i><span>SSL</span></div>
            <div class="badge"><i class="fas fa-shield-alt"></i><span>Secured</span></div>
            <div class="badge"><i class="fas fa-eye-slash"></i><span>Private</span></div>
            <div class="badge"><i class="fas fa-fingerprint"></i><span>Auth</span></div>
          </div>

          <div class="copy">
            &copy; 2026 MISO &mdash; Barangay Tabu, Ilog City<br>
            <a href="#">Privacy Policy</a> &nbsp;&middot;&nbsp; <a href="#">Terms of Use</a>
          </div>

        </div>
      </div>

    </div>
  </div>

  <script>
  (function(){
    const canvas=document.getElementById('canvas'),ctx=canvas.getContext('2d');
    let W,H,particles=[];
    function resize(){W=canvas.width=window.innerWidth;H=canvas.height=window.innerHeight;}
    function rand(a,b){return a+Math.random()*(b-a);}
    function Particle(){this.reset();}
    Particle.prototype.reset=function(){
      this.x=rand(0,W);this.y=rand(0,H);this.r=rand(0.4,1.6);
      this.vx=rand(-0.12,0.12);this.vy=rand(-0.22,-0.05);
      this.alpha=rand(0.15,0.55);this.gold=Math.random()<0.15;
    };
    Particle.prototype.draw=function(){
      ctx.beginPath();ctx.arc(this.x,this.y,this.r,0,Math.PI*2);
      ctx.fillStyle=this.gold?`rgba(201,168,76,${this.alpha})`:`rgba(255,255,255,${this.alpha})`;
      ctx.fill();
    };
    Particle.prototype.update=function(){
      this.x+=this.vx;this.y+=this.vy;
      if(this.y<-4||this.x<-4||this.x>W+4)this.reset();
    };
    function init(){
      resize();particles=[];
      const count=Math.floor((W*H)/8000);
      for(let i=0;i<count;i++)particles.push(new Particle());
    }
    function loop(){
      ctx.clearRect(0,0,W,H);
      particles.forEach(p=>{p.update();p.draw();});
      requestAnimationFrame(loop);
    }
    window.addEventListener('resize',init);
    init();loop();
  })();

  function updateClock(){
    const now=new Date();
    let h=now.getHours(),m=now.getMinutes(),s=now.getSeconds();
    const ampm=h>=12?'PM':'AM';h=h%12||12;
    document.getElementById('clock').textContent=
      String(h).padStart(2,'0')+':'+String(m).padStart(2,'0')+':'+String(s).padStart(2,'0')+' '+ampm;
  }
  updateClock();setInterval(updateClock,1000);

  document.getElementById('togglePwd').addEventListener('click',function(){
    const inp=document.getElementById('passwordField'),icon=document.getElementById('eyeIcon');
    const isPass=inp.type==='password';
    inp.type=isPass?'text':'password';
    icon.classList.toggle('fa-eye-slash',!isPass);
    icon.classList.toggle('fa-eye',isPass);
  });

  document.getElementById('submitBtn').addEventListener('click',function(e){
    const btn=this,rect=btn.getBoundingClientRect();
    const rip=document.createElement('span');rip.className='ripple';
    const size=Math.max(rect.width,rect.height);
    rip.style.cssText=`width:${size}px;height:${size}px;left:${e.clientX-rect.left-size/2}px;top:${e.clientY-rect.top-size/2}px;`;
    btn.appendChild(rip);setTimeout(()=>rip.remove(),700);
  });

  document.getElementById('loginForm').addEventListener('submit',function(){
    document.getElementById('submitBtn').classList.add('loading');
  });
  </script>

</body>
</html>