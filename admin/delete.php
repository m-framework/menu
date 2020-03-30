<?php

namespace modules\menu\admin;

use m\module;
use m\core;
use modules\menu\models\menu;

class delete extends module {

    public function _init()
    {
        $item = new menu(!empty($this->get->delete) ? $this->get->delete : null);

        if (!empty($item->id) && !empty($this->user->profile) && $this->user->is_admin() && $item->destroy()) {
            core::redirect('/' . $this->conf->admin_panel_alias . '/menu');
        }
    }
}
