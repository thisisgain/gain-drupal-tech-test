<?php

namespace Drupal\gain_drupal_tech_test\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class RecentArticlesSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'gain_drupal_tech_test.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'gain_drupal_tech_test_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $config = $this->config('gain_drupal_tech_test.settings');

    $form['items_to_show'] = [
      '#type' => 'number',
      '#title' => $this->t('Number of recent articles'),
      '#description' => $this->t('Configure how many recent articles should appear in the block.'),
      '#default_value' => $config->get('items_to_show') ?: 5,
      '#min' => 1,
      '#max' => 50,
      '#required' => TRUE,
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    $this->configFactory()
      ->getEditable('gain_drupal_tech_test.settings')
      ->set('items_to_show', (int) $form_state->getValue('items_to_show'))
      ->save();

    parent::submitForm($form, $form_state);
  }

}