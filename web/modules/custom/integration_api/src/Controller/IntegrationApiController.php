<?php

declare(strict_types=1);

namespace Drupal\integration_api\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\AutowireTrait;
use Drupal\simple_voting\Service\SimpleVotingService;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Expose functionalities to enable API Integration.
 */
class IntegrationApiController extends ControllerBase {

  use AutowireTrait;

  /**
   * Logger.
   */
  private LoggerInterface $logger;

  /**
   * The controller constructor.
   */
  public function __construct(
    #[Autowire('simple_voting.service')]
    private readonly SimpleVotingService $simpleVotingService,
  ) {
    $this->logger = $this->getLogger('integration_api');
  }


  /**
   * Handles the API Calls.
   *
   * @param Request $request
   *   The request object.
   * @param string $action
   *   The action the user is trying to perform.
   *
   * @return JsonResponse
   *   Json encoded response.
   */
  public function __invoke(Request $request, string $action = ''): JsonResponse {
    try {
      $responseBody = match ($action) {
        'get-voting' => $this->getVoting($request),
        'add-vote' => $this->saveVote($request),
        default => throw new Exception('Provide a valid action.'),
      };

      return $this->response(TRUE, $responseBody);

    }
    catch (Exception $e) {
      $this->logger->error($e->getMessage());
      return $this->response(FALSE, ['messages' => [$e->getMessage()]]);
    }
  }

  /**
   * Parse the response into the expected pattern.
   *
   * @param bool $success
   *   False when happened an error processing the request.
   * @param array $body
   *   Array containing the response values.
   *
   * @return JsonResponse
   *   The json format response.
   */
  public function response(bool $success, array $body): JsonResponse {
    return new JsonResponse(['success' => $success] + $body);
  }

  /**
   * Get active votings.
   *
   * @param Request $request
   *   The request object.
   *
   * @return array
   *   Formatted array with result values.
   *
   * @throws Exception
   */
  public function getVoting(Request $request): array {
    /** @var string|null $votingId */
    $votingId = $request->query->get('votingId');

    return ['items' => $this->simpleVotingService->getVoting($votingId)];
  }

  /**
   * Creates new user.
   *
   * @param Request $request
   *   The request object.
   *
   * @return array
   *   Formatted array with result values.
   *
   * @throws Exception
   */
  public function saveVote(Request $request): array {
    $payload = $request->getPayload();

    $params = [
      'userId' => $payload->get('UserId') ?? '',
      'simpleVotingId' => $payload->get('SimpleVotingId') ?? '',
      'optionId' => $payload->get('OptionId') ?? '',
    ];

    if (!$this->simpleVotingService->saveVote(...$params)) {
      throw new Exception('Error saving vote');
    }

    return ['vote_saved' => TRUE];
  }

}
