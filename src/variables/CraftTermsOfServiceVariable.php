<?php
/**
 * Craft Terms of Service plugin for Craft CMS 3.x
 *
 * Force logged in users to accept the sites TOS before being able to use the site.
 *
 * @link      https://mandarindesign.no
 * @copyright Copyright (c) 2018 Mandarin Design
 */

namespace mandarindesign\crafttermsofservice\variables;

use mandarindesign\craftsms\CraftSms;
use mandarindesign\crafttermsofservice\CraftTermsOfService;

use Craft;
use craft\redactor\Field;
use mandarindesign\crafttermsofservice\records\CraftTermsOfServiceRecord;

/**
 * Craft Terms of Service Variable
 *
 * Craft allows plugins to provide their own template variables, accessible from
 * the {{ craft }} global variable (e.g. {{ craft.craftTermsOfService }}).
 *
 * https://craftcms.com/docs/plugins/variables
 *
 * @author    Mandarin Design
 * @package   CraftTermsOfService
 * @since     1.0.0
 */
class CraftTermsOfServiceVariable
{
    // Public Methods
    // =========================================================================

    /**
     * Query db for value of plugin settings
     * See README.md
     *
     */
    public function get($value)
    {
        // Logic
        if ($value === 'tosConsent') {
            if (CraftTermsOfService::$plugin->getSettings()->tosEnabled) {
                if (Craft::$app->user->getIsGuest()) {
                    // Get the latest version the user has accepted from cookie
                    if (!isset($_COOKIE['tosConsent'])) {
                        $userAcceptedVersion = 0;
                    } else {
                        $userAcceptedVersion = (int)$_COOKIE['tosConsent'];
                    }
                } else {
                    // Get the latest version the user has accepted from the db
                    $userId = Craft::$app->user->id;
                    $userAcceptedVersion = CraftTermsOfServiceRecord::find()->where("userId = $userId")->one();
                    $userAcceptedVersion = $userAcceptedVersion->userAcceptedVersion;
                }

                // If the user accepted TOS version does not match the latest TOS version, return true to show the TOS
                if ($userAcceptedVersion != CraftTermsOfService::$plugin->getSettings()->tosCurrentVersion) {
                    return true;
                }
            } else {
                // Skip the whole validation thing if the TOS plugin isn't enabled
                return false;
            }
        } else {
            // Get `tosHeadline`, `tosBody` or `tosPluginName`
            return CraftTermsOfService::$plugin->getSettings()->$value;
        }
    }

    /**
     * Enable redactor field for use in plugins CP Section
     */
    public function redactorField($options = null)
    {
        $field = new Field;
        $field->handle = $options['name'];
        // $field->redactorConfig = 'Simple.json';
        $redactorField = $field->getInputHtml($options['value']);

        return '<div class="field">
            <div class="heading">
                <label>'.$options['label'].'</label>
                <div class="instructions">
                    <p>'.$options['instructions'].'</p>
                </div>
            </div>
            <div class="input ltr">
                '.$redactorField.'
            </div>
        </div>';
    }
}
