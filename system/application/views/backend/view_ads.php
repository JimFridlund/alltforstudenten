<?php $this->load->view('_header'); ?>

<script src="<?php echo base_url() ?>public/js/jquery.modal.js" type="text/javascript"></script>
<link href="<?php echo base_url() ?>public/css/modal.css" rel="stylesheet" type="text/css"/>
<script src="http://178.79.129.108/material/public/js/jquery.tablesorter.min.js" type="text/javascript"></script>

<script type="text/javascript">
$(document).ready(function() { 
	$('#kommuner').jqm();
	$(".regions_show").click(function() {
		$('#kommun_lista').empty();
		var this_id = $(this).attr('id');
		$.post("<?php echo base_url(); ?>ajax/list_regions_to", { id:this_id }, function(data) {
			$('#kommun_lista').append(data);
			$('#kommuner').jqmShow({toTop: true});
		});
	});
});
</script>

<script type="text/javascript">
$(document).ready(function() { 
    $(".tablesorter").tablesorter({
		sortList: [[0,0]],
		headers: { 0: { sorter: false }, 1: { sorter: false }, 5: { sorter: false }}
	});
});

</script>

<style>

.right_button {
	display:none;
}
#backend_menu {margin-top:135px;}

#info_list {
	width:850px;
}

#left {
	width:900px;
}
.left_box {
	width:860px;
}


table.order_list {
	border-spacing:0;
	border-collapse:collapse;
}

th {
	height: 27px;
	background: #666;
	color: #fff;
	text-align:left;
	font-size: 15px;
	font-weight: bold;
	text-shadow: 0px 1px 1px #000;
}

tr.alt td {
	background: #f5f9fd;
	text-shadow: 0 1px 0 #fff;
}


#backend_menu {
	padding: 20px;
	background: #fff;
	position: relative;
	-webkit-box-shadow: 0px 2px 4px #eee;
	-moz-box-shadow: 0px 2px 4px #eee;
	box-shadow: 0px 2px 4px #eee;
	-webkit-border-radius:4px;  
	-moz-border-radius:4px;  
	border-radius:4px;
}


#backend_menu ul.menu {
	list-style-type: none;
	margin: 0 0 20px 0;
	font-size: 14px;
	font-weight: bold;
	display:table;
	width:100%;
}
#backend_menu ul.menu li {
	margin: 0 0 10px 0;
	display: table-cell;
}
#backend_menu ul.menu li a {
	background: #fff;
}
#backend_menu ul.menu li.active a {
	color: #333;
}
#backend_menu ul.menu li img {
	margin: 0 5px 0 0;
	float: left;
	display: inline;
}
#backend_menu ul.menu li#last {
	border: none;
	border-top: 1px dotted #eee;
	padding: 10px 0 0 0;
	margin: 0;
}

/*--------- Listor ---------*/

table.order_list th,
table.order_list td {
	margin:0;
	padding:5px;
}

table.order_list {
	border-spacing:0;
	border-collapse:collapse;
	width:100%;
}

th {
	height: 27px;
	background: #666;
	color: #fff;
	text-align:left;
	font-size: 15px;
	font-weight: bold;
	text-shadow: 0px 1px 1px #000;
}

tr.alt td {
	background: #f5f9fd;
	text-shadow: 0 1px 0 #fff;
}
#info_list {
	width:850px;
}




/*------ Listor --------*/
.tablesorter {
	width: 100%;
	border-collapse: collapse; 
	border-spacing: 0;
	font-size: 11px;
	margin: 0 0 15px 0;
}
.tableinfo {
	width: 100%;
	padding: 5px;
	background: #f4f4f4;
	text-shadow: 0 1px 0 #fff;
	border-bottom: 1px solid #dfdfdf;
}
.tableinfo tr th {
	padding: 5px;
	cursor: pointer;
	text-align: left;
}
.tableinfo tr th.nosort {
	cursor: default;
}
.tableinfo tr th:hover {
	color: #333;
}
.tableinfo tr th.header {
	background: url(../images/backend/icons/sortable.png) no-repeat 97% 8px;
}
.tableinfo tr th.headerSortDown {
	background: #efefef url(../images/backend/icons/down.gif) no-repeat 97% 10px;
}
.tableinfo tr th.headerSortUp {
	background: #efefef url(../images/backend/icons/up.gif) no-repeat 98% 10px;
}
.tablesorter tbody tr {
	width: 100%;
	border-bottom: 1px solid #e8f0fa;
	cursor: pointer;
}
.tablesorter tbody tr.hide {
	display: none;
}
.tablesorter tbody tr:hover {
	background: #fffff4;
	border-bottom: 1px solid #f1f1e4;
}
.tablesorter tbody tr:nth-child(even) {
	background: #f7fbff;
}
.tablesorter tbody tr td {
	padding: 7px 5px;
}
.tablesorter tbody tr td.date {
	width: 80px;
}
.tablesorter tbody tr td.actions {
	height: 18px;
	text-align: right;
}
.tablesorter tbody tr td.actions img {
	margin: 0 0 0 5px;
}
.tablesorter tbody tr td.bold {
	font-weight: bold;
}
.tablesorter tbody tr td.light {
	color: #aaa;
}
.sort_order {
	width: 20px;
	padding: 1px;
	font-size: 9px;
}
.emptylist {
	width: 683px;
	padding: 7px 0;
	text-align: center;
	font-weight: bold;
	float: left;
	margin: -13px 0 0 0;
	background: #f8f4c6;
	display: none;
	border-bottom: 1px solid #efebbf;}
	
	th.header {cursor:pointer;}
</style>

<div id="backend_menu">
	<?php $this->load->view('backend/_menu'); ?>
</div>
	
<div id="left">
	<div class="left_box">
		<h1>Alla annonser</h1> <a href="<?php echo base_url() ?>backend/lagg_till/annons" class="heading_button">Ny annons</a>
		
		<?php echo $this->session->flashdata('message'); ?>

<table class="order_list tablesorter">
	<thead>
		<tr>
			<th>Rubrik</th>
			<th>Kommuner</th>
			<th>Utgår</th>
			<th>Säljare</th>
			<th>Ordernr.</th>
			<th>Åtgärder</th>
		</tr>
	</thead>
		
		<?php if($ads['results'] > 0): ?>
		<?php $alt = ''; ?>
		<?php foreach($ads['results'] as $data): ?>
			
		<tr class="<?php if ($alt=='') { $alt = 'alt'; }
				else { echo 'alt'; $alt = ''; } ?>">
			<td><a href="<?php echo base_url() ?>backend/redigera/annons/<?php echo $data->id; ?>"><?php echo $data->title; ?></a></td>
			<td>
				<?php echo $this->studenten->count_kommuner_to_ad($data->id); ?> - 
				<div style="display:inline;" id="ad_<?php echo $data->id; ?>" class="regions_show">Visa kommuner</div>
			</td>
					
			<td><?php echo $data->date_expire; ?></td>
			<td><?php echo $data->seller; ?></td>
			<td><?php echo $data->orderid; ?></td>
			<td><a href="<?php echo base_url() ?>backend/redigera/annons/<?php echo $data->id; ?>" title="Redigera"><img src="<?php echo base_url() ?>public/images/icons/icon_edit.gif" alt="Redigera" title="Redigera" /></a>
				<a href="<?php echo base_url() ?>backend/tabort/annons/<?php echo $data->id; ?>" onclick="return delete_ad();" title="Ta bort"><img src="<?php echo base_url() ?>public/images/icons/icon_delete.gif" alt="Ta bort" /></a>
			</td>
			
		</tr>
		<?php endforeach; ?>
		<?php else: ?>
			<div class="msg empty">Du har inte lagt in några annonser ännu.</div>
		<?php endif; ?>
		
</table>


	</div>
		
<div>

</div>
		
		
		<?php if($pagination) echo '<div class="pagination"><strong>Sidan: </strong>'.$pagination.'</div>'; ?>
	</div>



<div class="modal_window" id="kommuner">
	<div class="modal_top"><a href="#" class="jqmClose" title="Stäng fönstret">Close</a></div>
	<div class="modal_content">
		<ul id="kommun_lista" style="font-size: 14px; list-style-type: none;">
		
		</ul>
	</div>
	<div class="modal_bott"></div>
</div>



<?php $this->load->view('_footer'); ?>