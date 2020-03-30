<?php

namespace modules\menu\admin;

use m\module;
use m\view;
use m\i18n;
use m\config;
use modules\admin\admin\overview_data;

class overview extends module {

    public function _init()
    {
        $arr = [];

        foreach (['horizontal','vertical','footer'] as $type) {

            $arr[] = $this->view->overview_item->prepare([
                'name' => i18n::get(ucfirst($type) . ' menu'),
                'link' => '~language_prefix~/' . config::get('admin_panel_alias') . '/menu/' . $type,
            ]);
        }

        view::set('content', $this->view->overview->prepare([
            'items' => implode('', $arr)
        ]));
    }
}
