<?php
/**
 * Craft Terms of Service plugin for Craft CMS 3.x
 *
 * Force visitors to accept TOS before being allowed access to parts, or all of your site.
 *
 * @link      https://mandarindesign.no
 * @copyright Copyright (c) 2018 Mandarin Design
 */

namespace mandarindesign\crafttermsofservice\models;

use mandarindesign\crafttermsofservice\CraftTermsOfService;

use Craft;
use craft\base\Model;

/**
 * CraftTermsOfService Settings Model
 *
 * This is a model used to define the plugin's settings.
 *
 * Models are containers for data. Just about every time information is passed
 * between services, controllers, and templates in Craft, it’s passed via a model.
 *
 * https://craftcms.com/docs/plugins/models
 *
 * @author    Mandarin Design
 * @package   CraftTermsOfService
 * @since     1.0.0
 */
class Settings extends Model
{
    // Public Properties
    // =========================================================================

    /**
     * Some field model attribute
     *
     * @var string
     */

    public $tosPluginName = 'Terms of Service';
    public $tosCurrentVersion;
    public $tosEnabled = 0;
    public $tosHeadline;
    public $tosBody;

    // Public Methods
    // =========================================================================

    /**
     * Returns the validation rules for attributes.
     *
     * Validation rules are used by [[validate()]] to check if attribute values are valid.
     * Child classes may override this method to declare different validation rules.
     *
     * More info: http://www.yiiframework.com/doc-2.0/guide-input-validation.html
     *
     * @return array
     */
    public function rules()
    {
        return [
            // [['tosHeadline', 'tosBody'], 'string']
        ];
    }
}
