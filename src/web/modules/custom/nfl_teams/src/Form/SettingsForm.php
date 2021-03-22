<?php
/**
 * @file
 * Contains Drupal\nfl_teams\Form
 */
namespace Drupal\nfl_teams\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

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

    // setting information list for API status, weight is moving this below save button
    $form['api_status_fieldset'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('NFL Teams API Status'),
      '#weight' => 500
    ];

    $form['api_status_fieldset']['url_info'] = [
      '#type' => 'markup',
      '#markup' => 'TO DO!'
    ];

    // if have URL and $checkApiFlag we will check API connection as well
    if ($checkApiFlag) {

      print 'TO DO! Check API, try to call and maybe show JSON result?';

      // add button to go back to module configuration screen from this fieldset
      $current_path = \Drupal::service('path.current')->getPath();
      $form['api_url_fieldset']['api_url_status']['#markup'].= '<p><a class="button" href="' . $current_path .'">Back to module configuration</a></p>';


    }

    // different return depends on parameter if we are callng API or not
    if ($checkApiFlag) {
      // just return form without save information - information form
      return $form;
    } else {
      // retunr form with submit button and able to save configurable fields
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
