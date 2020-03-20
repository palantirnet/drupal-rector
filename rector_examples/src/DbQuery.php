<?php

/**
 * @file
 * Contains \Drupal\rector_examples\DbQuery.
 */

namespace Drupal\rector_examples;

use Drupal\Core\Messenger\MessengerTrait;

class DbQuery {

  use MessengerTrait;

  /**
   * Example of static calls from a class with the trait.
   *
   * @return array
   */
  public function example() {
    db_query('select * from user');

    db_query('select * from user where name="%test"', ['%test'=>'Adam']);

    $args = ['%test'=>'Adam'];
    $opts = ['target' => 'default',
      'fetch' => \PDO::FETCH_OBJ,
      'return' => Database::RETURN_STATEMENT,
      'throw_exception' => TRUE,
      'allow_delimiter_in_query' => FALSE,
    ];

    db_query('select * from user where name="%test"', $args);

    db_query('select * from user where name="%test"', $args, $opts);

    $query = 'select * from user where name="%test"';
    $args = ['%test'=>'Adam'];
    $opts = [
      'target' => 'default',
      'fetch' => \PDO::FETCH_OBJ,
      'return' => Database::RETURN_STATEMENT,
      'throw_exception' => TRUE,
      'allow_delimiter_in_query' => FALSE,
    ];

    Database::getConnection(_db_get_target($options))->query($query, $args, $opts);

    return NULL;
  }

}
