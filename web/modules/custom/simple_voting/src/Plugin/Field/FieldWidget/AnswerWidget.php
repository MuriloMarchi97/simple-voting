<?php

namespace Drupal\simple_voting\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\simple_voting\Plugin\Field\FieldType\Answer;

/**
 * Plugin implementation of the 'answer_option_widget' widget.
 *
 * @FieldWidget(
 *   id = "answer_option_widget",
 *   label = @Translation("Answer Option Widget"),
 *   field_types = {
 *     "answer_option"
 *   }
 * )
 */
class AnswerWidget extends WidgetBase {

  /**
   * Get the form element for the field widget.
   *
   * @param FieldItemListInterface<Answer> $items
   *   The field items to prepare for display.
   * @param mixed $delta
   *   The delta of the item.
   * @param array $element
   *   The form element.
   * @param array $form
   *   The form.
   * @param FormStateInterface $form_state
   *   The form state.
   *
   * @return array
   *   The form element.
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state): array {
    $element = [
      '#type' => 'fieldset',
      '#title' => $this->t('Answer Option @delta', ['@delta' => $delta]),
    ];

    // Title field.
    $element['name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Title'),
      '#required' => TRUE,
      '#default_value' => $items[$delta]->name ?? '',
    ];

    $element['description'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Description'),
      '#required' => FALSE,
      '#default_value' => $items[$delta]->description ?? '',
    ];

    $element['image'] = [
      '#type' => 'hidden',
      '#title' => $this->t('image id'),
      '#required' => FALSE,
      '#default_value' => $items[$delta]->image ?? 0,
    ];

    $element['image_uploader'] = [
      '#type' => 'managed_file',
      '#title' => $this->t('Upload Image'),
      '#upload_location' => 'public://images/',
      '#default_value' => !empty($items[$delta]->image) ? [$items[$delta]->image] : [],
      '#required' => FALSE,
    ];

    return $element;
  }

}
