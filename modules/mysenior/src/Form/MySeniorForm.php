<?php

namespace Drupal\mysenior\Form;

use Drupal\Core\Database\Database;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\Link;


class MySeniorForm extends FormBase
{
  /**
   * {@inheritdoc}
   */
  public function getFormId()
  {
    return 'mysenior_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state)
  {
    $conn = Database::getConnection();
    $data = array();
    if (isset($_GET['id'])) {
      $query = $conn->select('seniors_table', 'm')
        ->condition('id', $_GET['id'])
        ->fields('m');
      $data = $query->execute()->fetchAssoc();
    }

    $form['first_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('first name'),
      '#required' => true,
      '#default_value' => (isset($data['first_name'])) ? $data['first_name'] : '',
    ];
    $form['last_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('last name'),
      '#required' => true,
      '#default_value' => (isset($data['last_name'])) ? $data['last_name'] : '',
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('save'),
      '#buttom_type' => 'primary'
    ];

    $back = Url::fromUserInput('/mysenior/seniors');

    $form['action']['cancel'] = ['#markup' => Link::fromTextAndUrl(t('Back to page'), $back)->toString(),];

    return $form;
  }

  /**
   * @param array $form
   * @param FormStateInterface $form_state
   */
  public function validateForm(array &$form, FormStateInterface $form_state)
  {
    if (is_numeric($form_state->getValue('first_name'))) {
      $form_state->setErrorByName('first_name', $this->t('Error, The First Name Must Be A String'));
    }
    if (is_numeric($form_state->getValue('last_name'))) {
      $form_state->setErrorByName('last_name', $this->t('Error, The Last Name Must Be A String'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state)
  {

    $data = array(
      'first_name' => $form_state->getValue('first_name'),
      'last_name' => $form_state->getValue('last_name'),
    );

    if (isset($_GET['id'])) {

      \Drupal::database()->update('seniors_table')->fields($data)->condition('id', $_GET['id'])->execute();
    } else {

      \Drupal::database()->insert('seniors_table')->fields($data)->execute();
    }

    \Drupal::messenger()->addStatus('Succesfully saved');
    $url = new Url('mysenior.display_data');
    $response = new RedirectResponse($url->toString());
    $response->send();
  }

}
