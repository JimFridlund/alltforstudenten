<?php 
//------------------------------------------------ 
// File: 'phpmail.php' 
// Func: using mail(); 
//------------------------------------------------ 

$Subject = "Test E-mail";                          
$toEmail = "material@studeravidare.se";        



if(isset($_POST['submit']))
{ 
      $resultMail = mail($toEmail, $Subject, $nMessage); 
      if($resultMail) 
      { 
            print "Your e-mail has been sent."; 
      } 
      else 
      { 
            print "Your e-mail has not been sent."; 
      } 
}  

?>