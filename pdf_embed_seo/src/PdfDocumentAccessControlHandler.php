<?php

namespace Drupal\pdf_embed_seo;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Access controller for the PDF Document entity.
 */
class PdfDocumentAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\pdf_embed_seo\Entity\PdfDocumentInterface $entity */

    switch ($operation) {
      case 'view':
        // Published documents can be viewed by anyone with permission.
        if ($entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view pdf document');
        }
        // Unpublished documents can only be viewed by the owner or admins.
        return AccessResult::allowedIf(
          $account->hasPermission('administer pdf embed seo') ||
          ($account->id() === $entity->getOwnerId())
        )->cachePerUser()->addCacheableDependency($entity);

      case 'update':
        // Check for admin permission or edit permission.
        if ($account->hasPermission('administer pdf embed seo')) {
          return AccessResult::allowed()->cachePerPermissions();
        }
        if ($account->hasPermission('edit pdf document')) {
          return AccessResult::allowed()->cachePerPermissions();
        }
        // Check for edit own permission.
        if ($account->hasPermission('edit own pdf document') && $account->id() === $entity->getOwnerId()) {
          return AccessResult::allowed()->cachePerUser()->addCacheableDependency($entity);
        }
        return AccessResult::neutral();

      case 'delete':
        // Check for admin permission or delete permission.
        if ($account->hasPermission('administer pdf embed seo')) {
          return AccessResult::allowed()->cachePerPermissions();
        }
        if ($account->hasPermission('delete pdf document')) {
          return AccessResult::allowed()->cachePerPermissions();
        }
        // Check for delete own permission.
        if ($account->hasPermission('delete own pdf document') && $account->id() === $entity->getOwnerId()) {
          return AccessResult::allowed()->cachePerUser()->addCacheableDependency($entity);
        }
        return AccessResult::neutral();
    }

    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermissions($account, [
      'administer pdf embed seo',
      'create pdf document',
    ], 'OR');
  }

}
