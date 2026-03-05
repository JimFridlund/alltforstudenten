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

  // ✅ CHANGE + delegation
  $(document).on('change', 'select.lanSelect', function(){
    populateKommun($(this));
  });

  // Lägg till ny rad
  $("#addRow").click(function() {

    var rowCount = $("#myTable tr").length;
    var idx = rowCount - 1;

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

  <h1>Redigera annons</h1>

  <?php echo validation_errors('<div class="msg err">','</div>'); ?>

  <form method="post" enctype="multipart/form-data" action="">

    <fieldset>
      <legend>Uppgifter</legend>

      <p>
        <label>Rubrik</label><br />
        <input type="text" name="title" value="<?php echo $ad[0]->title; ?>" class="text" />
      </p>

      <p>
        <label>Kategori</label><br />
        <select name="category" class="cities">
          <option value="">Ingen kategori vald</option>
          <?php if(!empty($cat)): ?>
            <?php foreach($cat as $c): ?>
              <option value="<?php echo $c->id; ?>" <?php if($ad[0]->category == $c->id) echo 'selected="selected"'; ?>>
                <?php echo $c->title; ?>
              </option>
            <?php endforeach; ?>
          <?php endif; ?>
        </select>
      </p>

      <p>
        <label>Adress</label><br />
        <input type="text" name="adress" value="<?php echo $ad[0]->adress; ?>" class="text" />
      </p>

      <p>
        <label>Postnr</label><br />
        <input type="text" name="postnr" value="<?php echo $ad[0]->postnr; ?>" class="text" />
      </p>

      <p>
        <label>Ort</label><br />
        <input type="text" name="ort" value="<?php echo $ad[0]->ort; ?>" class="text" />
      </p>

      <p>
        <label>Telefon</label><br />
        <input type="text" name="tel" value="<?php echo $ad[0]->tel; ?>" class="text" />
      </p>

      <p>
        <label>Webb</label><br />
        <input type="text" name="www" value="<?php echo $ad[0]->www; ?>" class="text" />
      </p>

      <p>
        <label>E-post</label><br />
        <input type="text" name="email" value="<?php echo $ad[0]->email; ?>" class="text" />
      </p>

      <p>
        <label>Text info (tooltip)</label><br />
        <textarea name="text_info" class="text" style="height:80px;"><?php echo $ad[0]->text_info; ?></textarea>
      </p>

      <p>
        <label>Erbjudande (popup)</label><br />
        <textarea name="boka_text" class="text" style="height:120px;"><?php echo $ad[0]->boka_text; ?></textarea>
      </p>

      <p>
        <label>Fortnox OrderID</label><br />
        <input type="text" name="orderid" value="<?php echo $ad[0]->orderid; ?>" class="text" />
      </p>

      <p>
        <label>Säljare</label><br />
        <input type="text" name="seller" value="<?php echo $ad[0]->seller; ?>" class="text" />
      </p>

      <p>
        <label>Slutdatum</label><br />
        <input type="text" name="date_expire" value="<?php echo $ad[0]->date_expire; ?>" class="text" />
      </p>

      <p>
        <label>Logga</label><br />
        <input type="file" name="logo" />
        <?php if($ad[0]->logo != ""): ?>
          <br /><br />
          <img src="<?php echo base_url(); ?>uploads/logo/<?php echo $ad[0]->logo; ?>" style="max-width:200px;" />
          <br /><a href="<?php echo base_url(); ?>backend/tabort/logo/<?php echo $ad[0]->id; ?>">Ta bort logga</a>
        <?php endif; ?>
      </p>

    </fieldset>

    <fieldset>
      <legend>Län &amp; kommuner</legend>

      <table id="myTable" cellpadding="0" cellspacing="0" border="0" style="width:100%;">
        <tr>
          <td style="width:50%;"><strong>Län</strong></td>
          <td style="width:50%;"><strong>Kommun</strong></td>
        </tr>

        <?php
          $i = 0;
          if(!empty($regions)):
            foreach($regions as $r):
        ?>
          <tr>
            <td>
              <select name="field[<?php echo $i; ?>][main]" class="cities lanSelect">
                <option value="">Välj län</option>
                <?php foreach($lan as $l): ?>
                  <option value="<?php echo $l->id; ?>" <?php if($r->parent_id == $l->id) echo 'selected="selected"'; ?>>
                    <?php echo $l->title; ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </td>
            <td>
              <select name="field[<?php echo $i; ?>][sub]" class="cities kommunSelect">
                <?php
                  // Om du redan har kommun sparad: visa den som selected option
                  if(!empty($r->region_id) && !empty($r->title)):
                ?>
                  <option value="<?php echo $r->region_id; ?>" selected="selected"><?php echo $r->title; ?></option>
                <?php else: ?>
                  <option value="">Välj kommun</option>
                <?php endif; ?>
              </select>
            </td>
          </tr>
        <?php
              $i++;
            endforeach;
          else:
        ?>
          <tr>
            <td>
              <select name="field[0][main]" class="cities lanSelect">
                <option value="">Välj län</option>
                <?php foreach($lan as $l): ?>
                  <option value="<?php echo $l->id; ?>"><?php echo $l->title; ?></option>
                <?php endforeach; ?>
              </select>
            </td>
            <td>
              <select name="field[0][sub]" class="cities kommunSelect">
                <option value="">Välj kommun</option>
              </select>
            </td>
          </tr>
        <?php endif; ?>

      </table>

      <p style="margin-top:12px;">
        <a href="javascript:void(0);" id="addRow" class="btn">+ Lägg till fler kommuner</a>
      </p>

    </fieldset>

    <p>
      <input type="submit" value="Spara ändringar" class="btn" />
    </p>

  </form>

</div>

<?php $this->load->view('_footer'); ?>