<!-- begin:: Content Head -->
<div class="subheader py-2  subheader-solid " id="kt_subheader">
    <div class="container-fluid  d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
        <div class="d-flex align-items-center flex-wrap mr-2">
            <h5 class="text-dark font-weight-bold mt-2 mb-2 mr-5">
                <?= $nameController; ?>
            </h5>
            <?php if (isset($countList)) { ?>
                <span class="subheader-separator subheader-separator-ver mt-2 mb-2 mr-5 bg-gray-200"></span>
                <div class="d-flex align-items-center" id="kt_subheader_search">
                    <span class="text-dark-50 font-weight-bold" id="kt_subheader_total">
                        <span class="kt_subheader_total"><?= count($countList); ?></span> <?= lang('Core.total'); ?> </span>
                    <form class="ml-5">
                        <div class="input-group input-group-sm input-group-solid" style="max-width: 175px">
                            <input type="text" class="form-control" placeholder="<?= lang('Core.search'); ?>" id="kt_subheader_search_form">
                            <div class="input-group-append">
                                <span class="input-group-text">
                                    <span class="svg-icon">
                                        <!--begin::Svg Icon | path:/metronic/theme/html/demo1/dist/assets/media/svg/icons/General/Search.svg-->
                                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                <rect x="0" y="0" width="24" height="24"></rect>
                                                <path d="M14.2928932,16.7071068 C13.9023689,16.3165825 13.9023689,15.6834175 14.2928932,15.2928932 C14.6834175,14.9023689 15.3165825,14.9023689 15.7071068,15.2928932 L19.7071068,19.2928932 C20.0976311,19.6834175 20.0976311,20.3165825 19.7071068,20.7071068 C19.3165825,21.0976311 18.6834175,21.0976311 18.2928932,20.7071068 L14.2928932,16.7071068 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"></path>
                                                <path d="M11,16 C13.7614237,16 16,13.7614237 16,11 C16,8.23857625 13.7614237,6 11,6 C8.23857625,6 6,8.23857625 6,11 C6,13.7614237 8.23857625,16 11,16 Z M11,18 C7.13400675,18 4,14.8659932 4,11 C4,7.13400675 7.13400675,4 11,4 C14.8659932,4 18,7.13400675 18,11 C18,14.8659932 14.8659932,18 11,18 Z" fill="#000000" fill-rule="nonzero"></path>
                                            </g>
                                        </svg>
                                        <!--end::Svg Icon-->
                                    </span>
                                    <!--<i class="flaticon2-search-1 icon-sm"></i>-->
                                </span>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="kt-subheader__group kt-hidden" id="kt_subheader_group_actions">
                    <div class="kt-subheader__desc"><span id="kt_subheader_group_selected_rows"></span> <?= lang('Core.elements_selected'); ?>:</div>
                    <div class="btn-toolbar kt-margin-l-20">
                        <div class="dropdown m-2" id="kt_subheader_group_actions_status_change-etat">
                            <button type="button" class="btn btn-light-dark btn-bold btn-sm dropdown-toggle" data-toggle="dropdown">
                                <?= lang('Core.status_update_etat'); ?>
                            </button>
                            <div class="dropdown-menu">
                                <ul class="navi">
                                    <li class="navi-section navi-section--first">
                                        <span class="navi-section-text"><?= lang('Core.change_status_to_etat'); ?>:</span>
                                    </li>
                                    <?php foreach ($gettype as $k => $v) { ?>
                                        <li class="navi-item">
                                            <a href="#" class="navi-link" data-toggle="status-change" data-status="1">
                                                <span class="navi-text" data-status-etat="<?= $k; ?>"><span class="kt-badge kt-badge--unified-dark kt-badge--inline kt-badge--bold"><?= lang('Core.' . $v); ?></span></span>
                                            </a>
                                        </li>
                                    <?php } ?>
                                </ul>
                            </div>
                        </div>
                        <?php if (ENVIRONMENT !== 'developement') { ?>
                            <button class="btn btn-light-success btn-bold btn-sm btn-icon-h m-2" id="kt_subheader_group_actions_fetch" data-toggle="modal" data-target="#kt_datatable_records_fetch_modal">
                                <?= lang('Core.list_selectionne'); ?>
                            </button>
                        <?php } ?>
                        <button class="btn btn-light-danger btn-bold btn-sm btn-icon-h m-2" id="kt_subheader_group_actions_delete_all">
                            <?= lang('Core.delete_all'); ?>
                        </button>
                    </div>
                </div>
            <?php }  ?>
        </div>
        <div class="kt-subheader__toolbar">
            <a href="#" class="">
            </a>
            <?php if (isset($multilangue_list)) { ?>
                <?php if (service('Settings')->setting_activer_multilangue == true) { ?>
                    <?php $setting_supportedLocales = unserialize(service('Settings')->setting_supportedLocales); ?>
                    <div class="lang_tabs" data-dft-lang="<?= service('Settings')->setting_lang_iso; ?>" style="display: block;">

                        <?php foreach ($setting_supportedLocales as $k => $v) {
                            $langExplode = explode('|', $v); ?>
                            <a href="javascript:;" data-lang="<?= $langExplode[1]; ?>" data-id_lang="<?= $langExplode[0]; ?>" class="btn <?= (service('switchlanguage')->getIdLocale() == $langExplode[0]) ? 'active'  : ''; ?> lang_tab btn-outline-brand"><?= ucfirst($langExplode[1]); ?></a>
                        <?php   } ?>
                    </div>
                <?php   } ?>
            <?php } ?>
            <?php if (inGroups(1, user()->id) && $fakedata == true) { ?>
                <a href="/<?= env('CI_SITE_AREA'); ?><?= str_replace('add', 'fake', $addPathController); ?>/10" data-toggle="kt-tooltip" title="<?= lang('Core.Fake data'); ?>" data-placement="bottom" data-original-title="<?= lang('Core.Fake data'); ?>" class="btn btn-sm btn-icon btn-bg-light btn-icon-primary btn-hover-primary"> <i class="flaticon2-gear"></i>
                </a>
            <?php } ?>
            <?php if (isset($add)) { ?>
                <a href="/<?= env('CI_SITE_AREA'); ?><?= $addPathController; ?>" class="btn btn-primary font-weight-bolder btn-sm">
                <span class="svg-icon svg-icon-md">
                        <!--begin::Svg Icon | path:/metronic/theme/html/demo1/dist/assets/media/svg/icons/Design/Flatten.svg-->
                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <rect x="0" y="0" width="24" height="24"></rect>
                                <circle fill="#000000" cx="9" cy="15" r="6"></circle>
                                <path d="M8.8012943,7.00241953 C9.83837775,5.20768121 11.7781543,4 14,4 C17.3137085,4 20,6.6862915 20,10 C20,12.2218457 18.7923188,14.1616223 16.9975805,15.1987057 C16.9991904,15.1326658 17,15.0664274 17,15 C17,10.581722 13.418278,7 9,7 C8.93357256,7 8.86733422,7.00080962 8.8012943,7.00241953 Z" fill="#000000" opacity="0.3"></path>
                            </g>
                        </svg>
                        <!--end::Svg Icon-->
                    </span>
                    <?= $add; ?> </a>
            <?php } ?>
        </div>
    </div>
</div>

<!-- end:: Content Head -->
