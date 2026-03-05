<?php $this->load->view('_header'); ?>

<div class="container" style="max-width:1120px;padding:18px 14px 40px;">

  <section style="background:#fff;border:1px solid #e6eaf0;border-radius:16px;padding:16px 16px 12px;box-shadow:0 8px 22px rgba(0,0,0,.05);">
    <h1 style="margin:0 0 10px;font-size:22px;font-weight:900;color:#0b2a4a;">
      <?php if(!empty($s)): ?>
        Sökresultat för "<?php echo htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); ?>"
      <?php else: ?>
        Sök
      <?php endif; ?>
    </h1>

    <?php echo form_open('sok'); ?>
      <div style="display:flex;gap:10px;flex-wrap:wrap;">
        <input
          type="text"
          name="s_value"
          value="<?php echo htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); ?>"
          placeholder="Sök på skola eller kommun…"
          style="flex:1;min-width:240px;border:1px solid #e6eaf0;border-radius:12px;padding:12px 12px;font-size:14px;"
        />
        <button type="submit" style="border:1px solid #e6eaf0;border-radius:12px;padding:12px 14px;font-weight:900;background:#f5c542;cursor:pointer;">
          Sök
        </button>
      </div>
    </form>

    <div style="margin-top:14px;color:#5b6472;font-size:13px;">
      Tips: skriv minst två tecken för autosuggest på startsidan. Den här sidan är bara fallback.
    </div>
  </section>

  <section style="margin-top:14px;background:#fff;border:1px solid #e6eaf0;border-radius:16px;padding:14px;box-shadow:0 8px 22px rgba(0,0,0,.05);">
    <?php if($result > 0): ?>
      <ul style="list-style:none;margin:0;padding:0;display:grid;gap:10px;">
        <?php foreach($result as $data): ?>
          <li style="border:1px solid #eef1f6;border-radius:14px;padding:12px;">
            <a
              href="<?php echo base_url(); ?>visa/<?php echo $this->studenten->parent_permalink($data->parent); ?>/<?php echo $data->permalink; ?>"
              style="text-decoration:none;font-weight:900;color:#0f4c81;"
              title="<?php echo htmlspecialchars($data->region_title, ENT_QUOTES, 'UTF-8'); ?>"
            >
              <?php echo htmlspecialchars($data->region_title, ENT_QUOTES, 'UTF-8'); ?>
            </a>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php else: ?>
      <div style="color:#5b6472;font-size:14px;">
        Din sökning genererade inga resultat.
      </div>
    <?php endif; ?>
  </section>

</div>

<?php $this->load->view('_footer'); ?>