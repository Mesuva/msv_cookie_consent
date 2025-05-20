<?php defined('C5_EXECUTE') or die("Access Denied.");

if (!function_exists('ccParagraphsToBreaks')) {
    function ccParagraphsToBreaks(string $html): string {
        $paragraphs = [];
        $pattern = '/<p[^>]*>(.*?)<\/p>/is';

        // Extract the content of each paragraph
        preg_match_all($pattern, $html, $matches);
        $paragraphs = $matches[1];

        // Join the paragraph content with <br> tags
        $output = implode('<br>', $paragraphs);

        return $output;
    }
}

$forceConsent = $packageConfig->get('settings.force_consent') ? 'true' : 'false';
$mode = $packageConfig->get('settings.mode') ? $packageConfig->get('settings.mode') : 'opt-in';
$modalLayout = $packageConfig->get('settings.modal_layout');
$modal_position = $packageConfig->get('settings.modal_position');

$site = app()->make('site')->getSite();
$defaultLocale = $site->getDefaultLocale()->getLocale();

$loc = \Concrete\Core\Localization\Localization::getInstance();
$currentLocale = $loc->getLocale();

$nonDefaultLocale = ($currentLocale != $defaultLocale);

$settingsLayout = $packageConfig->get('settings.settings_layout');
$settings_position = $packageConfig->get('settings.settings_position');

$trackingScripts = trim($packageConfig->get('settings.'.$position.'_tracking_scripts'));
$targetingScripts = trim($packageConfig->get('settings.'.$position.'_targeting_scripts'));

$otherPosition = 'header';
if ($position == 'header') {
    $otherPosition = 'footer';
}

$otherTrackingScripts = trim($packageConfig->get('settings.'.$otherPosition.'_tracking_scripts'));
$otherTargetingScripts = trim($packageConfig->get('settings.'.$otherPosition.'_targeting_scripts'));

$hasTrackingScripts = $trackingScripts || $otherTrackingScripts;
$hasTargetingScripts = $targetingScripts || $otherTargetingScripts;

$modalTitle = trim($packageConfig->get('settings.text.modal_title'));
$modalDescription = \Concrete\Core\Editor\LinkAbstractor::translateFrom(trim($packageConfig->get('settings.text.modal_description')));
$preferencesHeaderTitle = trim($packageConfig->get('settings.text.preferences_header_title'));
$preferencesHeaderDescription = \Concrete\Core\Editor\LinkAbstractor::translateFrom(trim($packageConfig->get('settings.text.preferences_header_description')));
$preferencesFooterTitle = trim($packageConfig->get('settings.text.preferences_footer_title'));
$preferencesFooterDescription = \Concrete\Core\Editor\LinkAbstractor::translateFrom(trim($packageConfig->get('settings.text.preferences_footer_description')));


if (!$modalDescription || $nonDefaultLocale) {
    $modalDescription = '<p>' . t('We use cookies to enhance your browsing experience, serve personalized ads or content, and analyze our traffic.') . '</p>';
}

if ($trackingScripts) {
    $trackingScripts = str_replace('type="text/javascript"', '', $trackingScripts);
    $trackingScripts = str_replace('type=\'text/javascript\'', '', $trackingScripts);
    $trackingScripts = str_replace('<script', '<script type="text/plain" data-category="analytics" ', $trackingScripts);
}

if ($targetingScripts) {
    $targetingScripts = str_replace('type="text/javascript"', '', $targetingScripts);
    $targetingScripts = str_replace('type=\'text/javascript\'', '', $targetingScripts);
    $targetingScripts = str_replace('<script', '<script type="text/plain" data-category="targeting" ', $targetingScripts);
}

if (!$modalTitle || $nonDefaultLocale) {
    $modalTitle = t('We use cookies on this website');
}

if ($nonDefaultLocale) {
    if ($preferencesHeaderTitle) {
        $preferencesHeaderTitle = t('Cookie Consent Header Title');

        if ($preferencesHeaderTitle == 'Cookie Consent Header Title') {
            $preferencesHeaderTitle = '';
        }
    }

    if ($preferencesHeaderDescription) {
        $preferencesHeaderDescription = t('Cookie Consent Header Description');

        if ($preferencesHeaderDescription == 'Cookie Consent Header Description') {
            $preferencesHeaderDescription = '';
        }
    }

    if ($preferencesFooterTitle) {
        $preferencesFooterTitle = t('Cookie Consent Footer Title');

        if ($preferencesFooterTitle == 'Cookie Consent Footer Title') {
            $preferencesFooterTitle = '';
        }
    }

    if ($preferencesFooterDescription) {
        $preferencesFooterDescription = t('Cookie Consent Footer Description');

        if ($preferencesFooterDescription == 'Cookie Consent Footer Description') {
            $preferencesFooterDescription = '';
        }
    }

}

$modalDescription = ccParagraphsToBreaks($modalDescription);
$preferencesHeaderDescription = ccParagraphsToBreaks($preferencesHeaderDescription);
$preferencesFooterDescription = ccParagraphsToBreaks($preferencesFooterDescription);


if ($position == 'footer') {
    $theme = $packageConfig->get('settings.theme');
    $enabledTheme = '';
    if ($theme == 'dark') {
        $enabledTheme = "document.body.classList.toggle('cc--darkmode');";
    }
    if ($theme == 'auto') {
        $enabledTheme = "if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) { document.body.classList.toggle('cc--darkmode'); }";
    }

    ?>
    <script type="text/javascript">
        window.addEventListener('load', function () {
                <?= $enabledTheme; ?>
                CookieConsent.run({
                    disablePageInteraction: <?= $forceConsent; ?>,
                    mode: '<?= $mode; ?>',
                    guiOptions: {
                        consentModal: {
                            layout: '<?= $modalLayout; ?>',
                            position: '<?= $modal_position; ?>'
                        },
                        preferencesModal: {
                            layout: '<?= $settingsLayout; ?>',
                            position: '<?= $settings_position; ?>'
                        }
                    },

                    categories: {
                        necessary: {
                            enabled: true,  // this category is enabled by default
                            readOnly: true  // this category cannot be disabled
                        }
                        <?php if ($hasTrackingScripts) { ?>
                        , analytics: {
                            enabled: <?= $mode == 'opt-out' ? 'true' : 'false';?>,
                            readOnly: false,

                            // Delete specific cookies when the user opts-out of this category
                            autoClear: {
                                cookies: [
                                    {
                                        name: /^_ga/,   // regex: match all cookies starting with '_ga'
                                    },
                                    {
                                        name: '_gid',   // string: exact cookie name
                                    }
                                ]
                            }
                        }
                        <?php } ?>
                        <?php if ($hasTargetingScripts) { ?>
                        , targeting: {
                            enabled: <?= $mode == 'opt-out' ? 'true' : 'false';?>,
                            readOnly: false
                        }
                        <?php } ?>
                    },

                    language: {
                        default: 'en',
                        translations: {
                            en: {
                                consentModal: {
                                    title: <?= json_encode($modalTitle);?>,
                                    description: <?= json_encode($modalDescription);?>,
                                    acceptAllBtn: '<?= t('Accept all');?>',
                                    acceptNecessaryBtn: '<?= t('Reject all');?>',
                                    showPreferencesBtn: '<?= t('Manage cookie preferences');?>'
                                },
                                preferencesModal: {
                                    title: '<?= t('Manage cookie preferences');?>',
                                    acceptAllBtn: '<?= t('Accept all');?>',
                                    acceptNecessaryBtn: '<?= t('Reject all');?>',
                                    savePreferencesBtn: '<?= t('Accept current selection');?>',
                                    closeIconLabel: '<?= t('Close');?>',
                                    sections: [
                                        <?php if ($preferencesHeaderTitle || $preferencesHeaderDescription ) { ?>
                                        {
                                            title: <?= json_encode($preferencesHeaderTitle); ?>,
                                            description: <?= json_encode($preferencesHeaderDescription); ?>
                                        }
                                        ,
                                        <?php } ?>

                                        {
                                            title: '<?= t('Strictly Necessary cookies');?>',
                                            description: '<?= t('These cookies are essential for the proper functioning of the website and cannot be disabled.');?>',
                                            linkedCategory: 'necessary'
                                        }
                                        <?php if ($hasTrackingScripts) { ?>
                                        ,
                                        {
                                            title: '<?= t('Performance and Analytics cookies');?>',
                                            description: '<?= t('These cookies are used to understand how visitors interact with the website. These cookies help provide information on metrics such as the number of visitors, bounce rate, traffic source, etc.');?>',
                                            linkedCategory: 'analytics'
                                        }
                                        <?php } ?>
                                        <?php if ($hasTargetingScripts) { ?>
                                        ,
                                        {
                                            title: '<?= t('Advertisement and Targeting cookies');?>',
                                            description: '<?= t('These cookies are used to provide visitors with customized advertisements based on the pages you visited previously and to analyze the effectiveness of the ad campaigns.');?>',
                                            linkedCategory: 'targeting'
                                        }
                                        <?php } ?>

                                        <?php if ($preferencesFooterTitle ||$preferencesFooterDescription ) { ?>
                                        ,
                                        {
                                            title: <?= json_encode($preferencesFooterTitle); ?>,
                                            description: <?= json_encode($preferencesFooterDescription); ?>
                                        }
                                        <?php } ?>
                                    ]
                                }
                            }
                        }
                    }
                })
            }
        );
    </script>

<?php  } ?>
<?= $trackingScripts; ?>
<?= $targetingScripts; ?>
