<?php
$meta_title = 'Make up till studentbalen 2026 – smink som håller hela kvällen';
$meta_desc  = 'Guide till smink inför studentbalen 2026: bas som håller, ögonlook, setting, bokning av makeupartist och checklista för baldagen.';
$meta_kw    = 'make up bal 2026, balsmink, studentbal smink, makeupartist studentbal, smink som håller, setting spray';

$this->load->view('_header');

// Säker student_year
$student_year = function_exists('afs_student_year') ? afs_student_year($this) : (int)date('Y');
?>

<div class="container">

  <!-- Hero (samma som övriga bal-sidor) -->
  <div class="hero" style="margin-bottom:18px;">
    <img
      src="/images/bal-student/makeup.png"
      alt="Make up till studentbal <?php echo $student_year; ?>"
      style="width:100%; height:auto; display:block; border-radius:16px;"
      loading="eager"
    />
  </div>

  <p style="margin:0 0 10px;">
    <a href="<?php echo base_url(); ?>balen">← Tillbaka till Balen</a>
  </p>

  <h1>Make up till studentbalen <?php echo $student_year; ?> – smink som håller</h1>

  <p>
    Balsmink ska se bra ut på bild, kännas bra i verkligheten och framför allt hålla hela kvällen.
    Här får du en enkel guide – oavsett om du sminkar dig själv eller bokar en makeupartist.
  </p>

  <h2>När ska man boka makeupartist?</h2>
  <ul>
    <li>Så fort du vet baldatum och ungefärlig tid för fotografering.</li>
    <li>Helger i maj/juni blir fullbokade snabbast.</li>
    <li>Om ni är flera som vill boka samma person: boka tidigt och samordna tider.</li>
  </ul>

  <h2>Smink som håller – grunden</h2>
  <p>
    Den största skillnaden mellan “vardagssmink” och “balsmink” är hållbarheten.
    Satsa på en bas som tål värme, dans och många timmar.
  </p>

  <h3>Bas</h3>
  <ul>
    <li>Förbered huden: återfuktning + primer (anpassad för din hudtyp).</li>
    <li>Välj foundation som du vet håller (testa minst en gång innan).</li>
    <li>Fixera med puder där du blir blank (inte överallt om du vill ha glow).</li>
  </ul>

  <h3>Ögon</h3>
  <ul>
    <li>Använd ögonprimer så skuggan sitter.</li>
    <li>Vattenfast mascara om du vill vara helt safe.</li>
    <li>Om du vill ha fransar: testa innan så du vet att du trivs.</li>
  </ul>

  <h3>Setting</h3>
  <ul>
    <li>Setting spray är din bästa vän.</li>
    <li>Ta med blotting paper/puder för touch-ups.</li>
  </ul>

  <h2>Välj look – vad passar till balen?</h2>
  <p>
    Välj en look som matchar kläderna och som känns som “du”.
    Du kommer vara fotograferad och synas i många timmar – så välj något du är bekväm i.
  </p>

  <ul>
    <li>Naturligt glow</li>
    <li>Soft glam</li>
    <li>Markerade ögon</li>
    <li>Fokus på läppar</li>
  </ul>

  <h2>Checklista – smink inför balen</h2>
  <ul>
    <li>✔ Testa looken i förväg (helst i dagsljus)</li>
    <li>✔ Se till att foundation matchar hals/axlar</li>
    <li>✔ Ta med mini-kit: puder, läpprodukt, tops, setting spray</li>
    <li>✔ Planera tiden: smink + hår + ombyte tar längre tid än man tror</li>
  </ul>

  <h2>Budget – vad kostar balsmink?</h2>
  <ul>
    <li>Sminka själv: billigare (men test krävs)</li>
    <li>Makeupartist: högre pris, men mer tryggt och ofta bättre hållbarhet</li>
    <li>Provsmink: kan vara separat kostnad men ger trygghet</li>
  </ul>

  <h2>Relaterade guider</h2>
  <ul>
    <li><a href="<?php echo base_url(); ?>balen/frisor">Frisyr till balen</a></li>
    <li><a href="<?php echo base_url(); ?>balen/balklader">Balkläder</a></li>
    <li><a href="<?php echo base_url(); ?>balen/skor">Skor till balen</a></li>
    <li><a href="<?php echo base_url(); ?>balen/fotograf">Fotograf</a></li>
  </ul>

  <h2>Hitta make up i din kommun</h2>
  <p>
    På våra kommunsidor hittar du rekommenderad aktör per ort.
    Gå via län och kommun för att se vad som gäller där du bor.
  </p>

  <p>
    <a href="<?php echo base_url(); ?>visa">Sök via län och kommun</a>
  </p>

  <h2>Vanliga frågor</h2>
  <p><strong>Behöver man setting spray?</strong><br>Inte ett måste – men det hjälper mycket för hållbarheten.</p>
  <p><strong>Vad är vanligast – mycket eller lite smink?</strong><br>Soft glam är vanligast: lite mer än vardag, men inte maskigt.</p>
  <p><strong>Kan jag sminka mig själv?</strong><br>Ja – men testa looken innan och se till att den håller flera timmar.</p>

</div>

<?php $this->load->view('_footer'); ?>