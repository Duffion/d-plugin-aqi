<?php

// Creating the widget
class aqinfo_widget extends WP_Widget
{

    // The construct part
    function __construct()
    {
        parent::__construct(

            // Base ID of your widget
            'aqinfo_widget',

            // Widget name will appear in UI
            __('AQInfo Display', 'd-text'),

            // Widget description
            array('description' => __('Display an AQI status bubble for your users.', 'd-text'),)
        );
    }

    // Creating widget front-end
    public function widget($args, $instance)
    {
        $purple_air_index = (int) apply_filters('widget_purple_air_index', $instance['purple_air_index']);
        $pr_cache = get_option('d_aqi__purple_air');
        if ($pr_cache && isset($pr_cache['data']) && isset($pr_cache['data'][$purple_air_index])) {
            $device = $pr_cache['data'][$purple_air_index];
?>
            <?= $args['before_widget']; ?>
            <div class="d-aqinfo d-aqi-widget d-widget">
                <div data-aqinfo-widget='<?= $device->ID; ?>' data-aqinfo=' <?= json_encode($device); ?>'>
                    <span class="d-loading">Loading AQI</span>
                    <div class="d-aqinfo--bubble">
                        <div class="d-aqinfo--cir" data-aqinfo-ranking="Healthy">
                            <h3>Current AQI</h3>
                            <span class="d-aqinfo--cir-title">Healthy</span>
                        </div>
                        <div class="d-aqinfo--popover">This was last updated at <?= date('H:i:s a'); ?></div>
                        <div class="d-aqinfo--cir-bottom">
                            <strong class="d-aqinfo--cir-index d-aqinfo--index">0</strong>
                        </div>
                    </div>
                </div>
            </div>
            <?= $args['after_widget']; ?>
        <?php
        }
    }

    // Creating widget Backend
    public function form($instance)
    {
        $pr_cache = get_option('d_aqi__purple_air');
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('purple_air_index'); ?>"><?php _e('Purple Air Feed:'); ?></label>
            <select name="<?= $this->get_field_id('purple_air_index'); ?>" id="<?= $this->get_field_id('purple_air_index'); ?>">
                <option value="">Select one...</option>
                <?php if (count($pr_cache['data']) > 0) {
                    foreach ($pr_cache['data'] as $i => $d) {
                ?>
                        <option value="<?= $i; ?>" <?= ($instance['purple_air_index'] === $i) ? ' selected' : '' ?>><?= $d->Label; ?></option>
                <?php
                    }
                } ?>

            </select>
    <?php
    }

    // Updating widget replacing old instances with new
    public function update($new_instance, $old_instance)
    {
        $instance = [];
        $instance['purple_air_index'] = (!empty($new_instance['purple_air_index']) ? $new_instance['purple_air_index'] : '');

        return $instance;
    }

    // Class wpb_widget ends here
}
