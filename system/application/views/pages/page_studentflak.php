<?php
$meta_title = 'Studentflak 2026 – boka i tid, regler och checklista';
$meta_desc  = 'Guide till studentflak 2026: boka i tid, säkerhet, regler, musik, dekorationer, budget och checklista för en bra och säker flakdag.';
$meta_kw    = 'studentflak 2026, boka studentflak, regler studentflak, flak student, studentflak säkerhet, musik studentflak';

$this->load->view('_header');
$student_year = function_exists('afs_student_year') ? afs_student_year($this) : (int)date('Y');
?>

<div class="container">

  <div class="hero" style="margin-bottom:18px;">
    <img
      src="/images/bal-student/studentflak.png"
      alt="Studentflak <?php echo $student_year; ?>"
      style="width:100%; height:auto; display:block; border-radius:16px;"
      loading="eager"
    />
  </div>

  <p style="margin:0 0 10px;">
    <a href="<?php echo base_url(); ?>balen">← Tillbaka</a>
  </p>

  <h1>Studentflak <?php echo $student_year; ?> – boka i tid och gör det säkert</h1>

  <p>
    Studentflaket är ofta dagens största “projekt”: bokning, tider, säkerhet och musik.
    Här är en enkel guide som hjälper er att planera smart och undvika de vanligaste misstagen.
  </p>

  <h2>När ska man boka studentflak?</h2>
  <ul>
    <li>Boka så tidigt som möjligt – populära datum försvinner först.</li>
    <li>Var tydliga med datum, tider och antal personer.</li>
    <li>Se till att ni vet var upphämtning/avsläpp sker.</li>
  </ul>

  <h2>Säkerhet & regler</h2>
  <p>
    Regler kan skilja sig mellan kommuner och arrangörer. Prioritera alltid säkerhet.
  </p>
  <ul>
    <li>Inga riskfyllda konstruktioner eller lösa delar som kan falla av.</li>
    <li>Ha tydliga ansvariga vuxna/klassansvariga enligt upplägg.</li>
    <li>Planera hur ni åker säkert – och hur ni tar er hem efteråt.</li>
  </ul>

  <h2>Musik, el och volym</h2>
  <ul>
    <li>Bestäm musikansvarig och testkör utrustningen i förväg.</li>
    <li>Kolla strömförsörjning och backup-plan om något dör.</li>
    <li>Håll koll på volym och omgivning – ni vill ha kul utan att få problem.</li>
  </ul>

  <h2>Dekorationer</h2>
  <ul>
    <li>Banderoller ska sitta ordentligt – inga fladdrande kanter.</li>
    <li>Välj tydlig text och stor kontrast.</li>
    <li>Tänk på väder: tejp och buntband räddar allt.</li>
  </ul>

  <h2>Budget – vad kostar studentflak?</h2>
  <ul>
    <li>Flak + chaufför (ofta största posten)</li>
    <li>Musik/ljud</li>
    <li>Dekorationer</li>
    <li>Eventuella tillstånd/extra kostnader beroende på upplägg</li>
  </ul>
  <p>Tips: dela upp ansvar och betalning tidigt så ingen sitter med allt i slutet.</p>

  <h2>Checklista – studentflak</h2>
  <ul>
    <li>✔ Boka flak och spika tider</li>
    <li>✔ Bestäm rutt/upphämtning/avsläpp</li>
    <li>✔ Säkerhet: vem ansvarar för vad?</li>
    <li>✔ Musik och el (testa i förväg)</li>
    <li>✔ Dekorationer (säkra ordentligt)</li>
    <li>✔ Plan för hemresa</li>
  </ul>

  <h2>Relaterat</h2>
  <ul>
    <li><a href="<?php echo base_url(); ?>balen/catering">Catering</a></li>
    <li><a href="<?php echo base_url(); ?>balen/studentplakat">Studentplakat</a></li>
  </ul>

</div>

<?php $this->load->view('_footer'); ?>