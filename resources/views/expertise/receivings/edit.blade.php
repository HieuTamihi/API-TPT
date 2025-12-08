@include('partials.header', ['activeGroup' => 'manageProfess', 'activeName' => 'receivings'])
@section('title', $title)
<form id="form-submit" action="{{ route('receivings.update', $receiving->id) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="content-wrapper--2Column m-0 min-height--none">
        <div class="content-header-fixed-report-1 pt-2">
            <div class="content__header--inner pl-4">
                <div class="content__heading--left d-flex opacity-1">
                    <input type="hidden" id="branch_id" name="branch_id" value="1">
                    <div class="d-flex mb-2 mr-2 p-1 border rounded" style="order: 0;">
                        <span class="text text-13-black m-0" style="flex: 2;">Loại phiếu :</span>
                        <div class="form-check form-check-inline mr-1">
                            <label class="text text-13-black form-check-label mr-1" for="warranty">Bảo hành</label>
                            <input type="radio" class="form-check-input loaiPhieu" id="warranty" name="form_type"
                                value="1" {{ $receiving->form_type == 1 ? 'checked' : '' }}>
                        </div>
                        <div class="form-check form-check-inline mr-1">
                            <label class="text text-13-black form-check-label mr-1" for="service">Dịch vụ</label>
                            <input type="radio" class="form-check-input loaiPhieu" id="service" name="form_type"
                                value="2" {{ $receiving->form_type == 2 ? 'checked' : '' }}>
                        </div>
                        <div class="form-check form-check-inline">
                            <label class="form-check-label text text-13-black mr-1" for="serviceWarranty">Bảo hành dịch
                                vụ</label>
                            <input type="radio" class="form-check-input loaiPhieu" id="serviceWarranty"
                                name="form_type" value="3" {{ $receiving->form_type == 3 ? 'checked' : '' }}>
                        </div>
                    </div>
                    <div class="d-flex mb-2 mr-2 p-1 border rounded" style="order: 0;">
                        <span class="text text-13-black m-0" style="flex: 2;">Tình trạng :</span>
                        <div class="form-check form-check-inline mr-1">
                            <select class="form-check-input border-0 text text-13-black" name="status" id="status">
                                <option value="1"
                                    {{ isset($receiving) && $receiving->status == 1 ? 'selected' : '' }}>Tiếp nhận
                                </option>
                                <option value="2"
                                    {{ isset($receiving) && $receiving->status == 2 ? 'selected' : '' }}>Xử lý</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="d-flex content__heading--right">
                    <div class="row m-0">
                        <a href="{{ route('receivings.index') }}">
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
                        @unlessrole('Kế toán')
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
                        @endunlessrole
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
            <div id="main">
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
                                <input type="text" id="form_code_receiving" name="form_code_receiving"
                                    style="flex:2;" placeholder="Nhập thông tin"
                                    value="{{ $receiving->form_code_receiving }}"
                                    class="text-13-black w-50 border-0 bg-input-guest date_picker bg-input-guest-blue py-2 px-2">
                            </div>
                            <div
                                class="d-flex w-100 justify-content-between py-2 px-3 border border-bottom-0 border-right-0 align-items-center text-left text-nowrap position-relative height-44">
                                <span class="text-13-black btn-click required-label font-weight-bold"
                                    style="width: 195px;">Khách hàng</span>
                                <input placeholder="Nhập thông tin" autocomplete="off" required id="customer_name"
                                    class="text-13-black w-100 border-0 bg-input-guest bg-input-guest-blue py-2 px-2"
                                    value="{{ $receiving->customer->customer_name }}" style="flex:2;" />
                                <input type="hidden" name="customer_id" id="customer_id"
                                    value="{{ $receiving->customer_id }}">
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
                                        <ul class="m-0 p-0 scroll-data">
                                            @foreach ($customers as $item)
                                                <li class="p-2 align-items-center text-wrap border-top"
                                                    data-id="{{ $item->id }}">
                                                    <a href="#" title="{{ $item->customer_name }}"
                                                        style="flex:2;" id="{{ $item->id }}"
                                                        data-name="{{ $item->customer_name }}"
                                                        data-phone="{{ $item->phone }}"
                                                        data-address="{{ $item->address }}"
                                                        data-contact="{{ $item->contact_person }}" name="search-info"
                                                        class="search-info">
                                                        <span
                                                            class="text-13-black-black">{{ $item->customer_name }}</span>
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div
                                class="d-flex w-100 justify-content-between py-2 px-3 border border-bottom-0 border-right-0 align-items-center text-left text-nowrap position-relative height-44">
                                <span class="text-13-black text-nowrap mr-3" style="width: 180px;">Người lập
                                    phiếu</span>
                                <input type="hidden" name="user_id" value="{{ $receiving->user_id }}">
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
                                    id="dateCreate"
                                    class="text-13-black w-50 border-0 bg-input-guest bg-input-guest-blue py-2 px-2"
                                    style=" flex:2;"
                                    value="{{ date_format(new DateTime($receiving->date_created), 'd/m/Y') }}" />
                                <input type="hidden" value="{{ $receiving->date_created }}" name="date_created"
                                    id="hiddenDateCreate">
                            </div>
                            <div
                                class="d-flex w-100 justify-content-between py-2 px-3 border border-bottom-0 border-right-0 align-items-center text-left text-nowrap position-relative height-44">
                                <span class="text-13-black btn-click" style="width:195px"> Người liên hệ </span>
                                <input name="contact_person" placeholder="Nhập thông tin" autocomplete="off"
                                    class="text-13-black w-100 border-0 bg-input-guest bg-input-guest-blue py-2 px-2"
                                    value="{{ $receiving->contact_person }}" style="flex:2;" />
                            </div>
                            <div
                                class="d-flex w-100 justify-content-between py-2 px-3 border border-bottom-0 border-right-0 align-items-center text-left text-nowrap position-relative height-44">
                                <span class="text-13-black text-nowrap mr-3" style="width: 180px;">SĐT liên hệ</span>
                                <input class="text-13-black w-50 border-0 bg-input-guest bg-input-guest-blue py-2 px-2"
                                    autocomplete="off" placeholder="Nhập thông tin" style="flex:2;" name="phone"
                                    value="{{ $receiving->phone }}" />
                            </div>

                        </div>
                        <div class="d-flex w-100">
                            <div
                                class="d-flex w-100 justify-content-between py-2 px-3 border border-bottom-0 border-right-0 align-items-center text-left text-nowrap position-relative height-44">
                                <span class="text-13-black text-nowrap mr-3" style="width: 180px;">Địa chỉ</span>
                                <input id="" placeholder="Nhập thông tin" name="address"
                                    value="{{ $receiving->address }}"
                                    class="text-13-black w-50 border-0 bg-input-guest bg-input-guest-blue py-2 px-2"style="flex:2;" />
                            </div>
                        </div>
                        <div class="d-flex w-100">
                            <div
                                class="d-flex w-100 justify-content-between py-2 px-3 border border-bottom-0 border-right-0 align-items-center text-left text-nowrap position-relative height-44">
                                <span class="text-13-black text-nowrap mr-3" style="width: 180px;">Ghi chú</span>
                                <input name="notes" placeholder="Nhập thông tin" autocomplete="off"
                                    value="{{ $receiving->notes }}"
                                    class="text-13-black w-50 border-0 addr bg-input-guest addr bg-input-guest-blue py-2 px-2"style="flex:2;" />
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
                        </p>
                    </div>
                    <div class="container-fluided">
                        <section class="content">
                            <table class="table" id="inputcontent">
                                <thead>
                                    <tr style="height:44px;">
                                        <th class="border-right px-2 p-0 pl-4"></th>
                                        <th class="border-right px-2 p-0 pl-4">
                                            <span class="text-table text-secondary">Mã hàng</span>
                                        </th>
                                        <th class="border-right px-2 p-0 text-left">
                                            <span class="text-table text-secondary">Tên hàng</span>
                                        </th>
                                        <th class="border-right px-2 p-0 text-left">
                                            <span class="text-table text-secondary">Hãng</span>
                                        </th>
                                        <th class="border-right px-2 p-0 text-center" style="width: 3%">
                                            <span class="text-table text-secondary">SL</span>
                                        </th>
                                        <th class="border-right px-2 p-0 text-right">
                                            <span class="text-table text-secondary">Serial Number</span>
                                        </th>
                                        <th class="border-right px-2 p-0 text-right">
                                            <span class="text-table text-secondary">Thông tin</span>
                                        </th>
                                        <th class="border-right px-2 p-0 text-right">
                                            <span class="text-table text-secondary">Tình trạng tiếp nhận</span>
                                        </th>
                                        <th class="border-right note px-2 p-0 text-right">
                                            <span class="text-table text-secondary">Ghi chú</span>
                                        </th>
                                        <th class="" style="width: 3%"></th>
                                    </tr>
                                </thead>
                                <tbody id="tbody-product-data">
                                    @php
                                        $indexRecei = 0;
                                    @endphp
                                    @foreach ($receivedProducts as $productId => $productGroup)
                                        @foreach ($productGroup as $receivedProduct)
                                            <tr class="row-product bg-white" id="serials-data"
                                                data-index="{{ $indexRecei }}"
                                                data-product-code="{{ $receivedProduct->product->product_code }}"
                                                data-product-id="{{ $receivedProduct->product_id }}">
                                                <td
                                                    class="border-right p-2 text-13 align-top border-bottom border-top-0">

                                                </td>
                                                <td
                                                    class="border-right p-2 text-13 align-top border-bottom border-top-0 d-none">
                                                    <input type="text" autocomplete="off"
                                                        class="border-0 pl-1 pr-2 py-1 w-100 product_id height-32"
                                                        readonly name="product_id[{{ $indexRecei }}][product_id]"
                                                        value="{{ $receivedProduct->product_id }}">
                                                </td>
                                                <td
                                                    class="border-right position-relative p-2 text-13 align-top border-bottom border-top-0 pl-4">
                                                    <input type="text" autocomplete="off"
                                                        class="border-0 pl-1 pr-2 py-1 w-100 height-32" readonly
                                                        placeholder="Tìm mã hàng"
                                                        value="{{ $receivedProduct->product->product_code }}"
                                                        readonly>
                                                    <ul class='list_product bg-white position-absolute w-100 rounded shadow p-0 scroll-data'
                                                        style='z-index: 99;top: 75%;left: 1.5rem;display: none;'>
                                                        @foreach ($products_all as $product_value)
                                                            <li data-id="{{ $product_value->id }}"
                                                                class="product-item">
                                                                <a href='javascript:void(0);'
                                                                    class='text-dark d-flex justify-content-between p-2 idProduct w-100'
                                                                    data-name="{{ $product_value->product_name }}"
                                                                    data-brand="{{ $product_value->brand }}"
                                                                    data-id="{{ $product_value->id }}">
                                                                    <span class='w-50 text-13-black'
                                                                        style='flex:2'>{{ $product_value->product_code }}</span>
                                                                </a>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </td>
                                                <td
                                                    class="border-right p-2 text-13 align-top border-bottom border-top-0">
                                                    <input type="text" autocomplete="off"
                                                        class="border-0 pl-1 pr-2 py-1 w-100 product_name height-32"
                                                        readonly
                                                        value="{{ $receivedProduct->product->product_name }}">
                                                </td>
                                                <td
                                                    class="border-right p-2 text-13 align-top border-bottom border-top-0">
                                                    <input type="text" autocomplete="off"
                                                        class="border-0 pl-1 pr-2 py-1 w-100 brand height-32" readonly
                                                        value="{{ $receivedProduct->product->brand }}">
                                                </td>
                                                <td
                                                    class="border-right p-2 text-13 align-top border-bottom border-top-0">
                                                    <input type="text" autocomplete="off"
                                                        class="border-0 pl-1 pr-2 text-center py-1 w-100 height-32"
                                                        readonly value="{{ $receivedProduct->quantity }}">
                                                </td>
                                                <td
                                                    class="border-right p-2 text-13 align-top border-bottom position-relative border-top-0 d-flex align-items-center">
                                                    <input type="text" autocomplete="off"
                                                        class="border-0 pl-1 serial pr-2 py-1 w-100 height-32" readonly
                                                        name="product_id[{{ $indexRecei }}][serial]"
                                                        data-index="{{ $indexRecei }}"
                                                        value="{{ $receivedProduct->serial->serial_code ?? '' }}">
                                                    <span class="check-icon-seri"></span>
                                                    @php
                                                        $exportDate = optional(
                                                            optional(
                                                                optional(
                                                                    optional($receivedProduct->serial)->exports,
                                                                )->first(),
                                                            )->export,
                                                        )->date_create;
                                                    @endphp
                                                    @if ($exportDate)
                                                    <span class="date-export">
                                                        <i class="fas fa-calendar-alt" style="cursor: pointer;"
                                                            aria-hidden="true"></i>
                                                        <div class="date-popup">
                                                            {{ $exportDate ? \Carbon\Carbon::parse($exportDate)->format('d/m/Y') : '' }}
                                                        </div>
                                                        </span>
                                                    @endif
                                                </td>
                                                <td
                                                    class="border-right p-2 text-13 align-top border-bottom border-top-0 product-cell position-relative">
                                                    <input type="hidden" autocomplete="off"
                                                        class="border-0 pl-1 pr-2 py-1 w-100 id_seri height-32"
                                                        name="product_id[{{ $indexRecei }}][id_seri][]"
                                                        data-index="{{ $indexRecei }}"
                                                        value="{{ $receivedProduct->serial_id }}">
                                                    <input type="hidden" autocomplete="off"
                                                        class="border-0 pl-1 pr-2 py-1 w-100 id_warranty height-32"
                                                        name="product_id[{{ $indexRecei }}][id_warranty][]"
                                                        data-index="{{ $indexRecei }}"
                                                        value="{{ $receivedProduct->warrantyReceived[0]->id ?? '' }}">
                                                    <input type="text" autocomplete="off"
                                                        class="border-0 pl-1 pr-2 py-1 w-100 warranty-input name_warranty height-32"
                                                        name="product_id[{{ $indexRecei }}][name_warranty][]"
                                                        readonly data-index="{{ $indexRecei }}"
                                                        value="{{ $receivedProduct->warrantyReceived[0]->name_warranty ?? '' }}">
                                                    <span class="date-export"></span>
                                                    <span class="check-icon"></span>
                                                    <ul class='warranty-dropdown bg-white position-absolute w-100 rounded shadow p-0 scroll-data'
                                                        style='z-index: 99;top: 75%;display: none;'>
                                                    </ul>
                                                </td>
                                                <td
                                                    class="border-right p-2 text-13 align-top border-bottom border-top-0">
                                                    <input type="text" autocomplete="off"
                                                        class="border-0 pl-1 pr-2 py-1 w-100 warranty height-32 bg-input-guest-blue"
                                                        name="product_id[{{ $indexRecei }}][warranty][]"
                                                        data-index="{{ $indexRecei }}"
                                                        value="{{ $receivedProduct->warrantyReceived[0]->state_recei ?? '' }}">
                                                </td>
                                                <td
                                                    class="border-right p-2 text-13 align-top border-bottom border-top-0">
                                                    <input type="text" autocomplete="off"
                                                        class="border-0 pl-1 pr-2 py-1 w-100 note_seri height-32 bg-input-guest-blue"
                                                        name="product_id[{{ $indexRecei }}][note_seri][]"
                                                        data-index="{{ $indexRecei }}"
                                                        value="{{ $receivedProduct->warrantyReceived[0]->note ?? '' }}">
                                                </td>
                                                <td class="p-2 align-top border-bottom border-top-0 border-right"><svg
                                                        class="delete-row" width="17" height="17"
                                                        viewBox="0 0 17 17" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                            d="M13.1417 6.90625C13.4351 6.90625 13.673 7.1441 13.673 7.4375C13.673 7.47847 13.6682 7.5193 13.6589 7.55918L12.073 14.2992C11.8471 15.2591 10.9906 15.9375 10.0045 15.9375H6.99553C6.00943 15.9375 5.15288 15.2591 4.92702 14.2992L3.34113 7.55918C3.27393 7.27358 3.45098 6.98757 3.73658 6.92037C3.77645 6.91099 3.81729 6.90625 3.85826 6.90625H13.1417ZM9.03125 1.0625C10.4983 1.0625 11.6875 2.25175 11.6875 3.71875H13.8125C14.3993 3.71875 14.875 4.19445 14.875 4.78125V5.3125C14.875 5.6059 14.6371 5.84375 14.3438 5.84375H2.65625C2.36285 5.84375 2.125 5.6059 2.125 5.3125V4.78125C2.125 4.19445 2.6007 3.71875 3.1875 3.71875H5.3125C5.3125 2.25175 6.50175 1.0625 7.96875 1.0625H9.03125ZM9.03125 2.65625H7.96875C7.38195 2.65625 6.90625 3.13195 6.90625 3.71875H10.0938C10.0938 3.13195 9.61805 2.65625 9.03125 2.65625Z"
                                                            fill="#6B6F76"></path>
                                                    </svg></td>
                                            </tr>
                                            @foreach ($receivedProduct->warrantyReceived as $index => $warrantyReceived)
                                                @if ($index > 0)
                                                    <tr class="row-warranty bg-white"
                                                        data-index="{{ $indexRecei }}" data-product-code=""
                                                        data-product-id="{{ $receivedProduct->product_id }}"
                                                        data-seri="{{ $receivedProduct->serial_id }}">
                                                        <td colspan="6"
                                                            class="border-right p-2 text-13 align-top border-bottom border-top-0">
                                                            <input type="hidden" autocomplete="off"
                                                                class="border-0 pl-1 pr-2 py-1 w-100 product_code height-32"
                                                                placeholder="Tìm mã hàng"
                                                                value="{{ $receivedProduct->product->product_code }}"
                                                                readonly>
                                                            <input type="hidden" autocomplete="off"
                                                                class="border-0 pl-1 pr-2 py-1 w-100 serial height-32"
                                                                value="{{ $receivedProduct->serial->serial_code ?? '' }}">
                                                            <input type="hidden" autocomplete="off"
                                                                class="border-0 pl-1 pr-2 py-1 w-100 product_id height-32"
                                                                value="{{ $receivedProduct->product_id }}">
                                                        </td>
                                                        <td
                                                            class="border-right p-2 text-13 align-top border-bottom border-top-0 product-cell position-relative">
                                                            <input type="hidden" autocomplete="off"
                                                                class="border-0 pl-1 pr-2 py-1 w-100 id_seri height-32"
                                                                name="product_id[{{ $indexRecei }}][id_seri][]"
                                                                data-index="{{ $indexRecei }}"
                                                                value="{{ $receivedProduct->serial_id }}">
                                                            <input type="hidden" autocomplete="off"
                                                                class="border-0 pl-1 pr-2 py-1 w-100 id_warranty height-32"
                                                                name="product_id[{{ $indexRecei }}][id_warranty][]"
                                                                data-index="{{ $indexRecei }}"
                                                                value="{{ $warrantyReceived->id }}">
                                                            <input type="text" autocomplete="off"
                                                                class="border-0 pl-1 pr-2 py-1 w-100 warranty-input name_warranty height-32"
                                                                readonly
                                                                name="product_id[{{ $indexRecei }}][name_warranty][]"
                                                                data-index="{{ $indexRecei }}"
                                                                value="{{ $warrantyReceived->name_warranty }}">
                                                            <span class="date-export"></span>
                                                            <span class="check-icon"></span>
                                                            <ul class="warranty-dropdown bg-white position-absolute w-100 rounded shadow p-0 scroll-data"
                                                                style="z-index: 99;top: 75%;display: none;">

                                                            </ul>
                                                        </td>
                                                        <td
                                                            class="border-right p-2 text-13 align-top border-bottom border-top-0">
                                                            <input type="text" autocomplete="off"
                                                                class="border-0 pl-1 pr-2 py-1 w-100 warranty height-32 bg-input-guest-blue"
                                                                name="product_id[{{ $indexRecei }}][warranty][]"
                                                                data-index="{{ $indexRecei }}"
                                                                value="{{ $warrantyReceived->state_recei }}">
                                                        </td>
                                                        <td
                                                            class="border-right p-2 text-13 align-top border-bottom border-top-0">
                                                            <input type="text" autocomplete="off"
                                                                class="border-0 pl-1 pr-2 py-1 w-100 note_seri height-32 bg-input-guest-blue"
                                                                name="product_id[{{ $indexRecei }}][note_seri][]"
                                                                data-index="{{ $indexRecei }}"
                                                                value="{{ $warrantyReceived->note }}">
                                                        </td>
                                                        <td
                                                            class="p-2 align-top border-bottom border-top-0 border-right">
                                                            <svg class="delete-row" width="17" height="17"
                                                                viewBox="0 0 17 17" fill="none"
                                                                xmlns="http://www.w3.org/2000/svg">
                                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                                    d="M13.1417 6.90625C13.4351 6.90625 13.673 7.1441 13.673 7.4375C13.673 7.47847 13.6682 7.5193 13.6589 7.55918L12.073 14.2992C11.8471 15.2591 10.9906 15.9375 10.0045 15.9375H6.99553C6.00943 15.9375 5.15288 15.2591 4.92702 14.2992L3.34113 7.55918C3.27393 7.27358 3.45098 6.98757 3.73658 6.92037C3.77645 6.91099 3.81729 6.90625 3.85826 6.90625H13.1417ZM9.03125 1.0625C10.4983 1.0625 11.6875 2.25175 11.6875 3.71875H13.8125C14.3993 3.71875 14.875 4.19445 14.875 4.78125V5.3125C14.875 5.6059 14.6371 5.84375 14.3438 5.84375H2.65625C2.36285 5.84375 2.125 5.6059 2.125 5.3125V4.78125C2.125 4.19445 2.6007 3.71875 3.1875 3.71875H5.3125C5.3125 2.25175 6.50175 1.0625 7.96875 1.0625H9.03125ZM9.03125 2.65625H7.96875C7.38195 2.65625 6.90625 3.13195 6.90625 3.71875H10.0938C10.0938 3.13195 9.61805 2.65625 9.03125 2.65625Z"
                                                                    fill="#6B6F76"></path>
                                                            </svg>
                                                        </td>
                                                    </tr>
                                                    <tr id="row-add-warranty" data-index="{{ $indexRecei }}"
                                                        class="bg-white row-warranty" style="display: none"
                                                        data-product-code="{{ $receivedProduct->product->product_code }}"
                                                        data-product-id="{{ $receivedProduct->product_id }}"
                                                        data-seri="{{ $receivedProduct->serial->serial_code ?? '' }}">
                                                        <td colspan="6"
                                                            class="border-right p-2 text-13 align-top border-bottom border-top-0">
                                                        </td>
                                                        <td
                                                            class="border-right p-2 text-13 align-top border-bottom border-top-0">
                                                            <button type="button"
                                                                class="btn-add-warranty btn">+</button>
                                                        </td>
                                                        <td colspan="3"
                                                            class="border-right p-2 text-13 align-top border-bottom border-top-0">
                                                        </td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                            @php
                                                $indexRecei++;
                                            @endphp
                                        @endforeach
                                    @endforeach
                                </tbody>

                            </table>
                            <section class="content mt-2">
                                <div class="container-fluided">
                                    <div class="d-flex ml-4">
                                        <button type="button" data-modal-id="modal-id" data-toggle="modal"
                                            data-target="#modal-id"
                                            class="btn-add-item d-flex align-items-center h-100 py-1 px-2 rounded activity"
                                            style="margin-right:10px">
                                            <svg class="mr-2" xmlns="http://www.w3.org/2000/svg" width="12"
                                                height="12" viewBox="0 0 18 18" fill="none">
                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M9 0C9.58186 -2.96028e-08 10.0536 0.471694 10.0536 1.05356L10.0536 16.9464C10.0536 17.5283 9.58186 18 9 18C8.41814 18 7.94644 17.5283 7.94644 16.9464V1.05356C7.94644 0.471694 8.41814 -2.96028e-08 9 0Z"
                                                    fill="#42526E" />
                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M18 9C18 9.58187 17.5283 10.0536 16.9464 10.0536H1.05356C0.471694 10.0536 -2.07219e-07 9.58187 0 9C-7.69672e-07 8.41814 0.471695 7.94644 1.05356 7.94644H16.9464C17.5283 7.94644 18 8.41814 18 9Z"
                                                    fill="#42526E" />
                                            </svg>
                                            <span class="text-table">Thêm sản phẩm</span>
                                        </button>
                                    </div>
                                </div>
                            </section>
                        </section>
                    </div>
                </div>
            </div>
            {{-- View mini --}}
            <x-view_mini :guestOrProvider="$customers" :users="$users" name="TN" :data="$data"></x-view_mini>
        </div>
    </div>
</form>
<x-receive_card :receiving="$receiving" :receivedProducts="$receivedProducts"></x-receive_card>
<script src="{{ asset('js/addproduct.js') }}"></script>
<script src="{{ asset('js/receiving.js') }}"></script>
<script>
    var products = @json($products_all);
</script>
<script>
    let responseData = {};
    $(document).on("click", ".warranty-input", function() {
        const $row = $(this).closest("tr");
        const index = $row.data("index");
        const $rowWarranty = $(`.row-warranty[data-index="${index}"]`);
        const productCode = $row.find(".product_code").val().trim();
        const serial = $row.find(".serial").val().trim();
        const product = $row.find(".product_id").val().trim();
        $rowWarranty.last().show();
        if (productCode && serial) {
            $.ajax({
                url: "/warranty-lookup",
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
                data: {
                    product: product,
                    serial: serial
                },
                success: function(response) {
                    responseData[index] = response;
                    const $inputField = $row.find(".warranty-input");
                    const $dropdownList = $row.find(".warranty-dropdown");
                    const $dateExport = $row.find(".date-export");
                    $dropdownList.empty();

                    if (response.warranty && response.warranty.length > 0) {
                        const date = response.warranty[0].serial_number.exports[0].export
                            .date_create;
                        if (date) {
                            const formattedDate = new Date(date).toLocaleDateString('vi-VN');
                            $dateExport.html(`
                                <i class="fas fa-calendar-alt" style="cursor: pointer;"></i>
                                <div class="date-popup">${formattedDate}</div>
                            `);

                            // Thêm sự kiện click
                            $dateExport.find('i').on('click', function(e) {
                                e.stopPropagation();
                                $dateExport.find('.date-popup').toggle();
                            });

                            // Đóng popup khi click ra ngoài
                            $(document).on('click', function() {
                                $dateExport.find('.date-popup').hide();
                            });
                        }
                        populateWarrantyDropdown(response, $dropdownList);
                    } else {
                        $dateExport.empty();
                    }
                },
                error: function() {
                    showAutoToast("warning", "Seri bên ngoài không thuộc nội bộ.");
                },
            });
        }
    });
    $(document).ready(function() {
        function toggleListGuest(input, list, filterInput) {
            input.on("click", function() {
                list.show();
            });
            $(document).click(function(event) {
                if (
                    !$(event.target).closest(input).length &&
                    !$(event.target).closest(filterInput).length
                ) {
                    list.hide();
                }
            });
            var applyFilter = function() {
                var value = filterInput.val().toUpperCase();
                list.find("li").each(function() {
                    var text = $(this).find("a").text().toUpperCase();
                    $(this).toggle(text.indexOf(value) > -1);
                });
            };
            input.on("keyup", applyFilter);
            filterInput.on("keyup", applyFilter);
        }
        toggleListGuest(
            $("#customer_name"),
            $("#listCustomer"),
            $("#searchCustomer")
        );
        $('a[name="search-info"]').on('click', function() {
            const dataId = $(this).attr('id');
            const dataName = $(this).data('name');
            const phone = $(this).data('phone');
            const address = $(this).data('address');
            const contact = $(this).data('contact');
            $("#customer_id").val(dataId);
            $("#customer_name").val(dataName);
            $('[name="phone"]').val(phone);
            $('[name="contact"]').val(contact);
            $('[name="address"]').val(address);
        });
    });
</script>
