<?php
/**
 * @package Studenten 2011
 * @copyright 2010, iDenta labs
 * @author Kristoffer Lidman
 */
class Backend extends Controller
{
	function Backend()
	{
		parent::Controller();
		
		$this->load->library('Pagination');
		
		// Skydda allt så bara admin får tillgång.
		$this->dx_auth->check_uri_permissions();
		
		$this->load->model('backend_model');
	}
	
	function index()
	{
		$this->panel();
	}
	
	//----------------- Huvuddelar -----------------------
	
	// Huvudpanelen
	function panel()
	{
		// SEO
		$data['meta_title'] = "Administrationspanelen";
		$data['meta_desc'] = "";
		$data['meta_kw'] = "";
		
		// Visa sidan
		$this->load->view('backend/view_frontpage', $data);
	}
	
	// Alla annonser
	function annonser()
	{
		// Bestäm limit och offset
		$row_count = 2000;
		$offset = $this->uri->segment(3);
		
		// Hämta information
		$data['ads'] = $this->backend_model->get_ads($row_count, $offset);
		$count = $data['ads']['count'];
		
		// Pagination config
		$p_config['base_url'] = base_url().'backend/annonser/';
		$p_config['uri_segment'] = 3;
		$p_config['total_rows'] = $count;
		$p_config['per_page'] = $row_count;
		$p_config['num_links'] = 10;
		$p_config['next_link'] = ' &raquo;';
		$p_config['prev_link'] = '&laquo; ';
				
		// Init pagination
		$this->pagination->initialize($p_config);
		$data['pagination'] = $this->pagination->create_links();
		
		// SEO
		$data['meta_title'] = "Alla annonser";
		$data['meta_desc'] = "";
		$data['meta_kw'] = "";
		
		// Visa sidan
		$this->load->view('backend/view_ads', $data);
	}
	
	// Kategorier
	function kategorier()
	{
		// Hämta information
		$data['cat'] = $this->backend_model->get_categories();
		
		// SEO
		$data['meta_title'] = "Kategorier";
		$data['meta_desc'] = "";
		$data['meta_kw'] = "";
		
		// Visa sidan
		$this->load->view('backend/view_categories', $data);
	}
	
	// Regioner
	function regioner()
	{
		// Hämta information
		$data['lan'] = $this->backend_model->get_regions();
		
		// SEO
		$data['meta_title'] = "Län &amp; kommuner";
		$data['meta_desc'] = "";
		$data['meta_kw'] = "";
		
		// Visa sidan
		$this->load->view('backend/view_regions', $data);
	}
	
	// Skolor
	function skolor()
	{
		// Bestäm limit och offset
		$row_count = 2000;
		$offset = $this->uri->segment(3);
		
		// Hämta information
		$data['skolor'] = $this->backend_model->get_all_schools($row_count, $offset);
		$count = $data['skolor']['count'];
		
		// Pagination config
		$p_config['base_url'] = base_url().'backend/skolor/';
		$p_config['uri_segment'] = 3;
		$p_config['total_rows'] = $count;
		$p_config['per_page'] = $row_count;
		$p_config['num_links'] = 10;
		$p_config['next_link'] = ' &raquo;';
		$p_config['prev_link'] = '&laquo; ';
				
		// Init pagination
		$this->pagination->initialize($p_config);
		$data['pagination'] = $this->pagination->create_links();
		
		// SEO
		$data['meta_title'] = "Skolor";
		$data['meta_desc'] = "";
		$data['meta_kw'] = "";
		
		// Visa sidan
		$this->load->view('backend/view_schools', $data);
	}
	
	// Inställningar
	function installningar()
	{
		// Försöker vi spara?
		if($_POST) {
			$this->_save_settings();
		}
		
		// SEO
		$data['meta_title'] = "Inställningar";
		$data['meta_desc'] = "";
		$data['meta_kw'] = "";
		
		// Visa sidan
		$this->load->view('backend/view_settings', $data);
	}
	
	// Byt lösenord
	function losenord()
	{
		// Check if user logged in or not
		if ($this->dx_auth->is_logged_in()) {			
			
			// Ladda nödvändigheter
			$this->load->library('form_validation');
			
			$val = $this->form_validation;
			$min_password = 4;
			$max_password = 20;
			
			// Sätt regler
			$val->set_rules('old_password', 'Gamla lösenordet', 'trim|required|xss_clean|min_length['.$min_password.']|max_length['.$max_password.']');
			$val->set_rules('new_password', 'Nya lösenordet', 'trim|required|xss_clean|min_length['.$min_password.']|max_length['.$max_password.']|matches[confirm_new_password]');
			$val->set_rules('confirm_new_password', 'Bekräfta nya lösenordet', 'trim|required|xss_clean');
			
			// Sätt meddelanden
			$val->set_message('required', '%s kan inte lämnas tom');
			$val->set_message('matches', '%s stämmer inte med %s');
			$val->set_message('min_length', '%s måste innehålla minst %s tecken');
			$val->set_message('max_length', '%s får innehålla max %s tecken');
			
			// Validate rules and change password
			if ($val->run() AND $this->dx_auth->change_password($val->set_value('old_password'), $val->set_value('new_password'))) {
				
				// Skapa meddelande
				$this->session->set_flashdata('message', '<div class="msg ok">Lösenordet är nu bytt.</div>');
				
				// Klart
				redirect('backend/losenord');
				
			} else {
				
				// SEO
				$data['meta_title'] = "Byt lösenord";
				$data['meta_desc'] = "";
				$data['meta_kw'] = "";
				
				// Visa sidan
				$this->load->view('backend/view_password', $data);
			}
		}
	}
	
	// Lägg till ny
	function lagg_till()
	{
		// Hämta URI
		$type = $this->uri->segment(3);
		
		// Kolla så inte ID är tom
		if(empty($type)) {
			exit("fel");
		}
		
		// Ta reda på var vi försöker lägga till
		switch ($type) {
			
			// Det är en annons
			case 'annons':
				
				// Försöker vi spara?
				if($_POST) {
					$this->_save_ad();
				}
				
				// Hämta län och kategorier
				$data['lan'] = $this->backend_model->get_regions();
				$data['cat'] = $this->backend_model->get_categories();
				
				// SEO
				$data['meta_title'] = "Lägg till annons";
				$data['meta_desc'] = "";
				$data['meta_kw'] = "";
				
				// Visa sidan
				$this->load->view('backend/view_add_ad', $data);
				
			break;
			
			// Det är en kategori
			case 'kategori':
				
				// Försöker vi spara?
				if($_POST) {
					$this->_save_category();
				}
				
				// SEO
				$data['meta_title'] = "Lägg till kategori";
				$data['meta_desc'] = "";
				$data['meta_kw'] = "";
				
				// Visa sidan
				$this->load->view('backend/view_add_category', $data);
			break;
			
			// Det är ett län
			case 'lan':
				
				// Försöker vi spara?
				if($_POST) {
					$this->_save_region();
				}
				
				// SEO
				$data['meta_title'] = "Lägg till län";
				$data['meta_desc'] = "";
				$data['meta_kw'] = "";
				
				// Visa sidan
				$this->load->view('backend/view_add_region', $data);
			break;
			
			// Det är en kommun
			case 'kommun':
				
				// Kolla så länet kommer med, annars döda
				$lan = (int)$this->uri->segment(4);
				if(empty($lan) OR !is_numeric($lan)) {
					exit("fel");
				}
				
				// Försöker vi spara?
				if($_POST) {
					$this->_save_kommun($lan);
				}

				$data['lan_list'] = $this->backend_model->get_regions();
				$data['lan_id'] = $lan;
				
				// SEO
				$data['meta_title'] = "Lägg till kommun";
				$data['meta_desc'] = "";
				$data['meta_kw'] = "";
				
				// Visa sidan
				$this->load->view('backend/view_add_kommun', $data);
			break;
			
			// Det är en skola
			case 'skola':
				
				// Försöker vi spara?
				if($_POST) {
					$this->_save_school();
				}
				
				$data['regions'] = $this->backend_model->get_lan_kommuner();
				
				// SEO
				$data['meta_title'] = "Lägg till skola";
				$data['meta_desc'] = "";
				$data['meta_kw'] = "";
				
				// Visa sidan
				$this->load->view('backend/view_add_school', $data);
			break;
			default:
				echo "Whut?";
		}
	}
	
	// ---------------------------------
	// ---------- Undersidor -----------
	// ---------------------------------
	
	// Redigera
	function redigera()
	{
		// Hämta URI och ID
		$type = $this->uri->segment(3);
		$id = $this->uri->segment(4);
		
		// Kolla så inte ID är tom
		if(empty($id)) {
			exit("fel");
		}
		
		// Ta reda på var vi försöker redigera.
		switch ($type) {
			
			// Det är en annons
			case 'annons':
				
				// Försöker vi spara?
				if($_POST) {
					$this->_update_ad($id);
				}
				
				// Hämta listningen vi redigerar
				$data['ad'] = $this->backend_model->get_ad_by_id($id);
				
				// Hämta län och kategorier
				$data['regions'] = $this->backend_model->get_relations_to($id);
				$data['lan'] = $this->backend_model->get_regions();
				$data['cat'] = $this->backend_model->get_categories();
				
				// SEO
				$data['meta_title'] = "Redigera annons";
				
				// Visa sidan
				$this->load->view('backend/view_edit_ad', $data);
				
			break;
			
			// Det är en kategori
			case 'kategori':
				
				// Försöker vi spara?
				if($_POST) {
					$this->_update_category($id);
				}
				
				// Hämta listningen vi redigerar
				$data['cat'] = $this->backend_model->get_category_by_id($id);
				
				// SEO
				$data['meta_title'] = "Redigera kategori";
				
				// Visa sidan
				$this->load->view('backend/view_edit_category', $data);
			break;
			
			// Det är ett län
			case 'lan':
				
				// Försöker vi spara?
				if($_POST) {
					$this->_update_region($id);
				}
				
				// Hämta listningen vi redigerar, och kommuner i detta län
				$data['lan'] = $this->backend_model->get_region_by_id($id);
				$data['kommun'] = $this->backend_model->get_kommuner_to($id);
				
				// SEO
				$data['meta_title'] = "Redigera län";
				
				// Visa sidan
				$this->load->view('backend/view_edit_region', $data);
			break;
			
			// Det är en kommun
			case 'kommun':
				
				// Hämta listningen vi redigerar, och kommuner i detta län
				$kommun = $this->backend_model->get_kommun_by_id($id);
				
				// Försöker vi spara?
				if($_POST) {
					$this->_update_kommun($id, $kommun[0]->parent);
				}
				
				// Skicka till view
				$data['kommun'] = $kommun;
				$data['lan_list'] = $this->backend_model->get_regions();
				
				// SEO
				$data['meta_title'] = "Redigera kommun";
				
				// Visa sidan
				$this->load->view('backend/view_edit_kommun', $data);
			break;
			
			// Det är en skola
			case 'skola':
				
				// Försöker vi spara?
				if($_POST) {
					$this->_update_school($id);
				}
				
				// Skicka till view
				$data['skola'] = $this->backend_model->get_school_by_id($id);
				$data['lan_list'] = $this->backend_model->get_regions();
				$data['regions'] = $this->backend_model->get_lan_kommuner($data['skola'][0]->parent_id);
				
				// SEO
				$data['meta_title'] = "Redigera skola";
				
				// Visa sidan
				$this->load->view('backend/view_edit_school', $data);
			break;
			default:
				echo "Whut?";
		}
	}
	
	// Ta bort
	function tabort()
	{
		// Hämta URI och ID
		$type = $this->uri->segment(3);
		$id = $this->uri->segment(4);
		
		// Ta reda på var vi försöker ta bort.
		switch ($type) {
			
			// Det är en annons
			case 'annons':
				
				// Ta bort 
				$this->db->delete('list_ads', array('id' => $id));

				// Bestäm redirect och meddelande
				$redirect = "backend/annonser";
				$msg = "Annonsen är nu borttagen.";
			break;
			
			// Det är en kategori
			case 'kategori':
				
				// Ta bort 
				$this->db->delete('list_categories', array('id' => $id));

				// Bestäm redirect och meddelande
				$redirect = "backend/kategorier";
				$msg = "Kategorin är nu borttagen.";
			break;
			
			// Det är en logga
			case 'logo':
				
				// Plocka först fram filnamnet för att kunna ta bort det sen
				$my_logo = "./uploads/logo/" . $this->_get_logo($id);
				
				// Ta bort 
				$data = array(
				   'logo' => ""
				);
				$this->db->where('id', $id);
				$this->db->update('list_ads', $data);
				
				// Ta bort filen
				unlink($my_logo);

				// Bestäm redirect och meddelande
				$redirect = "backend/redigera/annons/".$id;
				$msg = "Logotypen är nu borttagen.";
			break;
			
			// Det är ett län
			case 'lan':
				
				// Ta bort 
				$this->db->delete('list_regions', array('id' => $id));

				// Bestäm redirect och meddelande
				$redirect = "backend/regioner";
				$msg = "Länet är nu borttaget.";
			break;
			
			// Det är en kommun
			case 'kommun':
				
				// Ta fram vilket län som är parent
				$lan = $this->backend_model->get_region_by_child($id);
				
				// Ta bort 
				$this->db->delete('list_regions', array('id' => $id));

				// Bestäm redirect och meddelande
				$redirect = "backend/redigera/lan/".$lan;
				$msg = "Kommunen är nu borttagen.";
			break;
			
			// Det är en skola
			case 'skola':
				
				// Ta bort 
				$this->db->delete('list_schools', array('id' => $id));

				// Bestäm redirect och meddelande
				$redirect = "backend/skolor";
				$msg = "Skolan är nu borttagen.";
				
			break;
		}
		
		// Sätt ett meddelande
		$this->session->set_flashdata('message', '<div class="msg ok">'.$msg.'</div>');
		
		// Tillbaka
		redirect($redirect);
	}
	
	// ---------------------------------
	// ---- Funktioner & callbacks -----
	// ---------------------------------
	
	// Spara ny listning
	function _save_ad()
	{
		// Sätt regler
		$this->form_validation->set_rules('title', 'Rubriken', 'required|trim|max_length[52]');
		
		// Sätt meddelanden
		$this->form_validation->set_message('required', '%s kan inte lämnas tom');
		$this->form_validation->set_message('min_length', '%s måste innehålla minst %s tecken');
		$this->form_validation->set_message('max_length', '%s får innehålla max %s tecken');
		
		// Kör kollen
		if ($this->form_validation->run() == TRUE) {
			
			// Skapa lat och long
			if($this->input->post('adress') != "") {
				$geocode = $this->studenten->geocode($this->input->post('adress').", ".$this->input->post('postnr') . " " . $this->input->post('ort'));
				$lat = $geocode['latitude'];
				$long = $geocode['longitude'];
			} else {
				$lat = "";
				$long = "";
			}
			
			// Spara objekt
			$data = array(
			   'title' => $this->input->post('title'),
			   'permalink' => $this->studenten->make_permalink($this->input->post('title')),
			   'category' => $this->input->post('category'),
			   'adress' => $this->input->post('adress'),
			   'postnr' => $this->input->post('postnr'),
			   'ort' => $this->input->post('ort'),
			   'tel' => $this->input->post('tel'),
			   'www' => $this->input->post('www'),
			   'lati' => $lat,
			   'longi' => $long,
			   'date_added' => date('Y-m-d H:i:s'),
			   'date_expire' => $this->input->post('date_expire'),
			   'text_info' => $this->input->post('text_info'),
			   'orderid' => $this->input->post('orderid'),
			   'seller' => $this->input->post('seller'),			   
			   'boka_text' => $this->input->post('boka_text'),
			   'email' => $this->input->post('email')
			   );
			$this->db->insert('list_ads', $data);
			$id = $this->db->insert_id();
			
			// Logga
			if(!empty($_FILES['logo']['name'])) {
				$this->_upload_logo($id);
			}
			
			// Kolla om vi har lagt till någon region
			$i = 0;
			if($this->input->post('field') != "") {
				foreach($this->input->post('field') as $data) {
					if((isset($data['main']) AND $data['main'] != "")) {
						$reg_id = $data['main'];
					}

					if($reg_id != "") {
						$sql = "INSERT INTO ads_relations (ad_id, region_id, sort_order) VALUES ('$id', '$reg_id', '$i')";
						$this->db->query($sql); $i++;
					}
				}
			}
			
			// Skapa meddelande
			$this->session->set_flashdata('message', '<div class="msg ok">Annonsen är nu sparad.</div>');
			
			// Klart
			redirect('backend/annonser');
		}
	}
	
	// Uppdatera listning
	function _update_ad($id)
	{
		// Sätt regler
		$this->form_validation->set_rules('title', 'Rubriken', 'required|trim|max_length[52]');
		
		// Sätt meddelanden
		$this->form_validation->set_message('required', '%s kan inte lämnas tom');
		$this->form_validation->set_message('min_length', '%s måste innehålla minst %s tecken');
		$this->form_validation->set_message('max_length', '%s får innehålla max %s tecken');
		
		// Kör kollen
		if ($this->form_validation->run() == TRUE) {
			
			// Skapa lat och long
			if($this->input->post('adress') != "") {
				$geocode = $this->studenten->geocode($this->input->post('adress').", ".$this->input->post('postnr') . " " . $this->input->post('ort'));
				$lat = $geocode['latitude'];
				$long = $geocode['longitude'];
			} else {
				$lat = "";
				$long = "";
			}
			
			// Spara objekt
			$data = array(
			   'title' => $this->input->post('title'),
			   'permalink' => $this->studenten->make_permalink($this->input->post('title')),
			   'category' => $this->input->post('category'),
			   'adress' => $this->input->post('adress'),
			   'postnr' => $this->input->post('postnr'),
			   'ort' => $this->input->post('ort'),
			   'tel' => $this->input->post('tel'),
			   'www' => $this->input->post('www'),
			   'date_expire' => $this->input->post('date_expire'),
			   'text_info' => $this->input->post('text_info'),
			   'orderid' => $this->input->post('orderid'),
			   'seller' => $this->input->post('seller'),
			   'boka_text' => $this->input->post('boka_text'),
			   'email' => $this->input->post('email'),
			   'lati' => $lat,
			   'longi' => $long
			);
			$this->db->where('id', $id);
			$this->db->update('list_ads', $data);
			
			// Spara kategorirelationer. Rensa först
			$this->_clear_relations($id); $i = 0;
			
			// Kolla om vi har lagt till någon region
			if($this->input->post('field') != "") {
				foreach($this->input->post('field') as $data) {
					if((isset($data['main']) AND $data['main'] != "")) {
						$reg_id = $data['main'];
					}

					if($reg_id != "") {
						$sql = "INSERT INTO ads_relations (ad_id, region_id, sort_order) VALUES ('$id', '$reg_id', '$i')";
						$this->db->query($sql); $i++;
					}
				}
			}
			
			// Logga
			if(!empty($_FILES['logo']['name'])) {
				$this->_upload_logo($id);
			}
			
			// Skapa meddelande
			$this->session->set_flashdata('message', '<div class="msg ok">Annonsen är nu uppdaterad.</div>');
			
			// Klart
			redirect('backend/annonser');
		}
	}
	
	// Spara ny kategori
	function _save_category()
	{
		// Sätt regler
		$this->form_validation->set_rules('title', 'Rubriken', 'required|trim|max_length[52]');
		
		// Sätt meddelanden
		$this->form_validation->set_message('required', '%s kan inte lämnas tom');
		$this->form_validation->set_message('min_length', '%s måste innehålla minst %s tecken');
		$this->form_validation->set_message('max_length', '%s får innehålla max %s tecken');
		
		// Kör kollen
		if ($this->form_validation->run() == TRUE) {
			
			// Permalänk?
			if($this->input->post('permalink') != "") {
				$permalink = $this->input->post('permalink');
			} else {
				$permalink = $this->studenten->make_permalink($this->input->post('title'));
			}
			
			// Spara objekt
			$data = array(
			   'title' => $this->input->post('title'),
			   'permalink' => $permalink
			);
			$this->db->insert('list_categories', $data);
			
			// Skapa meddelande
			$this->session->set_flashdata('message', '<div class="msg ok">Kategorin är nu sparad.</div>');
			
			// Klart
			redirect('backend/kategorier');
		}
	}
	
	// Uppdatera kategori
	function _update_category($id)
	{
		// Sätt regler
		$this->form_validation->set_rules('title', 'Rubriken', 'required|trim|max_length[52]');
		
		// Sätt meddelanden
		$this->form_validation->set_message('required', '%s kan inte lämnas tom');
		$this->form_validation->set_message('min_length', '%s måste innehålla minst %s tecken');
		$this->form_validation->set_message('max_length', '%s får innehålla max %s tecken');
		
		// Kör kollen
		if ($this->form_validation->run() == TRUE) {
			
			// Permalänk?
			if($this->input->post('permalink') != "") {
				$permalink = $this->input->post('permalink');
			} else {
				$permalink = $this->studenten->make_permalink($this->input->post('title'));
			}
			
			// Spara objekt
			$data = array(
			   'title' => $this->input->post('title'),
			   'permalink' => $permalink
			);
			$this->db->where('id', $id);
			$this->db->update('list_categories', $data);
			
			// Skapa meddelande
			$this->session->set_flashdata('message', '<div class="msg ok">Kategorin är nu uppdaterad.</div>');
			
			// Klart
			redirect('backend/kategorier');
		}
	}
	
	// Spara nytt län
	function _save_region()
	{
		// Sätt regler
		$this->form_validation->set_rules('title', 'Namnet', 'required|trim|max_length[52]');
		
		// Sätt meddelanden
		$this->form_validation->set_message('required', '%s kan inte lämnas tom');
		$this->form_validation->set_message('min_length', '%s måste innehålla minst %s tecken');
		$this->form_validation->set_message('max_length', '%s får innehålla max %s tecken');
		
		// Kör kollen
		if ($this->form_validation->run() == TRUE) {
			
			// Permalänk?
			if($this->input->post('permalink') != "") {
				$permalink = $this->input->post('permalink');
			} else {
				$permalink = $this->studenten->make_permalink($this->input->post('title'));
			}
			
			// Skapa lat och long
			$geocode = $this->studenten->geocode($this->input->post('title'));
			$lat = $geocode['latitude'];
			$long = $geocode['longitude'];
			
			// Spara objekt
			$data = array(
			   'title' => $this->input->post('title'),
			   'permalink' => $permalink,
			   'lati' => $lat,
			   'longi' => $long
			);
			$this->db->insert('list_regions', $data);
			
			// Skapa meddelande
			$this->session->set_flashdata('message', '<div class="msg ok">Länet är nu sparat.</div>');
			
			// Klart
			redirect('backend/regioner');
		}
	}
	
	// Uppdatera län
	function _update_region($id)
	{
		// Sätt regler
		$this->form_validation->set_rules('title', 'Namnet', 'required|trim|max_length[52]');
		
		// Sätt meddelanden
		$this->form_validation->set_message('required', '%s kan inte lämnas tom');
		$this->form_validation->set_message('min_length', '%s måste innehålla minst %s tecken');
		$this->form_validation->set_message('max_length', '%s får innehålla max %s tecken');
		
		// Kör kollen
		if ($this->form_validation->run() == TRUE) {
			
			// Permalänk?
			if($this->input->post('permalink') != "") {
				$permalink = $this->input->post('permalink');
			} else {
				$permalink = $this->studenten->make_permalink($this->input->post('title'));
			}
			
			// Skapa lat och long
			$geocode = $this->studenten->geocode($this->input->post('title'));
			$lat = $geocode['latitude'];
			$long = $geocode['longitude'];
			
			// Spara objekt
			$data = array(
			   'title' => $this->input->post('title'),
			   'permalink' => $permalink,
			   'lati' => $lat,
			   'longi' => $long
			);
			$this->db->where('id', $id);
			$this->db->update('list_regions', $data);
			
			// Skapa meddelande
			$this->session->set_flashdata('message', '<div class="msg ok">Länet är nu uppdaterat.</div>');
			
			// Klart
			redirect('backend/regioner');
		}
	}
	
	// Spara ny kommun
	function _save_kommun($lan)
	{
		// Sätt regler
		$this->form_validation->set_rules('title', 'Namnet', 'required|trim|max_length[52]');
		
		// Sätt meddelanden
		$this->form_validation->set_message('required', '%s kan inte lämnas tom');
		$this->form_validation->set_message('min_length', '%s måste innehålla minst %s tecken');
		$this->form_validation->set_message('max_length', '%s får innehålla max %s tecken');
		
		// Kör kollen
		if ($this->form_validation->run() == TRUE) {
			
			// Permalänk?
			if($this->input->post('permalink') != "") {
				$permalink = $this->input->post('permalink');
			} else {
				$permalink = $this->studenten->make_permalink($this->input->post('title'));
			}
			
			// Skapa lat och long
			$geocode = $this->studenten->geocode($this->input->post('title'));
			$lat = $geocode['latitude'];
			$long = $geocode['longitude'];
			
			// Spara objekt
			$data = array(
			   'parent' => $this->input->post('parent'),
			   'title' => $this->input->post('title'),
			   'permalink' => $permalink,
			   'lati' => $lat,
			   'longi' => $long
			);
			$this->db->insert('list_regions', $data);
			
			// Skapa meddelande
			$this->session->set_flashdata('message', '<div class="msg ok">Kommunen är nu sparad.</div>');
			
			// Klart
			redirect('backend/redigera/lan/'.$lan);
		}
	}
	
	// Uppdatera kommun
	function _update_kommun($id, $lan)
	{
		// Sätt regler
		$this->form_validation->set_rules('title', 'Namnet', 'required|trim|max_length[52]');
		
		// Sätt meddelanden
		$this->form_validation->set_message('required', '%s kan inte lämnas tom');
		$this->form_validation->set_message('min_length', '%s måste innehålla minst %s tecken');
		$this->form_validation->set_message('max_length', '%s får innehålla max %s tecken');
		
		// Kör kollen
		if ($this->form_validation->run() == TRUE) {
			
			// Permalänk?
			if($this->input->post('permalink') != "") {
				$permalink = $this->input->post('permalink');
			} else {
				$permalink = $this->studenten->make_permalink($this->input->post('title'));
			}
			
			// Spara objekt
			$data = array(
			   'parent' => $this->input->post('parent'),
			   'title' => $this->input->post('title'),
			   'permalink' => $permalink,
			   'lati' => $this->input->post('lati'),
			   'longi' => $this->input->post('longi')
			);
			$this->db->where('id', $id);
			$this->db->update('list_regions', $data);
			
			// Skapa meddelande
			$this->session->set_flashdata('message', '<div class="msg ok">Kommunen är nu uppdaterad.</div>');
			
			// Klart
			redirect('backend/redigera/lan/'.$lan);
		}
	}
	
	// Spara ny skola
	function _save_school()
	{
		// Sätt regler
		$this->form_validation->set_rules('title', 'Namnet', 'required|trim|max_length[52]');
		
		// Sätt meddelanden
		$this->form_validation->set_message('required', '%s kan inte lämnas tom');
		$this->form_validation->set_message('min_length', '%s måste innehålla minst %s tecken');
		$this->form_validation->set_message('max_length', '%s får innehålla max %s tecken');
		
		// Kör kollen
		if ($this->form_validation->run() == TRUE) {
			
			// Spara objekt
			$data = array(
			   'parent_id' => $this->input->post('parent'),
			   'type' => $this->input->post('type'),
			   'title' => $this->input->post('title'),
			   'url' => $this->input->post('url')
			);
			$this->db->insert('list_schools', $data);
			
			// Skapa meddelande
			$this->session->set_flashdata('message', '<div class="msg ok">Skolan är nu sparad.</div>');
			
			// Klart
			redirect('backend/skolor');
		}
	}
	
	// Uppdatera skola
	function _update_school($id)
	{
		// Sätt regler
		$this->form_validation->set_rules('title', 'Namnet', 'required|trim|max_length[52]');
		
		// Sätt meddelanden
		$this->form_validation->set_message('required', '%s kan inte lämnas tom');
		$this->form_validation->set_message('min_length', '%s måste innehålla minst %s tecken');
		$this->form_validation->set_message('max_length', '%s får innehålla max %s tecken');
		
		// Kör kollen
		if ($this->form_validation->run() == TRUE) {
			
			// Spara objekt
			$data = array(
			   'parent_id' => $this->input->post('parent'),
			   'type' => $this->input->post('type'),
			   'title' => $this->input->post('title'),
			   'url' => $this->input->post('url')
			);
			$this->db->where('id', $id);
			$this->db->update('list_schools', $data);
			
			// Skapa meddelande
			$this->session->set_flashdata('message', '<div class="msg ok">Skolan är nu uppdaterad.</div>');
			
			// Klart
			redirect('backend/skolor');
		}
	}
	
	// Spara inställningar
	function _save_settings()
	{
		$this->load->model('settings_model');
		
		// Spara allt
		$this->settings_model->save_settings();
		
		// Sätt meddelande
		$this->session->set_flashdata('message', '<div class="msg ok">Inställningarna är nu sparade.</div>');
		
		redirect('backend/installningar');
	}
	
	// Ladda upp logga
	function _upload_logo($id)
	{
		$config['upload_path'] = './uploads/logo/';
		$config['allowed_types'] = 'gif|jpg|jpeg|png';
		$config['encrypt_name'] = TRUE;	
		$config['max_size'] = '2000';
		$config['max_width'] = '900';
		$config['max_height'] = '900';						
		
		$this->load->library('upload', $config);
		
		if(!$this->upload->do_upload('logo')) echo $this->upload->display_errors();
		
		else {
			$fInfo = $this->upload->data();
			$this->_resize_logo($fInfo['file_name']);
			
			$data['uploadInfo'] = $fInfo;
			$data['thumbnail_name'] = $fInfo['raw_name'] . '_thumb' . $fInfo['file_ext'];
			
			// Save filename to db
			$data = array(
				'logo' => $fInfo['raw_name'] . $fInfo['file_ext']
			);
			
			$this->db->where('id', $id);
			$this->db->update('list_ads', $data);
		}
	}
	
	// Create thumbnail
	function _resize_logo($fileName) {
		$config['image_library'] = 'gd2';
		$config['source_image'] = './uploads/logo/' . $fileName;	
		$config['create_thumb'] = FALSE;
		$config['maintain_ratio'] = TRUE;
		$config['width'] = 200;
		
		$this->load->library('image_lib', $config);
		if(!$this->image_lib->resize()) echo $this->image_lib->display_errors();
	}
	
	// Hämta filnamn till logga
	function _get_logo($id)
	{
		$sql_check = "SELECT logo FROM list_ads WHERE id = '$id' LIMIT 1";
		$query = $this->db->query($sql_check);
		
		if($query->num_rows() > 0) {
			$row = $query->row(); 
			return $row->logo;
		} else {
			return "";
		}
	}
	
	// Rensa alla kategorirelationer till denna
	function _clear_relations($id)
	{
		$this->db->where('ad_id', $id);
		$this->db->delete('ads_relations'); 
		
		return TRUE;
	}
}
?>