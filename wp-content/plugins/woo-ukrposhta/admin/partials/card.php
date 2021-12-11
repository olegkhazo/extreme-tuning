<div>
    <div class="updated updatedpro mtb16 p0"  >
        <div class="card">
            <div class="card-header">
                <h3>Підтримка</h3>
            </div>
            <div class="card-body">
                <p>Якщо у вас виникли проблеми із створенням накладної або щось інше, то звертайтесь до нашої підтримки в Facebook.</p>
                <h5><a href="https://www.facebook.com/groups/morkvasupport" 
                        class="wpbtn button button-primary" 
                        target="_blank">
                        <?php echo '<img class="imginwpbtn" src="' . plugins_url('img/messenger.png', __FILE__) . '" />'; ?> Написати в чат</a></h5>
                <p>Щось не працює? (версія <?php echo MUP_PLUGIN_VERSION; ?>) <br> можливо, в оновленій версії уже вирішена ваша проблема (див. список змін)</p>
                <a href="plugin-install.php?tab=plugin-information&amp;plugin=morkvaup-plugin&amp;section=changelog&amp;TB_iframe=true&amp;width=772&amp;height=374" 
                    class="thickbox open-plugin-details-modal" >встановити останню версію плагіна</a>
            </div>
        </div>
    </div>
    <?php $path = MUP_PLUGIN_PATH . '/admin/partials/morkvaup-plugin-invoices-page.php'; if ( ! file_exists( $path ) ) : ?>

    <div class="card">
        <div class="card-header">
            <h3>Pro версія</h3>
        </div>
        <div class="card-body">
            <ul>
                <li>міжнародна доставка</li>
                <li>автоматизація та зручність оформлення</li>
                <li>відслідкувати статуси відправлень</li>
                <li>змінити оголошену вартість</li>
                <li>... та багато іншого</li>
            </ul>
            Оновіться до Pro-версії зараз!
            <h5><a href="https://www.morkva.co.ua/shop/woo-ukrposhta-pro/" class="button button-primary">Хочу Pro</a></h5>
        </div>
    </div>

    <?php endif; ?>

    <div class="clear"></div>
</div>
