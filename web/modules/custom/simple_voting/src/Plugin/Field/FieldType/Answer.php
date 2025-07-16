<?php

namespace Drupal\simple_voting\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Plugin implementation of the 'answer' field type.
 *
 * @FieldType(
 *   id = "answer_option",
 *   label = @Translation("Answer"),
 *   description = @Translation("A field type that combines a title, image and description."),
 *   default_widget = "answer_widget",
 *   default_formatter = "answer_formatter"
 * )
 */
class Answer extends FieldItemBase {

  /**
   * A brief description.
   */
  protected string $description;

  /**
   * Image target id.
   */
  protected int $image;

  /**
   * Gets the description.
   *
   * @return string
   *   The brief description.
   */
  public function getDescription(): string {
    return $this->description;
  }

  /**
   * Gets the description.
   *
   * @return string
   *   The brief description.
   */
  public function getTitle(): string {
    return $this->name;
  }

  /**
   * Gets the image target id.
   *
   * @return int
   *   The fid.
   */
  public function getImage(): int {
    return $this->image;
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition): array {
    return [
      'columns' => [
        'name' => [
          'type' => 'varchar',
          'length' => 255,
        ],
        'description' => [
          'type' => 'varchar',
          'length' => 255,
        ],
        'image' => [
          'description' => 'The ID of the file entity.',
          'type' => 'int',
          'unsigned' => TRUE,
        ],
      ],
      'foreign keys' => [
        'image' => [
          'table' => 'file_managed',
          'columns' => ['image' => 'fid'],
        ],
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition): array {
    $properties['name'] = DataDefinition::create('string')
      ->setLabel(t('Title'))
      ->setRequired(TRUE);

    $properties['description'] = DataDefinition::create('string')
      ->setLabel(t('Description'))
      ->setRequired(FALSE);

    $properties['image'] = DataDefinition::create('integer')
      ->setLabel(t('Image'))
      ->setRequired(FALSE)
      ->setDescription(t('The ID of the uploaded image file.'));

    return $properties;
  }

}
