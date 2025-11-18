<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Website đang tạm dừng — Đông Sơn Export</title>
  <meta name="robots" content="noindex, nofollow">
  <style>
    :root{
      --bronze-1:#D1A53A;
      --bronze-2:#B8923A;
      --dark:#2b2b2b;
      --bg:#0f0c05;
    }
    *{box-sizing:border-box}
    html,body{height:100%}
    body{
      margin:0;
      font-family:Inter, 'Segoe UI', Roboto, Arial, sans-serif;
      background: radial-gradient(1200px 600px at 10% 10%, rgba(209,165,58,0.06), transparent 10%),
                  radial-gradient(900px 500px at 90% 80%, rgba(184,129,36,0.04), transparent 10%),
                  linear-gradient(180deg,#0b0a07 0%, #13120e 100%);
      color:var(--dark);
      display:flex;
      align-items:center;
      justify-content:center;
      padding:32px;
    }
    .wrap{
      width:100%;
      max-width:980px;
      background: linear-gradient(180deg, rgba(255,255,255,0.02), rgba(255,255,255,0.01));
      border-radius:16px;
      padding:36px;
      position:relative;
      overflow:hidden;
      box-shadow: 0 20px 60px rgba(3,3,3,0.5);
      border:1px solid rgba(209,165,58,0.08);
      backdrop-filter: blur(6px) saturate(120%);
    }
    /* decorative bronze glow */
    .wrap::before{
      content:'';
      position:absolute;inset:-30% -10% auto -10%;
      height:250%;
      background: radial-gradient(circle at 20% 10%, rgba(209,165,58,0.06), transparent 8%),
                  radial-gradient(circle at 80% 90%, rgba(184,146,60,0.05), transparent 12%);
      pointer-events:none;
      transform: rotate(-8deg);
    }
    .grid{display:grid;grid-template-columns:1fr 360px;gap:28px;align-items:center}
    @media (max-width:860px){.grid{grid-template-columns:1fr;}.wrap{padding:24px}}

    .content h1{
      margin:0 0 8px;font-size:34px;line-height:1.05;color:var(--bronze-1);
      text-shadow:0 4px 18px rgba(209,165,58,0.18);
      letter-spacing:0.2px;
    }
    .content p{margin:0 0 18px;color:rgba(43,43,43,0.85);font-size:16px}
    .content .meta{font-size:14px;color:rgba(43,43,43,0.6)}

    .badge{
      display:inline-block;padding:10px 14px;border-radius:999px;background:linear-gradient(90deg,var(--bronze-1),var(--bronze-2));
      color:white;font-weight:600;font-size:13px;box-shadow:0 6px 18px rgba(209,165,58,0.18);
    }

    /* emblem */
    .panel{
      background:linear-gradient(180deg, rgba(255,255,255,0.02), rgba(255,255,255,0.006));
      border-radius:12px;padding:20px;text-align:center;border:1px solid rgba(209,165,58,0.06)
    }
    .emblem{
      width:120px;height:120px;margin:0 auto 14px;border-radius:50%;
      background:conic-gradient(from 180deg at 50% 50%, rgba(209,165,58,0.15), rgba(184,146,60,0.07));
      display:flex;align-items:center;justify-content:center;position:relative;overflow:hidden;
      box-shadow: 0 10px 30px rgba(0,0,0,0.35), inset 0 2px 8px rgba(255,255,255,0.03);
      border:4px solid rgba(209,165,58,0.12);
    }
    .emblem svg{width:70px;height:70px;filter:drop-shadow(0 6px 18px rgba(209,165,58,0.12));}

    /* subtle floating sparkles */
    .sparkle{position:absolute;border-radius:50%;opacity:0.6;background:radial-gradient(circle, rgba(255,230,150,0.9) 0%, rgba(209,165,58,0.35) 40%, transparent 60%)}
    .s1{width:36px;height:36px;left:-10px;top:10px;animation:float1 6s ease-in-out infinite}
    .s2{width:24px;height:24px;right:-6px;bottom:10px;animation:float2 7s ease-in-out infinite}
    .s3{width:18px;height:18px;left:20%;bottom:-8px;animation:float3 5.5s ease-in-out infinite}
    @keyframes float1{0%{transform:translateY(0) translateX(0) scale(1)}50%{transform:translateY(-10px) translateX(6px) scale(1.05)}100%{transform:translateY(0) translateX(0) scale(1)}}
    @keyframes float2{0%{transform:translateY(0) translateX(0) scale(1)}50%{transform:translateY(-12px) translateX(-6px) scale(1.08)}100%{transform:translateY(0) translateX(0) scale(1)}}
    @keyframes float3{0%{transform:translateY(0)}50%{transform:translateY(-8px)}100%{transform:translateY(0)}}

    .btn{
      display:inline-block;padding:12px 18px;border-radius:999px;background:linear-gradient(90deg,var(--bronze-1),var(--bronze-2));color:#fff;font-weight:700;text-decoration:none;box-shadow:0 12px 30px rgba(209,165,58,0.16);
      transition:transform .18s ease, box-shadow .18s ease;
    }
    .btn:hover{transform:translateY(-4px);box-shadow:0 20px 40px rgba(209,165,58,0.22)}

    footer{margin-top:18px;font-size:13px;color:rgba(43,43,43,0.55);text-align:center}
  </style>
</head>
<body>
  <div class="wrap">
    <div class="grid">
      <div class="content">
        <span class="badge">Thông báo</span>
        <h1>Website đang tạm dừng để bảo trì</h1>
        <p>Chúng tôi đang cập nhật, tối ưu và triển khai những cải tiến mới nhằm nâng cao trải nghiệm cho khách hàng. Dự kiến hoàn tất trong vài phút đến vài giờ.</p>
        <div style="display:flex;gap:12px;align-items:center;margin-top:6px">
          <a class="btn" href="mailto:info@dongsongexport.vn">Liên hệ hỗ trợ</a>
          <a class="btn" style="background:transparent;color:var(--bronze-1);border:1px solid rgba(209,165,58,0.12);box-shadow:none" href="/">Kiểm tra lại</a>
        </div>
        <div class="meta" style="margin-top:18px">Gợi ý: thử lại sau vài phút hoặc liên hệ qua email nếu cần hỗ trợ khẩn cấp.</div>
        <footer>© Đông Sơn Export • Bảo trì định kỳ • <span style="color:var(--bronze-1)">info@dongsongexport.vn</span></footer>
      </div>
      <div class="panel">
        <div class="emblem" aria-hidden="true">
          <svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
            <circle cx="32" cy="32" r="30" stroke="rgba(255,255,255,0.06)" stroke-width="2"/>
            <g fill="url(#g)">
              <path d="M32 12c5 0 9 4 9 9s-4 9-9 9-9-4-9-9 4-9 9-9z"/>
            </g>
            <defs>
              <linearGradient id="g" x1="0" x2="1">
                <stop offset="0" stop-color="#FFE9A8" stop-opacity="1"/>
                <stop offset="1" stop-color="#D1A53A" stop-opacity="1"/>
              </linearGradient>
            </defs>
          </svg>
        </div>
        <div style="font-weight:700;color:var(--dark);font-size:18px;margin-bottom:6px">Đông Sơn Export</div>
        <div style="color:rgba(43,43,43,0.65);font-size:14px;margin-bottom:10px">An toàn • Truy xuất nguồn gốc • Xuất khẩu</div>
        <div style="font-size:13px;color:rgba(43,43,43,0.6)">Hoặc liên hệ trực tiếp:</div>
        <div style="margin-top:10px;font-weight:700;color:var(--bronze-1)">+84 56 821 5678</div>
      </div>
    </div>
    <div class="sparkle s1"></div>
    <div class="sparkle s2"></div>
    <div class="sparkle s3"></div>
  </div>
</body>
</html>
