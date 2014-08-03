<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Fetch extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        //Load curl library at /libraries for handling curl http requests
        $this->load->library('curl');
        $this->load->library('unit_test');
        $this->load->model('request', 'funda_request');
        $this->load->model('makelaar', 'funda_makelaar');
    }

    public function index ($param = '')
    {
        $this->fetchRecords($param);
        $this->load->view('welcome_message');
    }

    private function fetchRecords ($param = '')
    {
        $ret = [];
        $current_page = 1;
        $last_page = false;
        $url_base = 'http://partnerapi.funda.nl/feeds/Aanbod.svc/json/a001e6c3ee6e4853ab18fe44cc1494de/?type=koop&zo=/amsterdam/';
        if ($param=='garden') {
            $url_base.='tuin/';
        }

        $this->funda_makelaar->emptyTable();

        while (!$last_page) {
            $url_call = $url_base.'&page='.$current_page.'&pagesize=25';
            $this->curl->create($url_call);
            $this->curl->option(CURLOPT_COOKIEFILE, 'saved_cookies.txt');
            $this->curl->option(CURLOPT_TIMEOUT, 5000);
            $this->curl->option(CURLOPT_CONNECTTIMEOUT, 5000);

            $ret1 = json_decode($this->curl->execute(), true);
            array_push($ret, $ret1['Objects']);

            sleep(0.6);

            if (count($ret1['Objects'])<1) {
                $last_page = true;
            } else {
                $current_page+=1;
            }
            $array['url_requested'] = $url_call;
            $array['time_requested'] = date("Y-m-d H:i:s");
            $array['objects_returned'] = count($ret1['Objects']);
            $this->funda_request->insert($array);
            

            foreach ($ret1['Objects'] as $obj) {
                //echo $this->unit->run($obj, 'is_array');
                $values['MakelaarId'] = $obj['MakelaarId'];
                $values['MakelaarNaam'] = $obj['MakelaarNaam'];
                $this->funda_makelaar->insert($values);
            }


        }
        


    }
}
