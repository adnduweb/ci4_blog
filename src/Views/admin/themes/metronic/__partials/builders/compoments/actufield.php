<?php $categoriesModel = new Adnduweb\Ci4_blog\Models\CategoriesModel(); ?>
<?php $field = isset($builder->id_field) ? $builder->id_field : "__field__"; ?>
<?php $optionsActus = isset($builder->id_field) ? json_decode($builder->options) : ""; ?>
<div class="kt-portlet kt-portlet--height-fluid <?= ($field == '__field__') ? '' : ' kt-portlet--collapse'; ?>" id="kt_portlet_tools<?= $field; ?>">
    <div class="kt-portlet__head">
        <div class="kt-portlet__head-label">
            <h3 class="kt-portlet__head-title">
                <?= lang('Core.actualites'); ?> <?= isset($builder->handle) ? ' : ' . $builder->handle : ""; ?>
            </h3>
        </div>
        <div class="kt-portlet__head-toolbar">
            <div class="kt-portlet__head-group">
                <a href="javascript:;" data-ktportlet-tool="toggle" data-field="<?= $field; ?>" class="btn btn-sm btn-icon btn-brand btn-icon-md"><i class="la la-angle-down"></i></a>
                <a href="javascript:;" data-ktportlet-tool="remove" data-id_builder="<?= isset($builder->id_builder) ? $builder->id_builder : ""; ?>" data-field="<?= $field; ?>" class="btn btn-sm btn-icon btn-warning removePortlet btn-icon-md"><i class="la la-close"></i></a>
            </div>
        </div>
    </div>
    <div class="kt-portlet__body" <?= ($field == '__field__') ? '' : 'style="display: none;overflow: hidden;padding-top: 0px;padding-bottom: 0px;"'; ?>>
        <div class="kt-portlet__content">
            <div class="row li_row form_output" data-type="text" data-field="<?= $field; ?>">

                <div class="col-md-12">
                    <div class="form-group">
                        <label><?= lang('Core.titre'); ?></label>
                        <?= form_input_spread([$field, 'content'], isset($builder->id_field) ? $builder->_prepareLang() : NULL, 'id="name" class="form-control lang"', 'text', false, 'builder'); ?>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="form-group">
                        <label><?= lang('Core.by_cat'); ?></label>
                        <select required name="builder[<?= $field; ?>][options][cat]" class="form-control" title="<?= ucfirst(lang('Core.choose_one_of_the_following')); ?>" id="options_cat">
                            <option <?= (isset($optionsActus->orderBy) && $optionsActus->orderBy == 'all') ? 'selected' : ""; ?> value="all">Toutes</option>
                            <?php foreach ($categoriesModel->getAllCat() as $cat) { ?>
                                <option <?= (isset($optionsActus->cat) && $optionsActus->cat == $cat->id_categorie) ? 'selected' : ""; ?> value="<?= $cat->id_categorie; ?>"><?= $cat->name; ?></option>
                            <?php } ?>
                            
                        </select>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="form-group">
                        <label><?= lang('Core.order_by'); ?></label>
                        <select required name="builder[<?= $field; ?>][options][orderBy]" class="form-control" title="<?= ucfirst(lang('Core.choose_one_of_the_following')); ?>" id="options_order_by">
                            <option <?= (isset($optionsActus->orderBy) && $optionsActus->orderBy == 'desc') ? 'selected' : ""; ?> value="desc">Décroissant</option>
                            <option <?= (isset($optionsActus->orderBy) && $optionsActus->orderBy == 'asc') ? 'selected' : ""; ?> value="asc">Croissant</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label><?= lang('Core.per_page'); ?></label>
                        <input class="form-control" type="number" value="<?= (isset($optionsActus->per_page)) ? $optionsActus->per_page : ""; ?>" name="builder[<?= $field; ?>][options][per_page]" id="options_per_page">
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="form-group">
                        <label><?= lang('Core.class_css'); ?></label>
                        <input type="text" name="builder[<?= $field; ?>][class]" class="form-control form_input_label" value="<?= isset($builder->class) ? $builder->class : ""; ?>" data-field="<?= $field; ?>" placeholder="Votre class" />
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label><?= lang('Core.id_css'); ?></label>
                        <input type="text" name="builder[<?= $field; ?>][id]" data-field="<?= $field; ?>" class="form-control form_input_placeholder" value="<?= isset($builder->id) ? $builder->id : ""; ?>" placeholder="Votre id" />
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="form-group">
                        <label><?= lang('Core.handle'); ?></label>
                        <input type="text" name="builder[<?= $field; ?>][handle]" data-field="<?= $field; ?>" class="form-control form_input_placeholder" value="<?= isset($builder->handle) ? $builder->handle : ""; ?>" placeholder="Handle" />
                    </div>
                </div>
                <?php if ($field != "__field__") { ?>
                    <?= form_hidden('builder[' . $field . '][id_builder]', $builder->id_builder); ?>
                <?php } ?>
                <?= form_hidden('builder[' . $field . '][type]', 'actufield'); ?>
                <?= form_hidden('builder[' . $field . '][id_field]', $field); ?>
                <?= form_hidden('builder[' . $field . '][id_item]', $form->id_item); ?>
                <?= form_hidden('builder[' . $field . '][id_module]', $form->id_module); ?>
            </div>
        </div>
    </div>
</div>

<?php if ($field == "__field__") { ?>
    __script__
<?php } ?>