<?php
$meta_title = 'Knyta slips till studentbalen 2026 – enkla knutar steg för steg';
$meta_desc  = 'Snabb guide: knyta slips till studentbalen 2026. Lär dig enkla knutar, rätt längd, vanliga misstag och en checklista inför balen.';
$meta_kw    = 'knyta slips, slipsknut, enkel slipsknut, windsor, half windsor, four in hand, studentbal slips';

$this->load->view('_header');

// Säker student_year
$student_year = function_exists('afs_student_year') ? afs_student_year($this) : (int)date('Y');
?>

<div class="container">

  <!-- Hero (samma som övriga bal-sidor) -->
  <div class="hero" style="margin-bottom:18px;">
    <img
      src="/images/bal-student/slips.png"
      alt="Knyta slips till studentbal <?php echo $student_year; ?>"
      style="width:100%; height:auto; display:block; border-radius:16px;"
      loading="eager"
    />
  </div>

  <p style="margin:0 0 10px;">
    <a href="<?php echo base_url(); ?>balen">← Tillbaka till Balen</a>
  </p>

  <h1>Knyta slips till studentbalen <?php echo $student_year; ?> – snabbt och enkelt</h1>

  <p>
    Slipsen ska sitta bra, vara lagom lång och kännas bekväm hela kvällen.
    Här är en snabb guide med två enkla knutar och en liten checklista inför balen.
  </p>

  <h2>Rätt längd – så ska slipsen sitta</h2>
  <ul>
    <li>Slipsens spets ska sluta ungefär vid bältet (mitten av spännet).</li>
    <li>För lång slips ser slarvigt ut – för kort ser barnsligt ut.</li>
    <li>Knuten ska sitta tight mot kragen utan glapp.</li>
  </ul>

  <h2>Knuten som alltid funkar: Four-in-hand</h2>
  <p>
    Four-in-hand är den vanligaste knuten: enkel, snabb och funkar till nästan allt.
  </p>

  <ol>
    <li>Lägg slipsen runt halsen. Den breda delen ska hänga längre ner än den smala.</li>
    <li>Korsa den breda delen över den smala.</li>
    <li>Varva den breda delen runt en gång till (så det blir ett “lås”).</li>
    <li>För upp den breda delen bakom och upp genom öglan vid halsen.</li>
    <li>För ner den breda delen genom “fickan” du skapade framtill.</li>
    <li>Strama åt och justera. Dra knuten upp mot kragen.</li>
  </ol>

  <h2>Lite mer “bal”: Half Windsor</h2>
  <p>
    Half Windsor är lite mer symmetrisk och “dressad” men fortfarande enkel att lära sig.
  </p>

  <ol>
    <li>Lägg slipsen runt halsen med den breda delen längre ner.</li>
    <li>Korsa den breda delen över den smala.</li>
    <li>För den breda delen upp genom öglan vid halsen och ner igen.</li>
    <li>För den breda delen bakom den smala och ut på andra sidan.</li>
    <li>För upp den breda delen genom öglan vid halsen igen.</li>
    <li>För ner den genom “fickan” framtill, strama åt och justera.</li>
  </ol>

  <h2>Vanliga misstag</h2>
  <ul>
    <li>För kort eller för lång slips (kolla bältet).</li>
    <li>Knuten sitter för löst (glapp vid kragen).</li>
    <li>Slipsen är vriden (kolla att tyget ligger plant).</li>
    <li>Du testar första gången samma dag – öva minst en gång innan.</li>
  </ul>

  <h2>Checklista – slips</h2>
  <ul>
    <li>✔ Välj slips som matchar kostym/skjorta</li>
    <li>✔ Öva knuten minst en gång innan balen</li>
    <li>✔ Kolla längden (slutar vid bältet)</li>
    <li>✔ Ha en extra säkerhetsnål/liten nål om något släpper</li>
  </ul>

  <h2>Relaterade guider</h2>
  <ul>
    <li><a href="<?php echo base_url(); ?>balen/balklader">Balkläder</a></li>
    <li><a href="<?php echo base_url(); ?>balen/skor">Skor</a></li>
    <li><a href="<?php echo base_url(); ?>balen/fotograf">Fotograf</a></li>
    <li><a href="<?php echo base_url(); ?>balen/transport">Transport</a></li>
  </ul>

</div>

<?php $this->load->view('_footer'); ?>