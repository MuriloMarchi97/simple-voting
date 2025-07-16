<?php

namespace Drupal\simple_voting\Service;

use Drupal\simple_voting\Entity\SimpleVoting;

/**
 * Simple voting service.
 */
class SimpleVotingService {
  // @todo implement services to interact with voting module.

  /**
   * Retrieves simple voting entity.
   *
   * @param string|null $votingId
   *   The voting id.
   *
   * @return SimpleVoting[]
   *   Array of found entities.
   */
  public function getVoting(?string $votingId = null): array {
    // @todo load specific entity when have id, otherwise load all active votings.
    return [];
  }

  /**
   * Save user vote.
   *
   * @param string $userId
   *    The current user id.
   * @param string $simpleVotingId
   *   The voting entity id.
   * @param string $optionId
   *   The option user voted on.
   *
   * @return bool
   *   Either true on success or false on failure.
   */
  public function saveVote(string $userId, string $simpleVotingId, string $optionId): bool {
    // @todo save vote on specific entity option and return sucess or failure.
    return TRUE;
  }
}
