<?php
$this->load->view('_header');

// Prefill (från controller)
$prefill_kommun = isset($prefill_kommun) ? $prefill_kommun : '';
$prefill_lan    = isset($prefill_lan) ? $prefill_lan : '';
$prefill_cat    = isset($prefill_cat) ? $prefill_cat : '';
$status         = isset($status) ? $status : '';
?>

<div class="container">

  <h1>Kontakt & bokning</h1>

  <p style="max-width:820px;">
    Vi har <strong>en aktör per kategori och kommun</strong>. Det betyder att den som ligger på kommunsidan
    är den som syns – och blir top of mind när elever och föräldrar planerar studentåret.
  </p>

  <?php if($status === 'ok'): ?>
    <div style="background:#e8fff1;border:1px solid #bfead1;padding:14px;border-radius:12px;margin:16px 0;max-width:820px;">
      <strong>Tack!</strong> Vi har tagit emot din förfrågan och återkommer snarast.
    </div>
  <?php elseif($status === 'fel'): ?>
    <div style="background:#fff3f3;border:1px solid #f0caca;padding:14px;border-radius:12px;margin:16px 0;max-width:820px;">
      <strong>Oj!</strong> Fyll i minst <strong>Namn</strong>, <strong>E-post</strong>, <strong>Kategori</strong> och <strong>Kommun</strong>.
    </div>
  <?php endif; ?>

  <!-- Premium highlight -->
  <div style="
    background:#eef5ff;
    border:1px solid #cfe3ff;
    padding:18px;
    border-radius:16px;
    box-shadow:0 8px 22px rgba(9,50,96,0.10);
    margin:18px 0;
    max-width:820px;
  ">
    <div style="font-weight:900;color:#0b2a4a;font-size:16px;margin-bottom:6px;">
      Exklusivt upplägg
    </div>
    <div style="color:#233244;line-height:1.5;">
      <strong>1 plats per kommun och kategori.</strong> När platsen är tagen syns inga konkurrenter på samma lista i samma kommun.
      Skicka en förfrågan nedan så kollar vi om din plats är ledig.
    </div>
  </div>

  <h2>Kontaktuppgifter</h2>
  <ul>
    <li><strong>Weblings Sverige AB</strong></li>
    <li><a href="mailto:info@weblings.se">info@weblings.se</a></li>

  </ul>

  <hr>

  <h2>Boka plats på specifik kommunsida</h2>

  <form method="post" action="<?php echo base_url(); ?>kontakt/skicka" style="max-width:820px;">
    <!-- honeypot (spam) -->
    <div style="display:none;">
      <label>Website</label>
      <input type="text" name="website" value="">
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
      <div>
        <label><strong>Namn *</strong></label><br>
        <input type="text" name="name" required
          style="width:100%;padding:11px 12px;border:1px solid #d6dbe3;border-radius:12px;">
      </div>

      <div>
        <label><strong>Företag</strong></label><br>
        <input type="text" name="company"
          style="width:100%;padding:11px 12px;border:1px solid #d6dbe3;border-radius:12px;">
      </div>

      <div>
        <label><strong>E-post *</strong></label><br>
        <input type="email" name="email" required
          style="width:100%;padding:11px 12px;border:1px solid #d6dbe3;border-radius:12px;">
      </div>

      <div>
        <label><strong>Telefon</strong></label><br>
        <input type="text" name="phone"
          style="width:100%;padding:11px 12px;border:1px solid #d6dbe3;border-radius:12px;">
      </div>

      <div>
        <label><strong>Kategori *</strong></label><br>
        <input type="text" name="category" required
          value="<?php echo htmlspecialchars($prefill_cat, ENT_QUOTES, 'UTF-8'); ?>"
          placeholder="t.ex. Catering, Frisör, Fotograf…"
          style="width:100%;padding:11px 12px;border:1px solid #d6dbe3;border-radius:12px;">
        <div style="color:#5b6472;font-size:12px;margin-top:6px;">
          Tips: skriv den kategori du vill synas i.
        </div>
      </div>

      <div>
        <label><strong>Kommun *</strong></label><br>
        <input type="text" name="kommun" required
          value="<?php echo htmlspecialchars($prefill_kommun, ENT_QUOTES, 'UTF-8'); ?>"
          placeholder="t.ex. Borås stad"
          style="width:100%;padding:11px 12px;border:1px solid #d6dbe3;border-radius:12px;">
        <div style="color:#5b6472;font-size:12px;margin-top:6px;">
          Tips: länka hit från kommunsidan så blir detta ifyllt automatiskt.
        </div>
      </div>

      <div style="grid-column:1/-1;">
        <label><strong>Län</strong></label><br>
        <input type="text" name="lan"
          value="<?php echo htmlspecialchars($prefill_lan, ENT_QUOTES, 'UTF-8'); ?>"
          placeholder="t.ex. Västra Götalands län"
          style="width:100%;padding:11px 12px;border:1px solid #d6dbe3;border-radius:12px;">
      </div>

      <div style="grid-column:1/-1;">
        <label><strong>Meddelande</strong></label><br>
        <textarea name="message" rows="6"
          placeholder="Berätta kort vad ni erbjuder, länk till hemsida/Instagram och om ni vill synas i fler kommuner."
          style="width:100%;padding:11px 12px;border:1px solid #d6dbe3;border-radius:12px;"></textarea>
      </div>
    </div>

    <div style="margin-top:14px;display:flex;gap:12px;align-items:center;flex-wrap:wrap;">
      <button type="submit" style="padding:12px 18px;border:0;border-radius:12px;background:#093260;color:#fff;font-weight:900;">
        Skicka bokningsförfrågan →
      </button>
      <span style="color:#5b6472;font-size:13px;">
        Vi återkommer normalt snabbt på vardagar.
      </span>
    </div>

    <div style="margin-top:14px;color:#5b6472;font-size:12px;max-width:820px;line-height:1.45;">
      Genom att skicka in formuläret godkänner du att vi kontaktar dig med information om platsen är ledig och hur bokningen går till.
    </div>
  </form>

</div>

<?php $this->load->view('_footer'); ?>