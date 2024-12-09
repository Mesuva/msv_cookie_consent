<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<form method="post" class="ccm-dashboard-content-form" id="cookie-consent-form"
      action="<?= $view->action('update_configuration') ?>">

    <?= $token->output('cookie_consent_settings'); ?>

    <div class="form-group">
        <label>
            <?= $form->checkbox('disabled', 1, $disabled); ?>
            <?= t('Disable Cookie Consent (this also disables any scripts added below)'); ?>
        </label>
    </div>

    <fieldset>
        <legend><?= t('Header Scripts'); ?></legend>
        <div class="row mb-3">
            <div class="col-md-6">

                <label for="header_tracking_scripts_input"><?= t('Performance and Analytics cookies'); ?></label>
                <div class="form-group">
                    <?= $form->textarea('header_tracking_scripts_input', $header_tracking_scripts, ['rows' => 10, 'placeholder' => t('Enter tracking scripts, e.g. Google Analytics, Matomo. Include script tags.')]) ?>
                    <?= $form->hidden('header_tracking_scripts', '') ?>
                </div>

            </div>
            <div class="col-md-6">

                <label for="header_targeting_scripts_input"><?= t('Advertisement and Targeting cookies'); ?></label>
                <div class="form-group">
                    <?= $form->textarea('header_targeting_scripts_input', $header_targeting_scripts, ['rows' => 10, 'placeholder' => t('Enter advertising scripts, e.g. Google, Adwords. Include script tags.')]) ?>
                    <?= $form->hidden('header_targeting_scripts', '') ?>
                </div>

            </div>
        </div>
    </fieldset>

    <fieldset>
        <legend><?= t('Footer Scripts'); ?></legend>
        <div class="row mb-3">
            <div class="col-md-6">

                <label for="footer_tracking_scripts_input"><?= t('Performance and Analytics cookies'); ?></label>
                <div class="form-group">
                    <?= $form->textarea('footer_tracking_scripts_input', $footer_tracking_scripts, ['rows' => 10, 'placeholder' => t('Enter tracking scripts, e.g. Google Analytics, Matomo. Include script tags.')]) ?>
                    <?= $form->hidden('footer_tracking_scripts', '') ?>
                </div>


            </div>
            <div class="col-md-6">

                <label for="footer_targeting_scripts_input"><?= t('Advertisement and Targeting cookies'); ?></label>
                <div class="form-group">
                    <?= $form->textarea('footer_targeting_scripts_input', $footer_targeting_scripts, ['rows' => 10, 'placeholder' => t('Enter advertising scripts, e.g. Google, Adwords. Include script tags.')]) ?>
                    <?= $form->hidden('footer_targeting_scripts', '') ?>
                </div>

            </div>
        </div>
    </fieldset>


    <fieldset>
        <legend><?= t('Appearance'); ?></legend>

        <div class="form-group">
            <?= $form->label('theme', t('Theme')) ?>
            <?= $form->select('theme', [
                'auto' => t('Automatic - based on user preference'),
                'light' => t('Light'),
                'dark' => t('Dark'),
            ], $color_theme) ?>
        </div>

        <div class="form-group">
            <?= $form->label('force_consent', t('Force Consent')) ?>
            <?= $form->select('force_consent', [
                '' => t('No'),
                '1' => t('Yes, prevent page from being accessed until consent decision made'),
            ], $force_consent) ?>
        </div>

        <div class="form-group">
            <?= $form->label('mode', t('Mode')) ?>
            <?= $form->select('mode', [
                'opt-in' => t('Opt-In - scripts will only run if the user accepts'),
                'opt-out' => t('Opt-Out - scripts will automatically run, until user rejects'),
            ], $mode) ?>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <?= $form->label('modal_layout', t('Consent Dialog Layout')) ?>
                    <?= $form->select('modal_layout', [
                        'box wide' => t('Box Wide'),
                        'box inline' => t('Box Inline'),
                        'cloud inline' => t('Cloud'),
                        'bar inline' => t('Bar'),
                    ], $modal_layout) ?>
                </div>

                <div class="form-group">
                    <?= $form->label('modal_position', t('Consent Dialog Position')) ?>
                    <?= $form->select('modal_position', [
                        'bottom center' => t('Bottom Center'),
                        'bottom left' => t('Bottom Left'),
                        'bottom right' => t('Bottom Right'),
                        'middle center' => t('Middle Center'),
                        'middle left' => t('Middle Left'),
                        'middle right' => t('Middle Right'),
                        'top center' => t('Top Center'),
                        'top left' => t('Top Left'),
                        'top right' => t('Top Right'),
                    ], $modal_position) ?>
                </div>


            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <?= $form->label('settings_layout', t('Preferences Layout')) ?>
                    <?= $form->select('settings_layout', [
                        'box' => t('Box'),
                        'bar wide' => t('Bar'),
                    ], $settings_layout) ?>
                </div>

                <div class="form-group">
                    <?= $form->label('settings_position', t('Preferences Position (bar layout only)')) ?>
                    <?= $form->select('settings_position', [
                        'left' => t('Left'),
                        'right' => t('Right'),
                    ], $settings_position) ?>
                </div>

            </div>
        </div>
    </fieldset>

    <?php

    $editor = app()->make('editor');
    $editor->getPluginManager()->deselect(array('autogrow'));
    $editor->setAllowFileManager(false); // disable FileManager
    ?>

    <fieldset>
        <legend><?= t('Labels and Additional Information'); ?></legend>

        <div class="form-group">
            <?= $form->label('modal_title', t('Consent Dialog Title')) ?>
            <?= $form->text('modal_title', $modal_title, ['placeholder' => t('We use cookies on this website')]) ?>
            <?php if ($multipleLocales) { ?>
            <div class="help-block">
                <?= t('When translating, translate the phrase'); ?>: <strong><?=t ('We use cookies on this website');?></strong>
            </div>
            <?php } ?>
        </div>

        <div class="form-group">
            <?= $form->label('modal_description', t('Consent Dialog Description')) ?>
            <?php
            echo $editor->outputStandardEditor('modal_description', $modal_description);
            ?>
            <?php if ($multipleLocales) { ?>
                <div class="help-block"><?= t('When translating, translate the phrase'); ?>: <strong><?= 'We use cookies to enhance your browsing experience, serve personalized ads or content, and analyze our traffic.';?></strong></div>
            <?php } ?>
        </div>


        <div class="form-group">
            <?= $form->label('preferences_header_title', t('Preferences Header Title')) ?>
            <?= $form->text('preferences_header_title', $preferences_header_title) ?>
            <?php if ($multipleLocales) { ?>
            <div class="help-block">
                <?= t('When translating, translate the phrase'); ?>: <strong><?= 'Cookie Consent Header Title';?></strong>
            </div>
            <?php } ?>
        </div>

        <div class="form-group">
            <?= $form->label('preferences_header_description', t('Preferences Header Details')) ?>
            <?php
            echo $editor->outputStandardEditor('preferences_header_description', $preferences_header_description);
            ?>
            <?php if ($multipleLocales) { ?>
            <div class="help-block">
                <?= t('When translating, translate the phrase'); ?>: <strong><?= 'Cookie Consent Header Description';?></strong>
            </div>
            <?php } ?>
        </div>


        <div class="form-group">
            <?= $form->label('preferences_footer_title', t('Preferences Footer Title')) ?>
            <?= $form->text('preferences_footer_title', $preferences_footer_title) ?>
            <?php if ($multipleLocales) { ?>
            <div class="help-block">
                <?= t('When translating, translate the phrase'); ?>: <strong><?= 'Cookie Consent Footer Title' ;?></strong>
            </div>
            <?php } ?>
        </div>

        <div class="form-group">
            <?= $form->label('preferences_footer_description', t('Preferences Footer Details')) ?>
            <?php
            echo $editor->outputStandardEditor('preferences_footer_description', $preferences_footer_description);
            ?>
            <?php if ($multipleLocales) { ?>
            <div class="help-block">
                <?= t('When translating, translate the phrase'); ?>: <strong><?= 'Cookie Consent Footer Description' ;?></strong>
            </div>
            <?php } ?>
        </div>

    </fieldset>
    <fieldset>
        <legend><?= t('Notes'); ?></legend>
        <p class=""><?= t('Add the attribute %s to a link or button to allow visitors to open the cookie preferences modal, and %s to open the consent modal.', '<strong>data-cc="show-preferencesModal"</strong>', '<strong>data-cc="show-consentModal"</strong>'); ?>
            <br /><br /><?= t('For example, add the following HTML to a Content or HTML block:');?><p>

            <p class="alert alert-info fw-bold" style="white-space: pre; font-family: monospace;"><?= h('<a href="#" data-cc="show-consentModal">'); ?><?= t('Manage Cookies'); ?><?= h('</a>');?></p>

    </fieldset>


    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <button class="pull-right btn btn-primary float-end" type="submit"><?= t('Save') ?></button>
        </div>
    </div>
</form>


<script>
    document.addEventListener('DOMContentLoaded', function (event) {
        var trackingCodeForm = document.getElementById("cookie-consent-form");

        var trackingHeaderScriptsInput = document.getElementById("header_tracking_scripts_input");
        var targetingHeaderScriptsInput = document.getElementById("header_targeting_scripts_input");

        var trackingHeaderScripts = document.getElementById("header_tracking_scripts");
        var targetingHeaderScripts = document.getElementById("header_targeting_scripts");

        var trackingFooterScripts = document.getElementById("footer_tracking_scripts");
        var targetingFooterScripts = document.getElementById("footer_targeting_scripts");

        var trackingFooterScriptsInput = document.getElementById("footer_tracking_scripts_input");
        var targetingFooterScriptsInput = document.getElementById("footer_targeting_scripts_input");

        trackingCodeForm.addEventListener("submit", function (e) {
            trackingHeaderScripts.value = b64EncodeUnicode(trackingHeaderScriptsInput.value);
            trackingHeaderScriptsInput.setAttribute("disabled", "disabled");
            targetingHeaderScripts.value = b64EncodeUnicode(targetingHeaderScriptsInput.value);
            targetingHeaderScriptsInput.setAttribute("disabled", "disabled");

            trackingFooterScripts.value = b64EncodeUnicode(trackingFooterScriptsInput.value);
            trackingFooterScriptsInput.setAttribute("disabled", "disabled");
            targetingFooterScripts.value = b64EncodeUnicode(targetingFooterScriptsInput.value);
            targetingFooterScriptsInput.setAttribute("disabled", "disabled");
        });
    });

    function b64EncodeUnicode(str) {
        return btoa(encodeURIComponent(str).replace(/%([0-9A-F]{2})/g, function (match, p1) {
            return String.fromCharCode('0x' + p1);
        }));
    }
</script>
