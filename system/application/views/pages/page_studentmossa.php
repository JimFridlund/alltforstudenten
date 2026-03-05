<?php
$meta_title = 'Studentmössa 2026 – beställ i tid, text och checklista';
$meta_desc  = 'Guide till studentmössa 2026: när du ska beställa, vad du ska välja, text, storlek, tillbehör och checklista så du blir klar i tid.';
$meta_kw    = 'studentmössa 2026, beställa studentmössa, text studentmössa, studentmössa storlek, studentmössa tillbehör';

$this->load->view('_header');
$student_year = function_exists('afs_student_year') ? afs_student_year($this) : (int)date('Y');
?>

<div class="container">

  <div class="hero" style="margin-bottom:18px;">
    <img
      src="/images/bal-student/studentmossa.png"
      alt="Studentmössa <?php echo $student_year; ?>"
      style="width:100%; height:auto; display:block; border-radius:16px;"
      loading="eager"
    />
  </div>

  <p style="margin:0 0 10px;">
    <a href="<?php echo base_url(); ?>balen">← Tillbaka</a>
  </p>

  <h1>Studentmössa <?php echo $student_year; ?> – beställ i tid</h1>

  <p>
    Studentmössan är den klassiska symbolen för studenten – och en av de saker som flest stressar med i onödan.
    Här är en enkel guide så du väljer rätt och blir klar i tid.
  </p>

  <h2>När ska man beställa studentmössa?</h2>
  <ul>
    <li>Så tidigt som möjligt när beställningarna öppnar.</li>
    <li>Räkna med leveranstid och marginal om något blir fel.</li>
    <li>Om ni beställer via skolan/klass: håll koll på sista datum.</li>
  </ul>

  <h2>Storlek – så undviker du fel</h2>
  <ul>
    <li>Mät huvudets omkrets (enkel måttstock räcker).</li>
    <li>Om du är mellan två storlekar: välj hellre lite större än för tight.</li>
    <li>Testa hemma när den kommer – så du hinner åtgärda i tid.</li>
  </ul>

  <h2>Text och detaljer</h2>
  <p>
    Välj text som du fortfarande tycker är kul om några år.
    De vanligaste valen är namn, klass, skola eller en kort fras.
  </p>
  <ul>
    <li>Namn / smeknamn</li>
    <li>Skolans namn</li>
    <li>Examen/år</li>
    <li>Kort citat (håll det rumsrent)</li>
  </ul>

  <h2>Tillbehör som många glömmer</h2>
  <ul>
    <li>Mössband</li>
    <li>Mössbox</li>
    <li>Nål/tråd eller klämmor om något behöver fixas</li>
  </ul>

  <h2>Checklista – studentmössa</h2>
  <ul>
    <li>✔ Mät storlek</li>
    <li>✔ Välj text och detaljer</li>
    <li>✔ Beställ i tid</li>
    <li>✔ Prova när den kommer</li>
    <li>✔ Säkra tillbehör (band/box)</li>
  </ul>

  <h2>Relaterat</h2>
  <ul>
    <li><a href="<?php echo base_url(); ?>balen/studentplakat">Studentplakat</a></li>
    <li><a href="<?php echo base_url(); ?>balen/presenter">Presenter</a></li>
  </ul>

</div>

<?php $this->load->view('_footer'); ?>