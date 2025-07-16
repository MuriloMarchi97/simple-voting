<?php

namespace Drupal\simple_voting\Plugin\Field\FieldFormatter;

use Drupal\Core\DependencyInjection\DependencySerializationTrait;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\file\Entity\File;
use Drupal\simple_voting\Plugin\Field\FieldType\Answer;

/**
 * Plugin implementation of the 'answer_option_formatter'.
 *
 * @FieldFormatter(
 *   id = "answer_option_formatter",
 *   label = @Translation("Answer Option Formatter"),
 *   field_types = {
 *     "answer_option"
 *   }
 * )
 */
class AnswerFormatter extends FormatterBase {

  use DependencySerializationTrait;

  /**
   * {@inheritdoc}
   *
   * @param FieldItemListInterface<Answer> $items
   *   The field items to prepare for display.
   * @param mixed $langcode
   *   The language code to prepare for display.
   */
  public function viewElements(FieldItemListInterface $items, mixed $langcode): array {
    $elements = [];

    foreach ($items as $delta => $item) {
      $elements[$delta] = [
        '#theme' => 'answer_option',
        '#item_id' => $item->getName(),
        '#title' => $item->name,
        '#description' => $item->description,
        '#image' => $item->image ? File::load($item->image)->createFileUrl() : NULL,
      ];
    }

    return $elements;
  }

}
