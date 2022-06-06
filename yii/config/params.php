<?php

return [
    'adminEmail' => 'admin@example.com',
    'navigationMenu' =>
        [
            [
                'label' => 'Антиплагиат',
                'description' => 'Можно посмотреть исключения, логи и удалить отчет',
                'items' => [
                    [
                        'label' => 'Отчеты Антиплагиата',
                        'url' => ['antiplagiat-reports/index']
                    ],
                ]
            ],
            [
                'label' => 'Система',
                'description' => 'Часть панели для администрирование системы (шаблоны писем, названия и тд)',
                'items' => [
                    [
                        'label' => 'Шаблоны писем по умолчанию',
                        'url' => ['email/index']
                    ],
                    [
                        'label' => 'Системные названия',
                        'description' => 'Здесь редактируются названия используемые в системе (поля, кнопки и тд.)',
                        'url' => ['locale/index'],
                    ],
                ]
            ],
            [
                'label' => 'Пакетная обработка',
                'description' => 'Быстрая обработка данных',
                'items' => [
                    [
                        'label' => 'Копирование настроек журнала',
                        'url' => ['package/journalsettings'],
                    ]
                ]

            ],
        ],
    'company' => [
        'name' => 'OJS - Медиасфера'
    ],

];
