<?php
namespace Drupal\simple_voting\Form\Registration;

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
final class RegisterVoteForm extends FormBase {

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
    return 'simple_voting_register_vote';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ?SimpleVoting $entity = NULL): array {
    if ($form_state->isSubmitted()) {
      return $this->buildResultsForm($form_state);
    }
    if (!$entity instanceof SimpleVoting) {
      throw new NotFoundHttpException();
    }
    return $this->buildVoteForm($entity);
  }

  /**
   *  Builds the result form.
   *
   * @param FormStateInterface $form_state
   *   The form state interface.
   *
   * @return array
   *    The form array.
   */
  public function buildResultsForm(FormStateInterface $form_state): array {
    /** @var SimpleVoting $entity */
    $entity = $form_state->get('simple_vote');

    $form['title'] = [
      '#prefix' => '<h3>',
      '#markup' => $entity->getLabel(),
      '#suffix' => '</h3>',
    ];

    $form['question'] = [
      '#prefix' => '<h3>',
      '#markup' => $this->t('Thanks for voting.'),
      '#suffix' => '</h3>',
    ];

    if ($entity->showResults()) {
      $votes = $entity->getVotes();
      $results = [];
      foreach ($votes as $vote) {
        $results[$vote->option] += 1;
        $results['total'] += 1;
      }

      foreach ($entity->getAnswers() as $answer) {
        $option = $answer->getValue();
        $id = $answer->getName();
        $form["result_$id"] = [
          '#prefix' => '<p>',
          '#markup' => $this->t('The option: @title got @percentage of votes.',
          [
            '@title' => $option['name'],
            '@percentage' => isset($results[$id]) ?
              $results[$id]/$results['total']*100 . '%':
              '0%',
          ]),
          '#suffix' => '</p>',
        ];
      }
    }

    return $form;
  }

  /**
   * Builds the vote form.
   *
   * @return array
   *   The form array.
   */
  public function buildVoteForm(SimpleVoting $entity): array {
    $form['#voting_id'] = [
      '#type' => 'hidden',
      '#value' => $entity->id(),
    ];

    $form['title'] = [
      '#prefix' => '<h3 class="title-form">',
      '#markup' => $entity->getLabel(),
      '#suffix' => '</h3>',
    ];

    $form['question'] = [
      '#prefix' => '<h3 class="title-form">',
      '#markup' => $entity->getQuestion(),
      '#suffix' => '</h3>',
    ];

    $form['answer'] = [
      '#prefix' => '<div>',
      '#suffix' => '</div>',
    ];

    foreach ($entity->getAnswers() as $answer) {
      $option = $answer->getValue();
      $id = $answer->getName();
      $form['answer']["answer_$id"] = [
        '#theme' => 'answer_option',
        '#item_id' => $id,
        '#title' => $option['name'],
        '#description' => $option['description'],
        '#image' => $option['image'] ? File::load($option['image'])->createFileUrl() : NULL,
      ];
    }

    $form['actions'] = [
      '#type' => 'actions',
      'submit' => [
        '#type' => 'submit',
        '#value' => $this->t('Vote!'),
      ],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $inputValues = $form_state->getUserInput();
    $selectedOption = $inputValues['answer'];
    $votingId = $form['#voting_id']['#value'];

    $simpleVoting = SimpleVoting::load($votingId);

    $votes = $simpleVoting->get('votes');
    $votes->appendItem([
      'option' => $selectedOption,
      'user_id' => $this->currentUser()->id(),
    ]);
    $simpleVoting->save();

    $form_state->set('simple_vote', $simpleVoting);
    $form_state->setSubmitted();
    $form_state->setRebuild();

  }
}
