<?php

namespace Drupal\mytask\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Database;

/**
 * Class MydataController
 * @package Drupal\mytask\Controller
 */
class MydataController extends ControllerBase
{

  /**
   * @return array
   */
  public function show($id)
  {

    $conn = Database::getConnection();

    $query = $conn->select('mytable', 'm')
      ->condition('id', $id)
      ->fields('m');
      $data = $query->execute()->fetchAssoc();

      $title = $data['title'];
      $link = $data['link'];
      $senior = $data['senior'];
      $senior_time = $data['senior_time'];
      $junior_time = $data['junior_time'];

    return [
      '#type' => 'markup',
      '#markup' => "<h1>$title</h1><br>
                    <p>Uzdoties linkas : $link</p>
                    <p>Senior : $senior </p>
                    <p>Senior vertinimas : $senior_time</p>
                    <p>Junior laikas : $junior_time</p>"
    ];
  }
}
