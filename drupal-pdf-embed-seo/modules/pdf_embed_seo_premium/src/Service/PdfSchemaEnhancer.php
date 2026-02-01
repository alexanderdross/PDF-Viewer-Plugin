<?php

namespace Drupal\pdf_embed_seo_premium\Service;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Url;
use Drupal\pdf_embed_seo\Entity\PdfDocumentInterface;

/**
 * Service for enhancing PDF schema with GEO/AEO/LLM optimization.
 */
class PdfSchemaEnhancer {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs a PdfSchemaEnhancer object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * Enhance the DigitalDocument schema with premium data.
   *
   * @param array $schema
   *   The existing schema array.
   * @param \Drupal\pdf_embed_seo\Entity\PdfDocumentInterface $pdf_document
   *   The PDF document entity.
   *
   * @return array
   *   The enhanced schema array.
   */
  public function enhanceSchema(array $schema, PdfDocumentInterface $pdf_document): array {
    // AI Summary.
    if ($pdf_document->hasField('ai_summary') && !$pdf_document->get('ai_summary')->isEmpty()) {
      $schema['abstract'] = strip_tags($pdf_document->get('ai_summary')->value);
    }

    // Key Points as mainEntity ItemList.
    if ($pdf_document->hasField('key_points') && !$pdf_document->get('key_points')->isEmpty()) {
      $points = array_filter(array_map('trim', explode("\n", $pdf_document->get('key_points')->value)));
      if (!empty($points)) {
        $schema['mainEntity'] = [
          '@type' => 'ItemList',
          'name' => t('Key Points'),
          'itemListElement' => [],
        ];
        foreach ($points as $index => $point) {
          $schema['mainEntity']['itemListElement'][] = [
            '@type' => 'ListItem',
            'position' => $index + 1,
            'name' => $point,
          ];
        }
      }
    }

    // Reading time.
    if ($pdf_document->hasField('reading_time') && !$pdf_document->get('reading_time')->isEmpty()) {
      $reading_time = (int) $pdf_document->get('reading_time')->value;
      if ($reading_time > 0) {
        $schema['timeRequired'] = 'PT' . $reading_time . 'M';
      }
    }

    // Difficulty level.
    if ($pdf_document->hasField('difficulty_level') && !$pdf_document->get('difficulty_level')->isEmpty()) {
      $difficulty = $pdf_document->get('difficulty_level')->value;
      $level_map = [
        'beginner' => 'Beginner',
        'intermediate' => 'Intermediate',
        'advanced' => 'Advanced',
        'expert' => 'Expert',
      ];
      if (isset($level_map[$difficulty])) {
        $schema['proficiencyLevel'] = $level_map[$difficulty];
        $schema['educationalLevel'] = $level_map[$difficulty];
        $schema['typicalAgeRange'] = $this->getAgeRangeForLevel($difficulty);
        $schema['interactivityType'] = 'expositive';
        $schema['educationalUse'] = ['self study', 'reference'];
      }
    }

    // Document type.
    if ($pdf_document->hasField('document_type') && !$pdf_document->get('document_type')->isEmpty()) {
      $doc_type = $pdf_document->get('document_type')->value;
      $type_map = [
        'guide' => 'https://schema.org/Guide',
        'whitepaper' => 'https://schema.org/ScholarlyArticle',
        'report' => 'https://schema.org/Report',
        'ebook' => 'https://schema.org/Book',
        'manual' => 'https://schema.org/TechArticle',
        'brochure' => 'https://schema.org/AdvertiserContentArticle',
        'case-study' => 'https://schema.org/ScholarlyArticle',
        'datasheet' => 'https://schema.org/TechArticle',
        'presentation' => 'https://schema.org/PresentationDigitalDocument',
        'research' => 'https://schema.org/ScholarlyArticle',
        'form' => 'https://schema.org/DigitalDocument',
      ];
      if (isset($type_map[$doc_type])) {
        $schema['additionalType'] = $type_map[$doc_type];
      }
      $schema['learningResourceType'] = ucfirst(str_replace('-', ' ', $doc_type));
    }

    // Target audience.
    if ($pdf_document->hasField('target_audience') && !$pdf_document->get('target_audience')->isEmpty()) {
      $schema['audience'] = [
        '@type' => 'Audience',
        'audienceType' => $pdf_document->get('target_audience')->value,
      ];
    }

    // Table of Contents.
    if ($pdf_document->hasField('toc_items') && !$pdf_document->get('toc_items')->isEmpty()) {
      $toc_data = $pdf_document->get('toc_items')->value;
      $toc_items = json_decode($toc_data, TRUE);
      if (!empty($toc_items) && is_array($toc_items)) {
        $parts = [];
        foreach ($toc_items as $index => $toc) {
          if (!empty($toc['title'])) {
            $parts[] = [
              '@type' => 'WebPageElement',
              'name' => $toc['title'],
              'position' => $index + 1,
              'url' => $pdf_document->toUrl('canonical')->setAbsolute()->toString() . '#page=' . ($toc['page'] ?? 1),
            ];
          }
        }
        if (!empty($parts)) {
          $schema['hasPart'] = $parts;
        }
      }
    }

    // Prerequisites.
    if ($pdf_document->hasField('prerequisites') && !$pdf_document->get('prerequisites')->isEmpty()) {
      $prereq_list = array_filter(array_map('trim', explode("\n", $pdf_document->get('prerequisites')->value)));
      if (!empty($prereq_list)) {
        $schema['coursePrerequisites'] = $prereq_list;
      }
    }

    // Learning outcomes.
    if ($pdf_document->hasField('learning_outcomes') && !$pdf_document->get('learning_outcomes')->isEmpty()) {
      $outcome_list = array_filter(array_map('trim', explode("\n", $pdf_document->get('learning_outcomes')->value)));
      if (!empty($outcome_list)) {
        $schema['teaches'] = $outcome_list;
      }
    }

    // Related documents.
    if ($pdf_document->hasField('related_documents') && !$pdf_document->get('related_documents')->isEmpty()) {
      $related_items = [];
      foreach ($pdf_document->get('related_documents') as $item) {
        $related_doc = $item->entity;
        if ($related_doc && $related_doc->isPublished()) {
          $related_items[] = [
            '@type' => 'DigitalDocument',
            'name' => $related_doc->getTitle(),
            'url' => $related_doc->toUrl('canonical')->setAbsolute()->toString(),
          ];
        }
      }
      if (!empty($related_items)) {
        $schema['isRelatedTo'] = $related_items;
      }
    }

    return $schema;
  }

  /**
   * Generate FAQ schema for a PDF document.
   *
   * @param \Drupal\pdf_embed_seo\Entity\PdfDocumentInterface $pdf_document
   *   The PDF document entity.
   *
   * @return array|null
   *   The FAQ schema array or NULL if no FAQ items.
   */
  public function generateFaqSchema(PdfDocumentInterface $pdf_document): ?array {
    if (!$pdf_document->hasField('faq_items') || $pdf_document->get('faq_items')->isEmpty()) {
      return NULL;
    }

    $faq_data = $pdf_document->get('faq_items')->value;
    $faq_items = json_decode($faq_data, TRUE);

    if (empty($faq_items) || !is_array($faq_items)) {
      return NULL;
    }

    $faq_schema = [
      '@context' => 'https://schema.org',
      '@type' => 'FAQPage',
      '@id' => $pdf_document->toUrl('canonical')->setAbsolute()->toString() . '#faq',
      'mainEntity' => [],
    ];

    foreach ($faq_items as $faq) {
      if (!empty($faq['question']) && !empty($faq['answer'])) {
        $faq_schema['mainEntity'][] = [
          '@type' => 'Question',
          'name' => $faq['question'],
          'acceptedAnswer' => [
            '@type' => 'Answer',
            'text' => $faq['answer'],
          ],
        ];
      }
    }

    return !empty($faq_schema['mainEntity']) ? $faq_schema : NULL;
  }

  /**
   * Enhance WebPage schema with custom speakable content.
   *
   * @param array $schema
   *   The existing WebPage schema.
   * @param \Drupal\pdf_embed_seo\Entity\PdfDocumentInterface $pdf_document
   *   The PDF document entity.
   *
   * @return array
   *   The enhanced schema.
   */
  public function enhanceWebPageSchema(array $schema, PdfDocumentInterface $pdf_document): array {
    if ($pdf_document->hasField('custom_speakable') && !$pdf_document->get('custom_speakable')->isEmpty()) {
      $schema['speakable'] = [
        '@type' => 'SpeakableSpecification',
        'cssSelector' => [
          '.pdf-document-title',
          '.pdf-document-description',
          '.page-title',
        ],
        'xpath' => [
          "/html/head/meta[@name='description']/@content",
        ],
      ];
    }

    return $schema;
  }

  /**
   * Get age range for difficulty level.
   *
   * @param string $level
   *   The difficulty level.
   *
   * @return string
   *   The age range string.
   */
  protected function getAgeRangeForLevel(string $level): string {
    $ranges = [
      'beginner' => '12-',
      'intermediate' => '16-',
      'advanced' => '18-',
      'expert' => '21-',
    ];
    return $ranges[$level] ?? '18-';
  }

}
