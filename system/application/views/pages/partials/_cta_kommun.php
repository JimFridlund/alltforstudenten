<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

$label    = isset($label) ? trim($label) : 'aktör';
$category = isset($category) ? trim($category) : '';
$href     = base_url().'visa'.($category !== '' ? ('?cat='.urlencode($category)) : '');
?>

<a href="<?php echo $href; ?>"
   style="
     display:block;
     text-decoration:none;
     color:inherit;
     margin:22px 0;
   ">

  <div style="
    background:#eef5ff;
    padding:20px;
    border-radius:16px;
    border:1px solid #cfe3ff;
    box-shadow:0 8px 22px rgba(9,50,96,0.10);
    transition:all 0.15s ease;
  "
  onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 12px 28px rgba(9,50,96,0.15)';"
  onmouseout="this.style.transform='none';this.style.boxShadow='0 8px 22px rgba(9,50,96,0.10)';"
  >

    <div style="display:flex; gap:14px; align-items:flex-start;">

      <div style="
        width:42px;
        height:42px;
        border-radius:12px;
        background:#093260;
        color:#fff;
        display:flex;
        align-items:center;
        justify-content:center;
        font-weight:900;
        font-size:18px;
        flex:0 0 auto;
      ">★</div>

      <div style="flex:1;">

        <h3 style="margin:0 0 8px; font-size:18px; line-height:1.25;">
          Se om vi rekommenderar en <?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?> i din kommun
        </h3>

        <p style="margin:0 0 12px; color:#233244;">
          På kommunsidan finns <strong>en aktör per kategori och ort</strong>.
          De som ligger där är de som syns – och bokas först.
        </p>

        <div style="
          display:inline-block;
          padding:6px 12px;
          border-radius:999px;
          background:#ffffff;
          border:1px solid #d6e7ff;
          font-size:13px;
          color:#0b2a4a;
          font-weight:600;
          margin-bottom:14px;
        ">
          Exklusivt: 1 plats per kommun
        </div>

        <div style="
          font-weight:800;
          color:#093260;
        ">
          Sök din kommun →
        </div>

      </div>
    </div>

  </div>

</a>