</main>

<footer class="siteFooter">
  <div class="container footerInner" style="padding:28px 0;">

    <div style="display:flex;flex-wrap:wrap;gap:22px;justify-content:space-between;align-items:flex-start;">

      <div style="min-width:260px;max-width:520px;">
        <div style="font-weight:950;font-size:16px;margin-bottom:8px;">
          AlltFörStudenten
        </div>

        <div style="color:#5b6472;line-height:1.55;">
          Checklistor, inspiration och lokala sidor inför studenten.
          Hitta rätt för din kommun — och hitta gymnasieskolor över hela Sverige.
        </div>

        <div style="margin-top:14px;color:#5b6472;font-size:13px;">
          Copyright &copy; 2011 - <?php echo date("Y"); ?>
          <a href="<?php echo base_url(); ?>" style="font-weight:900;text-decoration:none;">
            Alltforstudenten.se
          </a>
        </div>
      </div>

      <div style="min-width:220px;">
        <div style="font-weight:950;font-size:14px;margin-bottom:10px;">
          Snabblänkar
        </div>

        <div style="display:grid;gap:8px;">
          <a href="<?php echo base_url(); ?>" style="font-weight:900;text-decoration:none;">Hem</a>
          <a href="<?php echo base_url(); ?>gymnasieskolor" style="font-weight:900;text-decoration:none;">Gymnasieskolor</a>
          <a href="<?php echo base_url(); ?>balen" style="font-weight:900;text-decoration:none;">Bal</a>
          <a href="<?php echo base_url(); ?>om-oss" style="font-weight:900;text-decoration:none;">Om oss</a>
          <a href="<?php echo base_url(); ?>kontakt" style="font-weight:900;text-decoration:none;">Kontakt</a>

          <?php if(isset($this->dx_auth) && $this->dx_auth->is_logged_in()): ?>
            <a href="<?php echo base_url(); ?>backend" rel="nofollow" style="font-weight:900;text-decoration:none;">Admin</a>
          <?php endif; ?>
        </div>
      </div>

      <div style="min-width:220px;">
        <div style="font-weight:950;font-size:14px;margin-bottom:10px;">
          Sitemap
        </div>

        <div style="display:grid;gap:8px;">
          <a href="<?php echo base_url(); ?>sitemap.xml" style="font-weight:900;text-decoration:none;">Sitemap</a>
          <a href="<?php echo base_url(); ?>sitemap-skolor.xml" style="font-weight:900;text-decoration:none;">Sitemap skolor</a>
        </div>
      </div>

    </div>

    <div style="margin-top:22px;padding-top:16px;border-top:1px solid #e6eaf0;color:#5b6472;font-size:13px;">
      Tips: Sök din kommun för att hitta lokala checklistor och erbjudanden inför studenten.
    </div>

  </div>
</footer>

</body>
</html>