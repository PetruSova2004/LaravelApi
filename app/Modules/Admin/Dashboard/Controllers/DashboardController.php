<?php

namespace App\Modules\Admin\Dashboard\Controllers;

use App\Modules\Admin\Dashboard\Classes\Base;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DashboardController extends Base
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->title = __("admin.dashboard_title_page"); // Заголовок для админки
        $this->content  = view('Admin::Dashboard.index')->with([
            'title' => $this->title
        ])->render(); // Центральная, Динамическая область проекта

        return $this->renderOutput(); // этот метод позволит нам вернуть содержимое шаблона и сохранить его в $content
    }

}
