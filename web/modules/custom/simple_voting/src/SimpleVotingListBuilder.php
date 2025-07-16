<?php

declare(strict_types=1);

namespace Drupal\simple_voting;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Logger\LoggerChannelTrait;

/**
 * Provides a list controller for the simple voting entity type.
 */
final class SimpleVotingListBuilder extends EntityListBuilder {

  use LoggerChannelTrait;

  /**
   * {@inheritdoc}
   */
  public function buildHeader(): array {
    $header['label'] = $this->t('Label');
    $header['status'] = $this->t('Status');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity): array {
    $row['id'] = $entity->getLabel();
    $row['status'] = $entity->getStatus() ? $this->t('Enabled') : $this->t('Disabled');
    return $row + parent::buildRow($entity);
  }

}
