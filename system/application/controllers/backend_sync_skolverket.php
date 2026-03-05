<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Backend_sync_skolverket extends Controller {

	function Backend_sync_skolverket()
	{
		parent::Controller();
	}

	// Test-URL:
	// https://www.alltforstudenten.se/backend/sync_skolverket/run?key=FridlundGren1&dry=1
	function run()
	{
		$key = $this->input->get('key', TRUE);
		$dry = $this->input->get('dry', TRUE);

		$expected = 'FridlundGren1';

		if ($key !== $expected) {
			show_404();
			return;
		}

		header('Content-Type: text/plain; charset=utf-8');

		echo "OK: backend_sync_skolverket->run()\n";
		echo "dry=" . ($dry ? $dry : '0') . "\n";
		echo "Nästa steg: här kopplar vi in synklogiken.\n";
	}
}