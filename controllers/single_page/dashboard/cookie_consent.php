<?php
namespace Concrete\Package\MsvCookieConsent\Controller\SinglePage\Dashboard;

use Concrete\Core\Asset\Asset;
use Concrete\Core\Asset\AssetList;
use Concrete\Core\Editor\LinkAbstractor;
use Concrete\Core\Package\PackageService;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Routing\Redirect;

class CookieConsent extends DashboardPageController
{
    public function view()
    {
        $packageConfig = $this->app->make(PackageService::class)->getByHandle('msv_cookie_consent')->getFileConfig();

        $this->set('color_theme', $packageConfig->get('settings.theme'));
        $this->set('force_consent', $packageConfig->get('settings.force_consent'));
        $this->set('mode', $packageConfig->get('settings.mode'));
        $this->set('modal_layout', $packageConfig->get('settings.modal_layout'));
        $this->set('modal_position', $packageConfig->get('settings.modal_position'));

        $this->set('settings_layout', $packageConfig->get('settings.settings_layout'));
        $this->set('settings_position', $packageConfig->get('settings.settings_position'));

        $this->set('header_tracking_scripts', $packageConfig->get('settings.header_tracking_scripts'));
        $this->set('header_targeting_scripts', $packageConfig->get('settings.header_targeting_scripts'));
        $this->set('footer_tracking_scripts', $packageConfig->get('settings.footer_tracking_scripts'));
        $this->set('footer_targeting_scripts', $packageConfig->get('settings.footer_targeting_scripts'));
        $this->set('disabled', $packageConfig->get('settings.disabled'));

        $this->set('modal_title', $packageConfig->get('settings.text.modal_title'));
        $this->set('modal_description', LinkAbstractor::translateFrom($packageConfig->get('settings.text.modal_description')));
        $this->set('preferences_header_title', $packageConfig->get('settings.text.preferences_header_title'));
        $this->set('preferences_header_description', LinkAbstractor::translateFrom($packageConfig->get('settings.text.preferences_header_description')));
        $this->set('preferences_footer_title', $packageConfig->get('settings.text.preferences_footer_title'));
        $this->set('preferences_footer_description', LinkAbstractor::translateFrom($packageConfig->get('settings.text.preferences_footer_description')));

        $site = app()->make('site')->getSite();
        $locales = $site->getLocales();
        $this->set('multipleLocales', count($locales) > 1);

        $this->set('token', $this->token);
    }

    public function update_configuration()
    {
        if ($this->post() && $this->token->validate('cookie_consent_settings')) {
            $packageConfig = $this->app->make(PackageService::class)->getByHandle('msv_cookie_consent')->getFileConfig();

            $packageConfig->save('settings.theme',  $this->post('theme'));
            $packageConfig->save('settings.force_consent',  $this->post('force_consent'));
            $packageConfig->save('settings.mode',  $this->post('mode'));
            $packageConfig->save('settings.modal_layout', $this->post('modal_layout'));
            $packageConfig->save('settings.modal_position', $this->post('modal_position'));

            $packageConfig->save('settings.settings_layout', $this->post('settings_layout'));
            $packageConfig->save('settings.settings_position', $this->post('settings_position'));

            $packageConfig->save('settings.header_tracking_scripts',  $this->post('header_tracking_scripts') ? base64_decode(trim($this->post('header_tracking_scripts'))) : '');
            $packageConfig->save('settings.header_targeting_scripts',  $this->post('header_targeting_scripts') ? base64_decode(trim($this->post('header_targeting_scripts'))) : '');
            $packageConfig->save('settings.footer_tracking_scripts',  $this->post('footer_tracking_scripts') ? base64_decode(trim($this->post('footer_tracking_scripts'))) : '');
            $packageConfig->save('settings.footer_targeting_scripts',  $this->post('footer_targeting_scripts') ? base64_decode(trim($this->post('footer_targeting_scripts'))) : '');

            $packageConfig->save('settings.text.modal_title', $this->post('modal_title'));
            $packageConfig->save('settings.text.modal_description',  LinkAbstractor::translateTo($this->post('modal_description')));

            $packageConfig->save('settings.text.preferences_header_title', $this->post('preferences_header_title'));
            $packageConfig->save('settings.text.preferences_header_description', LinkAbstractor::translateTo($this->post('preferences_header_description')));
            $packageConfig->save('settings.text.preferences_footer_title', $this->post('preferences_footer_title'));
            $packageConfig->save('settings.text.preferences_footer_description', LinkAbstractor::translateTo($this->post('preferences_footer_description')));

            $packageConfig->save('settings.disabled', $this->post('disabled') ? '1' : '0');

            $this->flash('success', t('Configuration Saved'));
            return Redirect::to('/dashboard/cookie_consent')->send();
        }
    }
}
