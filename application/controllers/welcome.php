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

    public function showTop($number)
    {
        //Get the top X records from the makelaar filled table
        $result = $this->funda_makelaar->getTop($number);

        $data['records'] = $result;
        $this->load->view('top_list', $data);
    }
}