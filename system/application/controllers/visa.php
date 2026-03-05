<?php
/**
 * @package Studenten 2011
 * @copyright 2010, iDenta labs
 * @author Kristoffer Lidman
 */
class Visa extends Controller {

	function Visa()
	{
		parent::Controller();
	}
	
	function index()
	{
		redirect('');
	}
	
	// Visa enskilt län
	function lan()
	{
		// Ladda modeller
		$this->load->model('region_model');
		
		// Ta fram den URL vi försöker använda
		$url = $this->uri->segment(2);
		$url2 = $this->uri->segment(3);
		
		// Hämta uppgifterna för att hitta användaren
		$lan = $this->region_model->get_region_by_url($url);
		$lan_id = $lan[0]->id;
		
		// Finns denna URL i db?
		if(!$lan > 0) {
			$this->my_error();
		} else {
			
			// Försöker vi även kolla en kommun?
			if(empty($url2)) {
				
				// Den finns iaf, då kör vi vidare
				$data['kommuner'] = $this->region_model->get_kommuner_to($lan_id);
				$data['markers'] = $data['kommuner'];
				
				// Till view
				$data['page_title'] = $lan[0]->title;
				$data['page_permalink'] = $lan[0]->permalink;
				$data['lan_lati'] = $lan[0]->lati;
				$data['lan_long'] = $lan[0]->longi;
				
				// SEO
				$data['meta_title'] = $lan[0]->title;
				$data['meta_desc'] = "Kommuner i ".$lan[0]->title;
				$data['meta_kw'] = "";
				
				// Visa sidan
				$this->load->view('view_single_lan', $data);
				
			} else {
			
				// Visa kommun
				$kommun = $this->region_model->get_kommun_by_url($url2);
				
				// Kolla först så den hittades
				if($kommun > 0) {
				
					// Plocka ihop vars
					$this->load->model('ads_model');
					$kommun_id = $kommun[0]->id;
					
					// Till view
					$data['page_title'] = $kommun[0]->title;
					$data['skola'] = $this->region_model->get_schools($kommun_id);
					$data['ads'] = $this->ads_model->get_ads_to($kommun_id, "studenten");
					$data['markers'] = "";
					$data['kommun_lati'] = $kommun[0]->lati;
					$data['kommun_long'] = $kommun[0]->longi;
					
					// SEO
					$data['meta_title'] = $kommun[0]->title;
					$data['meta_desc'] = "Studentflak, studentplakat och mycket mer i  ".$kommun[0]->title;
					$data['meta_kw'] = "";
					
					// Visa sidan
					$this->load->view('view_single_kommun', $data);
				}
			}
		}
	}
	
	// Länken finns ej i db
	function my_error()
	{
		// Sätt en 404 på den här
		$this->output->set_status_header('404');
		
		$data['meta_title'] = "404 - Länet du sökte hittades ej";
		$data['meta_desc'] = "";
		$data['meta_kw'] = "";
		
		$this->load->view('view_error_404', $data);
	}
	
	// Remap
	public function _remap($method)
	{
	     if (method_exists($this, $method))
			$this->$method();
	     else
			$this->my_error();
	}
}
?>