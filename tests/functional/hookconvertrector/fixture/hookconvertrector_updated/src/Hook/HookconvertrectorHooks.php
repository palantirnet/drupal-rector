<?php

namespace Drupal\hookconvertrector\Hook;

use Drupal\Core\Hook\Attribute\LegacyHook;
use Drupal\Core\Hook\Attribute\Hook;
/**
 * Hook implementations for hookconvertrector.
 */
class HookconvertrectorHooks
{
    /**
     * Implements hook_user_cancel().
     */
    #[Hook('user_cancel')]
    public function userCancel($edit, \UserInterface $account, $method)
    {
        $red = 'red';
        $method = [
            'red',
            'green',
            'blue',
        ];
        $edit = [
            'red' => 'red',
            'green' => 'green',
            'blue' => 'blue',
        ];
    }

    /**
     * Implements hook_page_attachments().
     */
    #[Hook('page_attachments')]
    public function pageAttachments(array &$page)
    {
        // Routes that don't use BigPipe also don't need no-JS detection.
        if (\Drupal::routeMatch()->getRouteObject()->getOption('_no_big_pipe')) {
            return;
        }
        $request = \Drupal::request();
        // BigPipe is only used when there is an actual session, so only add the no-JS
        // detection when there actually is a session.
        // @see \Drupal\big_pipe\Render\Placeholder\BigPipeStrategy.
        $session_exists = \Drupal::service('session_configuration')->hasSession($request);
        $page['#cache']['contexts'][] = 'session.exists';
        // Only do the no-JS detection while we don't know if there's no JS support:
        // avoid endless redirect loops.
        $has_big_pipe_nojs_cookie = $request->cookies->has(\BigPipeStrategy::NOJS_COOKIE);
        $page['#cache']['contexts'][] = 'cookies:' . \BigPipeStrategy::NOJS_COOKIE;
        if ($session_exists) {
            if (!$has_big_pipe_nojs_cookie) {
                // Let server set the BigPipe no-JS cookie.
                $page['#attached']['html_head'][] = [
                    [
                        // Redirect through a 'Refresh' meta tag if JavaScript is disabled.
                        '#tag' => 'meta',
                        '#noscript' => TRUE,
                        '#attributes' => [
                            'http-equiv' => 'Refresh',
                            'content' => '0; URL=' . \Url::fromRoute('big_pipe.nojs', [
                            ], [
                                'query' => \Drupal::service('redirect.destination')->getAsArray(),
                            ])->toString(),
                        ],
                    ],
                    'big_pipe_detect_nojs',
                ];
            } else {
                // Let client delete the BigPipe no-JS cookie.
                $page['#attached']['html_head'][] = [
                    [
                        '#tag' => 'script',
                        '#value' => 'document.cookie = "' . \BigPipeStrategy::NOJS_COOKIE . '=1; path=/; expires=Thu, 01 Jan 1970 00:00:00 GMT"',
                    ],
                    'big_pipe_detect_js',
                ];
            }
        }
    }
}
