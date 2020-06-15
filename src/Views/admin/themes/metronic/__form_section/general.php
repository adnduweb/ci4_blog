<div class="row">
    <label class="col-xl-3"></label>
    <div class="col-lg-9 col-xl-6">
        <h3 class="kt-section__title kt-section__title-sm"><?= lang('Core.info_taxe'); ?>:</h3>
    </div>
</div>

<!-- <div class="form-group form-group-sm row">
    <label class="col-xl-3 col-lg-3 col-form-label"><?= ucfirst(lang('Core.activation')); ?></label>
    <div class="col-lg-9 col-xl-6">
        <span class="kt-switch kt-switch--icon">
            <label>
                <input type="checkbox" <?= ($form->active == true) ? 'checked="checked"' : ''; ?> name="active" value="1">
                <span></span>
            </label>
        </span>
    </div>
</div> -->

<?php if (!empty($form->active)) { ?> <?= form_hidden('active', 1); ?> <?php } ?>


<div class="form-group form-group-sm row">
    <label for="etat" class="col-xl-3 col-lg-3 col-form-label"><?= ucfirst(lang('Core.etat')); ?></label>
    <div class="col-lg-9 col-xl-6">
        <select required name="type" class="form-control selectpicker file kt-selectpicker" publied-actions-box="true" title="<?= ucfirst(lang('Core.choose_one_of_the_following')); ?>" id="type">
            <?php foreach ($form->gettype as $k => $v) { ?>
                <option <?= $k == $form->type ? 'selected' : ''; ?> value="<?= $k; ?>"><?= lang('Core.' . $v); ?></option>
            <?php } ?>
        </select>
    </div>
</div>

<div class="form-group form-group-sm row">
    <label for="categorie" class="col-xl-3 col-lg-3 col-form-label"><?= ucfirst(lang('Core.categorie')); ?></label>
    <div class="col-lg-9 col-xl-6">
        <select required name="id_category_default[]" class="form-control selectpicker file kt-selectpicker" publied-actions-box="true" title="<?= ucfirst(lang('Core.choose_one_of_the_following')); ?>" id="id_category_default">
            <?= generate_menuOption(0, 0, $form->allCategories, $form->id_category_default); ?>
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
    <label for="sous_name" class="col-xl-3 col-lg-3 col-form-label"><?= ucfirst(lang('Core.sous_name')); ?>* : </label>
    <div class="col-lg-9 col-xl-6">
        <?= form_input_spread('sous_name', $form->_prepareLang(), 'id="sous_name" class="form-control lang"', 'text', false); ?>
    </div>
</div>

<div class="form-group row kt-shape-bg-color-1">
    <label for="sous_name" class="col-xl-3 col-lg-3 col-form-label"><?= ucfirst(lang('Core.slug')); ?>* : </label>
    <div class="col-lg-9 col-xl-6">
        <?= form_input_spread('slug', $form->_prepareLang(), 'id="slug" class="form-control lang"', 'text', true); ?>
        <span class="form-text text-muted"><?= lang('Core.Voir la page :'); ?> <a target="_blank" href="<?= base_urlFront(env('url.blog') . '/' . $form->getLink(1)); ?>"><?= base_urlFront(env('url.blog') . '/' . $form->getLink(1)); ?></a></span>
    </div>
</div>

<div class="form-group row kt-shape-bg-color-1">
    <label for="description_short" class="col-xl-3 col-lg-3 col-form-label"><?= ucfirst(lang('Core.description_short')); ?>* : </label>
    <div class="col-lg-9 col-xl-6">
        <?= form_textarea_spread('description_short', $form->_prepareLang(), 'class="form-control lang"', false); ?>
    </div>
</div>

<div class="form-group row kt-shape-bg-color-1">
    <label for="description" class="col-xl-3 col-lg-3 col-form-label"><?= ucfirst(lang('Core.description')); ?>* : </label>
    <div class="col-lg-9 col-xl-6">
        <?= form_textarea_spread('description', $form->_prepareLang(), 'id="description" class="form-control lang"', false, 'ckeditor'); ?>
    </div>
</div>

<div class="form-group form-group-sm row">
    <label class="col-xl-3 col-lg-3 col-form-label"><?= ucfirst(lang('Core.no_follow_no_index')); ?></label>
    <div class="col-lg-9 col-xl-6">
        <span class="kt-switch kt-switch--icon">
            <label>
                <input type="checkbox" <?= ($form->no_follow_no_index == true) ? 'checked="checked"' : ''; ?> name="no_follow_no_index" value="1">
                <span></span>
            </label>
        </span>
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

<div class="kt-separator kt-separator--border-dashed kt-separator--portlet-fit kt-separator--space-lg"></div>

<div class="form-group row fileImageUpload">
    <label for="picture_one" class="col-xl-3 col-lg-3 col-form-label"><?= ucfirst(lang('Core.picture_one')); ?>* : </label>
    <div class="col-lg-12 col-xl-12">
        <div>
            <?php $optionsPicture_one = [
                'acceptedFiles' => '.jpg, .jpeg, .png, .svg',
                'maxFiles' => 1,
                'maxFilesize' => 5,
                'uploadMultiple' => false,
                'crop' => true,
                'crop_width' => 700,
                'crop_height' => 500,
                'type' => 'image',
                'field' => 'picture_one',
                'builder' => false,
                'input' => false,
                'only' => 1
            ]; ?>
            <?= view('/admin/themes/metronic/controllers/medias/bundleUploadCrop', $optionsPicture_one) ?>
            <?= form_hidden('picture_one', $form->picture_one); ?>


            <div id="picture_one" class="picture_one" data-field="picture_one">
                <div class="kt-section__content_media">
                    <?php if (!empty($form->getPictureOneAtt())) { ?>
                        <?php if (!empty($form->getPictureOneAtt())) { ?>
                            <?php $pictureOne = $form->getPictureOneAtt(); ?>
                            <div class="kt-media is_unique kt-media_<?= $pictureOne->media->id_media; ?>" data-id-media="<?= $pictureOne->media->id_media; ?>">
                                <a href="javascript:;" class="kt-media">
                                    <img src="<?= $pictureOne->media->filename; ?>" alt="image">
                                </a>
                                <label class="kt-avatar__remove deletefile" data-container="body" data-only="true" data-toggle="kt-tooltip" title="" data-placement="top" data-original-title="remove image" data-id-field="picture_one" data-id-media="<?= $pictureOne->media->id_media; ?>" data-format="<?= $pictureOne->media->format; ?>" data-id-file="<?= $pictureOne->media->filename; ?>">
                                    <i class="fa fa-times"></i>
                                </label>
                            </div>
                        <?php } ?>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="form-group row fileImageUpload">
    <label for="picture_header" class="col-xl-3 col-lg-3 col-form-label"><?= ucfirst(lang('Core.picture_header')); ?>* : </label>
    <div class="col-lg-12 col-xl-12">
        <div>
            <?php $optionsPicture_header = [
                'acceptedFiles' => '.jpg, .jpeg, .png, .svg',
                'maxFiles' => 1,
                'maxFilesize' => 5,
                'uploadMultiple' => false,
                'crop' => true,
                'crop_width' => 1920,
                'crop_height' => 500,
                'type' => 'image',
                'field' => 'picture_header',
                'builder' => false,
                'input' => false,
                'only' => 1
            ]; ?>
            <?= view('/admin/themes/metronic/controllers/medias/bundleUploadCrop', $optionsPicture_header) ?>
            <?= form_hidden('picture_header', $form->picture_header); ?>


            <div id="picture_header" class="picture_header" data-field="picture_header">
                <div class="kt-section__content_media">
                    <?php if (!empty($form->getPictureHeaderAtt())) { ?>
                        <?php if (!empty($form->getPictureHeaderAtt())) { ?>
                            <?php $pictureHeader = $form->getPictureHeaderAtt(); ?>
                            <div class="kt-media is_unique kt-media_<?= $pictureHeader->media->id_media; ?>" data-id-media="<?= $pictureHeader->media->id_media; ?>">
                                <a href="javascript:;" class="kt-media">
                                    <img src="<?= $pictureHeader->media->filename; ?>" alt="image">
                                </a>
                                <label class="kt-avatar__remove deletefile" data-container="body" data-only="true" data-toggle="kt-tooltip" title="" data-placement="top" data-original-title="remove image" data-id-field="picture_header" data-id-media="<?= $pictureHeader->media->id_media; ?>" data-format="<?= $pictureHeader->media->format; ?>" data-id-file="<?= $pictureHeader->media->filename; ?>">
                                    <i class="fa fa-times"></i>
                                </label>
                            </div>
                        <?php } ?>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if (!empty($form->id_article)) { ?> <?= form_hidden('id_article', $form->id_article); ?> <?php } ?>