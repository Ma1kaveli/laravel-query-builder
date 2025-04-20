<?php

return [
    'check-types' => [
        'date-formats' => ['Y-m-d', 'd.m.Y', 'm/d/Y', 'd F Y', 'Y-m-d H:i:s'],
        'time-formats' => ['H:i', 'H:i:s', 'H:i:s.u']
    ],

    'repository' => [
        'user_id_field' => 'id',
        'is_root_field' => 'is_superadministrator',
    ],
];
