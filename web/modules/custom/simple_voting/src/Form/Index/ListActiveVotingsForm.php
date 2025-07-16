<?php
namespace Drupal\simple_voting\Form\Index;

use Drupal\Core\DependencyInjection\AutowireTrait;
use Drupal\Core\DependencyInjection\DependencySerializationTrait;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\file\Entity\File;
use Drupal\simple_voting\Entity\SimpleVoting;
use Drupal\simple_voting\Service\SimpleVotingService;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Provides a form to vote on an active poll.
 */
final class ListActiveVotingsForm extends FormBase {

  use DependencySerializationTrait;
  use AutowireTrait;

  public function __construct(
    #[Autowire('simple_voting.service')]
    protected SimpleVotingService $simpleVotingService,
  ){}

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'simple_voting_list';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ?SimpleVoting $entity = NULL): array {
    $form['title'] = [
      '#prefix' => '<h1>',
      '#markup' =>  $this->t('Thanks for visiting, choose one and vote!'),
      '#suffix' => '</h1>',
    ];

    foreach (SimpleVoting::loadMultiple() as $voting) {
      if (!$voting->getStatus()) {
        continue;
      }

      $id = $voting->id();

      $form["wrapper_$id"] = [
        '#prefix' => '<h3>',
        '#suffix' => '</h3>',
      ];
      $form["wrapper_$id"]["voting_$id"] = $voting->toAliasLink();
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {}
}
