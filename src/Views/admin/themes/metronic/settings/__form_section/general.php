<div class="row">
    <label class="col-xl-3"></label>
    <div class="col-lg-9 col-xl-6">
        <h3 class="kt-section__title kt-section__title-sm"><?= ucfirst(lang('Core.settings_front_blog')); ?>:</h3>
    </div>
</div>
<div class="form-group row">
    <label for="setting_blog_nbr_per_page" class="col-xl-3 col-lg-3 col-form-label"><?= ucfirst(lang('Core.setting_blog_nbr_per_page')); ?>* : </label>
    <div class="col-lg-9 col-xl-6">
        <input class="form-control" type="number" value="<?= old('setting_blog_nbr_per_page') ? old('setting_blog_nbr_per_page') : $form->setting_blog_nbr_per_page; ?>" name="global[setting_blog_nbr_per_page]" id="setting_blog_nbr_per_page">
    </div>
</div>