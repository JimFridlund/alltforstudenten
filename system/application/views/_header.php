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

<?php   
include '/mnt/persist/www/docroot/ip-block.php';
?>
	

	
<link href="<?php echo base_url() ?>public/css/dashboard.css" rel="stylesheet" type="text/css"/>
<?php endif; ?>

<link media="only screen and (max-device-width: 480px)" href="<?php echo base_url() ?>public/css/mobile.css" type="text/css" rel="stylesheet" />

<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCIRX67iBblQiixBX85b-Nce-hdfEXeDS4&callback=initMap"
  type="text/javascript"></script>


<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
<script src="<?php echo base_url() ?>public/js/jquery.student.js" type="text/javascript"></script>


<!--<script type="text/javascript" src="http://www.studeravidare.se/clickheat/js/clickheat.js"></script><noscript></noscript><script type="text/javascript"><!--
clickHeatSite = 'www.studenten2012.nu';clickHeatGroup = encodeURIComponent(window.location.pathname+window.location.search);clickHeatServer = 'http://www.studeravidare.se/clickheat/click.php';initClickHeat(); 
</script>//-->


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

 <script>
        	        $(document).ready( function() {
        	            $('a[target=_blank]').click(function(){
        	
        	                try{
        	                	// alert($(this).data("type") + '' + $(this).data("advertiser") + '' + $(this).data("advertiser") + ' - (<?php echo $this->settings->option['site_title']; ?>)' + $(this).attr('href'));
        	                    _gaq.push(['_trackEvent', $(this).data("type"), $(this).data("advertiser") + ' - (<?php echo $this->settings->option['site_title']; ?>)', $(this).attr('href')]);
        	                } catch(err) {alert('ERROR!');}
        	
        	                return true;
        	
        	            });
        	        });
</script>




</head>

<body>




<div id="wrapper">

 

<div style="margin-top:10px;margin-bottom:10px;padding-left:0px;">


<?php if($this->uri->segment(2)=="vastra-gotalands-lan"):?>
<script type="text/javascript">
// RANDOM IMAGE SCRIPT
 var images = [], 
 index = 0;

images[0] = "<a href = 'http://www.studenten2018.se/link/smartphoto.html' target='_blank'><img src='http://www.studenten2018.se/uploads/logo/studentbanner-900x140.jpg' alt=''></a>";
//images[0] = "<a href = 'http://tartfabriken.se' target='_blank'><img src='http://www.studenten2013.nu/uploads/logo/tartfabriken-toppbanner-studenten-1.png' alt=''></a>";

 index = Math.floor(Math.random() * images.length);
document.write(images[index]);

</script>

<? elseif($this->uri->segment(3)=="hassleholms-kommun" || $this->uri->segment(3)=="kristianstads-kommun" || $this->uri->segment(3)=="perstorps-kommun"):?>
<a href="http://www.frackhuset.se" target="_blank"><img src="http://www.studenten2018.se/public/images/frackhuset.jpg"></a>

<?php else :?>

  <script type="text/javascript">
// RANDOM IMAGE SCRIPT
 var images = [], 
 index = 0;

images[0] = "<a href = 'http://www.studenten2018.se/link/abc-gruppen-toppbanner.html' target='_blank'><img src='http://www.studenten2018.se/uploads/logo/abc-gruppen-2486-st-toppbanner.gif' alt=''></a>";
//images[1] = "<a href = 'http://www.studenten2018.se/link/erecruit.html' target='_blank'><img src='http://www.studenten2018.se/uploads/logo/erecruit-topbanner-918x112-st.gif' alt=''></a>";

images[1] = "<a href = 'https://www.bachelorbox.se/' target='_blank'><img src='http://www.studenten2018.se/uploads/logo/bachelorbox-toppbanner-st.gif' alt=''></a>";

 index = Math.floor(Math.random() * images.length);
document.write(images[index]);
</script>
  
      <?php endif; ?>

	  
	  
</div>


	<div id="header">
		<div class="frontpage"><a href="<?php echo base_url() ?>">Studenten 2017</a></div>
<a href="http://www.studeravidare.se">
<div class="endelavstuderavidare">
</div></a>		

		<div id="menu">
			<ul>
				<li class="home<?php if($this->uri->segment(1)=="") echo ' active';?>"><a href="<?php echo base_url() ?>">Hem</a></li>
				
<li class="bal<?php if($this->uri->segment(1)=="bal-student") echo ' active';?>"><a href="<?php echo base_url() ?>bal-student">Bal &amp; student</a></li>
				<li class="about<?php if($this->uri->segment(1)=="om-oss") echo ' active';?>"><a href="<?php echo base_url() ?>om-oss">Om oss</a></li>
			</ul>
		</div>
	</div>
