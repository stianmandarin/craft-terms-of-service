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
     * {{ craft.craftTermsOfService.get('tosEnabled') }}          // true or false
     * {{ craft.craftTermsOfService.get('tosHeadline') }}         // Plain text
     * {{ craft.craftTermsOfService.get('tosBody')|raw }}         // Redactor body
     * {{ craft.craftTermsOfService.get('userAcceptedVersion') }} // Unix timestamp of when the user last accepted TOS
     * {{ craft.craftTermsOfService.get('tosCurrentVersion') }}   // Unix timestamp of when the TOS was last updated
     * {{ craft.craftTermsOfService.get('tosPluginName') }} // Returns name of the plugin as set in settings
     *
     */
    public function get($value)
    {
        // Cache
        $userId = Craft::$app->user->id;

        // Logic
        if ($value === 'userAcceptedVersion') {
            // Get the latest version of the TOS
            $userAcceptedVersion = CraftTermsOfServiceRecord::find()->where("userId = $userId")->one();

            // Return whichever TOS version the user has accepted (if any)
            if ($userAcceptedVersion) {
                return $userAcceptedVersion->userAcceptedVersion;
            }
        } else {
            // Get `tosEnabled`, `tosHeadline` or `tosBody`, `tosCurrentVersion`, `tosPluginName`
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
