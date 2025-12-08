<!-- resources/views/partials/header.blade.php -->
@if (Auth::guest())
    <?php header('Location: ' . route('login'));
    exit(); ?>
@endif
@if (!Auth::user())
    <?php header('Location: ' . route('login'));
    exit(); ?>
@endif
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', $title ?? 'Trang chủ')</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('dist/img/icon/favicontpt.ico') }}">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}">
    <script src="https://kit.fontawesome.com/774b78ff1e.js" crossorigin="anonymous"></script>
    <!-- Tempusdominus Bootstrap 4 -->
    <link rel="stylesheet" href="{{ asset('plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('dist/css/adminlte.min.css') }}">
    <!-- Việt css -->
    <link rel="stylesheet" href="{{ asset('dist/css/css.css') }}">
    <!-- css custom note -->
    <link rel="stylesheet" href="{{ asset('dist/css/custom.css') }}">
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="{{ asset('plugins/overlayScrollbars/css/OverlayScrollbars.min.css') }}">
    <!-- jQuery -->
    <script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Thêm jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4="
        crossorigin="anonymous"></script>
    <!-- Bootstrap 4 -->
    <script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <!-- overlayScrollbars -->
    <script src="{{ asset('plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>
    <!-- AdminLTE App -->
    <script src="{{ asset('dist/js/adminlte.js') }}"></script>
    <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
    <script src="https://unpkg.com/xlsx/dist/xlsx.full.min.js"></script>
    <script src="{{ asset('dist/js/scripts.js') }}"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/vn.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script src="https://cdn.jsdelivr.net/npm/n2words@1.21.0/dist/n2words.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.16.9/xlsx.full.min.js"></script>
    <link href='https://fonts.googleapis.com/css?family=Inter' rel='stylesheet'>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

    <!-- Thêm các CSS chung cho toàn bộ trang -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <!-- Thêm Alpinejs -->
    {{-- <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script> --}}
    @livewireStyles
</head>

<body>
    <div class="wrapper">
        <!-- /.navbar -->
        <div class="header-fixed border-bottom-0">
            <!-- Main Sidebar Container -->
            <div class="d-flex align-items-center justify-content-between w-100 height-47" id="head-nav">
                <div class="logo-tpt align-baseline">
                    <img src="{{ asset('images/loto-tpp.png') }}" alt="" width="148px" height="54px">
                </div>
                <div class="d-flex content__heading--right flex-grow-1 justify-content-center">

                    <div class="dropdown">
                        <a class="text-white justify-content-center align-items-center mx-3 px-1 font-weight-600 navbar-head @if (!empty($activeGroup) && $activeGroup == 'systemFirst') active-navbar @endif"
                            href="#" role="button" data-toggle="dropdown" aria-expanded="false">
                            THIẾT LẬP
                        </a>
                        @hasanyrole('Admin|Quản lý kho|Bảo hành')
                            <div class="dropdown-menu" style="">
                                @can('admin')
                                    <a class="dropdown-item text-13" href="{{ route('groups.index') }}">Nhóm
                                        đối tượng</a>
                                @endcan
                                @hasanyrole('Admin|Quản lý kho')
                                    <a class="dropdown-item text-13" href="{{ route('customers.index') }}">Khách hàng</a>
                                @else
                                    @if (auth()->user()->id == 9)
                                        <a class="dropdown-item text-13" href="{{ route('customers.index') }}">Khách hàng</a>
                                    @endif
                                @endhasanyrole
                                <a class="dropdown-item text-13" href="{{ route('providers.index') }}">Nhà cung
                                    cấp</a>
                                <a class="dropdown-item text-13" href="{{ route('products.index') }}">Hàng hoá</a>
                                @can('admin')
                                    <a class="dropdown-item text-13" href="{{ route('users.index') }}">Nhân viên</a>
                                    <a class="dropdown-item text-13" href="{{ route('warehouses.index') }}">Kho</a>
                                @endcan
                            </div>
                        </div>
                    @endhasanyrole

                    <div class="dropdown">
                        <a class="text-white justify-content-center align-items-center mx-3 px-1 font-weight-600 navbar-head @if (!empty($activeGroup) && $activeGroup == 'manageProfess') active-navbar @endif"
                            href="#" role="button" data-toggle="dropdown" aria-expanded="false">
                            NGHIỆP VỤ
                        </a>
                        <div class="dropdown-menu" style="">
                            <a class="dropdown-item text-13" href="{{ route('imports.index') }}">Phiếu nhập hàng
                            </a>
                            @unlessrole('Bảo hành')
                                <a class="dropdown-item text-13" href="{{ route('exports.index') }}">Phiếu xuất hàng
                                </a>
                            @endunlessrole
                            <a class="dropdown-item text-13" href="{{ route('inventoryLookup.index') }}">
                                Tra cứu tồn kho
                            </a>
                            @unlessrole('Quản lý kho')
                                <a class="dropdown-item text-13" href="{{ route('warrantyLookup.index') }}">Tra cứu
                                    bảo hành
                                </a>
                                <a class="dropdown-item text-13" href="{{ route('receivings.index') }}">Phiếu tiếp
                                    nhận
                                </a>
                                <a class="dropdown-item text-13" href="{{ route('quotations.index') }}">Phiếu báo giá
                                </a>
                                <a class="dropdown-item text-13" href="{{ route('returnforms.index') }}">Phiếu trả
                                    hàng
                                </a>
                                <a class="dropdown-item text-13" href="{{ route('warehouseTransfer.index') }}">
                                    Phiếu chuyển kho
                                </a>
                            @endunlessrole
                        </div>
                    </div>
                    @hasanyrole('Admin|Kế toán')
                        <div class="dropdown">
                            <a class="text-white justify-content-center align-items-center mx-3 px-1 font-weight-600 navbar-head @if (!empty($activeGroup) && $activeGroup == 'reports') active-navbar @endif"
                                href="#" role="button" data-toggle="dropdown" aria-expanded="false">
                                BÁO CÁO
                            </a>
                            <div class="dropdown-menu" style="">
                                <a class="dropdown-item text-13" href="{{ route('reportOverview') }}">
                                    Tổng quát báo cáo
                                </a>
                                <a class="dropdown-item text-13" href="{{ route('reportExportImport') }}">
                                    Báo cáo hàng xuất nhập
                                </a>
                                <a class="dropdown-item text-13" href="{{ route('reportReceiptReturn') }}">
                                    Báo cáo hàng tiếp nhận - trả hàng
                                </a>
                                <a class="dropdown-item text-13" href="{{ route('reportQuotation') }}">
                                    Báo cáo phiếu báo giá
                                </a>
                            </div>
                        </div>
                    @endhasanyrole
                </div>
                <div class="d-flex align-items-center justify-content-end">
                    @livewire('notification-component')
                    <div class="settings-noitifi pr-4">
                        <div class="setting">
                            <svg width="26" height="26" viewBox="0 0 26 26" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M13 24.375C16.0168 24.375 18.9101 23.1766 21.0433 21.0433C23.1766 18.9101 24.375 16.0168 24.375 13C24.375 9.98316 23.1766 7.08989 21.0433 4.95666C18.9101 2.82343 16.0168 1.625 13 1.625C9.98316 1.625 7.08989 2.82343 4.95666 4.95666C2.82343 7.08989 1.625 9.98316 1.625 13C1.625 16.0168 2.82343 18.9101 4.95666 21.0433C7.08989 23.1766 9.98316 24.375 13 24.375ZM8.32162 17.6784L6.68038 19.3196C5.4304 18.0697 4.57915 16.4772 4.23426 14.7435C3.88937 13.0098 4.06634 11.2127 4.74279 9.57958C5.41923 7.94644 6.56478 6.55057 8.03455 5.56848C9.50432 4.5864 11.2323 4.06222 13 4.06222C14.7677 4.06222 16.4957 4.5864 17.9655 5.56848C19.4352 6.55057 20.5808 7.94644 21.2572 9.57958C21.9337 11.2127 22.1106 13.0098 21.7657 14.7435C21.4209 16.4772 20.5696 18.0697 19.3196 19.3196L17.6784 17.6784C17.2256 17.2255 16.688 16.8662 16.0964 16.6211C15.5047 16.376 14.8705 16.2499 14.2301 16.25H11.7699C11.1295 16.2499 10.4953 16.376 9.90364 16.6211C9.31198 16.8662 8.7744 17.2255 8.32162 17.6784Z"
                                    fill="white" />
                                <path
                                    d="M13 6.5C12.138 6.5 11.3114 6.84241 10.7019 7.4519C10.0924 8.0614 9.75 8.88805 9.75 9.75V10.5625C9.75 11.4245 10.0924 12.2511 10.7019 12.8606C11.3114 13.4701 12.138 13.8125 13 13.8125C13.862 13.8125 14.6886 13.4701 15.2981 12.8606C15.9076 12.2511 16.25 11.4245 16.25 10.5625V9.75C16.25 8.88805 15.9076 8.0614 15.2981 7.4519C14.6886 6.84241 13.862 6.5 13 6.5Z"
                                    fill="white" />
                            </svg>
                        </div>
                    </div>
                    <div id="dropdown-content" class="dropdown-content position-absolute setting-user-info">
                        @if (Route::has('login'))
                            @auth
                                <div class="email_info border-bottom" style="padding-left:16px;">
                                    {{ Auth::user()->email }}
                                </div>
                                <div class="logout_user">
                                    <a href="{{ route('profile.users.edit') }}" class="test-sm text-custom">Thông tin</a>
                                </div>
                                <div class="logout_user">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <a class="text-sm text-custom" href="#"
                                            onclick="event.preventDefault(); this.closest('form').submit();">Đăng xuất</a>
                                    </form>
                                </div>
                            @endauth
                        @endif
                    </div>
                </div>
            </div>
            <div
                class="px-3 py-2 border-bottom border-top bg-grey @if (
                    (!empty($activeGroup) && $activeGroup == 'systemFirst') ||
                        (!empty($activeGroup) && $activeGroup == 'manageProfess') ||
                        (!empty($activeGroup) && $activeGroup == 'statistic') ||
                        (!empty($activeGroup) && $activeGroup == 'reports')) d-block @else d-none @endif">
                <div class="@if (!empty($activeGroup) && $activeGroup == 'systemFirst') d-flex @else d-none @endif">
                    @can('admin')
                        <a href="{{ route('groups.index') }}" class="height-36">
                            <button type="button"
                                class="h-100 border text-dark justify-content-center align-items-center text-13 rounded bg-white ml-2  @if (!empty($activeName) && $activeName == 'groups') active @endif ">
                                Nhóm đối tượng
                            </button>
                        </a>
                    @endcan
                    @hasanyrole('Admin|Quản lý kho')
                        <a href="{{ route('customers.index') }}" class="height-36">
                            <button type="button"
                                class="h-100 border text-dark justify-content-center align-items-center text-13 rounded bg-white ml-2 @if (!empty($activeName) && $activeName == 'customers') active @endif ">
                                Khách hàng
                            </button>
                        </a>
                    @else
                        @if (auth()->user()->id == 9)
                            <a href="{{ route('customers.index') }}" class="height-36">
                                <button type="button"
                                    class="h-100 border text-dark justify-content-center align-items-center text-13 rounded bg-white ml-2 @if (!empty($activeName) && $activeName == 'customers') active @endif ">
                                    Khách hàng
                                </button>
                            </a>
                        @endif
                    @endhasanyrole
                    @hasanyrole('Admin|Quản lý kho')
                        <a href="{{ route('providers.index') }}" class="height-36">
                            <button type="button"
                                class="h-100 border text-dark justify-content-center align-items-center text-13 rounded bg-white ml-2 @if (!empty($activeName) && $activeName == 'providers') active @endif">
                                Nhà cung cấp
                            </button>
                        </a>
                    @else
                        @if (auth()->user()->id == 9)
                            <a href="{{ route('providers.index') }}" class="height-36">
                                <button type="button"
                                    class="h-100 border text-dark justify-content-center align-items-center text-13 rounded bg-white ml-2 @if (!empty($activeName) && $activeName == 'providers') active @endif">
                                    Nhà cung cấp
                                </button>
                            </a>
                        @endif
                    @endhasanyrole
                    <a href="{{ route('products.index') }}" class="height-36">
                        <button type="button"
                            class="h-100 border text-dark justify-content-center align-items-center text-13 rounded bg-white ml-2 @if (!empty($activeName) && $activeName == 'products') active @endif">
                            Hàng hóa
                        </button>
                    </a>

                    @can('admin')
                        <a href="{{ route('users.index') }}" class="height-36">
                            <button type="button"
                                class="h-100 border text-dark justify-content-center align-items-center text-13 rounded bg-white ml-2 @if (!empty($activeName) && $activeName == 'users') active @endif">
                                Nhân viên
                            </button>
                        </a>
                    @endcan
                    @hasanyrole('Admin')
                        <a href="{{ route('warehouses.index') }}" class="height-36">
                            <button type="button"
                                class="h-100 border text-dark justify-content-center align-items-center text-13 rounded bg-white ml-2 @if (!empty($activeName) && $activeName == 'warehouses') active @endif">
                                Kho
                            </button>
                        </a>
                    @endhasanyrole
                </div>
                <div class="@if (!empty($activeGroup) && $activeGroup == 'manageProfess') d-flex @else d-none @endif">
                    <a href="{{ route('imports.index') }}" class="height-36">
                        <button type="button"
                            class="h-100 border text-dark justify-content-center align-items-center text-13 rounded bg-white ml-2  @if (!empty($activeName) && $activeName == 'imports') active @endif ">
                            Phiếu nhập hàng
                        </button>
                    </a>
                    @unlessrole('Bảo hành')
                        <a href="{{ route('exports.index') }}" class="height-36">
                            <button type="button"
                                class="h-100 border text-dark justify-content-center align-items-center text-13 rounded bg-white ml-2 @if (!empty($activeName) && $activeName == 'exports') active @endif">
                                Phiếu xuất hàng
                            </button>
                        </a>
                    @endunlessrole
                    <a href="{{ route('inventoryLookup.index') }}" class="height-36">
                        <button type="button"
                            class="h-100 border text-dark justify-content-center align-items-center text-13 rounded bg-white ml-2 @if (!empty($activeName) && $activeName == 'inventoryLookup') active @endif">
                            Tra cứu tồn kho
                        </button>
                    </a>
                    @unlessrole('Quản lý kho')
                        <a href="{{ route('warrantyLookup.index') }}" class="height-36">
                            <button type="button"
                                class="h-100 border text-dark justify-content-center align-items-center text-13 rounded bg-white ml-2 @if (!empty($activeName) && $activeName == 'warrantyLookup') active @endif">
                                Tra cứu bảo hành
                            </button>
                        </a>

                        <a href="{{ route('receivings.index') }}" class="height-36">
                            <button type="button"
                                class="h-100 border text-dark justify-content-center align-items-center text-13 rounded bg-white ml-2 @if (!empty($activeName) && $activeName == 'receivings') active @endif">
                                Phiếu tiếp nhận
                            </button>
                        </a>
                        <a href="{{ route('quotations.index') }}" class="height-36">
                            <button type="button"
                                class="h-100 border text-dark justify-content-center align-items-center text-13 rounded bg-white ml-2 @if (!empty($activeName) && $activeName == 'quotations') active @endif">
                                Phiếu báo giá
                            </button>
                        </a>
                        <a href="{{ route('returnforms.index') }}" class="height-36">
                            <button type="button"
                                class="h-100 border text-dark justify-content-center align-items-center text-13 rounded bg-white ml-2 @if (!empty($activeName) && $activeName == 'returnforms') active @endif">
                                Phiếu trả hàng
                            </button>
                        </a>
                    @endunlessrole
                    <a href="{{ route('warehouseTransfer.index') }}" class="height-36">
                        <button type="button"
                            class="h-100 border text-dark justify-content-center align-items-center text-13 rounded bg-white ml-2 @if (!empty($activeName) && $activeName == 'warehouseTransfer') active @endif">
                            Phiếu chuyển kho
                        </button>
                    </a>
                </div>
                <div class="@if (!empty($activeGroup) && $activeGroup == 'reports') d-flex @else d-none @endif">
                    <a href="{{ route('reportOverview') }}" class="height-36">
                        <button type="button"
                            class="h-100 border text-dark justify-content-center align-items-center text-13 rounded bg-white ml-2  @if (!empty($activeName) && $activeName == 'overview') active @endif ">
                            Tổng quát báo cáo
                        </button>
                    </a>
                    <a href="{{ route('reportExportImport') }}" class="height-36">
                        <button type="button"
                            class="h-100 border text-dark justify-content-center align-items-center text-13 rounded bg-white ml-2 @if (!empty($activeName) && $activeName == 'export_import') active @endif">
                            Báo cáo hàng xuất nhập
                        </button>
                    </a>
                    <a href="{{ route('reportReceiptReturn') }}" class="height-36">
                        <button type="button"
                            class="h-100 border text-dark justify-content-center align-items-center text-13 rounded bg-white ml-2 @if (!empty($activeName) && $activeName == 'receipt_return') active @endif">
                            Báo cáo hàng tiếp nhận - trả hàng
                        </button>
                    </a>
                    <a href="{{ route('reportQuotation') }}" class="height-36">
                        <button type="button"
                            class="h-100 border text-dark justify-content-center align-items-center text-13 rounded bg-white ml-2 @if (!empty($activeName) && $activeName == 'quotations_report') active @endif">
                            Báo cáo phiếu báo giá
                        </button>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="alert notification d-flex justify-content-center align-items-center m-0 w-100"
        style="position: absolute;top: 0;">
        <div class="success">
            @if (Session::has('msg'))
                <div id="notification" class="alert alert-success alert-dismissible fade show" role="alert"
                    style="z-index: 999999;">
                    <div class="icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M12 4.38462C7.79374 4.38462 4.38462 7.79374 4.38462 12C4.38462 16.2063 7.79374 19.6154 12 19.6154C16.2063 19.6154 19.6154 16.2063 19.6154 12C19.6154 7.79374 16.2063 4.38462 12 4.38462ZM3 12C3 7.02903 7.02903 3 12 3C16.971 3 21 7.02903 21 12C21 16.971 16.971 21 12 21C7.02903 21 3 16.971 3 12Z"
                                fill="#ffff" />
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M16.1818 9.66432C16.4522 9.93468 16.4522 10.373 16.1818 10.6434L11.5664 15.2588C11.2961 15.5291 10.8577 15.5291 10.5874 15.2588L7.81813 12.4895C7.54777 12.2192 7.54777 11.7808 7.81813 11.5105C8.08849 11.2401 8.52684 11.2401 8.7972 11.5105L11.0769 13.7902L15.2027 9.66432C15.4731 9.39396 15.9115 9.39396 16.1818 9.66432Z"
                                fill="#ffff" />
                        </svg>
                    </div>
                    <div class="message pl-3">
                        {{ Session::get('msg') }}
                    </div>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span class="d-flex" aria-hidden="true"><svg width="24" height="24"
                                viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M18 18L6 6" stroke="white" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round" />
                                <path d="M18 6L6 18" stroke="white" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>
                        </span>
                    </button>
                </div>
            @endif
        </div>
        <div class="warning">
            @if (Session::has('warning'))
                <div id="notification" class="alert alert-warning alert-dismissible fade show m-0" role="alert"
                    style="z-index: 999999;">
                    <div class="icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M12 4.38462C7.79374 4.38462 4.38462 7.79374 4.38462 12C4.38462 16.2063 7.79374 19.6154 12 19.6154C16.2063 19.6154 19.6154 16.2063 19.6154 12C19.6154 7.79374 16.2063 4.38462 12 4.38462ZM12 21C7.02903 21 3 16.971 3 12C3 7.02903 7.02903 3 12 3C16.971 3 21 7.02903 21 12C21 16.971 16.971 21 12 21Z"
                                fill="#ffff" />
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M12 7.15384C12.3824 7.15384 12.6923 7.4638 12.6923 7.84615V12.4615C12.6923 12.8439 12.3824 13.1538 12 13.1538C11.6177 13.1538 11.3077 12.8439 11.3077 12.4615V7.84615C11.3077 7.4638 11.6177 7.15384 12 7.15384Z"
                                fill="#ffff" />
                            <circle cx="12" cy="15.6923" r="0.923077" fill="#ffff" />
                        </svg>
                    </div>
                    <div class="message pl-3">
                        {{ Session::get('warning') }}
                    </div>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span class="d-flex" aria-hidden="true"><svg width="24" height="24"
                                viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M18 18L6 6" stroke="white" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round" />
                                <path d="M18 6L6 18" stroke="white" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>
                        </span>
                    </button>
                </div>
            @endif
        </div>
    </div>
    @livewireScripts
</body>
<script>
    $('.setting').click(function() {
        $('#dropdown-content').toggleClass('show');
    });
    $(document).on('click', function(event) {
        if (!$(event.target).closest('.setting').length) {
            $('#dropdown-content').removeClass('show');
        }
    });
    $(document).ready(function() {
        // Tự động ẩn thông báo sau 3 giây
        setTimeout(function() {
            $('.alert-dismissible').fadeOut('slow', function() {
                $(this).remove(); // Xóa phần tử khỏi DOM
            });
        }, 3000);
    });
</script>
