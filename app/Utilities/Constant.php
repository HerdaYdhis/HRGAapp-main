<?php

namespace App\Utilities;

class Constant
{
    const LEGAL_PERMIT = [
        'PT' => [
            'label' => 'Perseroan Terbatas',
            'value' => 'PT',
        ],
        'STARTUP' => [
            'label' => 'Startup',
            'value' => 'STARTUP',
        ],
        'SCHOOL' => [
            'label' => 'Sekolah',
            'value' => 'SCHOOL',
        ],
    ];

    const GENDER = [
        '1' => [
            'label' => 'Laki-laki',
            'value' => 'Laki-laki',
        ],
        '2' => [
            'label' => 'Perempuan',
            'value' => 'Perempuan',
        ],
    ];
    const STATUS = [
        'OPEN' => [
            'label' => 'Open',
            'value' => 'open',
        ],
        'DONE' => [
            'label' => 'Done',
            'value' => 'done',
        ],
        'CANCEL' => [
            'label' => 'Cancel',
            'value' => 'cancelled',
        ],
    ];

    const PRICE = [
        'ACTIVE' => [
            'label' => 'Active',
            'value' => 'active',
        ],
        'NON-ACTIVE' => [
            'label' => 'Non-active',
            'value' => 'non-active',
        ],
    ];

    const WORKTYPE = [
        '1' => [
            'label' => 'Daily',
            'value' => '1',
        ],
        '2' => [
            'label' => 'Monthly',
            'value' => '2',
        ],
        '3' => [
            'label' => 'Project',
            'value' => '3',
        ],

    ];

}
