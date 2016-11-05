<?php

namespace AppBundle\Services;

use Redmine;

class RedmineClient
{
     protected $client;     
     
     public function __construct($url, $api) 
     {
        $this->client = new Redmine\Client($url, $api);         
     }
     
     public function getClient() 
     {
        return $this->client;     
     }
     
}
?>