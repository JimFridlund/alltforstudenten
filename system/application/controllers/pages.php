<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Pages extends Controller {

  function Pages()
  {
    parent::Controller();
  }

  /* =========================
     /balen (huvudsida)
     ========================= */
  function balen()
  {
    if (file_exists(APPPATH.'views/pages/page_balen.php')) {
      $data = array();
      $this->load->view('pages/page_balen', $data);
      return;
    }
    show_404();
  }

  /* =========================
     ALLA /balen/undersidor
     - Laddar views/pages/page_<slug>.php
     Ex: /balen/blommor -> views/pages/page_blommor.php
     ========================= */
  function bal_sub($sub = '')
  {
    $sub = trim((string)$sub);
    if ($sub === '') {
      redirect('balen', 'location', 302);
      return;
    }

    // Tillåt a-z, 0-9, _ och -
    if (!preg_match('/^[a-z0-9_-]+$/i', $sub)) {
      show_404();
      return;
    }

    // Om bindestreck i URL men underscore i filnamn
    $key = str_replace('-', '_', strtolower($sub));

    // Förväntad view-fil: views/pages/page_<key>.php
    $viewFile = APPPATH.'views/pages/page_'.$key.'.php';
    if (file_exists($viewFile)) {
      $data = array();
      $this->load->view('pages/page_'.$key, $data);
      return;
    }

    show_404();
  }

  /* =========================
     /om-oss
     ========================= */
  function om_oss()
  {
    if (file_exists(APPPATH.'views/pages/page_om_oss.php')) {
      $data = array();
      $this->load->view('pages/page_om_oss', $data);
      return;
    }
    show_404();
  }

  /* =========================
     /kontakt och /kontakt/ok|fel
     Prefill via POST från kommunsidor (robust)
     ========================= */
  function kontakt($statusSlug = '')
  {
    $data = array();

    // PREFILL från POST
    $data['prefill_kommun'] = trim((string)$this->input->post('prefill_kommun'));
    $data['prefill_lan']    = trim((string)$this->input->post('prefill_lan'));
    $data['prefill_cat']    = trim((string)$this->input->post('prefill_cat'));

    // Status via segment (/kontakt/ok eller /kontakt/fel)
    $statusSlug = trim((string)$statusSlug);
    if ($statusSlug === 'ok' || $statusSlug === 'fel') {
      $data['status'] = $statusSlug;
    } else {
      $data['status'] = '';
    }

    $data['meta_title'] = "Kontakt – boka plats i din kommun";
    $data['meta_desc']  = "Kontakta AlltFörStudenten.se och boka en exklusiv plats per kommun och kategori.";
    $data['meta_kw']    = "kontakt allt för studenten, boka plats kommun, annonsering student";

    if (file_exists(APPPATH.'views/pages/page_kontakt.php')) {
      $this->load->view('pages/page_kontakt', $data);
      return;
    }

    show_404();
  }

  /* =========================
     Kontakt skick (POST)
     ========================= */
  function kontakt_submit()
  {
    if (!$_POST) {
      redirect('kontakt', 'location', 302);
      return;
    }

    // Honeypot
    $hp = isset($_POST['website']) ? trim((string)$_POST['website']) : '';
    if ($hp !== '') {
      redirect('kontakt/ok', 'location', 302);
      return;
    }

    $name     = trim((string)$this->input->post('name'));
    $email    = trim((string)$this->input->post('email'));
    $phone    = trim((string)$this->input->post('phone'));
    $company  = trim((string)$this->input->post('company'));
    $kommun   = trim((string)$this->input->post('kommun'));
    $lan      = trim((string)$this->input->post('lan'));
    $category = trim((string)$this->input->post('category'));
    $message  = trim((string)$this->input->post('message'));

    if ($name === '' || $email === '' || $kommun === '' || $category === '') {
      redirect('kontakt/fel', 'location', 302);
      return;
    }

    $to = "info@weblings.se";
    $subject = "Bokningsförfrågan: ".$category." – ".$kommun;

    $body =
      "Ny bokningsförfrågan via AlltFörStudenten.se\n\n".
      "Namn: ".$name."\n".
      "E-post: ".$email."\n".
      "Telefon: ".$phone."\n".
      "Företag: ".$company."\n".
      "Kategori: ".$category."\n".
      "Kommun: ".$kommun."\n".
      "Län: ".$lan."\n\n".
      "Meddelande:\n".$message."\n";

    $headers  = "From: AlltForStudenten <info@weblings.se>\r\n";
    $headers .= "Reply-To: ".$email."\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

    @mail($to, "=?UTF-8?B?".base64_encode($subject)."?=", $body, $headers);

    redirect('kontakt/ok', 'location', 302);
  }

}