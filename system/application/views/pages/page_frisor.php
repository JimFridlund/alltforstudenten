<?php
$meta_title = 'Frisyr till studentbalen 2026 – boka rätt i tid';
$meta_desc  = 'Guide till frisyr inför studentbalen 2026: boka i tid, välj stil, provuppsättning, hållbarhet och checklista inför dagen.';
$meta_kw    = 'frisyr bal 2026, balfrisyr, studentbal frisör, uppsättning bal, boka frisör student';

$this->load->view('_header');

// Säker student_year
$student_year = function_exists('afs_student_year') ? afs_student_year($this) : (int)date('Y');
?>

<div class="container">

  <!-- Hero (samma som övriga bal-sidor) -->
  <div class="hero" style="margin-bottom:18px;">
    <img
      src="/images/bal-student/frisor.png"
      alt="Frisyr till studentbal <?php echo $student_year; ?>"
      style="width:100%; height:auto; display:block; border-radius:16px;"
      loading="eager"
    />
  </div>

  <p style="margin:0 0 10px;">
    <a href="<?php echo base_url(); ?>balen">← Tillbaka till Balen</a>
  </p>

  <h1>Frisyr till studentbalen <?php echo $student_year; ?> – boka i tid</h1>

  <p>
    Frisören är en av de tjänster som blir fullbokad snabbast inför studentperioden.
    Vill du ha en specifik stylist eller tid på dagen – boka tidigt.
  </p>

  <h2>När ska man boka frisör inför balen?</h2>
  <ul>
    <li>Så fort baldatum är klart.</li>
    <li>Helger i maj/juni blir fullbokade först.</li>
    <li>Populära salonger kan vara fulla flera månader i förväg.</li>
  </ul>

  <h2>Vilken balfrisyr ska man välja?</h2>
  <p>
    Välj en frisyr som matchar kläderna och som håller hela kvällen.
    Tänk på väder, dans och eventuell studentmössa senare under dagen.
  </p>

  <h3>Populära alternativ</h3>
  <ul>
    <li>Uppsättning</li>
    <li>Halvuppsatt</li>
    <li>Lösa lockar</li>
    <li>Sleek/stram look</li>
  </ul>

  <h2>Provuppsättning – behövs det?</h2>
  <p>
    Om du gör en mer avancerad frisyr är provuppsättning en trygg investering.
    Då vet du exakt hur det kommer se ut och hur lång tid det tar.
  </p>

  <h2>Checklista inför frisörbesöket</h2>
  <ul>
    <li>✔ Ta med inspirationsbilder</li>
    <li>✔ Kom med rent, torrt hår (om salongen inte säger annat)</li>
    <li>✔ Ha kläder som är lätta att ta av utan att förstöra frisyren</li>
    <li>✔ Planera tiden så du inte stressar vidare till fotografering</li>
  </ul>

  <h2>Budget – vad kostar balfrisyr?</h2>
  <ul>
    <li>Enklare styling: lägre pris</li>
    <li>Avancerad uppsättning: högre pris</li>
    <li>Provuppsättning: ofta separat kostnad</li>
  </ul>

  <h2>Relaterade guider</h2>
  <ul>
    <li><a href="<?php echo base_url(); ?>balen/makeup">Make up till balen</a></li>
    <li><a href="<?php echo base_url(); ?>balen/balklader">Balkläder</a></li>
    <li><a href="<?php echo base_url(); ?>balen/skor">Skor till balen</a></li>
  </ul>

  <h2>Hitta frisör i din kommun</h2>
  <p>
    På våra kommunsidor hittar du rekommenderad aktör per ort.
    Gå via län och kommun för att se vad som gäller där du bor.
  </p>

  <p>
    <a href="<?php echo base_url(); ?>visa">Sök via län och kommun</a>
  </p>

  <h2>Vanliga frågor</h2>
  <p><strong>Hur länge håller en balfrisyr?</strong><br>Med rätt produkter håller den hela kvällen.</p>
  <p><strong>Kan man fixa håret själv?</strong><br>Absolut – men testa i förväg så du vet att det håller.</p>
  <p><strong>Vad är vanligast?</strong><br>Uppsättningar och mjuka lockar är vanligast inför studentbalen.</p>

</div>

<?php $this->load->view('_footer'); ?>