<?php $this->load->view('_header'); ?>

<div id="left">
  <div class="left_box">
    <h1>Översikt - Alltförstudenten.se</h1>

    <p style="margin:0 0 14px;color:#5b6472;line-height:1.6;">
      Här hanterar du annonser, kategorier, län/kommuner, skolor och inställningar.
    </p>

    <div style="display:flex;flex-wrap:wrap;gap:10px;margin-top:10px;">
      <a href="<?php echo base_url(); ?>backend/annonser"
         style="display:inline-block;padding:10px 12px;border-radius:10px;background:#eaf2ff;color:#0b2a4a;font-weight:900;text-decoration:none;">
        Alla annonser
      </a>
      <a href="<?php echo base_url(); ?>backend/lagg_till/annons"
         style="display:inline-block;padding:10px 12px;border-radius:10px;background:#f5c542;color:#132033;font-weight:900;text-decoration:none;">
        Lägg till annons
      </a>
      <a href="<?php echo base_url(); ?>backend/kategorier"
         style="display:inline-block;padding:10px 12px;border-radius:10px;background:#f1f4f8;color:#1f2a37;font-weight:900;text-decoration:none;">
        Kategorier
      </a>
      <a href="<?php echo base_url(); ?>backend/regioner"
         style="display:inline-block;padding:10px 12px;border-radius:10px;background:#f1f4f8;color:#1f2a37;font-weight:900;text-decoration:none;">
        Län & kommuner
      </a>
      <a href="<?php echo base_url(); ?>backend/skolor"
         style="display:inline-block;padding:10px 12px;border-radius:10px;background:#f1f4f8;color:#1f2a37;font-weight:900;text-decoration:none;">
        Skolor
      </a>
      <a href="<?php echo base_url(); ?>backend/installningar"
         style="display:inline-block;padding:10px 12px;border-radius:10px;background:#f1f4f8;color:#1f2a37;font-weight:900;text-decoration:none;">
        Inställningar
      </a>
    </div>
  </div>
</div>

<div id="right">
  <div class="right_box">
    <?php $this->load->view('backend/_menu'); ?>
  </div>
</div>

<?php $this->load->view('_footer'); ?>