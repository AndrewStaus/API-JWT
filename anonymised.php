<?php

    class JWT_API{

        private $USER;
        private $PASSWORD;
        public $DOMAIN;
        public $token;
        public $expires;

        function __construct($domain, $user, $password) {
            $this->USER = $user;
            $this->PASSWORD = $password;
            $this->DOMAIN = $domain;
            $this->get_token();

        }

        function get_token() {

            $endpoint='/user/v1/login';
            $url = $this->DOMAIN.$endpoint;
        
            $curl = curl_init();

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_USERPWD, $this->USER . ":" . $this->PASSWORD);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_ENCODING, '');
            curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
            curl_setopt($ch, CURLOPT_TIMEOUT, 0);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            $response = curl_exec($ch);
            $expires_in = json_decode($response)->{'expires_in'};


            $this->token = json_decode($response)->{'id_token'};
            $this->expires = date("Y-m-d H:i:s", strtotime("+".$expires_in." second"));

        }

        function refresh_token() {
            $now = date("Y-m-d H:i:s");
            if ($this->expires <= $now) {
                $this->get_token();
            }
        }


        function get($call, $array=[]){
        
            $endpoint;

            if ($call == 'one'){
                $endpoint = '/api/v1/endpoint1?';

            } elseif ($call == 'two'){
                $endpoint = '/api/v1/endpoint2?';

            } elseif ($call == 'three'){
                $endpoint = '/api/v1/endpoint3?';

            } elseif ($call == 'four'){
                $endpoint = '/api/v1/endpoint4';

            } elseif ($call == 'five'){
                $endpoint = '/api/v1/endpoint5?';
            } else{
                return null;
            }
        
            $kwargs = '';
            foreach($array as $key => $value){
                if ($value != ''){
                    $kwargs = $kwargs.'&'.urlencode($key).'='.urlencode($value);
                }

            } 
            
            return $this->request($endpoint, $kwargs, 'GET');

        }

        function request($endpoint, $kwargs, $type){
            
            $this->refresh_token();

            $url = $this->DOMAIN.$endpoint.$kwargs;
    
            $curl = curl_init();

            $header = array();
            $header[] = 'Authorization: Bearer '.$this->token;

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_ENCODING, '');
            curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
            curl_setopt($ch, CURLOPT_TIMEOUT, 0);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $type);
            $response = curl_exec($ch);
            return $response;

            curl_close($ch);
        }
    }


    //Example and documentation:


    $username = 'Username';
    $password = 'Password';
    $domain = 'https://dev.api.com';

    $api = new Api($domain, $username, $password);


    echo "<br><br><h2>API Call One:</h2><br>";
    echo $api->get('one', [
                    "key1" => '123456',
                    "key2" => '',
                    "key3" => 'PR12345',
                    "key4" => '',
                    "key5" => '2'
                    ]);

 
    echo '<br><br><h2>API Call Two:</h2><br>';
    echo $api->get('two', [
                    'key1'=>'123456',
                    'key2'=>'123456',
                    'key3'=>'',
                    'key4'=>'',
                    'key5'=>''
                    ]);   

    echo '<br><br><h2>API Call Three:</h2><br>';
    echo $api->get('three', [
                    'key1' => '25'
                    ]);

    echo '<br><br><h2>API Call Four:</h2><br>';
    echo $api->get('four');
                

                 
?>