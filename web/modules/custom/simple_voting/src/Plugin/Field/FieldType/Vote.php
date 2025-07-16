<?php

namespace Drupal\simple_voting\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Plugin implementation of the 'vote' field type.
 *
 * @FieldType(
 *   id = "vote",
 *   label = @Translation("Vote"),
 *   description = @Translation("Links a user to a voted option."),
 * )
 */
class Vote extends FieldItemBase {

  /**
   * The option.
   */
  protected string $option;

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition): array {
    return [
      'columns' => [
        'option' => [
          'description' => 'The ID of the option voted.',
          'type' => 'int',
          'unsigned' => TRUE,
          'not null' => TRUE,
        ],
        'user_id' => [
          'description' => 'The ID of the user who voted.',
          'type' => 'int',
          'unsigned' => TRUE,
          'not null' => TRUE,
        ],
      ],
      'foreign keys' => [
        'option' => [
          'table' => 'simple_voting__answers',
          'columns' => ['option' => 'delta'],
        ],
        'user_id' => [
          'table' => 'users_field_data',
          'columns' => ['user_id' => 'uid'],
        ],
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition): array {
    $properties['option'] = DataDefinition::create('integer')
      ->setLabel(t('Option'))
      ->setRequired(TRUE)
      ->setDescription(t('The ID of the content entity being voted on.'));

    $properties['user_id'] = DataDefinition::create('integer')
      ->setLabel(t('User Id'))
      ->setRequired(TRUE)
      ->setDescription(t('The ID of the user who voted.'));

    return $properties;
  }

}
