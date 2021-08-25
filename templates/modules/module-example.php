<?php

/* Duffion module template - Module Example */

?>

<div class="d-duffion__module d-module-container d-pcs container px-4">
    <div class="d-duffion__module--inner">
        <?php // have the module run here //
        ?>
        <div class="row">

            <div class="col-12-sm gy-5">
                <h1 class="display-2">Module Example Template</h1>
                <p class=""></p>
            </div>

            <div class="col-12 pcs-scraper-rows">
                Some stuff can go here, or whatever... I dont care.
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