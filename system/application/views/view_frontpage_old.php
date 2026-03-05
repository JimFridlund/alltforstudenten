

<?php $this->load->view('_header'); ?>
	
<div class="wide_box searchbox">
	<p class="search_heading">Sök på skola eller kommun, eller välj ditt län i listan</p>
	<div class="search_container">
		<?php echo form_open('sok');?>
		<input type="text" class="fritext" name="s_value" value="Sök på skola eller kommun" />
		<input type="submit" class="submit" value="Sök" title="Sök" />
		</form>
	</div>
	
	<div class="list_container">
		<div class="alignleft">Inget län valt</div>
		<div class="list_button" title="Välj ditt län">Välj län</div>
		
		<div class="lan_container">
			<?php if($lan > 0): ?>
			<?php $i = 0; ?>
			<div>
			<?php foreach($lan as $data): ?>
			<div class="lan_box"><a href="<?php echo base_url() ?>visa/<?php echo $data->permalink; ?>" title="<?php echo $data->title; ?>"><?php echo $data->title; ?></a></div>
			<?php endforeach;?>
			</div>
			<?php else :?>
			<?php endif; ?>
		</div>
	</div>
</div>

<div class="wide_box">
	<img src="<?php echo base_url() ?>public/images/temp_img_big.jpg" alt="" />
</div>



<div class="infoboxes">
	<div class="box">
		<h2>Studenten, datum och firande</h2>
		<p>Firandet av studenten skiljer sig mycket på olika håll i Sverige. Ofta arrangeras baler och man äter fina middagar hemma. På avslutningsdagen sjungs Studentsången, därefter åker studenterna genom orten på flak, traktorer, lastbilar och tjusiga bilar. Det finns idag drygt 1000 gymnasieskolor i Sverige och i början av juni varje år så tar drygt 100 000 gymnasieelever studenten! Datum för <?php echo $this->settings->option['meta_title']; ?> varierar runt om i landet, det vanligaste är mellan 1 juni och 10 juni. Balen arrangeras i flesta fall före  studenten-dagen.</p>
		
		<img src="<?php echo base_url() ?>public/images/sjung.jpg" alt="" />
	</div>
	<div class="box">
		<h2>Varför denna studentmössa?</h2>
		<p>Att ta studenten härstammar från tiden då man efter gymnasieutbildning avslutade studierna med en studentexamen. Det vanliga var då att killarna som tagit studenten bar sina studentmössor över sommaren tills de ryckte in i lumpen. Studentexamen som den såg ut då avskaffades 1968.  Vad som hände sedan var att hela studentfirandet lades i malpåse, mer eller mindre under hela 70-talet. I samband med att man gjorde om gymnasieskolan till inriktningar (idag program) som är treåriga utbildningar så fick det hela en ordentlig uppsving igen.</p>
		
		<img src="<?php echo base_url() ?>public/images/mossa.jpg" alt="studentmössa" />
	</div>
	<div class="box right">
		<h2>Din checklista</h2>
		<p>För att hitta din checklista för <?php echo $this->settings->option['site_title']; ?> kan du enkel söka på din gymnasieskola eller klicka dig vidare genom att välja län. Här får du sedan information om vad som behövs för en lyckad student och bal. Frågor som &quot;Vem fixar studentflaket&quot; och &quot;Är fracken och eller balklänningen införskaffad?&quot; försöker vi besvara här. Andra saker som är viktiga att tänka på inför den stora dagen finns också listade. Detta är en bra hjälp till de ca 110 000 elever som  tar studenten i Sverige varje år.</p>
		
		
		<img src="<?php echo base_url() ?>public/images/studentflak.jpg" alt="" />
	</div>
</div>



<script type="text/javascript">
$(document).ready(function() {
	$(".list_container").click(function() {
	   $('.lan_container').slideToggle('fast', function() {

		});
		$(".list_button").toggleClass('open');
	});
});
</script>
	
<?php $this->load->view('_footer'); ?>