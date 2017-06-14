/*
 * 	Funktioner för Studenten 2011
 *	Skapad av iDenta Labs, identa.se
 *
 *	Använder jQuery
 *	http://jquery.com
 *
 */

var baseURL = "http://www.studenten2011.nu/";

// Fade av meddelande
$(document).ready(function() {
	$('.msg, #validation_errors').hide();
	$('.msg, #validation_errors').fadeIn(900);
});

// Kolla om man vill ta bort en annons
function delete_ad(message)
{
	var answer = confirm("Är du HELT säker på att du vill ta bort denna annons?")
	if (answer){
		document.messages.submit();
		return false;
	}
	return false;  
}

// Kolla om man vill ta bort en logga
function delete_logo(message)
{
	var answer = confirm("Är du HELT säker på att du vill ta bort denna logotyp?")
	if (answer){
		document.messages.submit();
		return false;
	}
	return false;  
}

// Kolla om man vill ta bort en kategori
function delete_cat(message)
{
	var answer = confirm("Är du HELT säker på att du vill ta bort denna kategori?")
	if (answer){
		document.messages.submit();
		return false;
	}
	return false;  
}

// Kolla om man vill ta bort ett län
function delete_lan(message)
{
	var answer = confirm("Är du HELT säker på att du vill ta bort detta län?")
	if (answer){
		document.messages.submit();
		return false;
	}
	return false;  
}

// Kolla om man vill ta bort en kommun
function delete_kommun(message)
{
	var answer = confirm("Är du HELT säker på att du vill ta bort denna kommun?")
	if (answer){
		document.messages.submit();
		return false;
	}
	return false;  
}

// Kolla om man vill ta bort en skola
function delete_school(message)
{
	var answer = confirm("Är du HELT säker på att du vill ta bort denna skola?")
	if (answer){
		document.messages.submit();
		return false;
	}
	return false;  
}

// Rensa en input
$(document).ready(function() {
	$("#fritext, .fritext").ClearInput();
});

(function($) {$.fn.ClearInput = function() {
	$(this).each(function() {
		var DefaultValue = this.defaultValue;
		$(this).focus(function(){
			var CurrValue = $(this).val();
			if(CurrValue == DefaultValue) {
				$(this).val("");
			}
		});
		$(this).blur(function(){
			var CurrValue = $(this).val();
			if(CurrValue.length == 0) {
				$(this).val(DefaultValue);
			}
		});
	});
}})(jQuery);