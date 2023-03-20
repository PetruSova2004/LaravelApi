<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class ModuleMake extends Command
{

    private $files;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:module {name}
                                        {--all}
                                        {--migration}
                                        {--vue}
                                        {--view}
                                        {--controller}
                                        {--model}
                                        {--api}'; // то как команда будет вызываться в консоли

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Filesystem $filesystem)
    {
        parent::__construct();

        $this->files = $filesystem;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */

    public function handle() // при вызове в консоли $signature будет отрабатывать данный метод
    {
        if ($this->option('all')) { // если при выполнении команды в консоли мы аргументом передаем параметр all
            $this->input->setOption('migration', true); // активируем все доступные параметры
            $this->input->setOption('vue', true);
            $this->input->setOption('view', true);
            $this->input->setOption('controller', true);
            $this->input->setOption('model', true);
            $this->input->setOption('api', true);
        }

        if ($this->option('model')) {
            $this->createModel();
        }

        if ($this->option('controller')) {
            $this->createController();
        }

        if ($this->option('api')) {
            $this->createApiController();
        }

        if ($this->option('migration')) {
            $this->createMigration();
        }

        if ($this->option('vue')) {
            $this->createVueComponent();
        }

        if ($this->option('view')) {
            $this->createView();
        }

    }

    private function createModel() // создаем конкретную модель
    {
        $model = Str::singular(Str::studly(class_basename($this->argument('name')))); // берем имя модели; в argument('name') - получим имя модуля которое передаем при создании("Admin/Model")

        $this->call('make:model', [ // call() - нужен для вызова команды
            'name' => "App\\Modules\\" . trim($this->argument('name')) . "\\Models\\" . $model
        ]); // указываем где нужно создать данную модель

    }

    private function createController()
    {
        $controller = Str::studly(class_basename($this->argument('name'))); // формируем имя контроллера
        $modelName = Str::singular(Str::studly(class_basename($this->argument('name')))); // имя модели

        $path = $this->getControllerPath($this->argument('name')); // путь к создаваемого контроллера


        if ($this->alreadyExists($path)) { // если такой файл(контроллер) существует
            $this->error('Controller already exists!');
        } else {
            $this->makeDirectory($path); //

            $stub = $this->files->get(base_path('resources/stubs/controller.model.api.stub')); // получаем файл по пути

            $stub = str_replace( // меняем значения из стабах
                [
                    'DummyNamespace',
                    'DummyRootNamespace',
                    'DummyClass',
                    'DummyFullModelClass',
                    'DummyModelClass',
                    'DummyModelVariable',
                ],
                [
                    "App\\Modules\\" . trim($this->argument('name')) . "\\Controllers",
                    $this->laravel->getNamespace(),
                    $controller . 'Controller',
                    "App\\Modules\\" . trim($this->argument('name')) . "\\Models\\{$modelName}",
                    $modelName,
                    lcfirst(($modelName))
                ],
                $stub
            );

            $this->files->put($path, $stub); // в файле $path мы ложем содержимое $stub
            $this->info('Controller created successfully.');
        }

        $this->updateModularConfig(); // обновления конфигурации модуля
        $this->createRoutes($controller, $modelName);


    }

    private function updateModularConfig() { // при создании нового модуля он автоматически прописался в файле modular.php - в 'modules'
        $group = explode('\\', $this->argument('name'))[0]; // получаем из выполняемой команды имя группы
        $module = Str::studly(class_basename($this->argument('name'))); // также как выше получаем имя модуля

        $modular = $this->files->get(base_path('config/modular.php')); // получаем содержимое файла modular.php

        $matches = []; // получаем все модули группы

        preg_match("/'modules' => \[.*?'{$group}' => \[(.*?)\]/s", $modular, $matches); // 1 - шаблон рег выражения; 2 - строка где будет осуществляться поиск; 3 - куда будет записан результат; Регулярное выражения находится на 10 урок, ~5 мин.

        if(count($matches) == 2) {
            if(!preg_match("/'{$module}'/", $matches[1])) { // если не найдено совпадение $module в $matches[1]
                $parts = preg_split("/('modules' => \[.*?'{$group}' => \[)/s", $modular, 2, PREG_SPLIT_DELIM_CAPTURE); // храним разбиваемы части
                if(count($parts) == 3) {
                    $configStr = $parts[0].$parts[1]."\n            '$module',".$parts[2]; // добавление модуля
                    $this->files->put(base_path('config/modular.php'), $configStr);
                }
            }
        }

    }

    private function createApiController()
    {
        $controller = Str::studly(class_basename($this->argument('name')));

        $modelName = Str::singular(Str::studly(class_basename($this->argument('name'))));

        $path = $this->getApiControllerPath($this->argument('name'));


        if ($this->alreadyExists($path)) {
            $this->error('Controller already exists!');
        } else {
            $this->makeDirectory($path);

            $stub = $this->files->get(base_path('resources/stubs/controller.model.api.stub'));

            $stub = str_replace(
                [
                    'DummyNamespace',
                    'DummyRootNamespace',
                    'DummyClass',
                    'DummyFullModelClass',
                    'DummyModelClass',
                    'DummyModelVariable',
                ],
                [
                    "App\\Modules\\" . trim($this->argument('name')) . "\\Controllers\\Api",
                    $this->laravel->getNamespace(),
                    $controller . 'Controller',
                    "App\\Modules\\" . trim($this->argument('name')) . "\\Models\\{$modelName}",
                    $modelName,
                    lcfirst(($modelName))
                ],
                $stub
            );

            $this->files->put($path, $stub);
            $this->info('Controller created successfully.');
            $this->updateModularConfig();
        }

        $this->createApiRoutes($controller, $modelName);
    }

    private function createMigration()
    {
        $table = Str::plural(Str::snake(class_basename($this->argument('name')))); // получаем имя таблицы исходя из передаваемого аргумента

        try {
            $this->call('make:migration', [ // вызываем команду и передаем аргументы
                'name' => "create_{$table}_table", // имя миграции
                '--create' => $table, // Имя таблицы которое будет создаваться при помощи миграции
                '--path' => "App\\Modules\\" . trim($this->argument('name')) . "\\Migrations" // Указываем путь где будут храниться миграции для каждого модуля
            ]);
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }

    }

    private function createVueComponent()
    {
        $path = $this->getVueComponentPath($this->argument('name'));

        $component = Str::studly(class_basename($this->argument('name')));

        if ($this->alreadyExists($path)) {
            $this->error('Vue Component already exists!');
        } else {
            $this->makeDirectory($path);

            $stub = $this->files->get(base_path('resources/stubs/vue.component.stub'));

            $stub = str_replace(
                [
                    'DummyClass',
                ],
                [
                    $component,
                ],
                $stub
            );

            $this->files->put($path, $stub);
            $this->info('Vue Component created successfully.');
        }
    }

    private function createView() // тут будут создаваться пустые файлы для будущих видов
    {
        $paths = $this->getViewPath($this->argument('name')); // путь к шаблону будущего модуля

        foreach ($paths as $path) { // в $path получаем путь к одному определенному элементу шаблона
            $view = Str::studly(class_basename($this->argument('name')));

            if ($this->alreadyExists($path)) {
                $this->error('View already exists!');
            } else {
                $this->makeDirectory($path); // Создаем директорию(папку)

                $stub = $this->files->get(base_path('resources/stubs/view.stub'));

                $stub = str_replace(
                    [
                        '',
                    ],
                    [
                    ],
                    $stub
                );

                $this->files->put($path, $stub); // записываем содержимое в файл
                $this->info('View created successfully.');
            }
        }
    }

    protected function getVueComponentPath($name): string  // путь к компонентам Vue
    {
        return base_path('resources/js/components/' . str_replace('\\', '/', $name) . ".vue");
    }

    protected function getViewPath($name): object // получим пути для создаваемых шаблонов
    {

        $arrFiles = collect([
            'create',
            'edit',
            'index',
            'show',
        ]);

        //str_replace('\\', '/', $name)
        $paths = $arrFiles->map(function ($item) use ($name) {
            return base_path('resources/views/' . str_replace('\\', '/', $name) . '/' . $item . ".blade.php");
        });

        return $paths;
    }

    private function getControllerPath($argument) // Путь к создаваемого файлу контроллера относительно файловой системы
    {
        $controller = Str::studly(class_basename($argument)); // имя контроллера
        return $this->laravel['path'] . '/Modules/' . str_replace('\\', '/', $argument) . "/Controllers/" . "{$controller}Controller.php";

    }

    private function getApiControllerPath($name) // Путь к Апи контроллера
    {
        $controller = Str::studly(class_basename($name));
        return $this->laravel['path'] . '/Modules/' . str_replace('\\', '/', $name) . "/Controllers/Api/" . "{$controller}Controller.php";

    }

    private function makeDirectory($path)
    {
        if (!$this->files->isDirectory(dirname($path))) { // если нет такой директории тогда создаем такую
            $this->files->makeDirectory(dirname($path), 0777, true, true);
        }

        return $path;
    }

    private function createRoutes(string $controller, string $modelName): void // создаем маршруты, которые будут обрабатывать CRUD запросы
    {

        $routePath = $this->getRoutesPath($this->argument('name')); // определяем путь для формирования файла

        if ($this->alreadyExists($routePath)) {
            $this->error('Routes already exists!');
        } else {

            $this->makeDirectory($routePath);

            $stub = $this->files->get(base_path('resources/stubs/routes.web.stub')); // мы читаем конкретный стаб

            $stub = str_replace( // заменяем данные
                [
                    'DummyClass',
                    'DummyRoutePrefix',
                    'DummyModelVariable',
                ],
                [
                    $controller . 'Controller',
                    Str::plural(Str::snake(lcfirst($modelName), '-')),
                    lcfirst($modelName)
                ],
                $stub
            );

            $this->files->put($routePath, $stub);
            $this->info('Routes created successfully.');
        }
    }

    private function createApiRoutes(string $controller, string $modelName): void
    {

        $routePath = $this->getApiRoutesPath($this->argument('name')); // полный путь к файлу маршрута который будет создан и записан в папку routes конкретного модуля

        if ($this->alreadyExists($routePath)) {
            $this->error('Routes already exists!');
        } else {

            $this->makeDirectory($routePath);

            $stub = $this->files->get(base_path('resources/stubs/routes.api.stub'));

            $stub = str_replace(
                [
                    'DummyClass',
                    'DummyRoutePrefix',
                    'DummyModelVariable',
                ],
                [
                    'Api\\' . $controller . 'Controller',
                    Str::plural(Str::snake(lcfirst($modelName), '-')),
                    lcfirst($modelName)
                ],
                $stub
            );

            $this->files->put($routePath, $stub);
            $this->info('Routes created successfully.');
        }

    }

    private function getApiRoutesPath($name): string // формируем полный путь к файлу маршрута для api
    {
        return $this->laravel['path'] . '/Modules/' . str_replace('\\', '/', $name) . "/Routes/api.php";

    }

    private function getRoutesPath($name): string // формируем полный путь к файлу обычных маршрутов
    {
        return $this->laravel['path'] . '/Modules/' . str_replace('\\', '/', $name) . "/Routes/web.php";

    }

    protected function alreadyExists($path): bool // проверяем существование некого элемента по передаваемого аргументу/пути
    {
        return $this->files->exists($path);
    }

}
