<?php
$meta_title = 'Transport till studentbalen 2026 – planera smart och kom i tid';
$meta_desc  = 'Guide till transport inför studentbalen 2026: taxi, samåkning, buss, limousine, tider, budget och checklista för en stressfri baldag.';
$meta_kw    = 'transport studentbal 2026, taxi bal, limousine bal, samåkning bal, baltransport, komma i tid bal';

$this->load->view('_header');

// Säker student_year
$student_year = function_exists('afs_student_year') ? afs_student_year($this) : (int)date('Y');
?>

<div class="container">

  <!-- Hero (samma som övriga bal-sidor) -->
  <div class="hero" style="margin-bottom:18px;">
    <img
      src="/images/bal-student/transport.png"
      alt="Transport till studentbal <?php echo $student_year; ?>"
      style="width:100%; height:auto; display:block; border-radius:16px;"
      loading="eager"
    />
  </div>

  <p style="margin:0 0 10px;">
    <a href="<?php echo base_url(); ?>balen">← Tillbaka till Balen</a>
  </p>

  <h1>Transport till studentbalen <?php echo $student_year; ?> – planera i tid</h1>

  <p>
    Transporten låter som en liten grej – tills det blir stress, regn, sena tider och alla ska till samma plats.
    Här är en enkel guide för att komma i tid, slippa panik och få en bättre baldag.
  </p>

  <h2>Bestäm tider först: foto → resa → ankomst</h2>
  <p>
    Innan du väljer taxi, buss eller bil: spika ungefärliga tider.
    De flesta problem uppstår när man planerar transporten sist.
  </p>
  <ul>
    <li>Vilken tid ska ni vara klara för fotografering?</li>
    <li>När måste ni vara framme på balen?</li>
    <li>Hur långt är det mellan foto-plats och bal-plats?</li>
    <li>Behöver ni extra tid för ombyte/hår/smink?</li>
  </ul>

  <h2>Vanliga alternativ</h2>

  <h3>Samåkning</h3>
  <ul>
    <li>Billigt och flexibelt.</li>
    <li>Planera parkering i förväg om balen är centralt.</li>
    <li>Ha alltid backup ifall någon blir sen.</li>
  </ul>

  <h3>Taxi</h3>
  <ul>
    <li>Smidigt – men blir snabbt fullt under studentperioden.</li>
    <li>Boka i förväg om ni ska åka vid populära tider.</li>
    <li>Var tydlig med adress och tid, särskilt om ni är flera.</li>
  </ul>

  <h3>Buss / kollektivtrafik</h3>
  <ul>
    <li>Budgetvänligt.</li>
    <li>Kolla tidtabell och marginaler (särskilt på kvällen).</li>
    <li>Planera hemresan – kvällsturer kan vara glesa.</li>
  </ul>

  <h3>Limousine / specialtransport</h3>
  <ul>
    <li>Kul upplevelse – men boka tidigt om du vill ha specifik bil.</li>
    <li>Var tydlig med antal personer och rutt.</li>
    <li>Ha koll på tider så ni inte betalar för väntan i onödan.</li>
  </ul>

  <h2>Budget – vad kostar baltransport?</h2>
  <ul>
    <li><strong>Samåkning:</strong> ofta billigast (bränsle/parkering)</li>
    <li><strong>Taxi:</strong> varierar – kan bli dyrt om ni åker flera korta sträckor</li>
    <li><strong>Specialbil:</strong> högre pris, men delas ofta på flera</li>
  </ul>
  <p>
    Tips: dela kostnaden. Transport blir mycket billigare per person om ni är flera som åker ihop.
  </p>

  <h2>Checklista – transport</h2>
  <ul>
    <li>✔ Bestäm ungefärliga tider (foto → bal)</li>
    <li>✔ Boka/planera transport i god tid</li>
    <li>✔ Kolla parkering/avlämningsplats</li>
    <li>✔ Ha backup-plan om något drar ut på tiden</li>
    <li>✔ Planera hemresan (det glöms ofta)</li>
  </ul>

  <h2>Vanliga misstag</h2>
  <ul>
    <li>Planera transport sist och anta att “det löser sig”</li>
    <li>För lite marginal mellan hår/smink/foto och avfärd</li>
    <li>Ingen plan för hemresan</li>
    <li>Inte kolla parkering/avlämning – blir kaos vid ankomst</li>
  </ul>

  <h2>Relaterade guider</h2>
  <ul>
    <li><a href="<?php echo base_url(); ?>balen/fotograf">Fotograf</a></li>
    <li><a href="<?php echo base_url(); ?>balen/frisor">Frisyr</a></li>
    <li><a href="<?php echo base_url(); ?>balen/makeup">Make up</a></li>
    <li><a href="<?php echo base_url(); ?>balen/balklader">Balkläder</a></li>
  </ul>

  <h2>Hitta transport lokalt</h2>
  <p>
    På våra kommunsidor hittar du rekommenderade aktörer per ort (en per kategori).
    Gå via län och kommun för att se vad som gäller där du bor.
  </p>

  <p>
    <a href="<?php echo base_url(); ?>visa">Sök via län och kommun</a>
  </p>

  <h2>Vanliga frågor</h2>
  <p><strong>Behöver man boka taxi i förväg?</strong><br>Ofta ja, särskilt under studentperioden och vid populära tider.</p>
  <p><strong>Vad är smartast – taxi eller samåkning?</strong><br>Samåkning är billigast, taxi är smidigast. Välj efter tider och avstånd.</p>
  <p><strong>Hur undviker man stress?</strong><br>Planera tiderna först och lägg in extra marginal.</p>

</div>

<?php $this->load->view('_footer'); ?>