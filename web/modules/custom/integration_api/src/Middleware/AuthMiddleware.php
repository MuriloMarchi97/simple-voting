<?php

namespace Drupal\integration_api\Middleware;

use Drupal\Core\Password\PasswordInterface;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Authentication middleware for integration API.
 */
final class AuthMiddleware implements EventSubscriberInterface {

  public function __construct(
    private readonly PasswordInterface $passwordService,
    private readonly AccountInterface $currentUser,
  ) {}

  /**
   * Checks permissions before accessing API.
   *
   * @param RequestEvent $event
   *   The request event.
   */
  public function onKernelRequest(RequestEvent $event): void {
    $request = $event->getRequest();

    // Ignores when user is admin or trying to access another page.
    if (
      !$event->isMainRequest() ||
      $this->currentUser->hasPermission('admin') ||
      !str_contains($request->getPathInfo(), '/integration/api')
    ) {
      return;
    }

    $isAuthorized = $this->checkApiAccess($request);

    if (!$isAuthorized) {
      // If is an authenticated user, trying to access the API redirect to 404.
      if ($this->currentUser->isAuthenticated()) {
        throw new NotFoundHttpException('Page not found.');
      }
      throw new AccessDeniedHttpException('Access denied.');
    }

  }

  /**
   * Checks user is authenticated and has with right permissions.
   *
   * @param Request $request
   *   The request object.
   *
   * @return bool
   *   True when have access, false when it's not authorized.
   */
  private function checkApiAccess(Request $request): bool {
    $authHeader = $request->headers->get('Authorization');

    if ($authHeader && preg_match('/^Basic (.+)$/i', $authHeader, $matches)) {
      $credentials = base64_decode($matches[1]);
      [$username, $password] = explode(':', $credentials, 2);

      $user = user_load_by_name($username);
      if (
        $user &&
        $user->hasPermission('administer integration_api') &&
        $this->passwordService->check($password, $user->getPassword())
      ) {
        return TRUE;
      }
    }

    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents(): array {
    return [
      'kernel.request' => 'onKernelRequest',
    ];
  }

}
