<?php

namespace Drupal\pdf_embed_seo_premium\Service;

use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\Url;
use Drupal\pdf_embed_seo\Entity\PdfDocumentInterface;
use Drupal\user\Entity\Role;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Service for managing role-based access to PDF documents.
 */
class PdfAccessManager {

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * Constructs a PdfAccessManager object.
   *
   * @param \Drupal\Core\Session\AccountProxyInterface $current_user
   *   The current user.
   */
  public function __construct(AccountProxyInterface $current_user) {
    $this->currentUser = $current_user;
  }

  /**
   * Check if a PDF document requires login.
   *
   * @param \Drupal\pdf_embed_seo\Entity\PdfDocumentInterface $pdf_document
   *   The PDF document entity.
   *
   * @return bool
   *   TRUE if login is required.
   */
  public function requiresLogin(PdfDocumentInterface $pdf_document): bool {
    if (!$pdf_document->hasField('require_login')) {
      return FALSE;
    }
    return (bool) $pdf_document->get('require_login')->value;
  }

  /**
   * Check if a PDF document has role restrictions.
   *
   * @param \Drupal\pdf_embed_seo\Entity\PdfDocumentInterface $pdf_document
   *   The PDF document entity.
   *
   * @return bool
   *   TRUE if role restriction is enabled.
   */
  public function hasRoleRestriction(PdfDocumentInterface $pdf_document): bool {
    if (!$pdf_document->hasField('role_restriction_enabled')) {
      return FALSE;
    }
    return (bool) $pdf_document->get('role_restriction_enabled')->value;
  }

  /**
   * Get allowed roles for a PDF document.
   *
   * @param \Drupal\pdf_embed_seo\Entity\PdfDocumentInterface $pdf_document
   *   The PDF document entity.
   *
   * @return array
   *   Array of allowed role IDs.
   */
  public function getAllowedRoles(PdfDocumentInterface $pdf_document): array {
    if (!$pdf_document->hasField('allowed_roles') || $pdf_document->get('allowed_roles')->isEmpty()) {
      return [];
    }

    $roles = [];
    foreach ($pdf_document->get('allowed_roles') as $item) {
      $roles[] = $item->value;
    }
    return $roles;
  }

  /**
   * Check if user has access to a PDF document.
   *
   * @param \Drupal\pdf_embed_seo\Entity\PdfDocumentInterface $pdf_document
   *   The PDF document entity.
   * @param \Drupal\Core\Session\AccountInterface|null $account
   *   The user account. Defaults to current user.
   *
   * @return bool
   *   TRUE if user has access.
   */
  public function userHasAccess(PdfDocumentInterface $pdf_document, ?AccountInterface $account = NULL): bool {
    if ($account === NULL) {
      $account = $this->currentUser;
    }

    // Check login requirement.
    if ($this->requiresLogin($pdf_document) && $account->isAnonymous()) {
      return FALSE;
    }

    // Check role restriction.
    if (!$this->hasRoleRestriction($pdf_document)) {
      return TRUE;
    }

    // Must be logged in for role-based access.
    if ($account->isAnonymous()) {
      return FALSE;
    }

    $allowed_roles = $this->getAllowedRoles($pdf_document);

    // If no roles specified, allow all logged-in users.
    if (empty($allowed_roles)) {
      return TRUE;
    }

    // Check if user has any of the allowed roles.
    $user_roles = $account->getRoles();
    foreach ($user_roles as $role) {
      if (in_array($role, $allowed_roles, TRUE)) {
        return TRUE;
      }
    }

    return FALSE;
  }

  /**
   * Get all available roles for selection.
   *
   * @return array
   *   Array of role ID => role label.
   */
  public function getAvailableRoles(): array {
    $roles = [];
    foreach (Role::loadMultiple() as $role) {
      if ($role->id() !== 'anonymous') {
        $roles[$role->id()] = $role->label();
      }
    }
    return $roles;
  }

  /**
   * Get the access denied message for a PDF document.
   *
   * @param \Drupal\pdf_embed_seo\Entity\PdfDocumentInterface $pdf_document
   *   The PDF document entity.
   *
   * @return array
   *   Render array for the access denied message.
   */
  public function getAccessDeniedMessage(PdfDocumentInterface $pdf_document): array {
    $requires_login = $this->requiresLogin($pdf_document);
    $has_restriction = $this->hasRoleRestriction($pdf_document);
    $is_anonymous = $this->currentUser->isAnonymous();

    $build = [
      '#type' => 'container',
      '#attributes' => ['class' => ['pdf-access-denied']],
    ];

    $build['title'] = [
      '#type' => 'html_tag',
      '#tag' => 'h3',
      '#value' => t('Access Restricted'),
    ];

    if ($requires_login && $is_anonymous) {
      $build['message'] = [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => t('You must be logged in to view this document.'),
      ];
      $build['login_link'] = [
        '#type' => 'link',
        '#title' => t('Log In'),
        '#url' => Url::fromRoute('user.login', [], [
          'query' => ['destination' => $pdf_document->toUrl('canonical')->toString()],
        ]),
        '#attributes' => ['class' => ['button', 'button--primary']],
      ];
    }
    elseif ($has_restriction) {
      $build['message'] = [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => t('You do not have permission to view this document.'),
      ];
    }

    return $build;
  }

  /**
   * Get redirect response for login requirement.
   *
   * @param \Drupal\pdf_embed_seo\Entity\PdfDocumentInterface $pdf_document
   *   The PDF document entity.
   *
   * @return \Symfony\Component\HttpFoundation\RedirectResponse|null
   *   Redirect response or NULL if not needed.
   */
  public function getLoginRedirect(PdfDocumentInterface $pdf_document): ?RedirectResponse {
    if ($this->requiresLogin($pdf_document) && $this->currentUser->isAnonymous()) {
      $login_url = Url::fromRoute('user.login', [], [
        'query' => ['destination' => $pdf_document->toUrl('canonical')->toString()],
      ])->toString();
      return new RedirectResponse($login_url);
    }
    return NULL;
  }

}
