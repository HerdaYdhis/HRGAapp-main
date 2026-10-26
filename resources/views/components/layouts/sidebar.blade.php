@php use App\Utilities\UserAccess; @endphp
<div class="sidebar-wrapper active">
    <div class="sidebar-header position-relative">
        <div class="d-flex justify-content-between align-items-center">
            <div class="logo rounded-3 pb-0 pe-2 ps-2 pt-0">
                <a href="{{ route('dashboard') }}">
                    <img src="{{ url('assets/images/logo/fav.svg') }}" alt="Logo" style=" height:35px">
                </a>
            </div>
            <div class="theme-toggle d-flex align-items-center mt-2 gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true"
                    role="img" class="iconify iconify--system-uicons" width="20" height="20"
                    preserveAspectRatio="xMidYMid meet" viewBox="0 0 21 21">
                    <g fill="none" fill-rule="evenodd" stroke="currentColor" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path
                            d="M10.5 14.5c2.219 0 4-1.763 4-3.982a4.003 4.003 0 0 0-4-4.018c-2.219 0-4 1.781-4 4c0 2.219 1.781 4 4 4zM4.136 4.136L5.55 5.55m9.9 9.9l1.414 1.414M1.5 10.5h2m14 0h2M4.135 16.863L5.55 15.45m9.899-9.9l1.414-1.415M10.5 19.5v-2m0-14v-2"
                            opacity=".3"></path>
                        <g transform="translate(-210 -1)">
                            <path d="M220.5 2.5v2m6.5.5l-1.5 1.5"></path>
                            <circle cx="220.5" cy="11.5" r="4"></circle>
                            <path d="m214 5l1.5 1.5m5 14v-2m6.5-.5l-1.5-1.5M214 18l1.5-1.5m-4-5h2m14 0h2"></path>
                        </g>
                    </g>
                </svg>
                <div class="form-check form-switch fs-6">
                    <input class="form-check-input me-0" type="checkbox" id="toggle-dark" style="cursor: pointer">
                    <label class="form-check-label"></label>
                </div>
                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true"
                    role="img" class="iconify iconify--mdi" width="20" height="20"
                    preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24">
                    <path fill="currentColor"
                        d="m17.75 4.09l-2.53 1.94l.91 3.06l-2.63-1.81l-2.63 1.81l.91-3.06l-2.53-1.94L12.44 4l1.06-3l1.06 3l3.19.09m3.5 6.91l-1.64 1.25l.59 1.98l-1.7-1.17l-1.7 1.17l.59-1.98L15.75 11l2.06-.05L18.5 9l.69 1.95l2.06.05m-2.28 4.95c.83-.08 1.72 1.1 1.19 1.85c-.32.45-.66.87-1.08 1.27C15.17 23 8.84 23 4.94 19.07c-3.91-3.9-3.91-10.24 0-14.14c.4-.4.82-.76 1.27-1.08c.75-.53 1.93.36 1.85 1.19c-.27 2.86.69 5.83 2.89 8.02a9.96 9.96 0 0 0 8.02 2.89m-1.64 2.02a12.08 12.08 0 0 1-7.8-3.47c-2.17-2.19-3.33-5-3.49-7.82c-2.81 3.14-2.7 7.96.31 10.98c3.02 3.01 7.84 3.12 10.98.31Z">
                    </path>
                </svg>
            </div>
            <div class="sidebar-toggler x">
                <a href="#" class="sidebar-hide d-xl-none d-block"><i class="bi bi-x bi-middle"></i></a>
            </div>
        </div>
    </div>
    <div class="sidebar-menu">
        <ul class="menu">
            <li class="sidebar-title">Menu</li>
            <li class="sidebar-item {{ request()->segment(1) == 'dashboard' ? 'active' : '' }}">
                <a href="{{ route('dashboard') }}" class='sidebar-link'>
                    <i class="bi bi-house-door-fill"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            @if (UserAccess::parent('mastertask') || auth()->user()->is_superadmin)
                @php
                    $isHokiActive = in_array(request()->segment(1), ['master-task', 'task', 'close-periode']);
                @endphp
                <li
                    class="sidebar-item {{ $isHokiActive ? 'active' : '' }} has-sub">
                    <a href="#" class='sidebar-link'>
                        <i class="bi bi-clipboard-check-fill"></i>
                        <span>Hoki</span>
                    </a>
                    <ul class="submenu {{ $isHokiActive ? 'active' : '' }}" style="{{ $isHokiActive ? 'display: block;' : '' }}">
                        <li class="submenu-item {{ request()->segment(1) == 'master-task' ? 'active' : '' }}">
                            <a href="{{ route('master-task') }}" class="submenu-link">Routine Task</a>
                        </li>

                        @if (UserAccess::parent('task') || auth()->user()->is_superadmin)
                            <li class="submenu-item {{ request()->segment(1) == 'task' ? 'active' : '' }}">
                                <a href="{{ route('task') }}" class="submenu-link">Task</a>
                            </li>
                        @endif
                        @if (UserAccess::parent('closeperiode') || auth()->user()->is_superadmin)
                            <li class="submenu-item {{ request()->segment(1) == 'close-periode' ? 'active' : '' }}">
                                <a href="{{ route('close-periode') }}" class="submenu-link">Close Periode</a>
                            </li>
                        @endif
                    </ul>
                </li>
            @endif

            @if (UserAccess::parent('scanqr') || auth()->user()->is_superadmin)
                @php
                    $isCanteenActive = in_array(request()->segment(1), ['canteen', 'canteen-fingerprint', 'fingerprint-device', 'canteenresult', 'schedule', 'canteenpage']);
                @endphp
                <li
                    class="sidebar-item {{ $isCanteenActive ? 'active' : '' }} has-sub">
                    <a href="#" class='sidebar-link'>
                        <i class="bi bi-cup-hot-fill"></i>
                        <span>Canteen</span>
                    </a>
                    <ul class="submenu {{ $isCanteenActive ? 'active' : '' }}" style="{{ $isCanteenActive ? 'display: block;' : '' }}">
                        <li class="submenu-item {{ request()->segment(1) == 'canteen' ? 'active' : '' }}">
                            <a href="{{ route('canteen') }}" class="submenu-link">Scan QR</a>
                        </li>

                        @if (UserAccess::parent('scanfingerprint') || auth()->user()->is_superadmin)
                            <li class="submenu-item {{ request()->segment(1) == 'canteen-fingerprint' ? 'active' : '' }}">
                                <a href="{{ route('scan-fingerprint') }}" class="submenu-link">Scan Fingerprint</a>
                            </li>
                        @endif

                        @if (UserAccess::parent('employeeschedule') || auth()->user()->is_superadmin)
                            <li class="submenu-item {{ request()->segment(1) == 'canteenresult' ? 'active' : '' }}">
                                <a href="{{ route('canteenresult') }}" class="submenu-link">Scann Result</a>
                            </li>
                            <li class="submenu-item {{ request()->segment(1) == 'schedule' ? 'active' : '' }}">
                                <a href="{{ route('employeeschedule') }}" class="submenu-link">Employee Schedule</a>
                            </li>
                        @endif

                        @if (UserAccess::parent('recapcanteen') || auth()->user()->is_superadmin)
                            <li class="submenu-item {{ request()->segment(1) == 'canteenpage' ? 'active' : '' }}">
                                <a href="{{ route('canteenpage') }}" class="submenu-link">Rekap Canteen</a>
                            </li>
                        @endif

                        @if (auth()->user()->is_superadmin)
                            <li class="submenu-item {{ request()->segment(1) == 'fingerprint-device' ? 'active' : '' }}">
                                <a href="{{ route('fingerprint-device') }}" class="submenu-link">
                                    <i class="bi bi-gear-fill me-1 text-muted" style="font-size:.75rem;"></i>Pengaturan Fingerprint
                                </a>
                            </li>
                        @endif
                    </ul>

                </li>
            @endif


            @if (UserAccess::parent('evaluation') || auth()->user()->is_superadmin)
                @php
                    $isEvaluationActive = in_array(request()->segment(1), ['evaluation', 'evaluation-result', 'warning-letter', 'discipline', 'master-kinerja', 'self-evaluation']);
                @endphp
                <li
                    class="sidebar-item {{ $isEvaluationActive ? 'active' : '' }} has-sub">
                    <a href="#" class='sidebar-link'>
                        <i class="bi bi-file-earmark-person"></i>
                        <span>Penilaian Kinerja</span>
                    </a>
                    <ul class="submenu {{ $isEvaluationActive ? 'active' : '' }}" style="{{ $isEvaluationActive ? 'display: block;' : '' }}">

                        @if (UserAccess::parent('performance') || auth()->user()->is_superadmin)
                            <li class="submenu-item {{ request()->segment(1) == 'master-kinerja' ? 'active' : '' }}">
                                <a href="{{ route('performance') }}" class="submenu-link">List Kinerja</a>
                            </li>
                        @endif


                        <li class="submenu-item {{ request()->segment(1) == 'evaluation' ? 'active' : '' }}">
                            <a href="{{ route('evaluation') }}" class="submenu-link">Penilaian
                                Kinerja</a>
                        </li>

                        @if (UserAccess::parent('discipline') || auth()->user()->is_superadmin)
                            <li class="submenu-item {{ request()->segment(1) == 'discipline' ? 'active' : '' }}">
                                <a href="{{ route('discipline') }}" class="submenu-link">Penilaian
                                    Kedisiplinan</a>
                            </li>
                        @endif
                        @if (UserAccess::parent('warningletter') || auth()->user()->is_superadmin)
                            <li class="submenu-item {{ request()->segment(1) == 'warning-letter' ? 'active' : '' }}">
                                <a href="{{ route('warning-letter') }}" class="submenu-link">Surat
                                    Peringatan</a>
                            </li>
                        @endif
                        @if (UserAccess::parent('evaluationresult') || auth()->user()->is_superadmin)
                            <li class="submenu-item {{ request()->segment(1) == 'evaluation-result' ? 'active' : '' }}">
                                <a href="{{ route('evaluation-result') }}" class="submenu-link">Rekap
                                    Tunjangan Kerja
                                    Pegawai</a>
                            </li>
                        @endif
                        @if (UserAccess::parent('selfevaluation') || auth()->user()->is_superadmin)
                            <li class="submenu-item {{ request()->segment(1) == 'self-evaluation' ? 'active' : '' }}">
                                <a href="{{ route('self-evaluation') }}" class="submenu-link">Rekap
                                    Tunjangan Kerja Anda</a>
                            </li>
                        @endif
                    </ul>
                </li>
            @endif

            @if (UserAccess::parent('pinjamruangan') || auth()->user()->is_superadmin)
                <li class="sidebar-item {{ request()->segment(1) === 'borrowingroom' ? 'active' : '' }}">
                    <a href="{{ route('pinjamruangan') }}" class='sidebar-link'>
                        <i class="bi bi-house-door-fill"></i>
                        <span>Pinjam Ruangan</span>
                    </a>
                </li>
            @endif



            {{-- @if (UserAccess::parent('profilsaya') || auth()->user()->is_superadmin) --}}
            <li class="sidebar-item {{ request()->segment(1) === 'profilsaya' ? 'active' : '' }}">
                <a href="{{ route('profilsaya') }}" class='sidebar-link'>
                    <i class="bi bi-person-lines-fill"></i>
                    <span>My Profile</span>
                </a>
            </li>
            {{-- @endif --}}
            @if (auth()->user()->is_superadmin)
                <li class="sidebar-title">User Management</li>

                @php
                    $isMasterActive = in_array(request()->segment(1), ['department', 'user-level', 'cost-center', 'coa', 'mealshift', 'branch', 'status-employee', 'price', 'ruangan']);
                @endphp
                <li
                    class="sidebar-item {{ $isMasterActive ? 'active' : '' }} has-sub">
                    <a href="#" class='sidebar-link'>
                        <i class="bi bi-boxes"></i>
                        <span>Master Data</span>
                    </a>
                    <ul class="submenu {{ $isMasterActive ? 'active' : '' }}" style="{{ $isMasterActive ? 'display: block;' : '' }}">
                        <li class="submenu-item {{ request()->segment(1) == 'department' ? 'active' : '' }}">
                            <a href="{{ route('department') }}" class="submenu-link">Department</a>
                        </li>

                        <li class="submenu-item {{ request()->segment(1) == 'user-level' ? 'active' : '' }}">
                            <a href="{{ route('user-level') }}" class="submenu-link">User Level</a>
                        </li>
                        <li class="submenu-item {{ request()->segment(1) == 'cost-center' ? 'active' : '' }}">
                            <a href="{{ route('cost-center') }}" class="submenu-link">Cost Center</a>
                        </li>
                        <li class="submenu-item {{ request()->segment(1) == 'coa' ? 'active' : '' }}">
                            <a href="{{ route('coa') }}" class="submenu-link">COA</a>
                        </li>
                        <li class="submenu-item {{ request()->segment(1) == 'mealshift' ? 'active' : '' }}">
                            <a href="{{ route('mealshift') }}" class="submenu-link">Meal Shift</a>
                        </li>
                        <li class="submenu-item {{ request()->segment(1) == 'branch' ? 'active' : '' }}">
                            <a href="{{ route('branch') }}" class="submenu-link">Branch</a>
                        </li>
                        <li class="submenu-item {{ request()->segment(1) == 'status-employee' ? 'active' : '' }}">
                            <a href="{{ route('status-employee') }}" class="submenu-link">Status Employee</a>
                        </li>
                        <li class="submenu-item {{ request()->segment(1) == 'price' ? 'active' : '' }}">
                            <a href="{{ route('price') }}" class="submenu-link">Price</a>
                        </li>
                        <li class="submenu-item {{ request()->segment(1) == 'ruangan' ? 'active' : '' }}">
                            <a href="{{ route('ruangan') }}" class="submenu-link">Ruangan</a>
                        </li>
                    </ul>
                </li>

                <li class="sidebar-item {{ request()->segment(1) === 'role' ? 'active' : '' }}">
                    <a href="{{ route('role') }}" class='sidebar-link'>
                        <i class="bi bi-universal-access"></i>
                        <span>Hak Akses</span>
                    </a>
                </li>
                <li class="sidebar-item {{ request()->segment(1) === 'user' ? 'active' : '' }}">
                    <a href="{{ route('user') }}" class='sidebar-link'>
                        <i class="bi bi-people-fill"></i>
                        <span>Pengguna</span>
                    </a>
                </li>
            @endif
            <li class="sidebar-item">
                <a href="{{ route('logout') }}" class='sidebar-link'
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="bi bi-box-arrow-right"></i>
                    <span>Keluar</span>
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </li>
        </ul>
        <ul class="menu">
            <li class="sidebar-item opacity-75">
                <img src="{{ url('assets/images/logo/hrgais.svg') }}" alt="Logo" style=" height:100%">
            </li>
        </ul>

    </div>
</div>
