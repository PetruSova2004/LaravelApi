<?php
return [
    'path' => base_path() . '/app/Modules', // полный путь к директории с модулями
    'base_namespace' => 'App\Modules', // базовое пространство имён для всех модулей
    'groupWithoutPrefix' => 'Pub',

    'groupMidleware' => [
        'Admin' => [ // все модули которые находятся в папке Admin будут закрыты для гостей
            'web' => ['auth'],
            'api' => ['auth:api'],
        ]
    ],

    'modules' => [ // указываем все модули которые Laravel должен обойти и считать маршруты каждого модуля
        'Admin' => [
            'TaskComment',
            'Task',
            'Analitics',
            'LeadComment',
            'Lead',
            'Sources',
            'Role',
            'Menu',
            'Dashboard',
            'User'
        ],

        'Pub' => [
            'Auth'
        ],
    ]
];
