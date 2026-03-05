<?php
$meta_title = 'Catering till student 2026 – meny, planering och checklista';
$meta_desc  = 'Guide till catering inför studenten 2026: portionsstorlek, menyval, allergier, logistik, tidplan och checklista för en lyckad mottagning.';
$meta_kw    = 'catering student 2026, studentmottagning catering, beställa catering studenten, studentbuffé, studentmat';

$this->load->view('_header');
$student_year = function_exists('afs_student_year') ? afs_student_year($this) : (int)date('Y');
?>

<div class="container">

  <div class="hero" style="margin-bottom:18px;">
    <img
      src="/images/bal-student/catering.png"
      alt="Catering till student <?php echo $student_year; ?>"
      style="width:100%; height:auto; display:block; border-radius:16px;"
      loading="eager"
    />
  </div>

  <p style="margin:0 0 10px;">
    <a href="<?php echo base_url(); ?>balen">← Tillbaka</a>
  </p>

  <h1>Catering till student <?php echo $student_year; ?> – så planerar du enkelt</h1>

  <p>
    Catering gör studentmottagningen mycket enklare – men det kräver lite planering.
    Här är en snabb guide till menyval, mängd, allergier och leverans.
  </p>

  <h2>När ska man boka catering?</h2>
  <ul>
    <li>Boka tidigt – studentveckan är högsäsong.</li>
    <li>Spika ungefärligt antal gäster i god tid.</li>
    <li>Kontrollera leveranstid och hur maten ska serveras.</li>
  </ul>

  <h2>Hur mycket mat behövs?</h2>
  <ul>
    <li>Räkna med att folk äter mer om det är många timmar och mycket aktivitet.</li>
    <li>Buffé är ofta enklast – men tänk på flöde och tallrikar/bestick.</li>
    <li>Ha alternativ för barn och personer som vill ha “enkelt”.</li>
  </ul>

  <h2>Allergier och specialkost</h2>
  <ul>
    <li>Fråga gästerna i god tid.</li>
    <li>Be cateringen märka upp rätter tydligt.</li>
    <li>Ha alltid ett par “säkra” alternativ.</li>
  </ul>

  <h2>Servering & logistik</h2>
  <ul>
    <li>Var ska maten stå? (skugga/kyla om det är varmt)</li>
    <li>Behövs kyl, värmeskåp eller ugn?</li>
    <li>Vem tar emot leveransen och ställer fram?</li>
  </ul>

  <h2>Checklista – catering</h2>
  <ul>
    <li>✔ Boka catering</li>
    <li>✔ Spika antal gäster (och marginal)</li>
    <li>✔ Samla allergier/specialkost</li>
    <li>✔ Planera leverans och serveringsyta</li>
    <li>✔ Säkerställ tallrikar/bestick/glas</li>
  </ul>

  <h2>Relaterat</h2>
  <ul>
    <li><a href="<?php echo base_url(); ?>balen/studentplakat">Studentplakat</a></li>
    <li><a href="<?php echo base_url(); ?>balen/studentflak">Studentflak</a></li>
  </ul>

</div>

<?php $this->load->view('_footer'); ?>