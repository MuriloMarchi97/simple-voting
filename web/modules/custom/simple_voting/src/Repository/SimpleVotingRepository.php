<?php

declare(strict_types=1);

namespace Drupal\simple_voting\Repository;

use Drupal\Core\DependencyInjection\AutowireTrait;
use Drupal\Core\Entity\EntityRepository;

/**
 * Repository for Simple Voting Entity.
 */
class SimpleVotingRepository extends EntityRepository {
  use AutowireTrait;
  // @todo implement interactions with Voting database, such as retrieve information and save votes.
}
