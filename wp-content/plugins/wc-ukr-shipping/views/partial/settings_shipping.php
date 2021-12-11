<div id="wcus-pane-shipping" class="wcus-tab-pane">

    <div class="wcus-form-group">
        <label for="wc_ukr_shipping_np_price"><?= __('options_label_fixed_price', WCUS_TRANSLATE_DOMAIN); ?></label>
        <input type="number" id="wc_ukr_shipping_np_price"
               name="wc_ukr_shipping[np_price]"
               class="wcus-form-control"
               min="0"
               step="0.000001"
               value="<?= get_option('wc_ukr_shipping_np_price', 0); ?>">
    </div>

    <div class="wcus-form-group">
        <label for="wc_ukr_shipping_np_block_pos"><?= __('options_label_field_position', WCUS_TRANSLATE_DOMAIN); ?></label>
        <select id="wc_ukr_shipping_np_block_pos"
                name="wc_ukr_shipping[np_block_pos]"
                class="wcus-form-control">
            <option value="billing" <?= wc_ukr_shipping_get_option('wc_ukr_shipping_np_block_pos') === 'billing' ? 'selected' : ''; ?>><?= __('options_field_position_main', WCUS_TRANSLATE_DOMAIN); ?></option>
            <option value="additional" <?= wc_ukr_shipping_get_option('wc_ukr_shipping_np_block_pos') === 'additional' ? 'selected' : ''; ?>><?= __('options_field_position_additional', WCUS_TRANSLATE_DOMAIN); ?></option>
        </select>
        <div class="wcus-form-group__tooltip"><?= __('options_tooltip_field_position', WCUS_TRANSLATE_DOMAIN); ?></div>
    </div>

    <div class="wcus-form-group wcus-form-group--horizontal">
        <label class="wcus-switcher">
            <input type="hidden" name="wc_ukr_shipping[address_shipping]" value="0">
            <input type="checkbox" name="wc_ukr_shipping[address_shipping]" value="1" <?= (int)get_option('wc_ukr_shipping_address_shipping', 1) === 1 ? 'checked' : ''; ?>>
            <span class="wcus-switcher__control"></span>
        </label>
        <div class="wcus-control-label"><?= __('options_label_address_shipping', WCUS_TRANSLATE_DOMAIN); ?></div>
    </div>

    <?php /* Store last warehouse */ ?>
    <div class="wcus-form-group wcus-form-group--horizontal">
      <label class="wcus-switcher">
        <input type="hidden" name="wc_ukr_shipping[np_save_warehouse]" value="0">
        <input type="checkbox" name="wc_ukr_shipping[np_save_warehouse]" value="1" <?= (int)get_option(WCUS_OPTION_SAVE_CUSTOMER_ADDRESS) === 1 ? 'checked' : ''; ?>>
        <span class="wcus-switcher__control"></span>
      </label>
      <div class="wcus-control-label"><?= __('options_label_save_customer_address', WCUS_TRANSLATE_DOMAIN); ?></div>
    </div>

</div>