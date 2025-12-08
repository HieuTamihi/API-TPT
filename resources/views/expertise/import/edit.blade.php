@include('partials.header', ['activeGroup' => 'manageProfess', 'activeName' => 'imports'])
@section('title', $title)
<form id="form-submit" action="{{ route('imports.update', $import->id) }}" method="POST">
    @csrf
    @method('PUT')
    <input type="hidden" value="{{ $import->id }}" id="import_id">
    <div class="content-wrapper--2Column m-0 min-height--none">
        <div class="content-header-fixed-report-1 pt-2">
            <div class="content__header--inner">
                <div class="content__heading--left opacity-0"></div>
                <div class="d-flex content__heading--right">
                    <div class="row m-0">
                        <a href="{{ route('imports.index') }}">
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
                        <?php $isCheck = true; ?>
                        @foreach ($productImports as $productId => $products)
                            @foreach ($products as $item)
                                <?php
                                // Kiểm tra đã được xuất chưa
                                $hasExported = \App\Models\ProductExport::where('product_id', $item->product_id)->where('sn_id', $item->sn_id)->exists();
                                ?>

                                @if ($hasExported)
                                    <?php $isCheck = false; ?>
                                @endif
                            @endforeach
                        @endforeach

                        <?php
                        $readonly = $isCheck ? '' : 'readonly';
                        $bg = $isCheck ? 'bg-input-guest-blue' : '';
                        $placeholder = $isCheck ? 'Nhập thông tin' : '';
                        ?>
                        @if ($isCheck)
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
                        @endif
                        <button id="sideGuest" type="button" class="btn-option border-0 mx-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none">
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
                    <div>
                        <div class="bg-filter-search border-0 text-center">
                            <p class="font-weight-bold text-uppercase info-chung--heading text-center">
                                THÔNG TIN PHIẾU NHẬP HÀNG
                            </p>
                        </div>
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <div class="row">
                            <div class="col-md-4 m-0 p-0">
                                <div
                                    class="d-flex w-100 justify-content-between py-2 px-3 border border-bottom-0 border-right-0 align-items-center text-left text-nowrap position-relative height-44">
                                    <span class="text-13-black text-nowrap mr-3 required-label font-weight-bold"
                                        style="width: 180px;">Mã phiếu</span>
                                    <input type="text" name="import_code" style="flex:2;" {{ $readonly }}
                                        placeholder="{{ $placeholder }}" required value="{{ $import->import_code }}"
                                        class="text-13-black w-50 border-0 bg-input-guest {{ $bg }} date_picker py-2 px-2">
                                </div>
                                <div
                                    class="d-flex w-100 justify-content-between py-2 px-3 border border-bottom-0 border-right-0 align-items-center text-left text-nowrap position-relative height-44">
                                    <span class="text-13-black text-nowrap mr-3 required-label font-weight-bold"
                                        style="width: 180px;">Ngày lập phiếu</span>
                                    <input placeholder="{{ $placeholder }}" autocomplete="off" required id="dateCreate"
                                        value="{{ date_format(new DateTime($import->date_create), 'd/m/Y') }}"
                                        type="text" {{ $readonly }}
                                        class="text-13-black w-50 border-0 bg-input-guest {{ $bg }} py-2 px-2"style=" flex:2;" />
                                    <input type="hidden" value="{{ $import->date_create }}" name="date_create"
                                        id="hiddenDateCreate">
                                </div>
                            </div>
                            <div class="col-md-4 m-0 p-0">
                                <div
                                    class="d-flex w-100 justify-content-between py-2 px-3 border border-bottom-0 border-right-0 align-items-center text-left text-nowrap position-relative height-44">
                                    <span class="text-13-black text-nowrap mr-3 required-label font-weight-bold"
                                        style="width: 180px;">Người lập phiếu</span>
                                    <input autocomplete="off" placeholder="{{ $placeholder }}" required id="user_name"
                                        value="{{ $import->name }}" readonly
                                        class="text-13-black w-50 border-0 bg-input-guest py-2 px-2" style="flex:2;" />
                                    <input type="hidden" name="user_id" id="user_id" value="{{ $import->user_id }}">
                                </div>
                                <div
                                    class="d-flex w-100 justify-content-between py-2 px-3 border border-bottom-0 border-right-0 align-items-center text-left text-nowrap position-relative height-44">
                                    <span class="text-13-black btn-click required-label font-weight-bold"
                                        style="width: 195px;">Nhà cung cấp</span>
                                    <input placeholder="{{ $placeholder }}" autocomplete="off" required
                                        id="provider_name" readonly
                                        class="text-13-black w-100 border-0 bg-input-guest {{ $bg }} py-2 px-2"
                                        style="flex:2;" value="{{ $import->provider_name }}" />
                                    <input type="hidden" name="provider_id" id="provider_id"
                                        value="{{ $import->provider_id }}">
                                    @if ($isCheck)
                                        <div class="">
                                            <div id="listProvider"
                                                class="bg-white position-absolute rounded list-guest shadow p-1 z-index-block"
                                                style="z-index: 99;display: none;">
                                                <div class="p-1">
                                                    <div class="position-relative">
                                                        <input type="text" placeholder="{{ $placeholder }}"
                                                            class="pr-4 w-100 input-search bg-input-guest"
                                                            id="searchProvider">
                                                        <span id="search-icon" class="search-icon">
                                                            <i class="fas fa-search text-table"
                                                                aria-hidden="true"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                                <ul class="m-0 p-0 scroll-data">
                                                    @foreach ($providers as $provider_value)
                                                        <li class="p-2 align-items-center text-wrap border-top"
                                                            data-id="{{ $provider_value->id }}">
                                                            <a href="#"
                                                                title="{{ $provider_value->provider_name }}"
                                                                style="flex:2;" id="{{ $provider_value->id }}"
                                                                data-name="{{ $provider_value->provider_name }}"
                                                                data-phone="{{ $provider_value->phone }}"
                                                                data-address="{{ $provider_value->address }}"
                                                                data-contact="{{ $provider_value->contact_person }}"
                                                                name="search-info" class="search-info">
                                                                <span
                                                                    class="text-13-black">{{ $provider_value->provider_name }}</span>
                                                            </a>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4 m-0 p-0">
                                <div style="width: 99%;"
                                    class="d-flex justify-content-between py-2 px-3 border border-bottom-0 border-right-0 align-items-center text-left text-nowrap position-relative height-44">
                                    <span class="text-13-black text-nowrap mr-3 font-weight-bold"
                                        style="width: 180px;">SĐT
                                        liên hệ</span>
                                    <input name="phone" placeholder="{{ $placeholder }}" type="number"
                                        value="{{ $import->phone }}" {{ $readonly }} min="0"
                                        step="1"
                                        oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0,11);"
                                        class="text-13-black w-50 border-0 bg-input-guest {{ $bg }} py-2 px-2"
                                        style="flex:2;" />
                                </div>
                                <div style="width: 99%;"
                                    class="d-flex justify-content-between py-2 px-3 border border-bottom-0 border-right-0 align-items-center text-left text-nowrap position-relative height-44">
                                    <span class="text-13-black text-nowrap mr-3 font-weight-bold"
                                        style="width: 180px;">Người liên hệ</span>
                                    <input name="contact_person" placeholder="{{ $placeholder }}" type="text"
                                        value="{{ $import->contact_person }}" {{ $readonly }}
                                        class="text-13-black w-50 border-0 bg-input-guest {{ $bg }} py-2 px-2"
                                        style="flex:2;" />
                                </div>
                            </div>
                            <div class="col-md-12 m-0 p-0">
                                <div style="width: 99.7%;"
                                    class="d-flex justify-content-between py-2 px-3 border border-bottom-0 border-right-0 align-items-center text-left text-nowrap position-relative height-44">
                                    <span class="text-13-black text-nowrap mr-3 font-weight-bold"
                                        style="width: 180px;">Địa
                                        chỉ</span>
                                    <input name="address" placeholder="{{ $placeholder }}" autocomplete="off"
                                        value="{{ $import->address }}" {{ $readonly }}
                                        class="text-13-black w-50 border-0 addr bg-input-guest addr {{ $bg }} py-2 px-2"style="flex:9.7;" />
                                </div>
                            </div>
                            <div class="col-md-12 m-0 p-0">
                                <div style="width: 99.7%;"
                                    class="d-flex justify-content-between py-2 px-3 border border-bottom-0 border-right-0 align-items-center text-left text-nowrap position-relative height-44">
                                    <span class="text-13-black font-weight-bold text-nowrap mr-3"
                                        style="width: 180px;">
                                        Ghi chú
                                    </span>
                                    <input
                                        class="text-13-black w-50 border-0 bg-input-guest {{ $bg }} py-2 px-2"
                                        value="{{ $import->note }}" autocomplete="off"
                                        placeholder="{{ $placeholder }}" style="flex:9.7;" name="note"
                                        {{ $readonly }} />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
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
                                        <th class="border-right px-2 p-0" style="width: 8%">
                                            <span class="text-table text-13-black font-weight-bold pl-3">Mã hàng</span>
                                        </th>
                                        <th class="border-right px-2 p-0 text-left" style="width: 15%; z-index:99;">
                                            <span class="text-table text-13-black font-weight-bold">Tên hàng</span>
                                        </th>
                                        <th class="border-right px-2 p-0 text-left" style="width: 8%;">
                                            <span class="text-table text-13-black font-weight-bold">Hãng</span>
                                        </th>
                                        <th class="border-right px-2 p-0" style="width: 8%;">
                                            <span class="text-table text-13-black font-weight-bold">Số lượng</span>
                                        </th>
                                        <th class="border-right px-2 p-0" style="width: 10%;">
                                            <span class="text-table text-13-black font-weight-bold">Serial
                                                Number</span>
                                        </th>
                                        <th class="border-right note px-2 p-0 text-left" style="width: 15%;">
                                            <span class="text-table text-13-black font-weight-bold">Ghi chú</span>
                                        </th>
                                        @if ($isCheck)
                                            <th class="border-right note px-2 p-0" style="width: 5%;"></th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody id="tbody-product-data">
                                    @php
                                        $sum = 0;
                                    @endphp
                                    @foreach ($productImports as $productId => $products)
                                        @php
                                            // Lấy thông tin sản phẩm của product_id
                                            $product = $products->first()->product;
                                        @endphp
                                        {{-- Hiển thị từng serial --}}
                                        @foreach ($products as $item)
                                            <tr id="serials-data" class="row-product bg-white" data-index="1"
                                                data-product-code="{{ $product->product_code }}"
                                                data-product-id="{{ $product->id }}">
                                                <td
                                                    class="border-right p-2 text-13 align-top border-bottom border-top-0 d-none">
                                                    <input type="text" autocomplete="off"
                                                        class="border-0 pl-1 pr-2 py-1 w-100 product_id height-32"
                                                        readonly="" name="product_id[]"
                                                        value="{{ $product->id }}">
                                                </td>
                                                <td
                                                    class="border-right p-2 text-13 align-top border-bottom border-top-0 pl-4">
                                                    <input type="text" autocomplete="off"
                                                        class="border-0 pl-1 pr-2 py-1 w-100 product_code height-32"
                                                        readonly="" name="product_code"
                                                        value="{{ $item->product->product_code }}">
                                                </td>
                                                <td
                                                    class="border-right p-2 text-13 align-top border-bottom border-top-0">
                                                    <input type="text" autocomplete="off"
                                                        class="border-0 pl-1 pr-2 py-1 w-100 product_name height-32"
                                                        readonly="" name="product_name"
                                                        value="{{ $item->product->product_name }}">
                                                </td>
                                                <td
                                                    class="border-right p-2 text-13 align-top border-bottom border-top-0">
                                                    <input type="text" autocomplete="off"
                                                        class="border-0 pl-1 pr-2 py-1 w-100 brand height-32"
                                                        readonly="" name="brand"
                                                        value="{{ $item->product->brand }}">
                                                </td>
                                                <td
                                                    class="border-right p-2 text-13 align-top border-bottom border-top-0">
                                                    <input type="text" autocomplete="off" {{ $readonly }}
                                                        class="border-0 pl-1 pr-2 py-1 w-100 height-32 qty {{ $bg }}"
                                                        name="qty" value="{{ $item->quantity }}">
                                                </td>
                                                <td
                                                    class="border-right p-2 text-13 align-top border-bottom border-top-0">
                                                    <input type="text" autocomplete="off"
                                                        class="border-0 pl-1 pr-2 py-1 w-100 serial height-32" readonly
                                                        name="serial[]" {{ $readonly }}
                                                        value="{{ $item->serialNumber->serial_code ?? '' }}">
                                                </td>
                                                <td
                                                    class="border-right p-2 text-13 align-top border-bottom border-top-0">
                                                    <input type="text" autocomplete="off" {{ $readonly }}
                                                        class="border-0 pl-1 pr-2 py-1 w-100 note_seri height-32 {{ $bg }}"
                                                        name="note_seri[]" value="{{ $item->note }}">
                                                </td>
                                                @if ($isCheck)
                                                    <td
                                                        class="p-2 align-top activity border-bottom border-top-0 border-right text-center">
                                                        <svg class="delete-row" width="17" height="17"
                                                            viewBox="0 0 17 17" fill="none"
                                                            xmlns="http://www.w3.org/2000/svg">
                                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                                d="M13.1417 6.90625C13.4351 6.90625 13.673 7.1441 13.673 7.4375C13.673 7.47847 13.6682 7.5193 13.6589 7.55918L12.073 14.2992C11.8471 15.2591 10.9906 15.9375 10.0045 15.9375H6.99553C6.00943 15.9375 5.15288 15.2591 4.92702 14.2992L3.34113 7.55918C3.27393 7.27358 3.45098 6.98757 3.73658 6.92037C3.77645 6.91099 3.81729 6.90625 3.85826 6.90625H13.1417ZM9.03125 1.0625C10.4983 1.0625 11.6875 2.25175 11.6875 3.71875H13.8125C14.3993 3.71875 14.875 4.19445 14.875 4.78125V5.3125C14.875 5.6059 14.6371 5.84375 14.3438 5.84375H2.65625C2.36285 5.84375 2.125 5.6059 2.125 5.3125V4.78125C2.125 4.19445 2.6007 3.71875 3.1875 3.71875H5.3125C5.3125 2.25175 6.50175 1.0625 7.96875 1.0625H9.03125ZM9.03125 2.65625H7.96875C7.38195 2.65625 6.90625 3.13195 6.90625 3.71875H10.0938C10.0938 3.13195 9.61805 2.65625 9.03125 2.65625Z"
                                                                fill="#6B6F76"></path>
                                                        </svg>
                                                    </td>
                                                @endif
                                            </tr>
                                        @endforeach

                                        @php
                                            $sum += $products->sum('quantity');
                                        @endphp
                                        {{-- Tổng số lượng --}}
                                        <tr id="serials-count" class="bg-white"
                                            data-product-code="{{ $product->product_name }}"
                                            data-product-id="{{ $product->id }}">
                                            <td colspan="2"
                                                class="border-right p-2 text-13 align-top border-bottom border-top-0">
                                            </td>
                                            <td
                                                class="border-right p-2 text-13 align-center border-bottom border-top-0 text-right text-purble">
                                                Tổng số lượng:
                                            </td>
                                            <td class="border-right p-2 text-13 align-top border-bottom border-top-0">
                                                <input type="text" autocomplete="off"
                                                    class="border-0 pl-1 pr-2 py-1 w-100 height-32 text-purble"
                                                    readonly="" name="serial_count"
                                                    value="{{ $products->sum('quantity') }}">
                                            </td>
                                            <td colspan="3"
                                                class="border-right p-2 text-13 align-top border-bottom border-top-0">
                                            </td>
                                        </tr>

                                        {{-- Nút thêm --}}
                                        @if ($isCheck)
                                            <tr id="add-row-product" class="bg-white" data-product-code="SP1"
                                                data-product-id="{{ $product->id }}">
                                                <td colspan="7"
                                                    class="border-right p-2 text-13 align-top border-bottom border-top-0 pl-4">
                                                    <button type="button" class="save-info-product btn"
                                                        data-product-id="{{ $product->id }}"
                                                        data-product-code="{{ $product->product_code }}"
                                                        data-product-name="{{ $product->product_name }}"
                                                        data-product-brand="{{ $product->brand }}"
                                                        data-product-warranty="{{ $product->warranty }}">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16"
                                                            height="16" viewBox="0 0 16 16" fill="none">
                                                            <path
                                                                d="M4.75 2.00007C2.67893 2.00007 1 3.679 1 5.75007V11.25C1 13.3211 2.67893 15 4.75 15H10.2501C12.3212 15 14.0001 13.3211 14.0001 11.25V8.00007C14.0001 7.58586 13.6643 7.25007 13.2501 7.25007C12.8359 7.25007 12.5001 7.58586 12.5001 8.00007V11.25C12.5001 12.4927 11.4927 13.5 10.2501 13.5H4.75C3.50736 13.5 2.5 12.4927 2.5 11.25V5.75007C2.5 4.50743 3.50736 3.50007 4.75 3.50007H7C7.41421 3.50007 7.75 3.16428 7.75 2.75007C7.75 2.33586 7.41421 2.00007 7 2.00007H4.75Z"
                                                                fill="#6D7075" />
                                                            <path
                                                                d="M12.1339 5.19461L10.7197 3.7804L6.52812 7.97196C5.77185 8.72823 5.25635 9.69144 5.0466 10.7402C5.03144 10.816 5.09828 10.8828 5.17409 10.8677C6.22285 10.6579 7.18606 10.1424 7.94233 9.38618L12.1339 5.19461Z"
                                                                fill="#6D7075" />
                                                            <path
                                                                d="M13.4559 1.45679C13.2663 1.39356 13.0571 1.44293 12.9158 1.58431L11.7803 2.71974L13.1945 4.13395L14.33 2.99852C14.4714 2.85714 14.5207 2.64802 14.4575 2.45834C14.2999 1.98547 13.9288 1.61441 13.4559 1.45679Z"
                                                                fill="#6D7075" />
                                                        </svg>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                    @if (!$isCheck)
                                        <tr class="pb-5">
                                            <td class="border-0"></td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                            <input type="hidden" name="data-test" id="data-test">
                            @if ($isCheck)
                                <section class="content mt-2">
                                    <div class="container-fluided">
                                        <div class="d-flex ml-4">
                                            <button type="button" data-modal-id="modal-id" data-toggle="modal"
                                                data-target="#modal-id"
                                                class="btn-save-print d-flex align-items-center h-100 py-1 px-2 rounded activity mb-5"
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
                                                <span class="text-table font-weight-bold">Thêm sản phẩm mới</span>
                                            </button>
                                        </div>
                                    </div>
                                </section>
                            @endif
                            <x-add-product-modal :id="'modal-id'" title="Thêm sản phẩm" :data-product="$productAll"
                                name="CNH" />
                        </section>
                    </div>
                    <div class="footer-summary">
                        <table class="table-footer">
                            <tr>
                                <td class="text-right" colspan="2"></td>
                                <td class="text-danger text-center ml-2">Tổng cộng: <span
                                        id="sumSN">{{ $sum }}</span>
                                </td>
                                <td colspan="3"></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            {{-- View mini --}}
            <x-view_mini :guestOrProvider="$providers" :users="$users" name="NH" :data="$data"></x-view_mini>
        </div>
    </div>
</form>
<script src="{{ asset('js/imports.js') }}"></script>
<script src="{{ asset('js/addproduct.js') }}"></script>
