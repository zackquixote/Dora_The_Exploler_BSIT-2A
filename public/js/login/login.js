/**
 * Login Page Scripts
 * Handles particle animation, live clock, password toggle, ripple effect, and form loading state.
 */

(function() {
    // ---------- Particle Canvas Animation ----------
    const canvas = document.getElementById('canvas');
    let ctx = canvas.getContext('2d');
    let W, H, particles = [];

    function resize() {
        W = canvas.width = window.innerWidth;
        H = canvas.height = window.innerHeight;
    }

    function rand(a, b) {
        return a + Math.random() * (b - a);
    }

    function Particle() {
        this.reset();
    }

    Particle.prototype.reset = function() {
        this.x = rand(0, W);
        this.y = rand(0, H);
        this.r = rand(0.4, 1.6);
        this.vx = rand(-0.12, 0.12);
        this.vy = rand(-0.22, -0.05);
        this.alpha = rand(0.15, 0.55);
        this.gold = Math.random() < 0.15;
    };

    Particle.prototype.draw = function() {
        ctx.beginPath();
        ctx.arc(this.x, this.y, this.r, 0, Math.PI * 2);
        ctx.fillStyle = this.gold ? `rgba(201,168,76,${this.alpha})` : `rgba(255,255,255,${this.alpha})`;
        ctx.fill();
    };

    Particle.prototype.update = function() {
        this.x += this.vx;
        this.y += this.vy;
        if (this.y < -4 || this.x < -4 || this.x > W + 4) {
            this.reset();
        }
    };

    function initParticles() {
        resize();
        particles = [];
        const count = Math.floor((W * H) / 8000);
        for (let i = 0; i < count; i++) {
            particles.push(new Particle());
        }
    }

    function animate() {
        ctx.clearRect(0, 0, W, H);
        particles.forEach(p => {
            p.update();
            p.draw();
        });
        requestAnimationFrame(animate);
    }

    window.addEventListener('resize', function() {
        initParticles();
    });
    initParticles();
    animate();

    // ---------- Live Clock ----------
    function updateClock() {
        const now = new Date();
        let h = now.getHours();
        let m = now.getMinutes();
        let s = now.getSeconds();
        const ampm = h >= 12 ? 'PM' : 'AM';
        h = h % 12 || 12;
        const clockSpan = document.getElementById('clock');
        if (clockSpan) {
            clockSpan.textContent = 
                String(h).padStart(2, '0') + ':' +
                String(m).padStart(2, '0') + ':' +
                String(s).padStart(2, '0') + ' ' + ampm;
        }
    }
    updateClock();
    setInterval(updateClock, 1000);

    // ---------- Password Toggle ----------
    const toggleBtn = document.getElementById('togglePwd');
    const passwordField = document.getElementById('passwordField');
    const eyeIcon = document.getElementById('eyeIcon');

    if (toggleBtn && passwordField && eyeIcon) {
        toggleBtn.addEventListener('click', function() {
            const isPassword = passwordField.type === 'password';
            passwordField.type = isPassword ? 'text' : 'password';
            eyeIcon.classList.toggle('fa-eye-slash', !isPassword);
            eyeIcon.classList.toggle('fa-eye', isPassword);
        });
    }

    // ---------- Ripple Effect on Auth Button ----------
    const submitBtn = document.getElementById('submitBtn');
    if (submitBtn) {
        submitBtn.addEventListener('click', function(e) {
            const btn = this;
            const rect = btn.getBoundingClientRect();
            const ripple = document.createElement('span');
            ripple.className = 'ripple';
            const size = Math.max(rect.width, rect.height);
            ripple.style.cssText = `
                width: ${size}px;
                height: ${size}px;
                left: ${e.clientX - rect.left - size / 2}px;
                top: ${e.clientY - rect.top - size / 2}px;
            `;
            btn.appendChild(ripple);
            setTimeout(() => ripple.remove(), 700);
        });
    }

    // ---------- Form Loading State ----------
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', function() {
            const btn = document.getElementById('submitBtn');
            if (btn) btn.classList.add('loading');
        });
    }
})();