<?php

namespace Drupal\simple_voting\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\simple_voting\Plugin\Field\FieldType\Answer;
use Drupal\simple_voting\Plugin\Field\FieldType\Vote;

/**
 * Defines the voting entity class.
 *
 * @ContentEntityType(
 *   id = "simple_voting",
 *   label = @Translation("Simple Voting"),
 *   label_collection = @Translation("Simple Voting"),
 *   label_singular = @Translation("simple voting"),
 *   label_plural = @Translation("simple voting"),
 *   label_count = @PluralTranslation(
 *     singular = "@count simple voting",
 *     plural = "@count simple voting",
 *   ),
 *   handlers = {
 *     "list_builder" = "Drupal\simple_voting\SimpleVotingListBuilder",
 *     "views_data" = "Drupal\views\EntityViewsData",
 *     "form" = {
 *       "add" = "Drupal\simple_voting\Form\SimpleVotingForm",
 *       "edit" = "Drupal\simple_voting\Form\SimpleVotingForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm",
 *       "delete-multiple-confirm" = "Drupal\Core\Entity\Form\DeleteMultipleForm",
 *    },
 *    "route_provider" = {
 *      "html" = "Drupal\Core\Entity\Routing\AdminHtmlRouteProvider",
 *    },
 *   },
 *   base_table = "simple_voting",
 *   admin_permission = "administer simple_voting",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "id",
 *     "uuid" = "uuid",
 *   },
 *   links = {
 *     "collection" = "/admin/content/voting",
 *     "add-form" = "/admin/content/voting/add",
 *     "canonical" = "/admin/content/voting/{simple_voting}",
 *     "edit-form" = "/admin/content/voting/{simple_voting}/edit",
 *     "delete-form" = "/admin/content/voting/{simple_voting}/delete",
 *     "delete-multiple-form" = "/admin/content/voting/delete",
 *   },
 * )
 */
final class SimpleVoting extends ContentEntityBase {

  const ENTITY_TYPE_ID = 'simple_voting';
  private const ALIAS_PREFIX = '/simple_voting/';

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type): array {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['label'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Title'))
      ->setDescription(t('Title of the voting.'))
      ->setRequired(TRUE)
      ->setSetting('max_length', 255)
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE);

    $fields['status'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Status'))
      ->setDescription(t('Can enable or disable voting.'))
      ->setDefaultValue(TRUE)
      ->setSetting('on_label', 'Active')
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'weight' => 1,
      ])
      ->setDisplayOptions('view', [
        'type' => 'boolean',
        'label' => 'above',
        'weight' => 1,
        'settings' => [
          'format' => 'enabled-disabled',
        ],
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['question'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Question'))
      ->setDescription(t('The question that users will be answering.'))
      ->setRequired(TRUE)
      ->setSetting('max_length', 255)
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 2,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'type' => 'string',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('view', TRUE);


    $fields['show_results'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Show results'))
      ->setDescription(t('Show or hide partial results for users.'))
      ->setDefaultValue(TRUE)
      ->setSetting('on_label', 'Show')
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'weight' => 3,
      ])
      ->setDisplayConfigurable('form', TRUE);

    $fields['answers'] = BaseFieldDefinition::create('answer_option')
      ->setLabel(t('Voting Options'))
      ->setDescription(t('Add options that users may choose to vote.'))
      ->setCardinality(FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED)
      ->setRequired(TRUE)
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'answer_option_formatter',
      ])
      ->setDisplayOptions('form', [
        'type' => 'answer_option_widget',
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['votes'] = BaseFieldDefinition::create('vote')
      ->setLabel(t('Votes Counter'))
      ->setDescription(t('Stores who voted on what.'))
      ->setCardinality(FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED)
      ->setRequired(TRUE);

    return $fields;
  }

  /**
   * Gets the label of the voting.
   */
  public function getLabel(): string {
    /** @var string $label */
    $label = $this->label->value;
    return $label;
  }

  /**
   * Gets the label of the voting.
   */
  public function getQuestion(): string {
    /** @var string $question */
    $question = $this->question->value;
    return $question;
  }

  /**
   * Gets the answers options.
   */
  public function getAnswers(): array {
    /** @var array<Answer> $answers */
    $answers = iterator_to_array($this->get('answers'));
    return $answers;
  }

  /**
   * Gets the answers options.
   */
  public function getVotes(): array {
    /** @var array<Vote> $votes */
    $votes = iterator_to_array($this->get('votes'));
    return $votes;
  }

  /**
   * Get status.
   *
   * @return bool
   *   Status.
   */
  public function getStatus(): bool {
    return boolval($this->get('status')->value);
  }

  /**
   * Get show_results.
   *
   * @return bool
   *   show_results.
   */
  public function showResults(): bool {
    return boolval($this->get('show_results')->value);
  }

  /**
   * Get alias link.
   *
   * @retrun Array
   *  Renderable array link.
   */
  public function toAliasLink(): array {
    $uri = sprintf("internal:%s%s", self::ALIAS_PREFIX, $this->id());
    return [
      '#type' => 'link',
      '#title' => t('@buttonText', ['@buttonText' => $this->getLabel()]),
      '#url' => Url::fromUri($uri)
    ];
  }

}
