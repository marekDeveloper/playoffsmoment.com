<?php

namespace Drupal\nfl_teams;

use Drupal\Component\Serialization\Json;
use Drupal\Component\Utility\UrlHelper;
use GuzzleHttp\Client;
use \GuzzleHttp\Exception\ClientException;

/**
 * Teams API object
 */
class TeamsAPI {

  /**
   * Flag for valid url
   *
   * @var bool
   */
  public $validUrlFlag;

  /**
   * The settings configuration
   */
  protected $fullUrl;

  /**
   * Store (error) messages
   *
   * @var string
   */
  protected $message;

  /**
   * TeamsAPI constructor
   */
  public function __construct() {

    // validate provided URL, if not empty and regex check
    // this will setup $this->fullUrl as well
    $this->validUrlFlag = $this->isValidUrl();
  }

  /**
   * TeamsAPI URL validator
   */
  public function isValidUrl() {

    // get config
    $config = \Drupal::config('nfl_teams.settings');

    // get URL from config
    $this->fullUrl = $config->get('api_url');

    // trim URL
    $this->fullUrl = trim($this->fullUrl);

    // check if not empty
    if (empty($this->fullUrl)) {
      $this->message.= 'API URL is empty' . PHP_EOL;
      return FALSE;
    }

    // use Drupal UrlHelper to perform fancy regex if url is valid (TRUE - absolute URL)
    if (UrlHelper::isValid($this->fullUrl, TRUE)) {
      // valid
      $this->message.= 'API URL seems to be valid (UrlHelper)' . PHP_EOL;
      // this will continue
    } else {
      // not valid
      $this->fullUrl = FALSE;
      $this->message.= 'API URL does not seem to be valid (UrlHelper)' . PHP_EOL;
      return FALSE;
    }

    // let's try to get headers
    $file_headers = @get_headers($this->fullUrl);
    $this->message.= print_r($file_headers, 1);
    if ($file_headers && strpos($file_headers[0], '200 OK')) {
      // valid
      $this->message.= 'API URL seems to be valid (get_headers)' . PHP_EOL;
      return TRUE;
    } else {
      // not valid
      $this->fullUrl = FALSE;
      $this->message.= 'API URL does not seem to be valid (get_headers)' . PHP_EOL;
      return FALSE;
    }

    // use file_exists() function to check if URL actually exists
    // TO DO! This will only work when allow_url_fopen?
    // does not seems to be working for API link provided
    /*
    if (file_exists($this->fullUrl)) {
      // valid
      $this->message.= 'API URL seems to be valid (file_exists)' . PHP_EOL;
      return TRUE;
    } else {
      // not valid
      $this->fullUrl = FALSE;
      $this->message.= 'API URL does not seem to be valid (file_exists)' . PHP_EOL;
      return FALSE;
    }
    */

  }

  // get data from API call - get json and convert to array and return array
  public function getApiData() {

    // always return array, possible empty if something goes wrong
    $dataResult = [];

    // there are multiple ways to fetch remote url content
    // 1. easiest is probably file_get_contents() - depends on PHP configuration for allow_url_fopen
    // 2. second would be to write direct PHP CURL code
    // 3. Drupal way is probably to use their Guzzle HTTP Client - this what I'm going to try

    // $teamsJson = file_get_contents($this->fullUrl);

    // prepare guzzle http adapter
    $adapter = new Client();

    try {

      // perform get request to remote API
      $request = $adapter->request('GET', $this->fullUrl);

      // check status code
      if ($request->getStatusCode() != 200) {

        // error
        $this->message.= 'API GET request did not return 200 Status Code';

      } else {

        // all seems to be fine - get content body which should be JSON - decode as well
        $jsonData = $request->getBody()->getContents();

        // decode 
        $dataResult = Json::decode($jsonData);

      } // end if

    } catch(ClientException $e) {

      $this->message = $e->getMessage();
      return FALSE;

    }

    // return final array
    return $dataResult;

  }

  // get Teams - get teams from json into array and parse out teams info from data structure
  public function getTeams() {

    $data = $this->getApiData();
    
  }

  // get messages, other than critical errors
  public function messages() {
    if ($this->message) {
      $out = '<pre class="messages messages--warning">' . $this->message . '</pre>';
      $this->message = '';
      return $out;
    }
    return FALSE;
  }

}
