<!-- begin:: Content Head -->
<div class="kt-subheader   kt-grid__item" id="kt_subheader">
    <div class="kt-container  kt-container--fluid ">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title">
                <?= $nameController; ?>
            </h3>
            <?php if (isset($countList)) { ?>
                <span class="kt-subheader__separator kt-subheader__separator--v"></span>
                <div class="kt-subheader__group" id="kt_subheader_search">
                    <span class="kt-subheader__desc" id="kt_subheader_total">
                        <span class="kt_subheader_total"><?= count($countList); ?></span> <?= lang('Core.total'); ?> </span>
                    <form class="kt-margin-l-20" id="kt_subheader_search_form">
                        <div class="kt-input-icon kt-input-icon--right kt-subheader__search">
                            <input type="text" class="form-control" placeholder="<?= lang('Core.search'); ?>" id="generalSearch">
                            <span class="kt-input-icon__icon kt-input-icon__icon--right">
                                <span>
                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                            <rect x="0" y="0" width="24" height="24" />
                                            <path d="M14.2928932,16.7071068 C13.9023689,16.3165825 13.9023689,15.6834175 14.2928932,15.2928932 C14.6834175,14.9023689 15.3165825,14.9023689 15.7071068,15.2928932 L19.7071068,19.2928932 C20.0976311,19.6834175 20.0976311,20.3165825 19.7071068,20.7071068 C19.3165825,21.0976311 18.6834175,21.0976311 18.2928932,20.7071068 L14.2928932,16.7071068 Z" fill="#000000" fill-rule="nonzero" opacity="0.3" />
                                            <path d="M11,16 C13.7614237,16 16,13.7614237 16,11 C16,8.23857625 13.7614237,6 11,6 C8.23857625,6 6,8.23857625 6,11 C6,13.7614237 8.23857625,16 11,16 Z M11,18 C7.13400675,18 4,14.8659932 4,11 C4,7.13400675 7.13400675,4 11,4 C14.8659932,4 18,7.13400675 18,11 C18,14.8659932 14.8659932,18 11,18 Z" fill="#000000" fill-rule="nonzero" />
                                        </g>
                                    </svg>

                                    <!--<i class="flaticon2-search-1"></i>-->
                                </span>
                            </span>
                        </div>
                    </form>
                </div>
                <div class="kt-subheader__group kt-hidden" id="kt_subheader_group_actions">
                    <div class="kt-subheader__desc"><span id="kt_subheader_group_selected_rows"></span> <?= lang('Core.elements_selected'); ?>:</div>
                    <div class="btn-toolbar kt-margin-l-20">
                        <div class="dropdown" id="kt_subheader_group_actions_status_change-etat">
                            <button type="button" class="btn btn-label-brand btn-bold btn-sm dropdown-toggle" data-toggle="dropdown">
                                <?= lang('Core.status_update_etat'); ?>
                            </button>
                            <div class="dropdown-menu">
                                <ul class="kt-nav">
                                    <li class="kt-nav__section kt-nav__section--first">
                                        <span class="kt-nav__section-text"><?= lang('Core.change_status_to_etat'); ?>:</span>
                                    </li>
                                    <?php foreach ($gettype as $k => $v) { ?>
                                        <li class="kt-nav__item">
                                            <a href="#" class="kt-nav__link" data-toggle="status-change" data-status="1">
                                                <span class="kt-nav__link-text" data-status-etat="<?= $k; ?>"><span class="kt-badge kt-badge--unified-dark kt-badge--inline kt-badge--bold"><?= lang('Core.' . $v); ?></span></span>
                                            </a>
                                        </li>
                                    <?php } ?>
                                </ul>
                            </div>
                        </div>
                        <?php if (ENVIRONMENT !== 'developement') { ?>
                            <button class="btn btn-label-success btn-bold btn-sm btn-icon-h" id="kt_subheader_group_actions_fetch" data-toggle="modal" data-target="#kt_datatable_records_fetch_modal">
                                <?= lang('Core.list_selectionne'); ?>
                            </button>
                        <?php } ?>
                        <button class="btn btn-label-danger btn-bold btn-sm btn-icon-h" id="kt_subheader_group_actions_delete_all">
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
                            <a href="javascript:;" data-lang="<?= $langExplode[1]; ?>" data-id_lang="<?= $langExplode[0]; ?>" class="btn <?= (service('Settings')->setting_id_lang == $langExplode[0]) ? 'active'  : ''; ?> lang_tab btn-outline-brand"><?= ucfirst($langExplode[1]); ?></a>
                        <?php   } ?>
                    </div>
                <?php   } ?>
            <?php } ?>

            <?php if (isset($add)) { ?>
                <a href="/<?= env('CI_SITE_AREA'); ?><?= $addPathController; ?>" class="btn btn-label-brand btn-bold">
                    <?= $add; ?> </a>
            <?php } ?>
        </div>
    </div>
</div>

<!-- end:: Content Head -->