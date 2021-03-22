<?php

namespace Drupal\nfl_teams;

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

  public function validateAPI() {

    // TO DO! Validate if we have valid URL in config and try to check that URL?

  }

  // get Teams
  public function getTeams() {

    $teamsListResult = FALSE;

    $teamsJson = file_get_contents($this->fullUrl);

    print_r($teamsJson);

    $adapter = new Client();

    try {

      // TO DO! Guzzle? or just fOpen?

    } catch(ClientException $e) {

      $this->message = $e->getMessage();
      return FALSE;

    }
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
