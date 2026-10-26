<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    use HasFactory;

    protected $casts = [
        'menu_access' => 'array',
    ];
    protected $guarded = [];

    const ROLE_ACCESS = [
        'mastertask' => [
            'label' => 'Master Task',
            'menu' => [
                'list-mastertask' => 'Daftar Master Task',
                'manage-mastertask' => 'Manajemen Master Task',
            ],
        ],
        'task' => [
            'label' => 'Task',
            'menu' => [
                'list-task' => 'Daftar Task',
                'manage-task' => 'Manajemen Task',
            ],
        ],
        'closeperiode' => [
            'label' => 'Close Periode',
            'menu' => [
                'list-closeperiode' => 'Daftar Close Periode',
                'manage-closeperiode' => 'Manajemen Close Periode',
            ],
        ],

        'performance' => [
            'label' => 'Indikator Kinerja',
            'menu' => [
                'list-performance' => 'Daftar Indikator Kinerja',
                'manage-performance' => 'Manajemen Indikator Kinerja',
            ],
        ],
        'evaluation' => [
            'label' => 'Penilaian Kinerja',
            'menu' => [
                'list-evaluation' => 'Daftar Penilaian Kinerja',
                'manage-evaluation' => 'Manajemen Penilaian Kinerja',
            ],
        ],

        'discipline' => [
            'label' => 'Penilaian Kedisiplinan',
            'menu' => [
                'list-discipline' => 'Daftar Penilaian Kedisiplinan',
                'manage-discipline' => 'Manajemen Penilaian Kedisiplinan',
            ],
        ],

        'warningletter' => [
            'label' => 'Surat Peringatan',
            'menu' => [
                'list-warningletter' => 'Daftar Surat Peringatan',
                'manage-warningletter' => 'Manajemen Surat Peringatan',
            ],
        ],

        'evaluationresult' => [
            'label' => 'Rekap Tunjangan Kerja
            Pegawai',
            'menu' => [
                'list-evaluationresult' => 'Daftar Rekap Tunjangan Kerja Pegawai',
                'manage-evaluationresult' => 'Manajemen Rekap Tunjangan Kerja Pegawai',
            ],
        ],
        'selfevaluation' => [
            'label' => 'Rekap Tunjangan Kerja
            Anda',
            'menu' => [
                'list-selfevaluation' => 'Daftar Rekap Tunjangan Kerja Anda',
                'manage-selfevaluation' => 'Manajemen Rekap Tunjangan Kerja Anda',
            ],
        ],

        'scanqr' => [
            'label' => 'Scan QR',
            'menu' => [
                'list-scanqr' => 'Daftar Scan QR',
                'manage-scanqr' => 'Manajemen Scan QR',
            ],
        ],
        'employeeschedule' => [
            'label' => 'Employee Schedule',
            'menu' => [
                'list-employeeschedule' => 'Daftar Employee Schedule',
                'manage-employeeschedule' => 'Manajemen Employee Schedule',
            ],
        ],
        'recapcanteen' => [
            'label' => 'Rekap Canteen',
            'menu' => [
                'list-recapcanteen' => 'Daftar Rekap Canteen',
                'manage-recapcanteen' => 'Manajemen Rekap Canteen',
            ],
        ],
        'pinjamruangan' => [
            'label' => 'Peminjaman Ruangan',
            'menu' => [
                'list-pinjamruang' => 'Daftar Peminjaman Ruangan',
                'manage-pinjamruang' => 'Manajemen Peminjaman Ruangan',
            ],
        ],
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'role_id', 'id');
    }
}
