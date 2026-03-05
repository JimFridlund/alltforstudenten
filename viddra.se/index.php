<?php
$page_title = "Viddra — Stop running out before payday";
include __DIR__ . '/includes/header.php';
?>

<style>
:root{
  --max: 1120px;
  --pad: 18px;
  --r: 18px;
  --b: rgba(0,0,0,.08);
  --bg: rgba(255,255,255,.65);
  --text: rgba(0,0,0,.84);
  --muted: rgba(0,0,0,.62);
}

.l-wrap{max-width:var(--max); margin:0 auto; padding:0 var(--pad);}
.l-hero{padding:44px 0 22px;}
.l-sec{padding:26px 0;}
.l-grid{display:grid; gap:18px;}
@media (min-width: 920px){
  .l-grid{grid-template-columns: 1.05fr .95fr; align-items:center; gap:22px;}
}

.l-eyebrow{
  display:inline-flex; gap:10px; align-items:center;
  padding:7px 11px; border-radius:999px;
  border:1px solid var(--b);
  background:rgba(255,255,255,.55);
  font-size:12.5px; color:var(--muted);
}
.l-dot{width:8px; height:8px; border-radius:99px; background:rgba(0,0,0,.35); display:inline-block;}

h1{
  font-family: Fraunces, serif;
  font-weight: 700;
  letter-spacing: -.02em;
  line-height: 1.02;
  margin: 12px 0 10px;
  font-size: 44px;
}
@media (min-width:920px){ h1{font-size: 54px;} }

.l-lead{
  max-width: 44ch;
  font-size: 18px;
  line-height: 1.55;
  color: var(--text);
  margin: 0 0 12px;
}
.l-claims{margin:12px 0 0; padding:0; list-style:none; max-width: 52ch;}
.l-claims li{padding:6px 0; color:var(--text); font-size:15.5px;}
.l-claims b{font-weight:800;}
.l-cta{display:flex; gap:10px; flex-wrap:wrap; margin-top:14px; align-items:center;}
.l-note{margin-top:10px; color:var(--muted); font-size:12.5px;}

.hero-img{
  width:100%;
  border-radius:22px;
  border:1px solid rgba(0,0,0,.08);
  box-shadow:0 22px 44px rgba(0,0,0,.08);
}

.l-panel{
  border:1px solid var(--b);
  border-radius:var(--r);
  background:var(--bg);
  padding:18px;
}

h2{
  font-family: Fraunces, serif;
  font-size: 26px;
  letter-spacing: -.01em;
  margin:0 0 10px;
}
@media (min-width:920px){ h2{font-size: 28px;} }

.l-copy{color:var(--text); line-height:1.65; margin:0 0 8px; max-width: 70ch;}
.l-muted{color:var(--muted);}

.l-3{display:grid; gap:12px; margin-top:12px;}
@media (min-width:920px){ .l-3{grid-template-columns: repeat(3, 1fr);} }

.l-mini{
  border:1px solid var(--b);
  border-radius:16px;
  background:rgba(255,255,255,.55);
  padding:14px;
}
.l-mini h3{margin:0 0 6px; font-size:14.5px;}
.l-mini p{margin:0; color:var(--muted); font-size:13.5px; line-height:1.55;}

.l-pricing{display:grid; gap:12px; margin-top:12px;}
@media (min-width:920px){ .l-pricing{grid-template-columns: 1fr 1fr;} }

.l-price{
  border:1px solid var(--b);
  border-radius:16px;
  background:rgba(255,255,255,.65);
  padding:16px;
}
.l-tag{display:inline-flex; padding:6px 10px; border-radius:999px; border:1px solid var(--b); background:rgba(0,0,0,.03); font-size:12px; color:var(--muted);}
.l-money{font-family:Fraunces, serif; font-size:34px; font-weight:700; margin-top:8px;}
.l-price ul{margin:10px 0 0; padding-left:18px; color:var(--text);}
.l-price li{margin:6px 0;}

.l-divider{height:1px; background:rgba(0,0,0,.08); margin: 6px 0 0;}

</style>

<section class="l-hero">
  <div class="l-wrap l-grid">

    <div>
      <div class="l-eyebrow"><span class="l-dot"></span> Payday clarity for couples</div>

      <h1>Stop running out<br>before payday.</h1>

      <p class="l-lead">
        Shared financial clarity for couples who earn enough — but still end up with too much month left at the end of the money.
      </p>

      <ul class="l-claims">
        <li><b>Days to payday</b> — know exactly how long you need to stretch.</li>
        <li><b>Safe daily spend</b> — one number you can trust.</li>
        <li><b>Buffer impact</b> — stop “accidentally” spending your emergency fund.</li>
      </ul>

      <div class="l-cta">
        <a class="btn primary" href="/app/register.php?next=/app/onboarding.php">Start free</a>
        <a class="btn" href="/app/login.php">Sign in</a>
      </div>

      <div class="l-note">No blame. No spreadsheets. Just the same numbers for both of you.</div>
    </div>

    <div>
      <img src="/assets/img/hero-payday.png" alt="Viddra payday dashboard preview" class="hero-img">
    </div>

  </div>
</section>

<section class="l-sec">
  <div class="l-wrap">
    <div class="l-panel">
      <h2>The cycle Viddra breaks</h2>
      <p class="l-copy">
        You set money aside. You agree to be careful. You plan to build a buffer.
      </p>
      <p class="l-copy l-muted">
        And then the buffer disappears anyway. Not because you don’t earn enough — but because you can’t see the month clearly.
      </p>
    </div>
  </div>
</section>

<section class="l-sec">
  <div class="l-wrap">
    <h2>What you see instantly</h2>
    <div class="l-3">
      <div class="l-mini">
        <h3>Are we safe this month?</h3>
        <p>Clear status: safe, at risk, or dipping into buffer.</p>
      </div>
      <div class="l-mini">
        <h3>One daily number</h3>
        <p>“We can safely spend £X per day.”</p>
      </div>
      <div class="l-mini">
        <h3>Course correction</h3>
        <p>Reduce by £Y/day to stay on track.</p>
      </div>
    </div>
  </div>
</section>

<section class="l-sec">
  <div class="l-wrap">
    <h2>Pricing</h2>
    <div class="l-pricing">
      <div class="l-price">
        <span class="l-tag">Free</span>
        <div class="l-money">£0</div>
        <ul>
          <li>Single household</li>
          <li>Basic projection</li>
          <li>Core tracking</li>
        </ul>
        <div class="l-cta">
          <a class="btn" href="/app/register.php">Start free</a>
        </div>
      </div>

      <div class="l-price">
        <span class="l-tag">Plus</span>
        <div class="l-money">£11 <span style="font-family:Inter,sans-serif;font-size:13px;color:rgba(0,0,0,.62);font-weight:800;">/ month</span></div>
        <ul>
          <li>Invite your partner</li>
          <li>Payday safety engine</li>
          <li>Buffer protection tracking</li>
          <li>Shared clarity</li>
        </ul>
        <div class="l-cta">
          <a class="btn primary" href="/app/register.php">Try Plus</a>
        </div>
      </div>
    </div>
    <div class="l-note">If it prevents one buffer dip, it pays for itself.</div>
  </div>
</section>

<section class="l-sec">
  <div class="l-wrap">
    <div class="l-panel">
      <h2>You don’t need to earn more.</h2>
      <p class="l-copy">You need to see the month clearly — together.</p>
      <div class="l-cta">
        <a class="btn primary" href="/app/register.php?next=/app/onboarding.php">Start free</a>
        <a class="btn" href="/app/login.php">Sign in</a>
      </div>
    </div>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>