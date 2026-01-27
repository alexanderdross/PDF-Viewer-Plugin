<?php

namespace Drupal\pdf_embed_seo\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface defining a PDF Document entity.
 */
interface PdfDocumentInterface extends ContentEntityInterface, EntityChangedInterface, EntityOwnerInterface {

  /**
   * Gets the PDF document title.
   *
   * @return string
   *   The PDF document title.
   */
  public function getTitle();

  /**
   * Sets the PDF document title.
   *
   * @param string $title
   *   The PDF document title.
   *
   * @return $this
   */
  public function setTitle($title);

  /**
   * Gets the PDF document description.
   *
   * @return string|null
   *   The PDF document description.
   */
  public function getDescription();

  /**
   * Gets the PDF file entity.
   *
   * @return \Drupal\file\FileInterface|null
   *   The PDF file entity, or NULL if not set.
   */
  public function getPdfFile();

  /**
   * Gets the thumbnail image entity.
   *
   * @return \Drupal\file\FileInterface|null
   *   The thumbnail image entity, or NULL if not set.
   */
  public function getThumbnail();

  /**
   * Checks if downloads are allowed.
   *
   * @return bool
   *   TRUE if downloads are allowed, FALSE otherwise.
   */
  public function allowsDownload();

  /**
   * Checks if printing is allowed.
   *
   * @return bool
   *   TRUE if printing is allowed, FALSE otherwise.
   */
  public function allowsPrint();

  /**
   * Gets the view count.
   *
   * @return int
   *   The number of times the PDF has been viewed.
   */
  public function getViewCount();

  /**
   * Increments the view count.
   *
   * @return $this
   */
  public function incrementViewCount();

  /**
   * Checks if the PDF document is published.
   *
   * @return bool
   *   TRUE if the PDF document is published, FALSE otherwise.
   */
  public function isPublished();

  /**
   * Sets the published status.
   *
   * @param bool $published
   *   TRUE for published, FALSE for unpublished.
   *
   * @return $this
   */
  public function setPublished($published = TRUE);

  /**
   * Gets the creation timestamp.
   *
   * @return int
   *   The creation timestamp.
   */
  public function getCreatedTime();

  /**
   * Sets the creation timestamp.
   *
   * @param int $timestamp
   *   The creation timestamp.
   *
   * @return $this
   */
  public function setCreatedTime($timestamp);

  /**
   * Gets the password.
   *
   * @return string|null
   *   The password, or NULL if not set.
   */
  public function getPassword();

  /**
   * Checks if the PDF document has a password.
   *
   * @return bool
   *   TRUE if a password is set, FALSE otherwise.
   */
  public function hasPassword();

}
