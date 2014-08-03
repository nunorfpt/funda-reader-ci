<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Welcome extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('makelaar', 'funda_makelaar');
    }

    public function index()
    {
        //Load the initial view
        $this->load->view('welcome_message');
        
    }

    public function showTop($number) {
    	$result = $this->funda_makelaar->getTop(10);

    	var_dump($result);
    	$this->load->view('welcome_message');
    }

}
