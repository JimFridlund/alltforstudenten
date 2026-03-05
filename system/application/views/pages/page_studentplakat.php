<?php
$meta_title = 'Studentplakat 2026 – tips, textidéer och checklista';
$meta_desc  = 'Guide till studentplakat 2026: storlek, material, bildval, textidéer, leveranstid och checklista så du blir klar i tid.';
$meta_kw    = 'studentplakat 2026, studentskylt 2026, skylt student, text studentplakat, beställa studentplakat, studentplakat bild';

$this->load->view('_header');
$student_year = function_exists('afs_student_year') ? afs_student_year($this) : (int)date('Y');
?>

<div class="container">

  <div class="hero" style="margin-bottom:18px;">
    <img
      src="/images/bal-student/studentplakat1.png"
      alt="Studentplakat <?php echo $student_year; ?>"
      style="width:100%; height:auto; display:block; border-radius:16px;"
      loading="eager"
    />
  </div>

  <p style="margin:0 0 10px;">
    <a href="<?php echo base_url(); ?>balen">← Tillbaka</a>
  </p>

  <h1>Studentplakat <?php echo $student_year; ?> – bli klar i tid</h1>

  <p>
    Studentplakatet är det som syns mest på utspringet. Här är en enkel guide till bild, text, storlek och beställning – så att du slipper stress sista veckan.
  </p>

  <h2>När ska man fixa studentplakat?</h2>
  <ul>
    <li>Så fort du har en bra bild och vet datum.</li>
    <li>Räkna med leveranstid om du beställer.</li>
    <li>Om du gör det själv: planera tid för utskrift/montering.</li>
  </ul>

  <h2>Bild som funkar</h2>
  <ul>
    <li>Välj en tydlig bild med bra ljus.</li>
    <li>Ansiktet ska synas även på håll.</li>
    <li>Undvik för mycket bakgrund – enkelhet vinner.</li>
  </ul>

  <h2>Textidéer</h2>
  <ul>
    <li>Förnamn + efternamn (enkelt och tydligt)</li>
    <li>Smeknamn</li>
    <li>En kort “one-liner”</li>
    <li>En intern grej (om den är rumsren 😄)</li>
  </ul>

  <h2>Storlek & material</h2>
  <ul>
    <li>Större syns bättre – men ska gå att hålla upp länge.</li>
    <li>Välj stabilt material som inte viker sig.</li>
    <li>Se till att pinne/handtag känns stabilt.</li>
  </ul>

  <h2>Checklista – studentplakat</h2>
  <ul>
    <li>✔ Välj bild</li>
    <li>✔ Bestäm text</li>
    <li>✔ Beställ / skriv ut i tid</li>
    <li>✔ Kontrollera att pinne/handtag är stabilt</li>
    <li>✔ Ha backup (tejpa extra om det behövs)</li>
  </ul>

  <h2>Relaterat</h2>
  <ul>
    <li><a href="<?php echo base_url(); ?>balen/catering">Catering</a></li>
    <li><a href="<?php echo base_url(); ?>balen/studentflak">Studentflak</a></li>
  </ul>

</div>

<?php $this->load->view('_footer'); ?>