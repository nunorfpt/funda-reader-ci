<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Welcome extends CI_Controller
{
    public function index()
    {
        //Load curl library at /libraries for handling curl http requests
        $this->load->library('curl');
        $this->getRecords();
        //Load the initial view
        $this->load->view('welcome_message');
    }

    public function getRecords()
    {
        $ret = [];
        for ($counter = 1; $counter<3; $counter++) {
            //$this->curl->create('http://partnerapi.funda.nl/feeds/Aanbod.svc/json/a001e6c3ee6e4853ab18fe44cc1494de/?type=koop&zo=/amsterdam/tuin/&page=1&pagesize=25');
            $this->curl->create('http://partnerapi.funda.nl/feeds/Aanbod.svc/json/a001e6c3ee6e4853ab18fe44cc1494de/?type=koop&zo=/amsterdam/&page='.$counter.'&pagesize=25');

            //Options for curl request, keep session open
            $this->curl->option(CURLOPT_COOKIEFILE, 'saved_cookies.txt');

            sleep(0.6);

            $ret1 = json_decode($this->curl->execute(), true);
            array_push($ret, $ret1['Objects']);
        }

        // Execute - returns responce
        echo var_dump($ret);
    }
}
