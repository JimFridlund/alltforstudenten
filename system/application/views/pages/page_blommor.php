<?php
$meta_title = 'Blommor till studentbalen 2026 – bukett, corsage och tips';
$meta_desc  = 'Guide till blommor inför studentbalen 2026: bukett till studenten, corsage till balen, färgval, band, beställning i tid och checklista.';
$meta_kw    = 'blommor studentbal 2026, corsage bal, bukett student, studentblommor, balbukett, beställa blommor bal';

$this->load->view('_header');

// Säker student_year
$student_year = function_exists('afs_student_year') ? afs_student_year($this) : (int)date('Y');
?>

<div class="container">

  <!-- Hero (samma som övriga bal-sidor) -->
  <div class="hero" style="margin-bottom:18px;">
    <img
      src="/images/bal-student/blommor.png"
      alt="Blommor till studentbal <?php echo $student_year; ?>"
      style="width:100%; height:auto; display:block; border-radius:16px;"
      loading="eager"
    />
  </div>

  <p style="margin:0 0 10px;">
    <a href="<?php echo base_url(); ?>balen">← Tillbaka till Balen</a>
  </p>

  <h1>Blommor till studentbalen <?php echo $student_year; ?> – bukett & corsage</h1>

  <p>
    Blommor är en klassiker under studenten och balen – både på bild och i traditioner.
    Här får du en enkel guide till bukett, corsage och hur du planerar så att allt blir klart i tid.
  </p>

  <h2>Bukett eller corsage – vad är vad?</h2>
  <ul>
    <li><strong>Bukett:</strong> hålls i handen eller ges vid gratulation/foto.</li>
    <li><strong>Corsage:</strong> liten blomsterdekoration som ofta bärs på handleden eller kavajslaget.</li>
  </ul>

  <h2>Färgval som funkar (och ser bra ut på bild)</h2>
  <p>
    Välj färger som matchar kläderna – men tänk också på helhetskänslan i bilderna.
    Ofta blir det snyggast om blommorna plockar upp 1–2 färger från outfiten.
  </p>
  <ul>
    <li>Neutralt och säkert: vitt, crème, grönt</li>
    <li>Mer fest: pastell, rosa, ljusblått</li>
    <li>Kontrast: en tydlig accentfärg som matchar detaljer (t.ex. slips/band)</li>
  </ul>

  <h2>Beställ i tid – särskilt i juni</h2>
  <p>
    Under studentperioden är florister ofta hårt bokade.
    Om du vill ha specifika färger eller en viss stil: beställ i god tid och var tydlig med datum/tid.
  </p>
  <ul>
    <li>Beställ senast 1–2 veckor innan om du vill vara helt safe.</li>
    <li>Vill du ha något mer avancerat: beställ ännu tidigare.</li>
    <li>Bekräfta upphämtningstid så du slipper stress på dagen.</li>
  </ul>

  <h2>Band, kort och små detaljer</h2>
  <ul>
    <li>Band i matchande färg gör mycket för helhetsintrycket.</li>
    <li>Ett kort med en kort hälsning är en fin detalj.</li>
    <li>Tänk på hållbarhet om det är varmt: välj blommor som klarar värme bättre.</li>
  </ul>

  <h2>Checklista – blommor</h2>
  <ul>
    <li>✔ Bestäm bukett och/eller corsage</li>
    <li>✔ Välj färger som matchar outfit</li>
    <li>✔ Beställ i tid (studentperioden blir snabbt fullbokad)</li>
    <li>✔ Bekräfta upphämtningstid</li>
    <li>✔ Förvara svalt fram till foto/bal</li>
  </ul>

  <h2>Vanliga misstag</h2>
  <ul>
    <li>Beställa för sent (begränsat utbud kvar)</li>
    <li>Inte planera upphämtningstid</li>
    <li>Välja väldigt känsliga blommor i värme utan plan för förvaring</li>
    <li>Färger som krockar med outfiten (särskilt på bild)</li>
  </ul>

  <h2>Relaterade guider</h2>
  <ul>
    <li><a href="<?php echo base_url(); ?>balen/fotograf">Fotograf</a></li>
    <li><a href="<?php echo base_url(); ?>balen/balklader">Balkläder</a></li>
    <li><a href="<?php echo base_url(); ?>balen/frisor">Frisyr</a></li>
    <li><a href="<?php echo base_url(); ?>balen/makeup">Make up</a></li>
  </ul>

  <h2>Hitta florist i din kommun</h2>
  <p>
    På våra kommunsidor hittar du rekommenderade aktörer per ort (en per kategori).
    Gå via län och kommun för att se vad som gäller där du bor.
  </p>

  <p>
    <a href="<?php echo base_url(); ?>visa">Sök via län och kommun</a>
  </p>

  <h2>Vanliga frågor</h2>
  <p><strong>Behöver man corsage?</strong><br>Nej – men det är en fin tradition och ser bra ut på bild.</p>
  <p><strong>Hur länge håller en bukett?</strong><br>Ofta hela dagen om den förvaras svalt och får vatten vid behov.</p>
  <p><strong>Vad är bäst att beställa?</strong><br>Något som matchar outfiten och som klarar värme om det är en varm dag.</p>

</div>

<?php $this->load->view('_footer'); ?>