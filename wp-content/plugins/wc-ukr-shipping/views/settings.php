<?php
  if ( ! defined('ABSPATH')) {
      exit;
  }
?>

<?= \kirillbdev\WCUSCore\Foundation\View::render('partial/top_links'); ?>

<div id="wc-ukr-shipping-settings" class="wcus-settings">
  <div class="wcus-settings__header">
    <h1 class="wcus-settings__title"><?= __('options_page_heading', WCUS_TRANSLATE_DOMAIN); ?></h1>
    <button type="submit" form="wc-ukr-shipping-settings-form" class="wcus-settings__submit wcus-btn wcus-btn--primary wcus-btn--md"><?= __('options_btn_save', WCUS_TRANSLATE_DOMAIN); ?></button>
    <div id="wcus-settings-success-msg" class="wcus-settings__success wcus-message wcus-message--success"></div>
  </div>
  <div class="wcus-settings__content">
    <form id="wc-ukr-shipping-settings-form" action="/" method="POST">

      <ul class="wcus-tabs">
        <li data-pane="wcus-pane-general" class="active"><?= __('options_tab_common', WCUS_TRANSLATE_DOMAIN); ?></li>
        <li data-pane="wcus-pane-shipping"><?= __('options_tab_shipping', WCUS_TRANSLATE_DOMAIN); ?></li>
        <li data-pane="wcus-pane-translates"><?= __('options_tab_translates', WCUS_TRANSLATE_DOMAIN); ?></li>
      </ul>

      <?= \kirillbdev\WCUSCore\Foundation\View::render('partial/settings_general'); ?>
      <?= \kirillbdev\WCUSCore\Foundation\View::render('partial/settings_shipping'); ?>
      <?= \kirillbdev\WCUSCore\Foundation\View::render('partial/settings_translates'); ?>

    </form>
  </div>
</div>
