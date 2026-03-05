<?php $this->load->view('_header'); ?>

<style>
/* Local, low-risk styling only for länssidor */
.afs-container{
	max-width: 1180px;
	margin: 0 auto;
	padding: 24px 24px 10px 24px;
}

.afs-container h1{
	margin-top: 10px;
}

.afs-intro{
	font-size: 18px;
	line-height: 1.6;
	max-width: 900px;
	margin: 10px 0 22px 0;
}

.afs-section{
	margin-top: 18px;
}

.kommun-grid{
	display: grid;
	grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
	gap: 10px 28px;
	list-style: none;
	padding: 0;
	margin: 12px 0 0 0;
}

.kommun-grid li{ margin:0; padding:0; }

.kommun-grid a{
	display: inline-block;
	padding: 6px 0;
	text-decoration: none;
	color: #093260;
	font-weight: 600;
}

.kommun-grid a:hover{ text-decoration: underline; }

/* Make breadcrumb breathe a bit if it's plain text */
.breadcrumb{
	margin: 14px 0 8px 0;
	font-size: 14px;
	opacity: .9;
}

/* Stack on very small screens */
@media (max-width: 520px){
	.afs-container{ padding: 18px 14px 6px 14px; }
	.afs-intro{ font-size: 16px; }
}
</style>

<div class="afs-container">

	<h1>Studenten 2026 i <?php echo $page_title; ?></h1>

	<p class="afs-intro">
		Här hittar du alla studentdatum 2026 i <?php echo $page_title; ?>.
		Välj din kommun nedan för att se checklistor, utspring och baldatum.
	</p>

	<div class="breadcrumb">
		<?php echo $this->studenten->make_breadcrumb(); ?>
	</div>

	<div class="afs-section">
		<h2>Välj kommun i <?php echo $page_title; ?></h2>

		<?php if($kommuner > 0): ?>
			<ul class="kommun-grid">
			<?php foreach($kommuner as $data): ?>
				<li>
					<a href="<?php echo base_url() ?>visa/<?php echo $page_permalink; ?>/<?php echo $data->permalink; ?>"
					   title="<?php echo $data->title; ?>">
						<?php echo $data->title; ?>
					</a>
				</li>
			<?php endforeach; ?>
			</ul>
		<?php else :?>
			<p>Inga kommuner inlagda.</p>
		<?php endif; ?>
	</div>

</div>

<script type="application/ld+json">
<?php
$canonical = base_url() . 'visa/' . $page_permalink;
$faq = array(
  "@context" => "https://schema.org",
  "@type" => "FAQPage",
  "mainEntity" => array(
    array(
      "@type" => "Question",
      "name" => "När är studenten 2026 i " . $page_title . "?",
      "acceptedAnswer" => array(
        "@type" => "Answer",
        "text" => "Studentdatum varierar mellan skolor och kommuner. Välj din kommun för att se mer information. Vi visar endast verifierade datum från officiella källor."
      )
    ),
    array(
      "@type" => "Question",
      "name" => "Var hittar jag baldatum?",
      "acceptedAnswer" => array(
        "@type" => "Answer",
        "text" => "Baldatum publiceras ofta av skolor senare under året. Där officiell information finns visas det på kommun- och skolsidor."
      )
    )
  )
);
echo json_encode($faq, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
?>
</script>

<?php $this->load->view('_footer'); ?>
