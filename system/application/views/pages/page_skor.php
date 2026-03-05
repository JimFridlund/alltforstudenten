<?php
$meta_title = 'Skor till studentbalen 2026 – välj rätt och undvik skoskav';
$meta_desc  = 'Guide till skor inför studentbalen 2026: bekväma val, hur du går in skor, sulor, klackhöjd, räddningskit och checklista.';
$meta_kw    = 'skor studentbal 2026, balskor, klackar bal, finskor bal, gå in skor, skoskav bal, sulor bal';

$this->load->view('_header');

// Säker student_year
$student_year = function_exists('afs_student_year') ? afs_student_year($this) : (int)date('Y');
?>

<div class="container">

  <!-- Hero (samma som övriga bal-sidor) -->
  <div class="hero" style="margin-bottom:18px;">
    <img
      src="/images/bal-student/skor.png"
      alt="Skor till studentbal <?php echo $student_year; ?>"
      style="width:100%; height:auto; display:block; border-radius:16px;"
      loading="eager"
    />
  </div>

  <p style="margin:0 0 10px;">
    <a href="<?php echo base_url(); ?>balen">← Tillbaka till Balen</a>
  </p>

  <h1>Skor till studentbalen <?php echo $student_year; ?> – bekvämt, snyggt och hållbart</h1>

  <p>
    De snyggaste skorna är inte värda något om du får skoskav efter 30 minuter.
    Här är en enkel guide till balskor som funkar från fotografering till dans – plus en checklista som räddar kvällen.
  </p>

  <h2>Välj skor efter hela kvällen – inte bara första bilden</h2>
  <ul>
    <li>Du ska stå, gå, sitta, dansa och ibland vara ute.</li>
    <li>Skor måste funka med kläderna – men också med dina fötter.</li>
    <li>Om du är osäker: välj hellre stabilt än extremt.</li>
  </ul>

  <h2>Klackar eller inte?</h2>
  <p>
    Det finns inget “måste” på studentbalen. Välj det du trivs i.
    Om du vill ha klack: välj stabil klack, prova hemma, och ha en plan B.
  </p>

  <h3>Trygga val om du vill ha klack</h3>
  <ul>
    <li>Blockklack</li>
    <li>Lägre klack</li>
    <li>Remmar som håller foten på plats</li>
  </ul>

  <h3>Trygga val utan klack</h3>
  <ul>
    <li>Fina ballerinaskor</li>
    <li>Loafers</li>
    <li>Dressade sneakers (om skolans upplägg tillåter)</li>
  </ul>

  <h2>Gå in skorna – så slipper du skoskav</h2>
  <ul>
    <li>Gå in dem hemma flera gånger innan balen.</li>
    <li>Testa med rätt strumpor/strumpbyxor.</li>
    <li>Prova på både hårt golv och mjukare underlag.</li>
    <li>Om de skaver direkt: åtgärda – hoppas inte att det “går över”.</li>
  </ul>

  <h2>Sulor, gel och småfix som gör stor skillnad</h2>
  <ul>
    <li>Gel-pads under framfoten kan avlasta vid klack.</li>
    <li>Hälgrepp gör att foten inte glider.</li>
    <li>Skavsårsplåster (riktiga – inte vanliga plåster).</li>
  </ul>

  <h2>Checklista – skor till balen</h2>
  <ul>
    <li>✔ Prova skorna med kläderna</li>
    <li>✔ Gå in dem (minst 2–3 gånger)</li>
    <li>✔ Packa räddningskit: skavsårsplåster, extra plåster, gel-pads</li>
    <li>✔ Ha plan B: extra skor i bil/väska om du kan</li>
  </ul>

  <h2>Vanliga misstag</h2>
  <ul>
    <li>Köpa skor för sent och gå dem “för första gången” på balen</li>
    <li>För hög klack utan stöd</li>
    <li>Ingen backup-plan</li>
    <li>Inte testa med strumpbyxor/strumpor som faktiskt ska användas</li>
  </ul>

  <h2>Relaterade guider</h2>
  <ul>
    <li><a href="<?php echo base_url(); ?>balen/balklader">Balkläder</a></li>
    <li><a href="<?php echo base_url(); ?>balen/frisor">Frisyr</a></li>
    <li><a href="<?php echo base_url(); ?>balen/makeup">Make up</a></li>
    <li><a href="<?php echo base_url(); ?>balen/transport">Transport</a></li>
  </ul>

  <h2>Hitta skor/kläder lokalt</h2>
  <p>
    På våra kommunsidor hittar du rekommenderade aktörer per ort (en per kategori).
    Gå via län och kommun för att se vad som gäller där du bor.
  </p>

  <p>
    <a href="<?php echo base_url(); ?>visa">Sök via län och kommun</a>
  </p>

  <h2>Vanliga frågor</h2>
  <p><strong>Hur hög klack är “lagom”?</strong><br>Det som känns stabilt för dig – och som du kan gå i minst några timmar.</p>
  <p><strong>Måste man ha finskor?</strong><br>Nej, men välj något som passar helheten och skolans upplägg.</p>
  <p><strong>Vad räddar kvällen mest?</strong><br>Skavsårsplåster + ingångna skor + en plan B.</p>

</div>

<?php $this->load->view('_footer'); ?>