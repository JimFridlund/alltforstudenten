<?php $this->load->view('_header'); ?>

<div class="container">

<h1>
<?php echo $kategori->title; ?> inför balen
</h1>

<?php foreach($ads as $ad): ?>

<div class="ad-box">

<?php if($ad->logo): ?>

<img src="<?php echo base_url(); ?>uploads/logo/<?php echo $ad->logo; ?>">

<?php endif; ?>

<h2><?php echo $ad->title; ?></h2>

<?php if($ad->tel): ?>

<p>Tel: <?php echo $ad->tel; ?></p>

<?php endif; ?>

<?php if($ad->www): ?>

<a href="<?php echo $ad->www; ?>" target="_blank">
Besök webb
</a>

<?php endif; ?>

</div>

<?php endforeach; ?>

</div>

<?php $this->load->view('_footer'); ?>