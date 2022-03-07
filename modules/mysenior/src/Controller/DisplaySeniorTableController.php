<?php

namespace Drupal\mysenior\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Link;
use Drupal\Core\Url;

/**
 * Class DisplaySeniorTableController
 * @package Drupal\mysenior\Controller
 */
class DisplaySeniorTableController extends ControllerBase
{

  public function seniors()
  {

    $header_table = array(
      'id' => t('ID'),
      'first_name' => t('first name'),
      'last_name' => t('last name'),
      'view' => t('View'),
      'delete' => t('Delete'),
      'edit' => t('Edit'),
    );

    $query = \Drupal::database()->select('seniors_table', 'm');
    $query->fields('m', ['id', 'first_name', 'last_name']);
    $results = $query->execute()->fetchAll();
    $rows = array();
    foreach ($results as $data) {
      $url_delete = Url::fromRoute('mysenior.delete_form', ['id' => $data->id], []);
      $url_edit = Url::fromRoute('mysenior.add_form', ['id' => $data->id], []);
      $url_view = Url::fromRoute('mysenior.show_data', ['id' => $data->id], []);
      $linkDelete = Link::fromTextAndUrl('Delete', $url_delete);
      $linkEdit = Link::fromTextAndUrl('Edit', $url_edit);
      $linkView = Link::fromTextAndUrl('View', $url_view);

      $rows[] = array(
        'id' => $data->id,
        'first_name' => $data->first_name,
        'last_name' => $data->last_name,
        'view' => $linkView,
        'delete' => $linkDelete,
        'edit' =>  $linkEdit,
      );

    }
    $add = Url::fromUserInput('/mysenior/add');
    $text = 'Add Task';

    $form['table'] = [
      '#type' => 'table',
      '#header' => $header_table,
      '#rows' => $rows,
      '#empty' => t('No data found'),
      '#caption' => Link::fromTextAndUrl($text,$add)->toString()
    ];
    return $form;

  }

}
