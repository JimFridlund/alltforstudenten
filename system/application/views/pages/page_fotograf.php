<?php
$meta_title = 'Fotograf till studentbalen 2026 – så får du bilder som blir bra';
$meta_desc  = 'Guide till fotograf inför studentbalen 2026: när du ska boka, vad som ingår, plats och tid för foto, pris, checklista och vanliga misstag.';
$meta_kw    = 'fotograf studentbal 2026, studentfotograf, balfotografering, boka fotograf bal, studentbilder, fotografering bal';

$this->load->view('_header');

// Säker student_year
$student_year = function_exists('afs_student_year') ? afs_student_year($this) : (int)date('Y');
?>

<div class="container">

  <!-- Hero (samma som övriga bal-sidor) -->
  <div class="hero" style="margin-bottom:18px;">
    <img
      src="/images/bal-student/fotograf.png"
      alt="Fotograf till studentbal <?php echo $student_year; ?>"
      style="width:100%; height:auto; display:block; border-radius:16px;"
      loading="eager"
    />
  </div>

  <p style="margin:0 0 10px;">
    <a href="<?php echo base_url(); ?>balen">← Tillbaka till Balen</a>
  </p>

  <h1>Fotograf till studentbalen <?php echo $student_year; ?> – boka smart</h1>

  <p>
    Bilderna från balen och studentperioden är ofta det som lever kvar längst.
    Här är en enkel guide för att få bra bilder utan stress – oavsett om du bokar fotograf eller fotar själv.
  </p>

  <h2>När ska man boka fotograf?</h2>
  <ul>
    <li>Så fort du vet ungefärligt datum och tidsfönster.</li>
    <li>Studentperioden är intensiv – tiderna går snabbt.</li>
    <li>Om ni är flera som vill boka samma fotograf: samordna tidigt.</li>
  </ul>

  <h2>Vad vill du ha för typ av bilder?</h2>
  <p>
    Bestäm vad du vill ha innan du bokar – då blir det lättare att välja rätt fotograf (och prisnivå).
  </p>
  <ul>
    <li>Porträtt (ensam)</li>
    <li>Parbilder</li>
    <li>Gruppbilder</li>
    <li>Detaljbilder (bukett, corsage, accessoarer)</li>
    <li>“Story”-bilder (på väg till balen, skratt, mingel)</li>
  </ul>

  <h2>Plats & tid – så planerar du fotograferingen</h2>
  <ul>
    <li>Välj en plats med bra ljus (skugga/”soft light” är oftast bäst).</li>
    <li>Planera in marginal: hår, smink och ombyte tar ofta längre tid än man tror.</li>
    <li>Ha en plan B om vädret blir dåligt (tak, entré, inomhusmiljö).</li>
  </ul>

  <h2>Vad ingår i ett fotopaket?</h2>
  <p>
    Det kan skilja mycket. Här är vanliga upplägg:
  </p>
  <ul>
    <li>Antal bilder (levererade/retuscherade)</li>
    <li>Fototid (t.ex. 30–90 min)</li>
    <li>Leverans (digitalt galleri, nedladdning)</li>
    <li>Retusch (ingår ibland, ibland extra)</li>
  </ul>

  <h2>Budget – vad kostar en fotograf?</h2>
  <ul>
    <li><strong>Kort pass:</strong> ofta billigare (snabba porträtt/grupp)</li>
    <li><strong>Längre pass:</strong> dyrare men mer variation</li>
    <li><strong>Retusch:</strong> kan påverka priset rejält</li>
  </ul>
  <p>
    Tips: dela kostnaden om ni är flera som fotas på samma tid/plats.
  </p>

  <h2>Checklista – fotograf</h2>
  <ul>
    <li>✔ Bestäm vilken typ av bilder du vill ha</li>
    <li>✔ Boka i god tid</li>
    <li>✔ Spika plats + plan B för väder</li>
    <li>✔ Samordna tider med frisör/makeup/transport</li>
    <li>✔ Packa smågrejer: hårspray, puder, vatten, plåster</li>
  </ul>

  <h2>Vanliga misstag</h2>
  <ul>
    <li>Boka för sent (bara “random tider” kvar)</li>
    <li>Ingen plan för väder</li>
    <li>För tajt schema mellan hår/smink och foto</li>
    <li>Otydligt vad som ingår i paketet</li>
  </ul>

  <h2>Relaterade guider</h2>
  <ul>
    <li><a href="<?php echo base_url(); ?>balen/frisor">Frisyr</a></li>
    <li><a href="<?php echo base_url(); ?>balen/makeup">Make up</a></li>
    <li><a href="<?php echo base_url(); ?>balen/transport">Transport</a></li>
    <li><a href="<?php echo base_url(); ?>balen/balklader">Balkläder</a></li>
  </ul>

  <h2>Hitta fotograf i din kommun</h2>
  <p>
    På våra kommunsidor hittar du rekommenderade aktörer per ort (en per kategori).
    Gå via län och kommun för att se vad som gäller där du bor.
  </p>

  <p>
    <a href="<?php echo base_url(); ?>visa">Sök via län och kommun</a>
  </p>

  <h2>Vanliga frågor</h2>
  <p><strong>Hur lång tid behövs för fotografering?</strong><br>30–60 min räcker ofta för porträtt och några gruppbilder.</p>
  <p><strong>Vad gör man om det regnar?</strong><br>Ha en plan B med tak/inomhus och kom i tid ändå.</p>
  <p><strong>Behöver man professionell fotograf?</strong><br>Nej – men det ger ofta bättre och mer konsekventa bilder.</p>

</div>

<?php $this->load->view('_footer'); ?>