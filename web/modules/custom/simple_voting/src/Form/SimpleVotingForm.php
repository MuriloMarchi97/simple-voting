<?php

declare(strict_types=1);

namespace Drupal\simple_voting\Form;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\DependencyInjection\AutowireTrait;
use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Entity\EntityRepositoryInterface;
use Drupal\Core\Entity\EntityTypeBundleInfoInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\file\Entity\File;

/**
 * Form controller for simple voting entity edit forms.
 */
final class SimpleVotingForm extends ContentEntityForm {

  use AutowireTrait;

  public function __construct(
    EntityRepositoryInterface $entity_repository,
    EntityTypeBundleInfoInterface $entity_type_bundle_info,
    TimeInterface $time,
  ) {
    parent::__construct($entity_repository, $entity_type_bundle_info, $time);
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    return parent::buildForm($form, $form_state);
  }


  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $values = $form_state->getValues();
    foreach ($values['answers'] as $key => $answer) {
      $fid = $values['answers'][intval($key)]['image_uploader'][0];
      $file = File::load($fid);
      if ($file) {
        $file->setPermanent();
        $file->save();
        $values['answers'][intval($key)]['image'] = $fid;
      }
    }
    $form_state->setValues($values);

    parent::submitForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state): int {
    if ($result = parent::save($form, $form_state)) {
      $form_state->setRedirectUrl(
        Url::fromRoute('entity.simple_voting.canonical', [
          'simple_voting' => $this->entity->id(),
        ])
      );
    }

    return $result;
  }

}
