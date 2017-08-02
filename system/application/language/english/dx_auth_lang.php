<?php

/*
	It is recommended for you to change 'auth_login_incorrect_password' and 'auth_login_username_not_exist' into something vague.
	For example: Username and password do not match.
*/

$lang['auth_login_incorrect_password'] = '<div class="error_message">Felaktigt lösenord eller användarnamn.</div>';
$lang['auth_login_username_not_exist'] = '<div class="error_message">Felaktigt lösenord eller användarnamn.</div>';

$lang['auth_username_or_email_not_exist'] = "Användarnamn eller e-mail existerar inte.";
$lang['auth_not_activated'] = "Ditt konto har inte blivit aktiverat. Var vänlig kolla din e-mail.";
$lang['auth_request_sent'] = "Din begäran av nytt lösenord har redan skickats. Var vänlig kolla din e-mail.";
$lang['auth_incorrect_old_password'] = "Ditt gamla lösenord stämmer inte.";
$lang['auth_incorrect_password'] = "Ditt lösenord är felaktigt.";

// Email subject
$lang['auth_account_subject'] = "%s kontoinformation";
$lang['auth_activate_subject'] = "%s aktivering av konto";
$lang['auth_forgot_password_subject'] = "Begäran av nytt lösenord till %s";

// Email content
$lang['auth_account_content'] = "Välkommen till %s,

Thank you for registering. Your account was successfully created.

Du kan logga in med ditt användarnamn eller din e-mail:

Användarnamn: %s
E-mail: %s
Lösenord: %s

Du kan nu logga in genom att besöka %s

Vi hoppas att ni är nöjda med vår tjänst, men om ni har några som helst frågor eller funderingar, tveka inte att kontakta oss.

Med vänliga hälsningar,
%s";

$lang['auth_activate_content'] = "Välkommen till %s,

För att aktivera ditt konto måste du klicka på länken nedan:
%s

Var vänlig aktivera kontot inom %s timmar, annars kommer er registrering att bli ogiltig och ni måste registrera er igen.

Du kan logga in med ditt användarnamn eller din e-mail:

Användarnamn: %s
E-mail: %s
Lösenord: %s

Vi hoppas att ni är nöjda med vår tjänst, men om ni har några som helst frågor eller funderingar, tveka inte att kontakta oss.

Med vänliga hälsningar,
%s";

$lang['auth_forgot_password_content'] = "%s,

You have requested your password to be changed, because you forgot the password.
Please follow this link in order to complete change password process:
%s

Ditt nya lösenord: %s
Säkerhetsnyckel för återaktivering: %s

After you successfully complete the process, you can change this new password into password that you want.

Var vänlig kontakta oss om ni har några problem med att komma åt ert konto %s.

Vi hoppas att ni är nöjda med vår tjänst, men om ni har några som helst frågor eller funderingar, tveka inte att kontakta oss.

Med vänliga hälsningar,
%s";

/* End of file dx_auth_lang.php */
/* Location: ./application/language/english/dx_auth_lang.php */