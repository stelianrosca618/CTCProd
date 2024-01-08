<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_missiveapp_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="padding-medium">
            <h1 class="tw-text-2xl tw-text-neutral-800 text-center tw-font-semibold tw-mb-5">
                <?php echo _l('admin_auth_login_heading'); ?>
            </h1>

            <div class="tw-bg-white tw-mx-2 sm:tw-mx-6 tw-py-6 tw-px-6 sm:tw-px-8 tw-shadow tw-rounded-lg">
                <?php echo form_open($this->uri->uri_string(), 'name="login_form" class="login-form" id="login-form"'); ?>

                    <div class="alert alert-vk alert-danger form-error-area hide">               
                        <strong>Error!</strong> <span></span>
                    </div>

                    <div class="form-group">
                        <label for="email" class="control-label">
                            <?php echo _l('admin_auth_login_email'); ?>
                        </label>
                        <input type="email" id="email" name="email" class="form-control" autofocus="1">
                    </div>

                    <div class="form-group">
                        <label for="password" class="control-label">
                            <?php echo _l('admin_auth_login_password'); ?>
                        </label>
                        <input type="password" id="password" name="password" class="form-control">
                    </div>

                    <div class="form-group">
                        <button type="submit" id="button-login" class="btn btn-primary btn-block">
                            <?php echo _l('admin_auth_login_button'); ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <div ></div>
    </div>
</div>
<?php init_missiveapp_footer(); ?>