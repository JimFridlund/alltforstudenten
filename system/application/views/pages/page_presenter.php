<?php
$meta_title = 'Studentpresenter 2026 – tips, budget och checklista';
$meta_desc  = 'Guide till studentpresenter 2026: presentidéer, budgetnivåer, vad som passar, smarta val och checklista så du blir klar i tid.';
$meta_kw    = 'studentpresenter 2026, present student, studentpresent tips, vad ger man i studentpresent, budget studentpresent';

$this->load->view('_header');
$student_year = function_exists('afs_student_year') ? afs_student_year($this) : (int)date('Y');
?>

<div class="container">

  <div class="hero" style="margin-bottom:18px;">
    <img
      src="/images/bal-student/presenter.png"
      alt="Studentpresenter <?php echo $student_year; ?>"
      style="width:100%; height:auto; display:block; border-radius:16px;"
      loading="eager"
    />
  </div>

  <p style="margin:0 0 10px;">
    <a href="<?php echo base_url(); ?>balen">← Tillbaka</a>
  </p>

  <h1>Studentpresenter <?php echo $student_year; ?> – idéer som funkar</h1>

  <p>
    Studentpresenter behöver inte vara krångligt. Det viktiga är att presenten känns genomtänkt – och att du blir klar i tid.
    Här är en enkel guide med idéer och budget.
  </p>

  <h2>Välj efter person – inte trend</h2>
  <ul>
    <li>Vad gillar personen? (resa, träning, teknik, inredning, mat)</li>
    <li>Vill du ge något praktiskt eller något minnesvärt?</li>
    <li>En mindre present + fint kort kan slå en “dyr grej” utan tanke.</li>
  </ul>

  <h2>Presentidéer (enkelt och säkert)</h2>
  <ul>
    <li>Pengar eller presentkort (med tydlig tanke: “till körkort”, “till resa”, “till boende”)</li>
    <li>Smycke eller klocka (klassiskt)</li>
    <li>Upplevelse (middag, spa, aktivitet)</li>
    <li>Hemmaprylar (om personen ska flytta hemifrån)</li>
    <li>Personlig present (fotobok, gravyr, minnessak)</li>
  </ul>

  <h2>Budget – tre nivåer</h2>
  <ul>
    <li><strong>Budget:</strong> mindre gåva + fint kort</li>
    <li><strong>Mellan:</strong> upplevelse / klassisk present</li>
    <li><strong>Premium:</strong> större investering (t.ex. resa, teknik, körkortsbidrag)</li>
  </ul>

  <h2>Checklista – studentpresent</h2>
  <ul>
    <li>✔ Bestäm budget</li>
    <li>✔ Välj present efter person</li>
    <li>✔ Köp i tid (studentveckan = mycket slutsålt)</li>
    <li>✔ Skriv kort</li>
    <li>✔ Slå in / ordna presenten</li>
  </ul>

  <h2>Relaterat</h2>
  <ul>
    <li><a href="<?php echo base_url(); ?>balen/catering">Catering</a></li>
    <li><a href="<?php echo base_url(); ?>balen/studentmossa">Studentmössa</a></li>
    <li><a href="<?php echo base_url(); ?>balen/studentplakat">Studentplakat</a></li>
  </ul>

</div>

<?php $this->load->view('_footer'); ?>