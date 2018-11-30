# Terms of Service plugin for Craft CMS 3.x

Use this plugin to force Craft users to consent to your TOS before being allowed access to all or certain portions of your site.

![Screenshot](resources/img/cp.png)

## Requirements

* This plugin requires Craft CMS 3.0.0-beta.23 or later.

## Installation

To install the plugin, follow these instructions.

1. Open your terminal and go to your Craft project:

        cd /path/to/project

2. Then tell Composer to load the plugin:

        composer require stianmandarin/craft-terms-of-service

3. In the Control Panel, go to Settings → Plugins and click the “Install” button for Terms of Service.

## Configuring Terms of Service

* Once installed, go to Terms of Service in the Craft CP (you can change this name in the plugin settings).
  * Every new user must consent to the TOS if enabled.
  * All users, regardless of previous consent, must consent again if you save a new version of your TOS (the TOS version updates automatically every time you save). 
* Use the code below as a jumping off point.
  * Note that `{{ craft.craftTermsOfService.get('tosConsent') }}` will only return true if the user accepted TOS version does not match the latest TOS version, even if the setting is set to enabled in the CP.

```
{% if craft.app.request.getParam('tos') == 'accepted' %}
    <p>Thank you for accepting our terms of service. You may now access our site.</p>
{% endif %}

{% if craft.craftTermsOfService.get('tosConsent') %}
    <h1>{{ craft.craftTermsOfService.get('tosHeadline') }}</h1>
    {{ craft.craftTermsOfService.get('tosBody')|raw }}
    <a href="/actions/craft-terms-of-service/default/accept-tos">{{ 'I accept'|t }}</a>
{% else %}
    {% block content %}
    {% endblock %}
{% endif %}
```

## Using Terms of Service

* `{{ craft.craftTermsOfService.get('tosConsent') }}` returns true if the TOS is enabled, but only if the (latest) TOS hasn't been accepted
* `{{ craft.craftTermsOfService.get('tosHeadline') }}` returns the TOS headline.
* `{{ craft.craftTermsOfService.get('tosBody')|raw }}` returns the TOS body.
* You can change the display name of the plugin in the CP by going to its settings page.

## Roadmap

* Add norwegian translations
* ~~Add cookies as option for consent validation~~
* ~~Simplify process for checking if visitor has consented to TOS~~
* ~~Fix bug where plugin would fail if no user was logged in~~

Brought to you by [Mandarin Design](https://mandarindesign.no)
