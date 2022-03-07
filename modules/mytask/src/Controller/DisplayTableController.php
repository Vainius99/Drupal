<?php

namespace Drupal\mytask\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Link;
use Drupal\Core\Url;

/**
 * Class DisplayTableController
 * @package Drupal\mytask\Controller
 */
class DisplayTableController extends ControllerBase
{

  public function tasks()
  {

    $header_table = array ('id' => t('ID'), 'title' => t('Title'),'link' => t('Link'), 'senior'=> t('Senior'),
    'senior_time'=>t('Senior time'),'junior_time'=>t('Junior time'),'status' => t('Status'), 'view' => t('View'),'edit'=> t('Edit'), 'delete'=> t('Delete'),);

    $query = \Drupal::database()->select('mytable', 'm');
    $query->fields('m', ['id', 'title', 'link', 'senior', 'senior_time','junior_time','status']);
    $results = $query->execute()->fetchAll();

    $rows = array();
    foreach ($results as $data) {
      $url_delete = Url::fromRoute('mytask.delete_form', ['id' => $data->id], []);
      $url_edit = Url::fromRoute('mytask.add_form', ['id' => $data->id], []);
      $url_view = Url::fromRoute('mytask.show_data', ['id' => $data->id], []);
      $linkDelete = Link::fromTextAndUrl('Delete', $url_delete);
      $linkEdit = Link::fromTextAndUrl('Edit', $url_edit);
      $linkView = Link::fromTextAndUrl('View', $url_view);
      if ( $data->status == 0 )  {
        $task = 'Not Finished';
      } else {
        $task = 'Done';
      };

      $rows[] = array(
        'id' => $data->id,
        'title' => $data->title,
        'link' => $data->link,
        'senior' => $data->senior,
        'senior_time' => $data->senior_time,
        'junior_time' => $data->junior_time,
        'task' => $task,
        'view' => $linkView,
        'delete' => $linkDelete,
        'edit' =>  $linkEdit,
      );

    }
    $add = Url::fromUserInput('/mytask/add');
    $text = 'Add Task';
    $form['table'] = [
      '#type' => 'table',
      '#header' => $header_table,
      '#rows' => $rows,
      '#empty' => t('No data found'),
      '#caption' => Link::fromTextAndUrl($text,$add)->toString(),
    ];
    return $form;
  }
}
