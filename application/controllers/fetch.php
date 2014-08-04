<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Fetch extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        //Load curl library at /libraries for handling curl http requests
        $this->load->library('curl');
        //Load codeigniter unit test library
        $this->load->library('unit_test');
        //Load model to store requests calls records
        $this->load->model('request', 'funda_request');
        //Load model of makelaar records
        $this->load->model('makelaar', 'funda_makelaar');
    }

    public function index ($param = '')
    {
        // this url gould be accessibibly by /fetch
        // if added a garden parameter, /fetch/garden, it will add the /tuin paramater to funda url
        $this->fetchRecords($param);
        $this->load->view('welcome_message');
    }

    private function fetchRecords ($param = '')
    {
        //Indicate start page
        $current_page = 1;
        //For check if last page
        $last_page = false;
        //Start building the url to request
        $url_base = 'http://partnerapi.funda.nl/feeds/Aanbod.svc/json/a001e6c3ee6e4853ab18fe44cc1494de/?type=koop&zo=/amsterdam/';
        //Hardcoded paramater for garden fetch
        if ($param=='garden') {
            $url_base.='tuin/';
        }

        //EMpty the table before inserting new records
        $this->funda_makelaar->emptyTable();

        //Loop to check record while not on last page
        while (!$last_page) {
            //Create a url for different pages
            $url_call = $url_base.'&page='.$current_page.'&pagesize=25';
            $this->curl->create($url_call);
            //Set curl options
            $this->curl->option(CURLOPT_COOKIEFILE, 'saved_cookies.txt');
            $this->curl->option(CURLOPT_TIMEOUT, 5000);
            $this->curl->option(CURLOPT_CONNECTTIMEOUT, 5000);

            // Decode the return data
            $ret1 = json_decode($this->curl->execute(), true);

            //If it only holds 100 calls per minute, the calls should wait 0.6 seconds between each one
            sleep(0.6);
            // Ifo no objects are returned, we are on the last page
            if (count($ret1['Objects'])<1) {
                $last_page = true;
            } else {
                //else increment the page number for next call
                $current_page+=1;
            }
            // Register the requests in the database
            // Added column timestamp so a cron job could check the date of the previous requets
            $array['url_requested'] = $url_call;
            $array['time_requested'] = date("Y-m-d H:i:s");
            $array['objects_returned'] = count($ret1['Objects']);
            $this->funda_request->insert($array);
            
            if($ret1['Objects']) {
                //Loop the array of objects in this page
                foreach ($ret1['Objects'] as $obj) {
                    // unit test to check if it is an object
                    //echo $this->unit->run($obj, 'is_array');
                    $values['MakelaarId'] = $obj['MakelaarId'];
                    $values['MakelaarNaam'] = $obj['MakelaarNaam'];
                    // Insert makelaar values in the database
                    $this->funda_makelaar->insert($values);
                }
            }


        }
        


    }
}
