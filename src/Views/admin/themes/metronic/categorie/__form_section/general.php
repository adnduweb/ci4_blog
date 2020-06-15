<div class="row">
    <label class="col-xl-3"></label>
    <div class="col-lg-9 col-xl-6">
        <h3 class="kt-section__title kt-section__title-sm"><?= lang('Core.info_categorie'); ?>:</h3>
    </div>
</div>

<div class="form-group form-group-sm row">
    <label class="col-xl-3 col-lg-3 col-form-label"><?= ucfirst(lang('Core.activation')); ?></label>
    <div class="col-lg-9 col-xl-6">
        <span class="kt-switch kt-switch--icon">
            <label>
                <input type="checkbox" <?= ($form->active == true) ? 'checked="checked"' : ''; ?> name="active" value="1">
                <span></span>
            </label>
        </span>
    </div>
</div>

<div class="form-group row">
    <label for="id_parent" class="col-xl-3 col-lg-3 col-form-label"><?= ucfirst(lang('Core.menu_parent')); ?>* : </label>
    <div class="col-lg-9 col-xl-6">
        <select name="id_parent" class="form-control kt-selectpicker" title="<?= ucfirst(lang('Core.choose_one_of_the_following')); ?>" id="id_parent">
            <?= generate_menuOption(0, 0, $form->allCategories, $form->id_parent); ?>
        </select>
    </div>

</div>

<div class="form-group row kt-shape-bg-color-1">
    <label for="name" class="col-xl-3 col-lg-3 col-form-label"><?= ucfirst(lang('Core.titre')); ?>* : </label>
    <div class="col-lg-9 col-xl-6">
        <?= form_input_spread('name', $form->_prepareLang(), 'id="name" class="form-control lang"', 'text', true); ?>
    </div>
</div>

<div class="form-group row kt-shape-bg-color-1">
    <label for="slug" class="col-xl-3 col-lg-3 col-form-label"><?= ucfirst(lang('Core.slug')); ?>* : </label>
    <div class="col-lg-9 col-xl-6">
        <?= form_input_spread('slug', $form->_prepareLang(), 'id="slug" class="form-control lang"', 'text', true); ?>
        <span class="form-text text-muted"><?= lang('Core.Voir la page :'); ?> <a target="_blank" href="<?= base_urlFront(env('url.blog_cat') . '/' . $form->getLink(1)); ?>"><?= base_urlFront(env('url.blog_cat') . '/' . $form->getLink(1)); ?></a></span>

    </div>
</div>


<div class="form-group row kt-shape-bg-color-1">
    <label for="description_short" class="col-xl-3 col-lg-3 col-form-label"><?= ucfirst(lang('Core.description_short')); ?>* : </label>
    <div class="col-lg-9 col-xl-6">
        <?= form_textarea_spread('description_short', $form->_prepareLang(), 'class="form-control lang"', true); ?>
    </div>
</div>

<div class="form-group row kt-shape-bg-color-1">
    <label for="meta_title" class="col-xl-3 col-lg-3 col-form-label"><?= ucfirst(lang('Core.meta_title')); ?>* : </label>
    <div class="col-lg-9 col-xl-6">
        <?= form_input_spread('meta_title', $form->_prepareLang(), 'id="meta_title" class="form-control lang"', 'text', false); ?>
    </div>
</div>

<div class="form-group row kt-shape-bg-color-1">
    <label for="meta_description" class="col-xl-3 col-lg-3 col-form-label"><?= ucfirst(lang('Core.meta_description')); ?>* : </label>
    <div class="col-lg-9 col-xl-6">
        <?= form_input_spread('meta_description', $form->_prepareLang(), 'id="meta_description" class="form-control lang"', 'text', false); ?>
    </div>
</div>


<?php if (!empty($form->id_category)) { ?> <?= form_hidden('id_category', $form->id_category); ?> <?php } ?>