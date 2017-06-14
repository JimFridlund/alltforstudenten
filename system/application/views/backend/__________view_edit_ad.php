<?php $this->load->view('_header'); ?>

<script src="<?php echo base_url() ?>public/js/jquery.livequery.js" type="text/javascript"></script>

<div id="left">
	<div class="left_box">
		<h1>Redigera annons</h1>
		
		<?php echo $this->session->flashdata('message'); ?>
		<?php if(validation_errors()) echo '<div id="validation_errors">'.validation_errors().'</div>'; ?>
		
		<?php $data = $ad[0]; $selected = ' selected="selected"'; ?>

		<?php echo form_open_multipart($this->uri->uri_string());?>
		<dl>
			<dt><label>Annonsens rubrik</label></dt>
			<dd>
				<input type="text" class="textfield" name="title" value="<?php echo $data->title; ?>" /> <div class="req" title="Obligatoriskt fält">Obligatorisk</div>
				<p>Fyll i annonsens rubrik.</p>
			</dd>
			
			<dd class="separator"></dd>
			
			<dt><label>Region &amp kommun</label></dt>
			<dd id="added_cats">
				<p>Inga kommuner tillagda</p>
				<ul>
					<?php if($regions != 0): ?>
					<?php foreach($regions as $rels): ?>
					<li id="region_<?php echo $rels->region_id; ?>"><strong><?php echo $this->studenten->get_kommun_parent_name($rels->region_id); ?></strong><input name="field[<?php echo $rels->region_id; ?>][main]" value="<?php echo $rels->region_id; ?>" type="hidden">&rarr; <?php echo $this->studenten->get_kommun_name($rels->region_id); ?> <span class="cat_remove" title="Ta bort">Ta bort</span></li>
					<?php endforeach; ?>
					<?php endif; ?>
				</ul>
			</dd>
			
			<dd id="cat_list">
				<div class="req" title="Obligatoriskt fält">Obligatorisk</div>
				<select name="lan" id="lan_main" size="5" class="textfield_select double">
					<option value="">Inget län valt</option>
					<?php if($lan > 0): ?>
					<?php foreach($lan as $data2): ?>
					<option value="<?php echo $data2->id; ?>"><?php echo $data2->title; ?></option>
					<?php endforeach;?>
					<?php else :?>
					<?php endif; ?>
				</select>
				
				<select name="region" id="lan_sub" size="10" class="textfield_select">
					<option value="" id="del">Ingen kommun vald</option>
				</select>
				<span id="add_cat" title="lägg till">+ Lägg till</span>
			</dd>
			
			<dd class="separator"></dd>
			
			<dt><label>Kategori</label></dt>
			<dd>
				<div class="req" title="Obligatoriskt fält">Obligatorisk</div>
				<select name="category" class="textfield_select">
					<option value="">Ingen kategori vald</option>
					<?php if($cat > 0): ?>
					<?php foreach($cat as $data3): ?>
					<option value="<?php echo $data3->id; ?>"<?php if($data->category == $data3->id) echo $selected;?>><?php echo $data3->title; ?></option>
					<?php endforeach;?>
					<?php endif; ?>
				</select>
				
				<p>Välj annonsens kategori.</p>
			</dd>
			
			<dd class="separator"></dd>
			
			<dt><label>Adress</label></dt>
			<dd>
				<input type="text" class="textfield_medium" name="adress" value="<?php echo $data->adress; ?>" /> <div class="req" title="Obligatoriskt fält">Obligatorisk</div>
				<p>Fyll i den stad där företaget finns.</p>
			</dd>
			
			<dd class="separator"></dd>
			
			<dt><label>Postnr &amp; ort</label></dt>
			<dd>
				<input type="text" class="textfield_postnr" name="postnr" value="<?php echo $data->postnr; ?>" /> <div class="req" title="Obligatoriskt fält">Obligatorisk</div>
				<input type="text" class="textfield_small" name="ort" value="<?php echo $data->ort; ?>" />
				<p>Fyll i den postnr &amp; ort där företaget finns.</p>
			</dd>
			
			<dd class="separator"></dd>
			
			<dt><label>Telefon</label></dt>
			<dd>
				<input type="text" class="textfield_medium" name="tel" value="<?php echo $data->tel; ?>" /> <div class="req" title="Obligatoriskt fält">Obligatorisk</div>
				<p>Fyll i företagets telefonnummer.</p>
			</dd>
			
			<dd class="separator"></dd>
			
			<dt><label>Hemsida</label></dt>
			<dd>
				<input type="text" class="textfield_medium" name="www" value="<?php echo $data->www; ?>" /> <div class="req" title="Obligatoriskt fält">Obligatorisk</div>
				<p>Fyll i URL till företagets hemsida. (anges utan http://)</p>
			</dd>
			
			<dd class="separator"></dd>
			
			<dt><label>Logotyp</label></dt>
			<dd>
				<?php if(isset($data->logo) AND $data->logo != ""): ?>
				<?php
				list($width, $height) = getimagesize(base_url().'uploads/logo/'. $data->logo);
				$size = $this->studenten->resize_dimensions(190, 75, $width, $height);
				$margin = $this->studenten->vertical_align(round($size['height']), 75);
				?>
				<div class="logo_container">
					<img src="<?php echo base_url() ?>uploads/logo/<?php echo $data->logo; ?>" style="margin-top: <?php echo $margin; ?>px" height="<?php echo $size['height']; ?>"  align="center" alt="" />
					<a href="<?php echo base_url(); ?>backend/tabort/logo/<?php echo $data->id; ?>" onclick="return delete_logo();" title="Ta bort logotypen">
					<img src="<?php echo base_url(); ?>public/images/icons/icon_delete.gif" class="remove_logo" alt="" /></a>
				</div>
				<?php endif; ?>
				<p><input type="file" id="upload" name="logo" /></p>
				<div class="alignleft"><p>Giltiga format är jpg, jpeg, gif och png med en storlek på max 2 MB.</p></div>
			</dd>
			
			<dd class="separator"></dd>
		</dl>
		
		<input type="submit" class="submit" value="Spara annons" />	
		</form>
	</div>
</div>

<div id="right">
	<div class="right_box">
		<?php $this->load->view('backend/_menu'); ?>
	</div>
</div>

<script type="text/javascript">
$(document).ready(function() {
	// Kolla om det är tomt från början
	if($('#added_cats ul li').length >= 1) {
		$('#added_cats p').hide();
	}
	
	// Markera dom första i dropdowns
	$("#lan_main option:first, #lan_sub option:first").attr('selected','selected');
	
	// Växla län
	$("#lan_main").click(function () {
		var this_lan = $("#lan_main").val();

		$.post("<?php echo base_url(); ?>ajax/populate_kommun", { id:this_lan }, function(data) {
			$("#lan_sub > option").remove();
			$('#lan_sub').append(data);
			$("#lan_sub option:first").attr('selected','selected');
		});
	});
	
	// Lägg till
	$("#add_cat").livequery('click', function () {
		var cat_main = $("#lan_main").val();
		var cat_main_text = $("#lan_main option:selected").text();
		var cat_sub = $("#lan_sub").val();
		var cat_sub_text = "&rarr; " + $("#lan_sub option:selected").text();
		
		// Kolla om tom
		if(cat_main != 0 || cat_main != "") {

			// Tom underkat?
			if(cat_sub != 0) {
				
				// Lägg till
				$('#added_cats p').fadeOut(function() {
					$('#added_cats ul').append('<li id="region_' + cat_sub + '"><strong>' + cat_main_text + '</strong><input type="hidden" name="field[' + cat_sub + '][main]" value="' + cat_sub + '" />' + cat_sub_text + ' <span class="cat_remove" title="Ta bort">Ta bort</span></li>');
				});
				
				// Dublett?
				$('#added_cats ul li').each(function(){
					var ids = $('[id='+this.id+']');
					
					// Dublett på huvudkategori?	
					if(ids.length>1 && ids[0]==this) {
						$(this).remove();
						$("#cat_list").animate({ opacity: 0.1 }, 100).animate({ opacity: 1.0 }, 100);
					}
				});
			} else {
				$("#cat_list").animate({ opacity: 0.1 }, 100).animate({ opacity: 1.0 }, 100);
			}

		} else {
			$("#cat_list").animate({ opacity: 0.1 }, 100).animate({ opacity: 1.0 }, 100);
		}
	});
	
	// Ta bort
	$(".cat_remove").livequery('click', function () {
		if($('#added_cats ul li').length == 1) {
			$('#added_cats p').fadeIn();
			$("#lan_main option[value='']").attr("selected", "selected");
			$("#lan_sub option[value='']").attr("selected", "selected");
		}

		$(this).parent("li").remove();
	});
});
</script>

<?php $this->load->view('_footer'); ?>