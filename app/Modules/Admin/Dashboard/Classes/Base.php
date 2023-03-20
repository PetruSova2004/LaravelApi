<?php
/**
 * Created by PhpStorm.
 * User: note
 * Date: 24.11.2020
 * Time: 22:51
 */

namespace App\Modules\Admin\Dashboard\Classes;


use App\Http\Controllers\Controller;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Lavary\Menu\Menu as Menu;

use App\Modules\Admin\Menu\Models\Menu as MenuModel;


class Base extends Controller // Base формирует определенные начальные данные
{

    protected $template;
    protected $user;
    protected $title;
    protected $content;
    protected $sidebar;
    protected $vars;
    protected $locale;

    public function __construct()
    {
        $this->template = "Admin::Dashboard.dashboard"; // Путь к основному шаблону админки

        $this->middleware(function ($request, $next) { // $next - след по цыпочке middleware
            $this->user = Auth::user();
            $this->locale = App::getLocale(); // получаем текущею локализацию('en', 'ru')
            return $next($request);
        });
    }

    protected function renderOutput()
    { // метод для формирования шаблона каждой страничке панели администратора

        $this->vars = Arr::add($this->vars, 'content', $this->content);// Массив в который доб элемент, Ключ массива, Содержимое

//        dd($this->locale);

        $menu = $this->getMenu(); // возвращаем меню для панели администратора

        $this->sidebar = view('Admin::layouts.parts.sidebar')->with([
            'menu' => $menu,
            'user' => $this->user
        ])->render(); // render() - вернет в виде строки отработанный шаблон и данная строка будет сохранена в $this->sidebar
        $this->vars = Arr::add($this->vars, 'sidebar', $this->sidebar);

        return view($this->template)->with($this->vars);
    }

    private function getMenu()
    { // возвращаем меню для панели администратора
        return (new Menu())->make('menuRenderer', function ($m) { // имя будущего меню, функция которая наполнит пустой контейнер меню пунктами меню
            foreach (MenuModel::menuByType(MenuModel::MENU_TYPE_ADMIN)->get() as $item) { // выбираем элементы нашего меню из БД по соответствующему типу
                $path = $item->path; // путь меню('dashboards.index')
//                dd($path);
                if ($path && $this->checkRoute($path)) {
                    $path = route($path); // получаем маршрут по ссылке('dashboards.index')
                }

                if ($item->parent == 0) {// $m - объект меню;
                    $m->add($item->title, $path)->id($item->id)->data('permissions',$this->getPermissions($item)); // add() - нужен, для того чтобы положить конкретную ссылку в контейнер LavaryMenu; в id() указываем идентификатор; data() - определяем привилегии для текущего пункта меню;
                } else {
                    if ($m->find($item->parent)) { // находим родительский пункт меню и добавляем новый дочерний путь
                        $m->find($item->parent)->add($item->title, $path)->id($item->id)->data('permissions',$this->getPermissions($item)); // сохраняем массив прав и привилегии которым должен обладать пользователь для доступа к соответствущему пункту меню
                    }
                }


            }
        })->filter(function ($item) { // фильтрует меню в зависимости от прав и привилегии
            if ($this->user && $this->user->canDo($item->data('permissions'))) { // Если у пользователя есть права и привилегии для выполнения для доступа к соответствущему пункту меню
                return true;
            }
            return false;
        });
    }

    private function checkRoute($path)
    {
        $routes = \Route::getRoutes()->getRoutes();
//        dd($routes);

        foreach ($routes as $route) {
            if ($route->getName() == $path) { // getName() - вернет имя текущего маршрута
                return true;
            }
        }

        return false;
    }

    private function getPermissions($item) // $item - объект модели меню
    {
        return $item->perms->map(function($item) { // map() - сформирует нам новую коллекцию псевдонимов привилегий для конкретного пункта меню
            return $item->alias;
        })->toArray();
    }

}
