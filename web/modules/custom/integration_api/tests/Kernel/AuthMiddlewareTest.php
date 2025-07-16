<?php

declare(strict_types=1);

namespace Drupal\Tests\integration_api\Kernel;

use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Session\AccountProxy;
use Drupal\integration_api\Middleware\AuthMiddleware;
use Drupal\KernelTests\KernelTestBase;
use Drupal\Tests\user\Traits\UserCreationTrait;
use Drupal\user\UserInterface;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Tests Integration API authorization.
 *
 * @group integration_api
 */
final class AuthMiddlewareTest extends KernelTestBase {

  use UserCreationTrait;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'user',
    'system',
  ];


  /**
   * Fake User.
   */
  protected UserInterface|false $fakeUser;

  /**
   * Auth Middleware Service.
   */
  private AuthMiddleware $authMiddleware;

  /**
   * {@inheritdoc}
   *
   * @throws Exception
   */
  protected function setUp(): void {
    parent::setUp();
    $this->installEntitySchema('user');
    $this->installConfig(['user']);

    $this->authMiddleware = new AuthMiddleware(
      $this->container->get('password'),
      $this->container->get('current_user')
    );

    $eventDispatcher = $this->container->get('event_dispatcher');

    $this->fakeUser = $this->createUser([], NULL, TRUE);
    assert($this->fakeUser instanceof AccountInterface);
    $accountProxy = new AccountProxy($eventDispatcher);
    $accountProxy->setAccount($this->fakeUser);
  }

  /**
   * Test user with access.
   *
   * @throws Exception
   */
  public function testRequestAuthorized(): void {
    assert($this->fakeUser instanceof AccountInterface);
    $this->setCurrentUser($this->fakeUser);
    $request = Request::create('/integration/api/some-endpoint', 'GET');

    $username = $this->fakeUser->getAccountName();
    $password = $this->fakeUser->getPassword();
    $auth = base64_encode("{$username}:{$password}");
    $request->headers->set('Authorization', 'Basic ' . $auth);

    $event = new RequestEvent($this->container->get('http_kernel'), $request, 1);

    $this->authMiddleware->onKernelRequest($event);
  }

  /**
   * Test user with access denied.
   *
   * @throws Exception
   */
  public function testRequestDeniedApi(): void {
    $request = Request::create('/integration/api/some-endpoint', 'GET');

    assert($this->fakeUser instanceof AccountInterface);
    $username = $this->fakeUser->getAccountName();
    $password = 'user-password';
    $auth = base64_encode("{$username}:{$password}");
    $request->headers->set('Authorization', 'Basic ' . $auth);

    $event = new RequestEvent($this->container->get('http_kernel'), $request, 1);

    $this->expectException(AccessDeniedHttpException::class);
    $this->expectExceptionMessage('Access denied.');
    $this->authMiddleware->onKernelRequest($event);

  }

}
