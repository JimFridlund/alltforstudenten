<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php if(!empty($meta_title)) echo $meta_title . " - "; ?><?php echo $this->settings->option['site_title']; ?></title>
<meta name="description" content="<?php if(!empty($meta_desc)) echo $meta_desc?>" />
<meta name="keywords" content="<?php if(!empty($meta_kw)) echo $meta_kw?>" />

<link href="<?php echo base_url() ?>public/css/style.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo base_url() ?>public/css/print.css" rel="stylesheet" type="text/css" media="print" />
<?php if($this->uri->segment(1)=="backend"):?>
<link href="<?php echo base_url() ?>public/css/dashboard.css" rel="stylesheet" type="text/css"/>
<?php endif; ?>

<script src="<?php echo base_url() ?>public/js/jquery-1.4.2.min.js" type="text/javascript"></script>
<script src="<?php echo base_url() ?>public/js/jquery.student.js" type="text/javascript"></script>
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-7733815-4']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
</head>

<body>

<div id="wrapper">
	<div id="header">
		<div class="frontpage"><a href="<?php echo base_url() ?>">Studenten 2021</a></div>
	

		<div id="menu">
			<ul>
				<li class="home<?php if($this->uri->segment(1)=="") echo ' active';?>"><a href="<?php echo base_url() ?>">Hem</a></li>
				
<li class="bal<?php if($this->uri->segment(1)=="bal-student") echo ' active';?>"><a href="<?php echo base_url() ?>bal-student">Bal &amp; student</a></li>
				<li class="about<?php if($this->uri->segment(1)=="om-oss") echo ' active';?>"><a href="<?php echo base_url() ?>om-oss">Om oss</a></li>
			</ul>
		</div>
	</div>