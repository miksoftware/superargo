<?php

return [
    'name' => 'Superargo (Supersalud)',
    'api_url' => 'https://pqrdsuperargo.supersalud.gov.co/api/api/adres/0/',
    'delay' => env('SUPERARGO_DELAY', 500), // ms entre peticiones
    'timeout' => env('SUPERARGO_TIMEOUT', 30), // segundos
    'verify_ssl' => env('SUPERARGO_VERIFY_SSL', false),
];
