<?php

/* Duffion module template - Module Example */
global $wp;

$cache = get_option('d_aqi__purple_air');
?>

<div class="d-duffion__module d-module-container d-pcs container px-4">
    <div class="d-duffion__module--inner">
        <?php // have the module run here //
        ?>
        <div class="row">

            <div class="col-12-sm gy-5">
                <h1 class="display-2">AQI - Configuration</h1>
                <p class=""></p>
            </div>

            <div class="col-12 d-aqi-settings">
                <form method='post' action='options.php'>
                    <?php

                    settings_fields('d-aqi');
                    do_settings_sections('d-aqi');
                    ?>
                    <div class="field-group actions">
                        <input name="submit" type="submit" class="btn btn-success" value="<?php _e("Save Changes") ?>" />
                    </div>
                </form>
            </div>

            <?php if ($cache) {
            ?>
                <div class="col-12 gy-5">
                    <h2>Last Run: <?= date('m-d-Y h:i:s a', $cache['updated']); ?></h2>
                    <div class="cards cache-output">
                        <?php foreach ($cache['data'] as $data) { ?>
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title"><?= $data->Label; ?> - <?= $data->DEVICE_LOCATIONTYPE; ?></strong></h4>
                                    <div class="card-text">
                                        <p><b>Last Seen:</b> <em><?= date('m-d-Y h:i:s a', $data->LastSeen); ?></em></p>
                                        <p><b>Current Temp (f)</b> <em><?= $data->temp_f; ?></em></p>
                                    </div>
                                    <div class="card-text">
                                        <a href="javascript:void(0);" data-d-aqi-pr="<?= $id; ?>">Make Primary</a>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>

            <div class="col-12 pcs-scraper-rows">
                <div class="button-bar">
                    <a href="<?= admin_url(add_query_arg(['page' => 'd-aqi', 'run' => 'purpleair'], 'admin.php')); ?>" class="btn btn-primary">Manually Run Purple Air</a>
                </div>
            </div>
            <?php
            // This is how modals need to be formed. You will need to have them wrapped in the modal-backdrop //
            ?>
            <div class="modal-backdrop hidden">
                <div class="modal" id="example-modal" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Add new job</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                Our Modal is pretty
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="button" class="btn btn-primary" data-fulcrum-action="save-job">Save changes</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>