<?php $this->load->view('backend/_header'); ?>

<script type="text/javascript">
$(document).ready(function() {

  function populateKommun($lanSelect){
    var id = $lanSelect.val();
    var $row = $lanSelect.closest('tr');
    var $kommunSelect = $row.find('select.kommunSelect');

    if(id != "") {
      $.post("<?php echo base_url(); ?>ajax/populate_kommun", {id: id}, function(data) {
        $kommunSelect.html(data);
        $kommunSelect.show();
        $kommunSelect.animate({ opacity: 0.1 }, 100).animate({ opacity: 1.0 }, 100);
      });
    } else {
      $kommunSelect.html('<option value="">Välj kommun</option>');
      $kommunSelect.animate({ opacity: 0.1 }, 100).animate({ opacity: 1.0 }, 100);
    }
  }

  // ✅ Viktigt: lyssna på CHANGE och med delegation (så nya rader funkar)
  $(document).on('change', 'select.lanSelect', function(){
    populateKommun($(this));
  });

  // Lägg till ny
  $("#addRow").click(function() {

    // Räknare (för unika name-index)
    var rowCount = $("#myTable tr").length;
    var idx = rowCount - 1; // första raden är index 0

    var newRow = '';
    newRow += '<tr>';
    newRow += '  <td>';
    newRow += '    <select name="field['+idx+'][main]" class="cities lanSelect">';
    newRow += '      <option value="">Välj län</option>';
<?php if(!empty($lan)): ?>
<?php foreach($lan as $l): ?>
    newRow += '      <option value="<?php echo $l->id; ?>"><?php echo addslashes($l->title); ?></option>';
<?php endforeach; ?>
<?php endif; ?>
    newRow += '    </select>';
    newRow += '  </td>';
    newRow += '  <td>';
    newRow += '    <select name="field['+idx+'][sub]" class="cities kommunSelect">';
    newRow += '      <option value="">Välj kommun</option>';
    newRow += '    </select>';
    newRow += '  </td>';
    newRow += '</tr>';

    $('#myTable tr:last').after(newRow);
  });

});
</script>

<div id="content">

  <h1>Lägg till annons</h1>

  <?php echo validation_errors('<div class="msg err">','</div>'); ?>

  <form method="post" enctype="multipart/form-data" action="">

    <fieldset>
      <legend>Uppgifter</legend>

      <p>
        <label>Rubrik</label><br />
        <input type="text" name="title" value="<?php echo set_value('title'); ?>" class="text" />
      </p>

      <p>
        <label>Kategori</label><br />

        <!-- OBS: Om du kör multi-kategori (category[]) måste view_add_ad.php matcha det.
             Om du INTE kör multi just nu, behåll single-select som nedan. -->
        <select name="category" class="cities">
          <option value="">Ingen kategori vald</option>
          <?php if(!empty($cat)): ?>
            <?php foreach($cat as $c): ?>
              <option value="<?php echo $c->id; ?>"><?php echo $c->title; ?></option>
            <?php endforeach; ?>
          <?php endif; ?>
        </select>
      </p>

      <p>
        <label>Adress</label><br />
        <input type="text" name="adress" value="<?php echo set_value('adress'); ?>" class="text" />
      </p>

      <p>
        <label>Postnr</label><br />
        <input type="text" name="postnr" value="<?php echo set_value('postnr'); ?>" class="text" />
      </p>

      <p>
        <label>Ort</label><br />
        <input type="text" name="ort" value="<?php echo set_value('ort'); ?>" class="text" />
      </p>

      <p>
        <label>Telefon</label><br />
        <input type="text" name="tel" value="<?php echo set_value('tel'); ?>" class="text" />
      </p>

      <p>
        <label>Webb</label><br />
        <input type="text" name="www" value="<?php echo set_value('www'); ?>" class="text" />
      </p>

      <p>
        <label>E-post</label><br />
        <input type="text" name="email" value="<?php echo set_value('email'); ?>" class="text" />
      </p>

      <p>
        <label>Text info (tooltip)</label><br />
        <textarea name="text_info" class="text" style="height:80px;"><?php echo set_value('text_info'); ?></textarea>
      </p>

      <p>
        <label>Erbjudande (popup)</label><br />
        <textarea name="boka_text" class="text" style="height:120px;"><?php echo set_value('boka_text'); ?></textarea>
      </p>

      <p>
        <label>Fortnox OrderID</label><br />
        <input type="text" name="orderid" value="<?php echo set_value('orderid'); ?>" class="text" />
      </p>

      <p>
        <label>Säljare</label><br />
        <input type="text" name="seller" value="<?php echo set_value('seller'); ?>" class="text" />
      </p>

      <p>
        <label>Slutdatum</label><br />
        <input type="text" name="date_expire" value="<?php echo set_value('date_expire'); ?>" class="text" />
      </p>

      <p>
        <label>Logga</label><br />
        <input type="file" name="logo" />
      </p>

    </fieldset>

    <fieldset>
      <legend>Län &amp; kommuner</legend>

      <table id="myTable" cellpadding="0" cellspacing="0" border="0" style="width:100%;">
        <tr>
          <td style="width:50%;"><strong>Län</strong></td>
          <td style="width:50%;"><strong>Kommun</strong></td>
        </tr>

        <tr>
          <td>
            <select name="field[0][main]" class="cities lanSelect">
              <option value="">Välj län</option>
              <?php if(!empty($lan)): ?>
                <?php foreach($lan as $l): ?>
                  <option value="<?php echo $l->id; ?>"><?php echo $l->title; ?></option>
                <?php endforeach; ?>
              <?php endif; ?>
            </select>
          </td>
          <td>
            <select name="field[0][sub]" class="cities kommunSelect">
              <option value="">Välj kommun</option>
            </select>
          </td>
        </tr>
      </table>

      <p style="margin-top:12px;">
        <a href="javascript:void(0);" id="addRow" class="btn">+ Lägg till fler kommuner</a>
      </p>

    </fieldset>

    <p>
      <input type="submit" value="Spara annons" class="btn" />
    </p>

  </form>

</div>

<?php $this->load->view('_footer'); ?>