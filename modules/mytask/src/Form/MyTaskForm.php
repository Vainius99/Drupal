<?php

namespace Drupal\mytask\Form;

use Drupal\Core\Database\Database;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\Link;

class MyTaskForm extends FormBase
{
  /**
   * {@inheritdoc}
   */
  public function getFormId()
  {
    return 'mytask_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state)
  {
    $conn = Database::getConnection();
    $data = array();
    if (isset($_GET['id'])) {
      $query = $conn->select('mytable', 'm')
        ->condition('id', $_GET['id'])
        ->fields('m');
      $data = $query->execute()->fetchAssoc();
    }
    $query1 = \Drupal::database()->select('seniors_table', 'm');
    $query1->fields('m');
    $results = $query1->execute()->fetchAll();
    $full_name = array();
    foreach ($results as $name) {
      $names = $name->first_name . ' ' . $name->last_name;
      $full_name [$names] = $names;
    };

    if (isset($data['status'])) {
      $status_value = $data['status'];
    }  else {
      $status_value = 0;
    };

    $form['title'] = [
      '#type' => 'textfield',
      '#title'=>$this->t('Title'),
      '#required'=>TRUE,
      '#default_value'=>(isset($data['title'])) ? $data['title']:'',
    ];

    $form['link'] = [
      '#type' => 'url',
      '#title'=>$this->t('Link'),
      '#required'=>TRUE,
      '#default_value'=>(isset($data['link'])) ? $data['link']:'',
    ];

    $form['senior'] = [
      '#type' => 'select',
      '#title'=>$this->t('Senior Name'),
      '#required'=>TRUE,
      '#options'=> $full_name,
      '#default_value'=>(isset($data['senior'])) ? $data['senior']:'',
    ];

    $form['senior_time'] = [
      '#type' => 'number' ,
      '#title'=>$this->t('Senior_time(hours)'),
      '#required'=>TRUE,
      '#default_value'=>(isset($data['senior_time'])) ? $data['senior_time']:0,
    ];

    $form['junior_time'] = [
      '#type' => 'number',
      '#title'=>$this->t('Junior time(hours)'),
      '#required'=>TRUE,
      '#default_value'=>(isset($data['junior_time'])) ? $data['junior_time']:0,
    ];

    $form['status'] = [
      '#type' => 'radios',
      '#options' => array(
        0 => $this
          ->t('no'),
        1 => $this
          ->t('yes'),
        ),
        '#title'=>$this->t('Is Task Finished ?'),
        '#default_value'=>$status_value,
      ];

    $form['submit'] = ['#type' => 'submit', '#value' => $this->t('save')];

    $back = Url::fromUserInput('/mytask/tasks');

    $form['action']['cancel'] = ['#markup' => Link::fromTextAndUrl(t('Back to page'), $back)->toString(),];

    return $form;
  }

  /**
   * @param array $form
   * @param FormStateInterface $form_state
   */

   public function validateForm(array &$form, FormStateInterface $form_state)
  {
    if (is_numeric($form_state->getValue('title'))) {
      $form_state->setErrorByName('title', $this->t('Error, The First Name Must Be A String'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state)
  {
    $data = array(
      'title' => $form_state->getValue('title'),
      'link' => $form_state->getValue('link'),
      'senior' => $form_state->getValue('senior'),
      'senior_time' => $form_state->getValue('senior_time'),
      'junior_time' => $form_state->getValue('junior_time'),
      'status' => $form_state->getValue('status'),
    );

    if (isset($_GET['id'])) {
      \Drupal::database()->update('mytable')->fields($data)->condition('id', $_GET['id'])->execute();
    } else {
      \Drupal::database()->insert('mytable')->fields($data)->execute();
    }
    \Drupal::messenger()->addStatus('Succesfully saved');
    $url = new Url('mytask.display_data');
    $response = new RedirectResponse($url->toString());
    $response->send();
  }
}
