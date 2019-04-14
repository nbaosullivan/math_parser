<?php

namespace Drupal\math_parser\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'math_parser' formatter.
 *
 * @FieldFormatter(
 *   id = "math_parser",
 *   label = @Translation("Math Parser"),
 *   field_types = {
 *     "math_parser",
 *     "string"
 *   }
 * )
 */
class MathParserFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];
    $summary[] = $this->t('Parses mathemtical strings and displays the result.');
    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
        'widget_type' => 'react',
      ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $element['widget_type'] = [
      '#title' => $this->t('Widget Type'),
      '#type' => 'select',
      '#options' => [
        'react' => $this->t('React'),
        'twig' => $this->t('Twig'),
        'answer' => $this->t('Answer only'),
      ],
      '#default_value' => $this->getSetting('widget_type'),
    ];

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $element = [];

    foreach ($items as $delta => $item) {
      $widget_type = $this->getSetting('widget_type');
      // Only include react javascript if we need it
      $libraries = ($widget_type == 'react') ? ['math_parser/math_parser'] : [];

      // Use service provided this module to calculate string provided
      $math_parser = \Drupal::Service('math_parser.math_parser');
      $parsed_value = $math_parser->calculate($item->value);

      if ($widget_type == 'answer') {

        $element[$delta] = [
          '#markup' => $parsed_value,
        ];

      }
      else {

        $element[$delta] = [
          '#theme' => 'formula_formatter_' . $widget_type,
          '#formula' => $item->value,
          '#result' => $parsed_value,
          '#delta' => $delta,
          // Order of multiple value field is used in react app
          '#attached' => [
            'library' => $libraries,
          ],
        ];
      }

    }

    return $element;
  }

}