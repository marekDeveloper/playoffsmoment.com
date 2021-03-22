<?php
/**
 * @file
 * Contains Drupal\nfl_teams\Form
 */
namespace Drupal\nfl_teams\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\nfl_teams\TeamsAPI;

class SettingsForm extends ConfigFormBase {
  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'nfl_teams.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'nfl_teams_admin_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    // get parameter from the query, if there is checkApiFlag as param we will try to fetch data
    // based on this parameter form might be different as well
    $checkApiFlag = \Drupal::request()->query->get('checkApiFlag');

    // get config values
    $config = $this->config('nfl_teams.settings');

    // Teams API object, needed for status information later
    $teamsApi = new TeamsAPI;

    // check config for allow_url_fopen
    $allow_url_fopen = ini_get('allow_url_fopen') ? 'TRUE' : 'FALSE';

    //**** Form fields ****//
    $form = [];

    $form['api_url_fieldset'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('NFL Teams API Info'),
    ];

    // check if we going to test API or show edit form
    if ($checkApiFlag) {
      
      $form['api_url_fieldset']['api_url'] = [
        '#type' => 'markup',
        '#markup' => $this->t('NFL Teams API URL: ') . $config->get('api_url') . '<br />'
      ];

    } else {

      $form['api_url_fieldset']['api_url'] = [
        '#type' => 'textfield',
        '#title' => $this->t('NFL Teams API URL'),
        '#description' => $this->t('Full URL for 3rd party API with Key.'),
        '#default_value' => $config->get('api_url'),
        '#required' => TRUE
      ];

    }

    // add info about $allow_url_fopen
    $form['api_url_fieldset']['allow_url_fopen'] = [
      '#type' => 'markup',
      '#markup' => $this->t('PHP Configuration') . " - allow_url_fopen: <b>{$allow_url_fopen}</b><br />"
    ];

    // setting information list for API status, weight is moving this below save button
    $form['api_status_fieldset'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('NFL Teams API Status'),
      '#weight' => 500
    ];

    $form['api_status_fieldset']['url_info'] = [
      '#type' => 'markup',
      '#markup' => '<div class="messages messages--' . ($teamsApi->validUrlFlag ? 'status' : 'error') . '">API URL Validation Status: ' . ($teamsApi->validUrlFlag ? 'OK' : 'ERROR') . '</div>'
    ];

    // add messages to help debug when needed
    $form['api_status_fieldset']['url_info']['#markup'].= $teamsApi->messages();

    // if have URL and $checkApiFlag we will check API connection as well
    if ($checkApiFlag) {

      $form['api_result_fieldset'] = [
        '#type' => 'fieldset',
        '#title' => $this->t('NFL Teams API Data'),
        '#weight' => 499
      ];

      $form['api_result_fieldset']['data'] = [
        '#type' => 'markup',
        '#markup' => '<div class="messages messages--status"><pre>' . print_r($teamsApi->getApiData(), 1) . '</pre></div>'
      ];

      // add button to go back to module configuration screen from this fieldset
      $current_path = \Drupal::service('path.current')->getPath();
      $form['api_url_fieldset']['api_url_status']['#markup'].= '<p><a class="button" href="' . $current_path .'">Back to module configuration</a></p>';

    } else {
      // add button to fetch API data
      $form['api_status_fieldset']['url_info']['#markup'].= '<a class="button button--primary" href="?checkApiFlag=1">SHOW API DATA</a>';
    }

    // different return depends on parameter if we are calling API or not
    if ($checkApiFlag) {
      // just return form without save information - information form
      return $form;
    } else {
      // return form with submit button and able to save configurable fields
      return parent::buildForm($form, $form_state);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $this->config('nfl_teams.settings')
      ->set('api_url', $form_state->getValue('api_url'))
      ->save();
  }
}
