<?php

namespace Concrete\Package\MsvCookieConsent;

use Concrete\Core\View\View;
use Concrete\Core\Page\Page;
use Concrete\Core\Asset\Asset;
use Concrete\Core\Asset\AssetList;
use Concrete\Core\Package\PackageService;
use Concrete\Core\Package\Package;
use Concrete\Core\Support\Facade\Events;
use Concrete\Core\Page\Single as SinglePage;

class Controller extends Package
{
    protected $pkgHandle = 'msv_cookie_consent';
    protected $appVersionRequired = '8.0';
    protected $pkgVersion = '1.0.2';

    protected $pkgAutoloaderRegistries = [
        'src/Concrete' => '\Concrete',
    ];

    public function getPackageName()
    {
        return t('Cookie Consent');
    }

    public function getPackageDescription()
    {
        return t('Adds a Cookie Consent dialog for new visitors');
    }

    public function on_start()
    {
        Events::addListener(
            'on_before_render',
            function ($e) {
                $packageConfig = $this->app->make(PackageService::class)->getByHandle('msv_cookie_consent')->getFileConfig();
                $disabled = (boolean)$packageConfig->get('settings.disabled');

                if (!$disabled) {
                    $view = $e->getArgument('view');
                    if ($view) {
                        $pageObject = false;

                        if (method_exists($view, 'getPageObject')) {
                            $pageObject = $view->getPageObject();
                        }

                        if ($pageObject
                            && !$pageObject->isSystemPage()
                            && !$pageObject->isAdminArea()
                            && !$pageObject->isMasterCollection()
                            && !$pageObject->isEditMode()
                            && !$pageObject->isPageDraft()
                        ) {
                            $pageController = $pageObject->getPageController();
                            if ($pageController) {
                                $al = AssetList::getInstance();
                                $al->register('css', 'cookie-consent', 'css/cookieconsent.css', ['version' => '3.0.0', 'position' => Asset::ASSET_POSITION_HEADER, 'minify' => false, 'combine' => false], $this);
                                $al->register('javascript', 'cookie-consent', 'js/cookieconsent.umd.js', ['version' => '3.0.0', 'position' => Asset::ASSET_POSITION_FOOTER, 'minify' => false, 'combine' => false], $this);
                                $al->register('raw-javascript-inline', 'cookie-consent-header', $this->getCookieConsentInit($packageConfig, 'header'), ['local' => true, 'version' => '1.0', 'position' => Asset::ASSET_POSITION_HEADER, 'minify' => false, 'combine' => false], $this);
                                $al->register('raw-javascript-inline', 'cookie-consent-init', $this->getCookieConsentInit($packageConfig), ['local' => true, 'version' => '1.0', 'position' => Asset::ASSET_POSITION_FOOTER, 'minify' => false, 'combine' => false], $this);

                                $pageController->requireAsset('css', 'cookie-consent');
                                $pageController->requireAsset('javascript', 'cookie-consent');
                                $pageController->requireAsset('raw-javascript-inline', 'cookie-consent-header');
                                $pageController->requireAsset('raw-javascript-inline', 'cookie-consent-init');
                            }
                        }
                    }
                }
            }
        );
    }

    private function getCookieConsentInit($packageConfig, $position = 'footer')
    {
        ob_start();
        if (file_exists(DIR_BASE . '/application/elements/cookie_consent_init.php')) {
            View::element('cookie_consent_init', ['packageConfig' => $packageConfig, 'position'=>$position]);
        } else {
            View::element('cookie_consent_init', ['packageConfig' => $packageConfig, 'position'=>$position], 'msv_cookie_consent');
        }
        $initScript = ob_get_clean();
        return $initScript;
    }

    public function install()
    {
        $pkg = parent::install();
        $pages = [];
        $pages[] = '/dashboard/cookie_consent';

        foreach ($pages as $path) {
            $page = Page::getByPath($path);
            if (!is_object($page) || $page->isError()) {
                SinglePage::add($path, $pkg);
            }
        }

        $packageConfig = $this->app->make(PackageService::class)->getByHandle('msv_cookie_consent')->getFileConfig();

        if (!$packageConfig->get('settings.text.modal_description')) {
            $packageConfig->save('settings.text.modal_description', '<p>' . t('We use cookies to enhance your browsing experience, serve personalized ads or content, and analyze our traffic.') . '</p>');
        }

        if (!$packageConfig->get('settings.mode')) {
            $packageConfig->save('settings.mode', 'opt-in');
        }

        if (!$packageConfig->get('settings.theme')) {
            $packageConfig->save('settings.theme', 'auto');
        }

        if (!$packageConfig->get('settings.modal_layout')) {
            $packageConfig->save('settings.modal_layout', 'box wide');
        }

        if (!$packageConfig->get('settings.modal_position')) {
            $packageConfig->save('settings.modal_position', 'bottom center');
        }

        if (!$packageConfig->get('settings.settings_layout')) {
            $packageConfig->save('settings.settings_layout', 'box');
        }

        if (!$packageConfig->get('settings.settings_position')) {
            $packageConfig->save('settings.settings_position', 'left');
        }
    }
}
