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
			$msg = 'Detta är ett mail via kontaktformuläret från Studenten 2011:'."\r\n\r\n".
				'Namn: ' . $from . "\r\n" .
				'Email: ' . $email . "\r\n\r\n" .
				'Meddelande: ' . "\r\n" .
				$msg;
			
			// Bygg mailet
			$config['wordwrap'] = TRUE;
			$config['wrapchars'] = 100;
			$config['useragent'] = "Studenten 2011";
			$this->email->initialize($config);
			$this->email->from($email, $from);
			$this->email->to('info@studeravidare.se');
			$this->email->subject('Kontaktformulär Studenten 2011');
			$this->email->message($msg);

			// Skicka
			$this->email->send();
			
			// Sätt ett meddelande
			$this->session->set_flashdata('message', '<div class="msg ok">Tack för ditt meddelande! Vi återkommer inom kort.</div>');
			
			redirect('om-oss/kontakt');
		}
	}
}