<?php
/**
 * @package Studenten 2011
 * @copyright 2010, iDenta labs
 * @author Kristoffer Lidman
 */
class Pages extends Controller {
	
	function Pages()
	{
		parent::Controller();
		
		// Sätt värden i var
		$this->main = $this->uri->segment(1);
		$this->sub = $this->uri->segment(2);
	}
	
	// Nej nej nej
	function index()
	{
		redirect('');
	}
	
	// Bal och student
	function bal_student()
	{
		// SEO
		$data['meta_title'] = "Bal och student";
		$data['meta_desc'] = "";
		$data['meta_kw'] = "";
		
		// Visa sidan
		$this->load->view('pages/page_bal_student', $data);
	}
	
	// Bal och student > Studentplakat
	function studentplakat()
	{
		// SEO
		$data['meta_title'] = "Studentplakat";
		$data['meta_desc'] = "";
		$data['meta_kw'] = "";
		
		// Visa sidan
		$this->load->view('pages/page_studentplakat', $data);
	}
	
	// Bal och student > Studentkläder
	function studentklader()
	{
		// SEO
		$data['meta_title'] = "Studentkläder";
		$data['meta_desc'] = "";
		$data['meta_kw'] = "";
		
		// Visa sidan
		$this->load->view('pages/page_studentklader', $data);
	}
	
	// Bal och student > Balkläder
	function balklader()
	{
		// SEO
		$data['meta_title'] = "Balkläder";
		$data['meta_desc'] = "";
		$data['meta_kw'] = "";
		
		// Visa sidan
		$this->load->view('pages/page_balklader', $data);
	}
	
	// Bal och student > Transport
	function transport()
	{
		// SEO
		$data['meta_title'] = "Transport";
		$data['meta_desc'] = "";
		$data['meta_kw'] = "";
		
		// Visa sidan
		$this->load->view('pages/page_transport', $data);
	}
	
	// Bal och student > Fotograf
	function fotograf()
	{
		// SEO
		$data['meta_title'] = "Fotograf";
		$data['meta_desc'] = "";
		$data['meta_kw'] = "";
		
		// Visa sidan
		$this->load->view('pages/page_fotograf', $data);
	}
	
	// Bal och student > Studentmössa
	function studentmossa()
	{
		// SEO
		$data['meta_title'] = "Studentmössa";
		$data['meta_desc'] = "";
		$data['meta_kw'] = "";
		
		// Visa sidan
		$this->load->view('pages/page_studentmossa', $data);
	}
	
	// Bal och student > Presenter
	function presenter()
	{
		// SEO
		$data['meta_title'] = "Presenter";
		$data['meta_desc'] = "";
		$data['meta_kw'] = "";
		
		// Visa sidan
		$this->load->view('pages/page_presenter', $data);
	}
	
	// Bal och student > Catering
	function catering()
	{
		// SEO
		$data['meta_title'] = "Catering";
		$data['meta_desc'] = "";
		$data['meta_kw'] = "";
		
		// Visa sidan
		$this->load->view('pages/page_catering', $data);
	}
	
	// Bal och student > Studentflak
	function studentflak()
	{
		// SEO
		$data['meta_title'] = "Studentflak";
		$data['meta_desc'] = "";
		$data['meta_kw'] = "";
		
		// Visa sidan
		$this->load->view('pages/page_studentflak', $data);
	}
	
	// Bal och student > Skor
	function skor()
	{
		// SEO
		$data['meta_title'] = "Skor";
		$data['meta_desc'] = "";
		$data['meta_kw'] = "";
		
		// Visa sidan
		$this->load->view('pages/page_skor', $data);
	}
	
	// Bal och student > Make up
	function makeup()
	{
		// SEO
		$data['meta_title'] = "Make up";
		$data['meta_desc'] = "";
		$data['meta_kw'] = "";
		
		// Visa sidan
		$this->load->view('pages/page_makeup', $data);
	}
	
	// Bal och student > Blommor
	function blommor()
	{
		// SEO
		$data['meta_title'] = "Blommor";
		$data['meta_desc'] = "";
		$data['meta_kw'] = "";
		
		// Visa sidan
		$this->load->view('pages/page_blommor', $data);
	}
	
	// Bal och student > Frisör
	function frisor()
	{
		// SEO
		$data['meta_title'] = "Frisör";
		$data['meta_desc'] = "";
		$data['meta_kw'] = "";
		
		// Visa sidan
		$this->load->view('pages/page_frisor', $data);
	}
	
	// Bal och student > Högskoleprovet
	function hogskoleprovet()
	{
		// SEO
		$data['meta_title'] = "Högskoleprovet";
		$data['meta_desc'] = "";
		$data['meta_kw'] = "";
		
		// Visa sidan
		$this->load->view('pages/page_hogskoleprovet', $data);
	}
	
	// Bal och student > Lär dig knyta slips
	function slips()
	{
		// SEO
		$data['meta_title'] = "Lär dig knyta slips";
		$data['meta_desc'] = "";
		$data['meta_kw'] = "";
		
		// Visa sidan
		$this->load->view('pages/page_slips', $data);
	}
	


	
	// Om oss
	function om_oss()
	{
		// SEO
		$data['meta_title'] = "Om oss";
		$data['meta_desc'] = "";
		$data['meta_kw'] = "";
		
		// Visa sidan
		$this->load->view('pages/page_about', $data);
	}
	
	// Webbplatsen
	function webbplatsen()
	{
		// SEO
		$data['meta_title'] = "Webbplatsen";
		$data['meta_desc'] = "";
		$data['meta_kw'] = "";
		
		// Visa sidan
		$this->load->view('pages/page_site', $data);
	}
	
	// Medarbetare
	function medarbetare()
	{
		// SEO
		$data['meta_title'] = "Medarbetare";
		$data['meta_desc'] = "";
		$data['meta_kw'] = "";
		
		// Visa sidan
		$this->load->view('pages/page_team', $data);
	}
	
	// Kontakta dentalkatalogen
	function kontakt()
	{
		// Skickar vi?
		if($_POST) {
			$this->_post_query();
		}
		
		// SEO
		$data['meta_title'] = "Kontakta oss";
		$data['meta_desc'] = "";
		$data['meta_kw'] = "";
		
		// Visa sidan
		$this->load->view('pages/page_contact', $data);
	}
	
	// Skicka formulär
	function _post_query()
	{
		// Plocka ihop das parameters
		$from = $this->input->post('name');
		$tel = $this->input->post('tel');
		$email = $this->input->post('email');
		$msg = $this->input->post('msg');
		
		// El spammo?
		if($this->input->post('to_email') != "") {
			echo "Spam";
			return FALSE;
		}
		
		// Ladda nödvändigheter
		$this->load->library('form_validation');
		
		// Sätt regler
		$this->form_validation->set_rules('name', 'Namn', 'trim');
		$this->form_validation->set_rules('email', 'E-mail', 'trim|required|valid_email');
		$this->form_validation->set_rules('tel', 'Telefon', 'trim');
		$this->form_validation->set_rules('msg', 'Meddelande', 'trim|required');
		
		// Sätt meddelanden
		$this->form_validation->set_message('required', '%s kan inte lämnas tom');
		$this->form_validation->set_message('valid_email', '%s är inte en giltig e-mailadress');
		$this->form_validation->set_message('min_length', '%s måste innehålla minst %s tecken');
		$this->form_validation->set_message('max_length', '%s får innehålla max %s tecken');
		
		// Kör kollen
		if ($this->form_validation->run() == TRUE) {
		
			// Kör på
			$this->load->library('email');
			
			// Bygg ihop msg
			$msg = 'Detta är ett mail via kontaktformuläret från Studenten 2014:'."\r\n\r\n".
				'Namn: ' . $from . "\r\n" .
				'Telefon: ' . $tel . "\r\n" .
				'Email: ' . $email . "\r\n\r\n" .
				'Meddelande: ' . "\r\n" .
				$msg;
			
			// Bygg mailet
			$config['wordwrap'] = TRUE;
			$config['wrapchars'] = 100;
			$config['useragent'] = "Studenten 2014";
			$this->email->initialize($config);
			$this->email->from($email, $from, $tel);
			$this->email->to('info@studeravidare.se');
			$this->email->subject('Kontaktformulär Studenten 2014');
			$this->email->message($msg);

			// Skicka
			$this->email->send();
			
			// Sätt ett meddelande
			$this->session->set_flashdata('message', '<div class="msg ok">Tack för ditt meddelande! Vi återkommer inom kort.</div>');
			
			redirect('om-oss/kontakt');
		}
	}
}