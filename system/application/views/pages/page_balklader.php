<?php
$meta_title = 'Balkläder 2026 – klädkoder, tips och checklista';
$meta_desc  = 'Guide till balkläder inför studentbalen 2026: klädkod, balklänning/kostym, budget, passform och checklista. Plus vanliga frågor.';
$meta_kw    = 'balkläder 2026, studentbal kläder, balklänning studentbal, kostym studentbal, klädkod bal';

$this->load->view('_header');

// Fix: $student_year finns inte i view-scope, räkna ut här
$student_year = function_exists('afs_student_year') ? afs_student_year($this) : (int)date('Y');
?>

<div class="container">

  <!-- ✅ Behåll hero-bilden -->
  <div class="hero" style="margin-bottom:18px;">
    <img
      src="/images/bal-student/bal-student.png"
      alt="Studentbal <?php echo $student_year; ?>"
      style="width:100%; height:auto; display:block; border-radius:16px;"
      loading="eager"
    />
  </div>

  <p style="margin:0 0 10px;">
    <a href="<?php echo base_url(); ?>balen">← Tillbaka till Balen</a>
  </p>

  <h1>Balkläder <?php echo $student_year; ?> – klädkod, tips och checklista</h1>

  <p>
    Balkläder handlar inte om “dyrt” – utan om att känna sig bekväm, följa eventuell klädkod och vara redo för en lång kväll.
    Här får du en snabb guide till kläder inför studentbalen och en enkel checklista.
  </p>

  <h2>Klädkod – vad gäller på studentbalen?</h2>
  <p>
    Vissa skolor anger klädkod (t.ex. “mörk kostym” eller “kavaj”). Om din skola inte har sagt något: välj en nivå som känns festlig,
    men som du kan röra dig och dansa i.
  </p>
  <ul>
    <li><strong>Osäker?</strong> Fråga skolan/kommittén eller kolla tidigare års bilder.</li>
    <li><strong>Komfort slår allt:</strong> du ska orka flera timmar.</li>
  </ul>

  <h2>Balklänning eller kostym?</h2>
  <p>
    Det vanligaste är balklänning eller kostym – men du väljer det du trivs i. Det viktiga är helheten: skor, passform och att du kan röra dig.
  </p>

  <h3>Balklänning – snabbguide</h3>
  <ul>
    <li>Prova i god tid (storlekar tar slut under våren).</li>
    <li>Planera <strong>skor</strong> samtidigt – längd och fall påverkas.</li>
    <li>Om du behöver justera: räkna med tid för skräddare.</li>
  </ul>

  <h3>Kostym – snabbguide</h3>
  <ul>
    <li>Passform är allt: axlarna ska sitta bra.</li>
    <li>Välj en skjorta som inte stramar i halsen (du kommer tacka dig själv).</li>
    <li>Testa helheten hemma: kavaj + skor + eventuellt fluga/slips.</li>
  </ul>

  <h2>Budget – vad kostar balkläder?</h2>
  <ul>
    <li><strong>Budget:</strong> second hand / hyra / enklare set</li>
    <li><strong>Mellan:</strong> nytt men prisvärt (vanligast)</li>
    <li><strong>Premium:</strong> skräddat / designer / komplett styling</li>
  </ul>
  <p>
    Tips: många lägger mer på kläder än man först tror – eftersom skor, accessoarer och “smågrejer” tillkommer.
  </p>

  <h2>Checklista – balkläder</h2>
  <ul>
    <li>✔ Bestäm outfit (klänning/kostym)</li>
    <li>✔ Skor (och prova dem i god tid)</li>
    <li>✔ Accessoarer (smycken, bälte, väska)</li>
    <li>✔ Plan för ytterplagg (svensk juni kan vara kylig)</li>
    <li>✔ “Räddningskit”: skavsårsplåster, nål & tråd, extra hårnålar</li>
  </ul>

  <h2>Relaterade guider</h2>
  <ul>
    <li><a href="<?php echo base_url(); ?>balen/skor">Skor till balen</a></li>
    <li><a href="<?php echo base_url(); ?>balen/frisor">Frisyr till balen</a></li>
    <li><a href="<?php echo base_url(); ?>balen/makeup">Make up till balen</a></li>
    <li><a href="<?php echo base_url(); ?>balen/blommor">Blommor & corsage</a></li>
  </ul>

  <h2>Hitta datum i din kommun</h2>
  <p>
    Balen kan ligga på olika datum beroende på skola. Vi visar baldatum när skolan publicerat det officiellt.
  </p>
  <p>
    <a href="<?php echo base_url(); ?>visa">Sök via län och kommun</a>
    eller
    <a href="<?php echo base_url(); ?>gymnasieskolor">sök gymnasieskola</a>.
  </p>

  <h2>Vanliga frågor</h2>
  <p><strong>Måste man ha lång klänning?</strong><br>Nej – välj det som passar dig och skolans upplägg.</p>
  <p><strong>När ska man köpa/hyra?</strong><br>Ju tidigare desto bättre, särskilt under våren när mycket tar slut och bokas upp.</p>
  <p><strong>Vad glöms oftast?</strong><br>Skor som inte är ingångna, ytterplagg och små “räddningskit”.</p>

</div>

<?php $this->load->view('_footer'); ?>