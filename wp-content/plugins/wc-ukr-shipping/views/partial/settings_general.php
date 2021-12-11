<div id="wcus-pane-general" class="wcus-tab-pane active">
    <div class="wcus-form-group">
        <label for="wc_ukr_shipping_np_api_key"><?= __('options_label_api_key', WCUS_TRANSLATE_DOMAIN); ?></label>
        <input type="text" id="wc_ukr_shipping_np_api_key"
               name="wc_ukr_shipping[np_api_key]"
               class="wcus-form-control"
               value="<?= get_option('wc_ukr_shipping_np_api_key', ''); ?>">
    </div>

    <div class="wcus-form-group">
        <label for="wc_ukr_shipping_np_lang"><?= __('options_label_warehouse_lang', WCUS_TRANSLATE_DOMAIN); ?></label>
        <select id="wc_ukr_shipping_np_lang"
                name="wc_ukr_shipping[np_lang]"
                class="wcus-form-control">
            <option value="ru" <?= get_option('wc_ukr_shipping_np_lang', 'uk') === 'ru' ? 'selected' : ''; ?>><?= __('options_warehouse_lang_ru', WCUS_TRANSLATE_DOMAIN); ?></option>
            <option value="uk" <?= get_option('wc_ukr_shipping_np_lang', 'uk') === 'uk' ? 'selected' : ''; ?>><?= __('options_warehouse_lang_ua', WCUS_TRANSLATE_DOMAIN); ?></option>
        </select>
    </div>

    <div class="wcus-form-group wcus-form-group--horizontal">
        <label class="wcus-switcher">
            <input type="hidden" name="wcus[checkout_new_ui]" value="0">
            <input type="checkbox" name="wcus[checkout_new_ui]" value="1" <?= (int)get_option('wcus_checkout_new_ui', 0) === 1 ? 'checked' : ''; ?>>
            <span class="wcus-switcher__control"></span>
        </label>
        <div class="wcus-control-label"><?= __('options_label_new_ui', WCUS_TRANSLATE_DOMAIN); ?></div>
    </div>

    <div class="wcus-form-group wcus-form-group--horizontal">
        <label class="wcus-switcher">
            <input type="hidden" name="wcus[show_poshtomats]" value="0">
            <input type="checkbox" name="wcus[show_poshtomats]" value="1" <?= (int)get_option('wcus_show_poshtomats', 1) === 1 ? 'checked' : ''; ?>>
            <span class="wcus-switcher__control"></span>
        </label>
        <div class="wcus-control-label"><?= __('options_label_show_poshtomats', WCUS_TRANSLATE_DOMAIN); ?></div>
    </div>

    <div class="wcus-form-group">
        <label for="wc_ukr_shipping_np_translates_type"><?= __('options_label_translates_type', WCUS_TRANSLATE_DOMAIN); ?></label>
        <select id="wc_ukr_shipping_np_translates_type"
                name="wc_ukr_shipping[np_translates_type]"
                class="wcus-form-control">
            <option value="<?= WCUS_TRANSLATE_TYPE_PLUGIN; ?>" <?= WCUS_TRANSLATE_TYPE_PLUGIN === (int)wc_ukr_shipping_get_option('wc_ukr_shipping_np_translates_type') ? 'selected' : ''; ?>><?= __('options_translates_type_options', WCUS_TRANSLATE_DOMAIN); ?></option>
            <option value="<?= WCUS_TRANSLATE_TYPE_MO_FILE; ?>" <?= WCUS_TRANSLATE_TYPE_MO_FILE === (int)wc_ukr_shipping_get_option('wc_ukr_shipping_np_translates_type') ? 'selected' : ''; ?>><?= __('options_translates_type_mo_files', WCUS_TRANSLATE_DOMAIN); ?></option>
        </select>
        <div class="wcus-form-group__tooltip"><?= __('options_tooltip_translates_type', WCUS_TRANSLATE_DOMAIN); ?></div>
    </div>

    <div class="wcus-form-group">
        <label for="wc_ukr_shipping_spinner_color"><?= __('options_label_spinner_color', WCUS_TRANSLATE_DOMAIN); ?></label>
        <input name="wc_ukr_shipping[spinner_color]" id="wc_ukr_shipping_spinner_color" type="text" value="<?= get_option('wc_ukr_shipping_spinner_color', '#dddddd'); ?>" />
    </div>

    <div id="wcus-warehouse-loader"></div>
</div>