<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Sync_skolverket extends Controller {

	function Sync_skolverket()
	{
		parent::Controller();

		// Om du vill kräva inloggning (dx_auth) också, slå på detta senare:
		// $this->load->library('dx_auth');
		// if(!$this->dx_auth->is_logged_in()) { show_404(); }
	}

	function index()
	{
		// Bara en friendly hint om någon råkar gå hit utan /run
		header('Content-Type: text/plain; charset=utf-8');
		echo "Sync_skolverket OK\n";
		echo "Använd: /backend/sync_skolverket/run?key=DINNYCKEL&dry=1\n";
	}

	function run()
	{
		$key = $this->input->get('key', TRUE);
		$dry = $this->input->get('dry', TRUE);

		// BYT till din nyckel (exakt)
		$expected = 'FridlundGren1';

		if ($key !== $expected) {
			show_404();
			return;
		}

		header('Content-Type: text/plain; charset=utf-8');

		echo "sync_skolverket/run OK\n";
		echo "dry=" . ($dry ? $dry : '0') . "\n";
		echo "Nästa steg: här kopplar vi på själva synk-logiken.\n";
	}
}