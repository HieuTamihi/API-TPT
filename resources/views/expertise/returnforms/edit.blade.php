@include('partials.header', ['activeGroup' => 'manageProfess', 'activeName' => 'returnforms'])
@section('title', $title)
<form id="form-submit" action="{{ route('returnforms.update', $returnForm->id) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="content-wrapper--2Column m-0 min-height--none">
        <div class="content-header-fixed-report-1 pt-2">
            <div class="content__header--inner pl-4">
                <div class="content__heading--left d-flex opacity-1">
                    <div class="d-flex mb-2 mr-2 p-1 border rounded" style="order: 0;">
                        <span class="text text-13-black m-0" style="flex: 2;">Chọn phiếu tiếp nhận :</span>
                        <div class="form-check form-check-inline mr-1">
                            <input type="hidden" name="reception_id" value="{{ $returnForm->reception_id }}">
                            <select class="form-check-input border-0 text text-13-black" name="reception" required
                                disabled id="reception">
                                <option value="">Chưa chọn phiếu</option>
                                @foreach ($receivings as $item)
                                    <option value="{{ $item->id }}"
                                        {{ $returnForm->reception_id == $item->id ? 'selected' : '' }}>
                                        {{ $item->form_code_receiving }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="d-flex mb-2 mr-2 p-1 border rounded" style="order: 0;">
                        <span class="text text-13-black m-0" style="flex: 2;">Loại phiếu :</span>
                        <div class="form-check form-check-inline mr-1">
                            <select class="form-check-input border-0 text text-13-black select-nodropdown"
                                name="status" id="status" disabled>
                                <option value="0">Chưa chọn</option>
                                <option value="1" {{ $returnForm->reception->form_type == 1 ? 'selected' : '' }}>
                                    Bảo hành</option>
                                <option value="2" {{ $returnForm->reception->form_type == 2 ? 'selected' : '' }}>
                                    Dịch
                                    vụ</option>
                                <option value="3" {{ $returnForm->reception->form_type == 3 ? 'selected' : '' }}>
                                    Bảo
                                    hành dịch vụ</option>
                            </select>
                        </div>
                    </div>
                    <div class="d-flex mb-2 mr-2 p-1 border rounded" style="order: 0;">
                        <span class="text text-13-black m-0" style="flex: 2;">Trạng thái :</span>
                        <div class="form-check form-check-inline mr-1">
                            <select class="form-check-input border-0 text text-13-black" name="status" required
                                id="status">
                                <option value="1" {{ $returnForm->status == 1 ? 'selected' : '' }}>Hoàn
                                    thành</option>
                                <option value="2" {{ $returnForm->status == 2 ? 'selected' : '' }}>Không đồng ý
                                </option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="d-flex content__heading--right">
                    <div class="row m-0">
                        <a href="{{ route('returnforms.index') }}">
                            <button type="button" class="btn-destroy btn-light mx-1 d-flex align-items-center h-100">
                                <svg class="mx-1" width="16" height="16" viewBox="0 0 16 16" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M8 15C11.866 15 15 11.866 15 8C15 4.13401 11.866 1 8 1C4.13401 1 1 4.13401 1 8C1 11.866 4.13401 15 8 15ZM6.03033 4.96967C5.73744 4.67678 5.26256 4.67678 4.96967 4.96967C4.67678 5.26256 4.67678 5.73744 4.96967 6.03033L6.93934 8L4.96967 9.96967C4.67678 10.2626 4.67678 10.7374 4.96967 11.0303C5.26256 11.3232 5.73744 11.3232 6.03033 11.0303L8 9.06066L9.96967 11.0303C10.2626 11.3232 10.7374 11.3232 11.0303 11.0303C11.3232 10.7374 11.3232 10.2626 11.0303 9.96967L9.06066 8L11.0303 6.03033C11.3232 5.73744 11.3232 5.26256 11.0303 4.96967C10.7374 4.67678 10.2626 4.67678 9.96967 4.96967L8 6.93934L6.03033 4.96967Z"
                                        fill="black" />
                                </svg>
                                <p class="m-0 p-0 text-dark">Hủy</p>
                            </button>
                        </a>
                        <button type="button" id="printButton"
                            class="btn-destroy btn-light mx-1 d-flex align-items-center h-100">
                            <svg class="mx-1" xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                viewBox="0 0 16 16" fill="none">
                                <g clip-path="url(#clip0_10424_110572)">
                                    <path opacity="0.995" fill-rule="evenodd" clip-rule="evenodd"
                                        d="M4.73438 -0.015625C6.90103 -0.015625 9.06772 -0.015625 11.2344 -0.015625C12.0674 0.148911 12.6664 0.617662 13.0312 1.39062C13.1025 1.57308 13.1546 1.76058 13.1875 1.95312C13.2031 2.64055 13.2083 3.32803 13.2031 4.01562C14.6799 3.91059 15.607 4.56684 15.9844 5.98438C15.9844 7.48438 15.9844 8.98438 15.9844 10.4844C15.607 11.9019 14.6799 12.5582 13.2031 12.4531C13.2177 13.3089 13.202 14.1631 13.1562 15.0156C12.9704 15.5194 12.6214 15.8423 12.1094 15.9844C9.35938 15.9844 6.60938 15.9844 3.85938 15.9844C3.34734 15.8423 2.99837 15.5194 2.8125 15.0156C2.76671 14.1631 2.75108 13.3089 2.76562 12.4531C1.30224 12.5609 0.375159 11.9151 -0.015625 10.5156C-0.015625 9.00522 -0.015625 7.49478 -0.015625 5.98438C0.3611 4.56772 1.28818 3.91147 2.76562 4.01562C2.76042 3.32803 2.76562 2.64055 2.78125 1.95312C3.01084 0.874588 3.66188 0.218337 4.73438 -0.015625ZM5.07812 0.921875C7.01566 0.916666 8.95316 0.921875 10.8906 0.9375C11.7125 1.04064 12.1657 1.50418 12.25 2.32812C12.2656 2.89053 12.2708 3.45303 12.2656 4.01562C9.41147 4.01562 6.55728 4.01562 3.70312 4.01562C3.69791 3.45303 3.70312 2.89053 3.71875 2.32812C3.80872 1.50383 4.26184 1.03508 5.07812 0.921875ZM2.29688 4.95312C6.08856 4.94791 9.88022 4.95312 13.6719 4.96875C14.3707 5.03219 14.8134 5.40197 15 6.07812C15.0349 6.79609 15.0505 7.51484 15.0469 8.23438C15.0505 8.95391 15.0349 9.67266 15 10.3906C14.849 10.9583 14.4896 11.3177 13.9219 11.4688C13.6836 11.5044 13.444 11.5201 13.2031 11.5156C13.2031 10.9948 13.2031 10.474 13.2031 9.95312C13.3597 9.95831 13.516 9.95309 13.6719 9.9375C13.9348 9.80163 14.0234 9.58809 13.9375 9.29688C13.8906 9.16666 13.8021 9.07812 13.6719 9.03125C9.88022 9.01041 6.08853 9.01041 2.29688 9.03125C2.01642 9.19225 1.93829 9.42663 2.0625 9.73438C2.11765 9.82728 2.19578 9.895 2.29688 9.9375C2.45278 9.95309 2.60903 9.95831 2.76562 9.95312C2.76562 10.474 2.76562 10.9948 2.76562 11.5156C2.09793 11.6042 1.56147 11.3854 1.15625 10.8594C1.01539 10.6293 0.942475 10.3793 0.9375 10.1094C0.916666 8.85938 0.916666 7.60938 0.9375 6.35938C1.02747 5.53509 1.4806 5.06634 2.29688 4.95312ZM3.70312 9.95312C6.55728 9.95312 9.41147 9.95312 12.2656 9.95312C12.2708 11.4948 12.2656 13.0365 12.25 14.5781C12.224 14.8542 12.0729 15.0052 11.7969 15.0312C9.25522 15.0521 6.71353 15.0521 4.17188 15.0312C3.89584 15.0052 3.74478 14.8542 3.71875 14.5781C3.70312 13.0365 3.69791 11.4948 3.70312 9.95312Z"
                                        fill="#151516" />
                                    <path opacity="0.987" fill-rule="evenodd" clip-rule="evenodd"
                                        d="M2.29742 6.01563C2.92251 6.01041 3.54752 6.01563 4.17242 6.03125C4.43539 6.16713 4.52392 6.38066 4.43805 6.67188C4.39117 6.8021 4.30264 6.89063 4.17242 6.9375C3.54742 6.95835 2.92242 6.95835 2.29742 6.9375C2.01696 6.7765 1.93884 6.54213 2.06305 6.23438C2.12601 6.14338 2.20413 6.07047 2.29742 6.01563Z"
                                        fill="#6C6F74" />
                                    <path opacity="0.991" fill-rule="evenodd" clip-rule="evenodd"
                                        d="M6.54743 11.0156C7.5058 11.0104 8.46415 11.0156 9.42243 11.0313C9.6854 11.1671 9.77393 11.3807 9.68805 11.6719C9.64118 11.8021 9.55265 11.8906 9.42243 11.9375C8.46408 11.9583 7.50577 11.9583 6.54743 11.9375C6.26696 11.7765 6.18883 11.5421 6.31305 11.2344C6.37602 11.1434 6.45415 11.0705 6.54743 11.0156Z"
                                        fill="#6C6F74" />
                                    <path opacity="0.991" fill-rule="evenodd" clip-rule="evenodd"
                                        d="M6.54743 13.0156C7.5058 13.0104 8.46415 13.0156 9.42243 13.0313C9.6854 13.1671 9.77393 13.3807 9.68805 13.6719C9.64118 13.8021 9.55265 13.8906 9.42243 13.9375C8.46408 13.9583 7.50577 13.9583 6.54743 13.9375C6.26696 13.7765 6.18883 13.5421 6.31305 13.2344C6.37602 13.1434 6.45415 13.0705 6.54743 13.0156Z"
                                        fill="#6C6F74" />
                                </g>
                                <defs>
                                    <clipPath id="clip0_10424_110572">
                                        <rect width="16" height="16" fill="white" />
                                    </clipPath>
                                </defs>
                            </svg>
                            <p class="m-0 p-0 text-dark">In phiếu</p>
                        </button>
                        @can('admin')
                            <button type="submit" class="custom-btn d-flex align-items-center h-100 mx-1 mr-4"
                                id="btn-get-unique-products">
                                <svg class="mx-1" xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                    viewBox="0 0 16 16" fill="none">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M8 15C11.866 15 15 11.866 15 8C15 4.13401 11.866 1 8 1C4.13401 1 1 4.13401 1 8C1 11.866 4.13401 15 8 15ZM11.7836 6.42901C12.0858 6.08709 12.0695 5.55006 11.7472 5.22952C11.4248 4.90897 10.9186 4.9263 10.6164 5.26821L7.14921 9.19122L5.3315 7.4773C5.00127 7.16593 4.49561 7.19748 4.20208 7.54777C3.90855 7.89806 3.93829 8.43445 4.26852 8.74581L6.28032 10.6427C6.82041 11.152 7.64463 11.1122 8.13886 10.553L11.7836 6.42901Z"
                                        fill="white" />
                                </svg>
                                <p class="m-0 p-0">Xác nhận</p>
                            </button>
                        @endcan
                        <button id="sideGuest" type="button" class="btn-option border-0 mx-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                viewBox="0 0 24 24" fill="none">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M6.375 3C3.68262 3 1.5 5.18262 1.5 7.875V16.1248C1.5 18.8173 3.68262 20.9998 6.375 20.9998H17.625C20.3174 20.9998 22.5 18.8173 22.5 16.1248V7.875C22.5 5.18262 20.3174 3 17.625 3H6.375ZM3.75 15.7498C3.75 17.4067 5.09314 18.7498 6.75 18.7498H17.625C19.0748 18.7498 20.25 17.5746 20.25 16.1248V7.875C20.25 6.42527 19.0748 5.25 17.625 5.25H6.75C5.09314 5.25 3.75 6.59314 3.75 8.25V15.7498Z"
                                    fill="#151516" />
                                <path d="M15.75 4.5H13.5V19.5H15.75V4.5Z" fill="#151516" />
                                <path d="M21 4.5H15V19.5H21V12.5V4.5Z" fill="#151516" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="content-wrapper2 px-0 py-0 margin-top-118">
            <div class="" id="main">
                {{-- Thông tin khách hàng --}}
                <div class="border">
                    <div class="info-form">
                        <div class="bg-filter-search border-0 text-center">
                            <p class="font-weight-bold text-uppercase info-chung--heading text-center">
                                THÔNG TIN PHIẾU TIẾP NHẬN
                            </p>
                        </div>
                        <div class="d-flex w-100">
                            <div
                                class="d-flex w-100 justify-content-between py-2 px-3 border border-bottom-0 border-right-0 align-items-center text-left text-nowrap position-relative height-44">
                                <span class="text-13-black text-nowrap mr-3 required-label" style="width: 180px;">Mã
                                    phiếu</span>
                                <input type="text" id="return_code" name="return_code" style="flex:2;"
                                    placeholder="Nhập thông tin" value="{{ $returnForm->return_code }}"
                                    class="text-13-black w-50 border-0 bg-input-guest date_picker bg-input-guest-blue py-2 px-2">
                            </div>
                            <div
                                class="d-flex w-100 justify-content-between py-2 px-3 border border-bottom-0 border-right-0 align-items-center text-left text-nowrap position-relative height-44">
                                <span class="text-13-black btn-click required-label font-weight-bold"
                                    style="width: 180px;">Khách
                                    hàng</span>
                                <input placeholder="Nhập thông tin" autocomplete="off" required id="customer_name"
                                    class="text-13-black w-100 border-0 bg-input-guest bg-input-guest-blue py-2 px-2"
                                    value="{{ $returnForm->customer->customer_name }}" style="flex:2;" />
                                <input type="hidden" name="customer_id" id="customer_id"
                                    value="{{ $returnForm->customer_id }}">
                                <div class="">
                                    <div id="listCustomer"
                                        class="bg-white position-absolute rounded list-guest shadow p-1 z-index-block"
                                        style="z-index: 99;display: none;">
                                        <div class="p-1">
                                            <div class="position-relative">
                                                <input type="text" placeholder="Nhập thông tin"
                                                    class="pr-4 w-100 input-search bg-input-guest"
                                                    id="searchCustomer">
                                                <span id="search-icon" class="search-icon">
                                                    <i class="fas fa-search text-table" aria-hidden="true"></i>
                                                </span>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <div
                                class="d-flex w-100 justify-content-between py-2 px-3 border border-bottom-0 border-right-0 align-items-center text-left text-nowrap position-relative height-44">
                                <span class="text-13-black text-nowrap mr-3" style="width: 180px;">Người lập
                                    phiếu</span>
                                <input type="hidden" name="user_id" value="{{ $returnForm->user_id }}">
                                <input class="text-13-black w-50 border-0 bg-input-guest py-2 px-2" autocomplete="off"
                                    placeholder="Nhập thông tin" style="flex:2;" name=""
                                    value="{{ Auth::user()->name }}" readonly />
                            </div>
                        </div>
                        <div class="d-flex w-100">
                            <div
                                class="d-flex w-100 justify-content-between py-2 px-3 border border-bottom-0 border-right-0 align-items-center text-left text-nowrap position-relative height-44">
                                <span class="text-13-black text-nowrap mr-3 required-label" style="width: 180px;">Ngày
                                    lập
                                    phiếu</span>
                                <input placeholder="Nhập thông tin" autocomplete="off" type="text"
                                    class="text-13-black w-50 border-0 bg-input-guest bg-input-guest-blue py-2 px-2"
                                    id="dateCreate" style="flex:2;"
                                    value="{{ date_format(new DateTime($returnForm->date_created), 'd/m/Y') }}" />
                                <input type="hidden" value="{{ $returnForm->date_created }}" name="date_created"
                                    id="hiddenDateCreate">
                            </div>
                            <div
                                class="d-flex w-100 justify-content-between py-2 px-3 border border-bottom-0 border-right-0 align-items-center text-left text-nowrap position-relative height-44">
                                <span class="text-13-black btn-click" style="width: 180px;"> Người liên hệ </span>
                                <input name="contact_person" placeholder="Nhập thông tin" autocomplete="off"
                                    class="text-13-black w-100 border-0 bg-input-guest bg-input-guest-blue py-2 px-2"
                                    id="contact_person" value="{{ $returnForm->contact_person }}" style="flex:2;" />
                            </div>
                            <div
                                class="d-flex w-100 justify-content-between py-2 px-3 border border-bottom-0 border-right-0 align-items-center text-left text-nowrap position-relative height-44">
                                <span class="text-13-black text-nowrap mr-3" style="width: 180px;">SĐT liên hệ</span>
                                <input class="text-13-black w-50 border-0 bg-input-guest bg-input-guest-blue py-2 px-2"
                                    autocomplete="off" placeholder="Nhập thông tin" style="flex:2;"
                                    name="phone_number" id="phone_number" value="{{ $returnForm->phone_number }}" />
                            </div>

                        </div>
                        <div class="d-flex w-100">
                            <div
                                class="d-flex w-100 justify-content-between py-2 px-3 border border-bottom-0 border-right-0 align-items-center text-left text-nowrap position-relative height-44">
                                <span class="text-13-black text-nowrap mr-3" style="width:100px;">Địa chỉ</span>
                                <input placeholder="Nhập thông tin" name="address"
                                    value="{{ $returnForm->address }}" id="address"
                                    class="text-13-black w-50 border-0 bg-input-guest bg-input-guest-blue py-2 px-2"style="flex:2;" />
                            </div>
                            <div
                                class="d-flex w-100 justify-content-between py-2 px-3 border border-bottom-0 border-right-0 align-items-center text-left text-nowrap position-relative height-44">
                            </div>
                            <div
                                class="d-flex w-100 justify-content-between py-2 px-3 border border-bottom-0 border-right-0 align-items-center text-left text-nowrap position-relative height-44">
                                <span class="text-13-black text-nowrap mr-3" style="width: 180px;">Phương thức trả
                                    hàng</span>
                                <select
                                    class="text-13-black w-50 border-0 addr bg-input-guest addr bg-input-guest-blue py-2 px-2"
                                    style="flex:2;" name="return_method" id="return_method">
                                    <option value="1" {{ $returnForm->return_method == 1 ? 'selected' : '' }}>
                                        Khách
                                        nhận trực tiếp</option>
                                    <option value="2" {{ $returnForm->return_method == 2 ? 'selected' : '' }}>
                                        Chuyển
                                        phát nhanh</option>
                                    <option value="3" {{ $returnForm->return_method == 3 ? 'selected' : '' }}>Gửi
                                        chành xe</option>
                                </select>
                            </div>
                        </div>
                        <div class="d-flex w-100">
                            <div
                                class="d-flex w-100 justify-content-between py-2 px-3 border border-bottom-0 border-right-0 align-items-center text-left text-nowrap position-relative height-44">
                                <span class="text-13-black text-nowrap mr-3" style="width: 100px;">Ghi chú</span>
                                <input name="notes" placeholder="Nhập thông tin" autocomplete="off"
                                    value="{{ $returnForm->notes }}"
                                    class="text-13-black w-50 border-0 addr bg-input-guest addr bg-input-guest-blue py-2 px-2"style="flex:2;" />
                            </div>
                            <div
                                class="d-flex w-100 justify-content-between py-2 px-3 border border-bottom-0 border-right-0 align-items-center text-left text-nowrap position-relative height-44">
                            </div>
                            <div
                                class="d-flex w-100 justify-content-between py-2 px-3 border border-bottom-0 border-right-0 align-items-center text-left text-nowrap position-relative height-44">
                            </div>
                        </div>
                    </div>
                </div>
                @if ($errors->any())
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                @endif
                {{-- Thông tin sản phẩm --}}
                <div class="content">
                    <div id="title--fixed" class="bg-filter-search text-center border-0">
                        <p class="font-weight-bold text-uppercase info-chung--heading text-center">THÔNG TIN HÀNG
                            HOÁ/DỊCH
                            VỤ
                        </p>
                    </div>
                    <div class="container-fluided">
                        <section class="content" style="overflow-x:visible">
                            <table class="table" id="inputcontent">
                                @php
                                    $hideReplacement =
                                        $returnForm->reception->form_type == 2 || $returnForm->reception->form_type == 3
                                            ? 'd-none'
                                            : '';
                                    $hideExtraWarranty =
                                        $returnForm->reception->form_type == 1 || $returnForm->reception->form_type == 3
                                            ? 'd-none'
                                            : '';
                                    $groupedReturns = $returnForm->productReturns->groupBy(function ($item) {
                                        return $item->product_id .
                                            '-' .
                                            $item->serial_number_id .
                                            '-' .
                                            $item->replacement_code;
                                    });
                                @endphp
                                <thead>
                                    <tr style="height:44px;">
                                        <th class="border-right px-2 p-0 pl-4" style="width:8%;">
                                            <span class="text-table text-secondary">Mã hàng</span>
                                        </th>
                                        <th class="border-right px-2 p-0 text-left" style="width:25%;">
                                            <span class="text-table text-secondary">Tên Hàng</span>
                                        </th>
                                        <th class="border-right px-2 p-0 text-left" style="width:10%;">
                                            <span class="text-table text-secondary">Hãng</span>
                                        </th>
                                        <th class="border-right px-2 p-0 text-right" style="width:7%;">
                                            <span class="text-table text-secondary">Số lượng</span>
                                        </th>
                                        <th class="border-right px-2 p-0 text-right" style="width:10%;">
                                            <span class="text-table text-secondary">Serial Number</span>
                                        </th>
                                        <th class="border-right note px-2 p-0 text-left col-replacement-code {{ $hideReplacement }}"
                                            style="width:10%;">
                                            <span class="text-table text-secondary">Mã hàng đổi</span>
                                        </th>
                                        <th class="border-right note px-2 p-0 text-left col-replacement-serial {{ $hideReplacement }}"
                                            style="width:10%;">
                                            <span class="text-table text-secondary">Serial Number đổi</span>
                                        </th>
                                        <th class="border-right note px-2 p-0 text-left col-extra-warranty {{ $hideExtraWarranty }}"
                                            style="width:8%;">
                                            <span class="text-table text-secondary">Thông tin</span>
                                        </th>
                                        <th class="border-right note px-2 p-0 text-left col-extra-warranty {{ $hideExtraWarranty }}"
                                            style="width:8%;">
                                            <span class="text-table text-secondary">Bảo hành</span>
                                        </th>
                                        <th class="border-right note px-2 p-0 text-left col-note" style="width:12%;">
                                            <span class="text-table text-secondary">Ghi chú</span>
                                        </th>
                                    </tr>

                                </thead>
                                <tbody id="tbody-data">
                                    @foreach ($groupedReturns as $group)
                                        @foreach ($group as $index => $item)
                                            <tr class="row-product bg-white">
                                                {{-- Dòng đầu tiên hiển thị đầy đủ --}}
                                                @if ($index == 0)
                                                    <td class="border-right p-2 text-13 align-top pl-4 border-bottom">
                                                        <input type="hidden"
                                                            name="return[{{ $loop->index }}][product_id]"
                                                            value="{{ $item->product_id }}">
                                                        <input type="text"
                                                            class="border-0 pl-1 pr-2 py-1 w-100 product_code" readonly
                                                            value="{{ $item->product->product_code }}">
                                                    </td>
                                                    <td class="border-right p-2 text-13 align-top border-bottom">
                                                        <input type="text" class="border-0 pl-1 pr-2 py-1 w-100"
                                                            readonly value="{{ $item->product->product_name }}">
                                                    </td>
                                                    <td class="border-right p-2 text-13 align-top border-bottom">
                                                        <input type="text" class="border-0 pl-1 pr-2 py-1 w-100"
                                                            readonly value="{{ $item->product->brand }}">
                                                    </td>
                                                    <td class="border-right p-2 text-13 align-top border-bottom">
                                                        <input type="text" class="border-0 pl-1 pr-2 py-1 w-100"
                                                            readonly value="{{ $item->quantity }}">
                                                        <input type="hidden" name="return[{{ $loop->index }}][quantity]" value="{{ $item->quantity }}">
                                                    </td>
                                                    <td class="border-right p-2 text-13 align-top border-bottom">
                                                        <input type="text" class="border-0 pl-1 pr-2 py-1 w-100"
                                                            readonly value="{{ $item->serialNumber->serial_code }}">
                                                        <input type="hidden" name="return[{{ $loop->index }}][serial_id]" value="{{ $item->serial_number_id }}">
                                                        <input type="hidden" name="return[{{ $loop->index }}][serial_code]" value="{{ $item->serialNumber->serial_code }}">
                                                    </td>
                                                    <td
                                                        class="border-right p-2 text-13 align-top border-bottom {{ $hideReplacement }}">
                                                        <input type="text" class="border-0 pl-1 pr-2 py-1 w-100"
                                                            readonly
                                                            value="{{ $item->product_replace->product_code ?? '' }}">
                                                    </td>
                                                @else
                                                    {{-- Các dòng sau chỉ hiển thị Serial Number thay thế --}}
                                                    <td class="border-right p-2 text-13 align-top pl-4 border-bottom"></td>
                                                    <td class="border-right p-2 text-13 align-top border-bottom"></td>
                                                    <td class="border-right p-2 text-13 align-top border-bottom"></td>
                                                    <td class="border-right p-2 text-13 align-top border-bottom"></td>
                                                    <td class="border-right p-2 text-13 align-top border-bottom"></td>
                                                    <td
                                                        class="border-right p-2 text-13 align-top border-bottom {{ $hideReplacement }}">
                                                    </td>
                                                @endif

                                                {{-- Serial đổi (luôn hiển thị) --}}
                                                <td class="border-right p-2 text-13 align-top border-bottom {{ $hideReplacement }}">
                                                    <input type="text" class="border-0 pl-1 pr-2 py-1 w-100"
                                                        readonly
                                                        value="{{ $item->replacementSerialNumber->serial_code ?? '' }}">
                                                    <input type="hidden" name="return[{{ $loop->index }}][replacement_code]" value="{{ $item->replacement_code ?? '' }}">
                                                    <input type="hidden" name="return[{{ $loop->index }}][replacement_serial_number_id]" value="{{ $item->replacementSerialNumber->serial_code ?? '' }}">
                                                </td>

                                                {{-- Extra Warranty & Ghi chú (nếu cần) --}}
                                                <td
                                                    class="border-right p-2 text-13 align-top border-bottom {{ $hideExtraWarranty }}">
                                                    <input type="text" class="border-0 pl-1 pr-2 py-1 w-100"
                                                        readonly value="{{ $item->warranties->name_warranty ?? '' }}">
                                                    <input type="hidden" name="return[{{ $loop->index }}][name_warranty]" value="{{ $item->warranties->name_warranty ?? '' }}">
                                                </td>
                                                <td
                                                    class="border-right p-2 text-13 align-top border-bottom {{ $hideExtraWarranty }}">
                                                    <input type="text" class="border-0 pl-1 pr-2 py-1 w-100"
                                                        readonly value="{{ $item->extra_warranty ?? '' }}">
                                                    <input type="hidden" name="return[{{ $loop->index }}][extra_warranty]" value="{{ $item->extra_warranty ?? '' }}">
                                                </td>
                                                @if ($index == 0)
                                                <td class="border-right p-2 text-13 align-top border-bottom">
                                                    <input type="text"
                                                        class="border-0 pl-1 pr-2 py-1 w-100 bg-input-guest-blue"
                                                        name="return[{{ $loop->index }}][note]"
                                                        value="{{ $item->notes ?? '' }}">
                                                </td>
                                                @else
                                                    <td class="border-right p-2 text-13 align-top border-bottom"></td>
                                                @endif
                                            </tr>
                                        @endforeach
                                    @endforeach
                                </tbody>
                            </table>
                        </section>
                    </div>
                </div>
            </div>
            {{-- View mini --}}
            <x-view_mini :guestOrProvider="$customers" :users="$users" name="TH" :data="$data"></x-view_mini>
        </div>
    </div>
</form>
<x-return_card :returnForm="$returnForm" :returnProducts="$returnProducts"></x-return_card>
<script src="{{ asset('js/returnform.js') }}"></script>
<script>
    $(document).ready(function() {
        $('#reception').change(function() {
            var selectedId = $(this).val();
            if (selectedId != 0) {
                // Gửi yêu cầu AJAX tới server để lấy dữ liệu chi tiết
                $.ajax({
                    url: '{{ route('getReceiving') }}',
                    type: 'GET',
                    async: false,
                    data: {
                        selectedId: selectedId,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        var formType = response.data.form_type;
                        if (formType === 2 || formType === 3) {
                            $(".col-replacement-code, .col-replacement-serial").addClass(
                                'd-none');
                            $(".col-extra-warranty").removeClass('d-none');
                        }
                        if (formType === 1 || formType === 3) {
                            $(".col-extra-warranty").addClass('d-none');
                        }
                        if (formType === 3) {
                            $(".col-replacement-code, .col-replacement-serial, .col-extra-warranty")
                                .addClass('d-none');
                        }
                        $('#status').val(response.data.form_type).change();
                        populateTableRows(response.product, "#tbody-data", response
                            .productData, response.data.form_type);
                        $('#customer_name').val(response.data.customer.customer_name);
                        $('#customer_id').val(response.data.customer_id);
                        $('#contact_person').val(response.data.contact_person);
                        $('#phone_number').val(response.data.phone);
                        $('#address').val(response.data.address);
                    }
                });
            }
        });
        flatpickr("#dateCreate", {
            locale: "vn",
            dateFormat: "d/m/Y",
            onChange: function(selectedDates) {
                // Lấy giá trị ngày đã chọn
                if (selectedDates.length > 0) {
                    const formattedDate = flatpickr.formatDate(
                        selectedDates[0],
                        "Y-m-d"
                    );
                    document.getElementById("hiddenDateCreate").value =
                        formattedDate;
                }
            },
        });
    });
    $(document).ready(function() {
        $(document).on('change click', '.replacement_serial_number_id, .search-item', function(event) {
            const $input = $(this);
            const $row = $input.closest('tr');
            const $serialInput = $row.find('.replacement_serial_number_id');
            const replacementCode = $row.find('input.replacement_code').val();
            const $checkIcon = $row.find('.check-icon');
            const serialNumber = $serialInput.val().trim();

            console.log('Serial Number:', serialNumber);
            console.log('Replacement Code:', replacementCode);

            // Kiểm tra trùng lặp trong các ô nhập liệu khác
            let isDuplicate = false;
            $('.replacement_serial_number_id').each(function() {
                const otherValue = $(this).val().trim();
                if ($(this)[0] !== $serialInput[0] && otherValue === serialNumber &&
                    serialNumber !== '') {
                    isDuplicate = true;
                    return false; // Thoát khỏi vòng lặp nếu tìm thấy trùng lặp
                }
            });

            if (isDuplicate) {
                $checkIcon.text('✖').css('color', 'red');
                return; // Không thực hiện kiểm tra AJAX nếu trùng lặp
            }

            // Kiểm tra nếu các giá trị không trống và thực hiện AJAX
            if (serialNumber !== "" && replacementCode !== "") {
                $.ajax({
                    url: '{{ route('checkSNReplace') }}',
                    type: 'GET',
                    data: {
                        serialNumber: serialNumber,
                        product_id: replacementCode,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        console.log(response);
                        if (response.status === 'success') {
                            $checkIcon.text('✔').css('color', 'green').attr('title',
                                response.message);
                        } else if (response.status === 'error') {
                            $checkIcon.text('✖').css('color', 'red').attr('title', response
                                .message);
                        }
                    },
                    error: function() {
                        $checkIcon.text('?').css('color', 'orange');
                        console.error('Có lỗi xảy ra khi kiểm tra số serial.');
                    }
                });
            } else {
                // Nếu ô trống, giữ biểu tượng mặc định hoặc không làm gì cả
                $checkIcon.text('').css('color', 'transparent');
            }
        });

    });
</script>
