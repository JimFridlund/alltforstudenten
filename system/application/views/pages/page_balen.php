<?php
$meta_title = 'Balen 2026 – guider och checklista till studentbal & student';
$meta_desc  = 'Navet för balen och studenten 2026: guider till balkläder, frisyr, make up, fotograf, transport, blommor, studentplakat, studentmössa, catering, studentflak och presenter.';
$meta_kw    = 'balen 2026, studentbal 2026, balkläder, frisyr bal, make up bal, fotograf studentbal, transport bal, studentplakat, studentmössa, catering student, studentflak, studentpresenter';

$this->load->view('_header');
$student_year = function_exists('afs_student_year') ? afs_student_year($this) : (int)date('Y');

$cardStyle = "display:block;padding:14px 14px;border-radius:14px;border:1px solid #e6eaf0;background:#f7f8fa;text-decoration:none;margin:0;";
$titleStyle = "font-weight:900;color:#0b2a4a;margin:0 0 4px;";
$descStyle  = "color:#5b6472;font-size:14px;line-height:1.45;margin:0;";
$gridStyle  = "display:grid;grid-template-columns:1fr 1fr;gap:12px;margin:14px 0 4px;";
?>

<div class="container">

  <h1>Balen <?php echo $student_year; ?> – guider och checklista</h1>

  <p>
    Här är navet till våra guider inför balen och studentperioden.
    Välj det du planerar just nu – och gå vidare till din kommun/skola för datum när du behöver det.
  </p>

  <hr>

  <h2>1) Inför balen</h2>

  <div style="<?php echo $gridStyle; ?>">
    <a style="<?php echo $cardStyle; ?>" href="<?php echo base_url(); ?>balen/balklader">
      <div style="<?php echo $titleStyle; ?>">Balkläder →</div>
      <p style="<?php echo $descStyle; ?>">Klädkod, passform, budget och checklista.</p>
    </a>

    <a style="<?php echo $cardStyle; ?>" href="<?php echo base_url(); ?>balen/frisor">
      <div style="<?php echo $titleStyle; ?>">Frisyr →</div>
      <p style="<?php echo $descStyle; ?>">Boka i tid, provuppsättning och hållbarhet.</p>
    </a>

    <a style="<?php echo $cardStyle; ?>" href="<?php echo base_url(); ?>balen/makeup">
      <div style="<?php echo $titleStyle; ?>">Make up →</div>
      <p style="<?php echo $descStyle; ?>">Bas som håller, setting och mini-kit.</p>
    </a>

    <a style="<?php echo $cardStyle; ?>" href="<?php echo base_url(); ?>balen/blommor">
      <div style="<?php echo $titleStyle; ?>">Blommor & corsage →</div>
      <p style="<?php echo $descStyle; ?>">Färgval, beställning och förvaring.</p>
    </a>

    <a style="<?php echo $cardStyle; ?>" href="<?php echo base_url(); ?>balen/fotograf">
      <div style="<?php echo $titleStyle; ?>">Fotograf →</div>
      <p style="<?php echo $descStyle; ?>">Plats, tid, paket och vanliga misstag.</p>
    </a>

    <a style="<?php echo $cardStyle; ?>" href="<?php echo base_url(); ?>balen/transport">
      <div style="<?php echo $titleStyle; ?>">Transport →</div>
      <p style="<?php echo $descStyle; ?>">Tider, bokning och hemresa.</p>
    </a>

    <a style="<?php echo $cardStyle; ?>" href="<?php echo base_url(); ?>balen/slips">
      <div style="<?php echo $titleStyle; ?>">Knyta slips →</div>
      <p style="<?php echo $descStyle; ?>">Snabb guide med enkla knutar.</p>
    </a>
  </div>

  <hr>

  <h2>2) Inför studenten</h2>

  <div style="<?php echo $gridStyle; ?>">
    <a style="<?php echo $cardStyle; ?>" href="<?php echo base_url(); ?>balen/studentmossa">
      <div style="<?php echo $titleStyle; ?>">Studentmössa →</div>
      <p style="<?php echo $descStyle; ?>">Storlek, text och beställning i tid.</p>
    </a>

    <a style="<?php echo $cardStyle; ?>" href="<?php echo base_url(); ?>balen/studentplakat">
      <div style="<?php echo $titleStyle; ?>">Studentplakat →</div>
      <p style="<?php echo $descStyle; ?>">Bild, text och material som syns.</p>
    </a>

    <a style="<?php echo $cardStyle; ?>" href="<?php echo base_url(); ?>balen/catering">
      <div style="<?php echo $titleStyle; ?>">Catering →</div>
      <p style="<?php echo $descStyle; ?>">Meny, allergier och leveranslogistik.</p>
    </a>

    <a style="<?php echo $cardStyle; ?>" href="<?php echo base_url(); ?>balen/studentflak">
      <div style="<?php echo $titleStyle; ?>">Studentflak →</div>
      <p style="<?php echo $descStyle; ?>">Boka tidigt, regler och säkerhet.</p>
    </a>

    <a style="<?php echo $cardStyle; ?>" href="<?php echo base_url(); ?>balen/presenter">
      <div style="<?php echo $titleStyle; ?>">Presenter →</div>
      <p style="<?php echo $descStyle; ?>">Budget, idéer och vad som passar.</p>
    </a>
  </div>

  <hr>

  <h2>Planera i rätt ordning</h2>
  <ol>
    <li>Spika kläder (så resten kan matchas).</li>
    <li>Boka frisör/makeup/fotograf i god tid (tiderna tar slut).</li>
    <li>Planera transport och tider (foto → bal → hemresa).</li>
    <li>Fixa mössa, plakat och mottagning (catering/presenter).</li>
  </ol>

  <hr>

  <h2>Hitta datum för bal & student</h2>
  <p>
    Datum varierar mellan skolor. Vi visar baldatum när skolan publicerat det officiellt.
  </p>
  <p>
    <a href="<?php echo base_url(); ?>visa">Sök via län och kommun</a>
    eller
    <a href="<?php echo base_url(); ?>gymnasieskolor">sök gymnasieskola</a>.
  </p>

</div>

<?php $this->load->view('_footer'); ?>