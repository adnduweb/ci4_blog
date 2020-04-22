<?= $this->extend('/admin/themes/metronic/__layouts/layout_1') ?>
<?= $this->section('main') ?>
<!-- end:: Header -->
<div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">
    <?= $this->include('\Adnduweb\Ci4_blog\Views\admin\themes\metronic\__partials\kt_list_toolbar') ?>

    <!-- begin:: Content -->
    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">

        <!--begin::Portlet-->
        <div class="kt-portlet kt-portlet--mobile">
            <div class="kt-portlet__body kt-portlet__body--fit">

                <!--begin: Datatable -->
                <div class="kt-datatable" id="kt_apps_article_list_datatable"></div>

                <!--end: Datatable -->
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
