<?php
return [
    'login' => env('ANTIPLAGIAT_LOGIN', 'testapi3@antiplagiat.ru'),
    'password' => env('ANTIPLAGIAT_PASSWORD', 'testapi3'),
    'company_name' => env('ANTIPLAGIAT_COMPANY_NAME', 'testapi3'),
    'url' => env('ANTIPLAGIAT_API_URL', ''),
    'email' => [
        'fileReplacedSubject' => env('ANTIPLAGIAT_EMAIL_FILEREPLACEDSUBJECT', 'Автор заменил исходный файл'),
        'subject' => env('ANTIPLAGIAT_EMAIL_SUBJECT', 'Проверка на плагиат')
    ]
];