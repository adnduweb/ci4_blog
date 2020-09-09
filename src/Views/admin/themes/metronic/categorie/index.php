<?= $this->extend('/admin/themes/metronic/__layouts/layout_1') ?>
<?= $this->section('main') ?>
<!-- end:: Header -->
<div class="content  d-flex flex-column flex-column-fluid" id="kt_content">
<?= $this->include('/admin/themes/metronic/__partials/kt_list_toolbar') ?>

    <div id="ContentPages" class="d-flex flex-column-fluid">

        <div class="container-fluid">
            <div class="flex-row ">
                <div class="card card-custom gutter-b">
                    <div class="card-header flex-wrap border-0 pt-6 pb-0">
                        <div class="card-title">
                            <h3 class="card-label"><?= lang('Core.Record Selection'); ?>
                                <span class="d-block text-muted pt-2 font-size-sm"><?= lang('Core.row selection and group actions'); ?></span></h3>
                        </div>
                        <?php if ($toolbarExport == true) { ?>
                            <div class="card-toolbar">
                                <?= $this->include('/admin/themes/metronic/__partials/kt_export_data') ?>
                            </div>
                        <?php } ?>
                    </div>
                    <div class="card-body px-0">
                        <!--begin: Datatable -->
                        <div class="datatable datatable-bordered datatable-head-custom" id="kt_apps_categorie_list_datatable"></div>
                    </div>
                    <!--end: Datatable -->
                </div>
            </div>
        </div>

        <!--end::Portlet-->

        <!--begin::Modal-->
        <?= $this->include('/admin/themes/metronic/__partials/kt_datatable_records_fetch_modal') ?>
        <!--end::Modal-->
    </div>

    <!-- end:: Content -->
</div>
<?= $this->endSection() ?>