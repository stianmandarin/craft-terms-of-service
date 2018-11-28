<?php
/**
 * Craft Terms of Service plugin for Craft CMS 3.x
 *
 * Force logged in users to accept the sites TOS before being able to use the site.
 *
 * @link      https://mandarindesign.no
 * @copyright Copyright (c) 2018 Mandarin Design
 */

namespace mandarindesign\crafttermsofservice\controllers;

use mandarindesign\crafttermsofservice\CraftTermsOfService;

use Craft;
use craft\web\Controller;
use mandarindesign\crafttermsofservice\records\CraftTermsOfServiceRecord;
use mandarindesign\crafttermsofservice\records\PluginsRecord;

/**
 * Default Controller
 *
 * Generally speaking, controllers are the middlemen between the front end of
 * the CP/website and your plugin’s services. They contain action methods which
 * handle individual tasks.
 *
 * A common pattern used throughout Craft involves a controller action gathering
 * post data, saving it on a model, passing the model off to a service, and then
 * responding to the request appropriately depending on the service method’s response.
 *
 * Action methods begin with the prefix “action”, followed by a description of what
 * the method does (for example, actionSaveIngredient()).
 *
 * https://craftcms.com/docs/plugins/controllers
 *
 * @author    Mandarin Design
 * @package   CraftTermsOfService
 * @since     1.0.0
 */
class DefaultController extends Controller
{

    // Protected Properties
    // =========================================================================

    /**
     * @var    bool|array Allows anonymous access to this controller's actions.
     *         The actions must be in 'kebab-case'
     * @access protected
     */
    protected $allowAnonymous = ['index', 'do-something'];

    // Public Methods
    // =========================================================================

    /**
     * Accept Terms of Service
     * e.g.: actions/craft-terms-of-service/default/save-tos
     *
     * @return mixed
     */
    public function actionSaveTos()
    {
        // Get updated TOS data
        $request = Craft::$app->request;
        $tosEnabled = $request->post('tosEnabled');
        $tosHeadline = $request->post('tosHeadline');
        $tosBody = $request->post('tosBody');
        $tosCurrentVersion = $request->post('tosCurrentVersion');

        // Compose new settings
        $settings = '{"tosEnabled":"'.$tosEnabled.'","tosHeadline":"'.$tosHeadline.'","tosBody":"'.$tosBody.'","tosCurrentVersion":"'.$tosCurrentVersion.'"}';
        $settings = str_replace(["\r", "\n"], '', $settings);

        // Update settings in db
        Craft::$app->db->createCommand("UPDATE plugins SET settings=:settings WHERE handle=:handle")
            ->bindValue(':settings', $settings)
            ->bindValue(':handle', 'craft-terms-of-service')
            ->execute();

        // Redirect to referrer with optional URL param
        $referrer = preg_replace('/\?.*/', '', Craft::$app->request->getReferrer());
        return $this->redirect($referrer.'?tos=saved');
    }

    /**
     * Accept Terms of Service
     * e.g.: actions/craft-terms-of-service/default/accept-tos
     *
     * @return mixed
     */
    public function actionAcceptTos()
    {
        // Cache
        $userId = Craft::$app->user->id;

        // Delete any previous TOS acceptance from the logged in user
        $existingAcceptance = CraftTermsOfServiceRecord::find()->where("userId = $userId")->one();
        if ($existingAcceptance) {
            $existingAcceptance->delete();
        }

        // Mark the user as having accepted the latest terms
        $tos = new CraftTermsOfServiceRecord();
        $tos->userId = $userId;
        $tos->userAcceptedVersion = CraftTermsOfService::$plugin->getSettings()->tosCurrentVersion;;
        $tos->save();

        // Redirect to referrer with optional URL param
        $referrer = preg_replace('/\?.*/', '', Craft::$app->request->getReferrer());
        return $this->redirect($referrer.'?tos=accepted');
    }
}
