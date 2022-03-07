<?php

namespace Drupal\mysenior\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Database;


/**
 * Class MydataController
 * @package Drupal\mysenior\Controller
 */
class MydataController extends ControllerBase
{

  /**
   * @return array
   */
  public function show($id)
  {

    $conn = Database::getConnection();

    $query = $conn->select('seniors_table', 'm')
      ->condition('id', $id)
      ->fields('m');
    $data = $query->execute()->fetchAssoc();
    $full_name = $data['first_name'] . ' ' . $data['last_name'];

    return [
      '#type' => 'markup',
      '#markup' => "<h1>Senior / tech : $full_name</h1><br>"
    ];
  }

}
