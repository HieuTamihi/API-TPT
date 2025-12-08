@include('partials.header', ['activeGroup' => 'manageProfess', 'activeName' => 'receivings'])
<form id="form-submit" action="{{ route('receivings.store') }}" method="POST">
    @csrf
    <div class="content-wrapper--2Column m-0 min-height--none">
        <div class="content-header-fixed-report-1 pt-2">
            <div class="content__header--inner pl-4">
                <input type="hidden" id="branch_id" name="branch_id" value="1">
                <div class="content__heading--left d-flex opacity-1">
                    <div class="d-flex mb-2 mr-2 p-1 border rounded box-shadow-border" style="order: 0;">
                        <span class="text text-13-black m-0" style="flex: 2;">Loại phiếu :</span>
                        <div class="form-check form-check-inline mr-1">
                            <label class="text text-13-black form-check-label mr-1" for="warranty">Bảo hành</label>
                            <input type="radio" class="form-check-input loaiPhieu" id="warranty" name="form_type"
                                value="1">
                        </div>
                        <div class="form-check form-check-inline mr-1">
                            <label class="text text-13-black form-check-label mr-1" for="service">Dịch vụ</label>
                            <input type="radio" class="form-check-input loaiPhieu" id="service" name="form_type"
                                value="2">
                        </div>
                        <div class="form-check form-check-inline">
                            <label class="form-check-label text text-13-black mr-1" for="serviceWarranty">Bảo hành dịch
                                vụ</label>
                            <input type="radio" class="form-check-input loaiPhieu" id="serviceWarranty"
                                name="form_type" value="3">
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
                    </div>
                </div>
            </div>
        </div>
        <div class="content-wrapper2 px-0 py-0 margin-top-118 blur-wrapper" id="content-wrapper">
            <div id="contextMenuPBH" class="dropdown-menu"
                style="display: none; background: #ffffff; position: absolute; width:13%;  padding: 3px 10px;  box-shadow: 0 0 10px -3px rgba(0, 0, 0, .3); border: 1px solid #ccc;">
                <a class="dropdown-item text-13-black" href="#" data-option="donhang">Tạo phiếu xuất kho</a>
            </div>
            {{-- Thông tin khách hàng --}}
            <div class="" id="main" style="margin-right: 10px !important;">

                <div class="border">
                    <div class="info-form">
                        <div class="bg-filter-search border text-center border-bottom-0 border-top-0">
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
                                    style="flex:2;" placeholder="Nhập thông tin" value="{{ $quoteNumber }}"
                                    class="text-13-black w-50 border-0 bg-input-guest date_picker bg-input-guest-blue py-2 px-2">
                            </div>
                            <div
                                class="d-flex w-100 justify-content-between py-2 px-3 border border-bottom-0 border-right-0 align-items-center text-left text-nowrap position-relative height-44">
                                <span class="text-13-black btn-click required-label font-weight-bold"
                                    style="width: 195px;">Khách hàng</span>
                                <input placeholder="Nhập thông tin" autocomplete="off" onkeypress="return false;"
                                    required id="customer_name"
                                    class="text-13-black w-100 border-0 bg-input-guest bg-input-guest-blue py-2 px-2"
                                    style="flex:2;" />
                                <input type="hidden" name="customer_id" id="customer_id">
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
                                <input type="hidden" name="user_id" value="{{ Auth::user()->id }}">
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
                                <input placeholder="Nhập thông tin" autocomplete="off" type="date"
                                    id="dateCreate"
                                    class="text-13-black w-50 border-0 bg-input-guest bg-input-guest-blue py-2 px-2"
                                    style=" flex:2;" value="{{ now()->format('Y-m-d') }} }}" />
                                <input type="hidden" value="{{ now()->format('Y-m-d') }}" name="date_created"
                                    id="hiddenDateCreate">
                            </div>
                            <div
                                class="d-flex w-100 justify-content-between py-2 px-3 border border-bottom-0 border-right-0 align-items-center text-left text-nowrap position-relative height-44">
                                <span class="text-13-black btn-click" style="width: 195px;"> Người liên hệ </span>
                                <input name="contact_person" placeholder="Nhập thông tin" autocomplete="off"
                                    class="text-13-black w-100 border-0 bg-input-guest bg-input-guest-blue py-2 px-2"
                                    style="flex:2;" />
                                <div class="">
                                    <div id="myUL"
                                        class="bg-white position-absolute rounded list-guest shadow p-1 z-index-block"
                                        style="z-index: 99;display: none;">
                                        <div class="p-1">
                                            <div class="position-relative">
                                                <input type="text" placeholder="Nhập công ty"
                                                    class="pr-4 w-100 input-search bg-input-guest" id="companyFilter">
                                                <span id="search-icon" class="search-icon">
                                                    <i class="fas fa-search text-table" aria-hidden="true"></i>
                                                </span>
                                            </div>
                                        </div>
                                        <ul class="m-0 p-0 scroll-data">
                                            {{-- @foreach ($guest as $guest_value)
                                            <li class="p-2 align-items-center text-wrap border-top"
                                                data-id="{{ $guest_value->id }}">
                                                <a href="#" title="{{ $guest_value->guest_name_display }}"
                                                    style="flex:2;" id="{{ $guest_value->id }}"
                                                    name="search-info" class="search-info">
                                                    <span
                                                        class="text-13-black">{{ $guest_value->guest_name_display }}</span>
                                                </a>
                                            </li>
                                        @endforeach --}}
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div
                                class="d-flex w-100 justify-content-between py-2 px-3 border border-bottom-0 border-right-0 align-items-center text-left text-nowrap position-relative height-44">
                                <span class="text-13-black text-nowrap mr-3" style="width: 180px;">SĐT liên hệ</span>
                                <input class="text-13-black w-50 border-0 bg-input-guest bg-input-guest-blue py-2 px-2"
                                    autocomplete="off" placeholder="Nhập thông tin" style="flex:2;"
                                    name="phone" />
                            </div>

                        </div>
                        <div class="d-flex w-100">
                            <div
                                class="d-flex w-100 justify-content-between py-2 px-3 border border-bottom-0 align-items-center text-left text-nowrap position-relative height-44">
                                <span class="text-13-black text-nowrap mr-3" style="width: 180px;">Địa chỉ</span>
                                <input id="" placeholder="Nhập thông tin" name="address"
                                    class="text-13-black w-50 border-0 bg-input-guest bg-input-guest-blue py-2 px-2"style="flex:2;" />
                            </div>
                        </div>
                        <div class="d-flex w-100">
                            <div
                                class="d-flex w-100 justify-content-between py-2 px-3 border border-bottom-0 align-items-center text-left text-nowrap position-relative height-44">
                                <span class="text-13-black text-nowrap mr-3" style="width: 180px;">Ghi chú</span>
                                <input name="notes" placeholder="Nhập thông tin" autocomplete="off"
                                    class="text-13-black w-50 border-0 addr bg-input-guest addr bg-input-guest-blue py-2 px-2"style="flex:2;" />
                            </div>
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
                                    <th class="border-right p-0 pl-4"></th>
                                    <th class="border-right px-2 p-0">
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
                                <tr class="row-product bg-white" id="serials-data" data-index="0"
                                    data-product-code="" data-product-id="">
                                    <td class="border-right p-2 text-13 align-top border-bottom border-top-0">
                                        <button type="button" data-modal-id="modal-id" data-toggle="modal"
                                            data-target="#modal-id"
                                            class="btn-copy-item d-flex align-items-center h-100 py-1 px-2 rounded activity ml-3"
                                            style="margin-right:10px">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                fill="currentColor" class="bi bi-copy" viewBox="0 0 16 16">
                                                <path fill-rule="evenodd"
                                                    d="M4 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2zm2-1a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1zM2 5a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1v-1h1v1a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h1v1z" />
                                            </svg>
                                        </button>
                                    </td>
                                    <td class="border-right p-2 text-13 align-top border-bottom border-top-0 d-none">
                                        <input type="text" autocomplete="off"
                                            class="border-0 pl-1 pr-2 py-1 w-100 product_id height-32" readonly
                                            name="product_id[0][product_id]" value="">
                                    </td>
                                    <td
                                        class="border-right position-relative p-2 text-13 align-top border-bottom border-top-0">
                                        <input type="text" autocomplete="off"
                                            class="border-0 pl-1 pr-2 py-1 w-100 product_code height-32 bg-input-guest-blue"
                                            placeholder="Tìm mã hàng" value="">
                                        <ul class='list_product bg-white position-absolute w-100 rounded shadow p-0 scroll-data'
                                            style='z-index: 99;top: 75%;left: 1.5rem;display: none;'>
                                            @foreach ($products as $product_value)
                                                <li data-id='{{ $product_value->id }}' class="product-item">
                                                    <a href='javascript:void(0);'
                                                        class='text-dark d-flex justify-content-between p-2 idProduct w-100'
                                                        data-name="{{ $product_value->product_name }}"
                                                        data-brand="{{ $product_value->brand }}"
                                                        data-id='{{ $product_value->id }}'>
                                                        <span class='w-50 text-13-black'
                                                            style='flex:2'>{{ $product_value->product_code }}</span>
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </td>
                                    <td class="border-right p-2 text-13 align-top border-bottom border-top-0">
                                        <input type="text" autocomplete="off"
                                            class="border-0 pl-1 pr-2 py-1 w-100 product_name height-32"
                                            readonly="" value="">
                                    </td>
                                    <td class="border-right p-2 text-13 align-top border-bottom border-top-0">
                                        <input type="text" autocomplete="off"
                                            class="border-0 pl-1 pr-2 py-1 w-100 brand height-32" readonly=""
                                            value="">
                                    </td>
                                    <td class="border-right p-2 text-13 align-top border-bottom border-top-0">
                                        <input type="text" autocomplete="off"
                                            class="border-0 pl-1 pr-2 text-center py-1 w-100 height-32" readonly=""
                                            value="1">
                                    </td>
                                    <td
                                        class="border-right p-2 text-13 align-top border-bottom border-top-0 position-relative d-flex align-items-center">
                                        <input type="text" autocomplete="off"
                                            class="border-0 pl-1 pr-2 py-1 w-100 serial height-32 bg-input-guest-blue"
                                            name="product_id[0][serial]" data-index="0" value="">
                                        <span class="check-icon-seri"></span>
                                        <span class="date-export"></span>
                                    </td>
                                    <td
                                        class="border-right p-2 text-13 align-top border-bottom border-top-0 product-cell position-relative">
                                        <input type="hidden" autocomplete="off"
                                            class="border-0 pl-1 pr-2 py-1 w-100 id_seri height-32"
                                            name="product_id[0][id_seri][]" data-index="0" value="">
                                        <input type="hidden" autocomplete="off"
                                            class="border-0 pl-1 pr-2 py-1 w-100 id_warranty height-32"
                                            name="product_id[0][id_warranty][]" data-index="0" value="">
                                        <input type="text" autocomplete="off"
                                            class="border-0 pl-1 pr-2 py-1 w-100 warranty-input name_warranty height-32 bg-input-guest-blue"
                                            name="product_id[0][name_warranty][]" data-index="0" value=""
                                            required>
                                        <span class="check-icon"></span>
                                        <ul class='warranty-dropdown bg-white position-absolute w-100 rounded shadow p-0 scroll-data'
                                            style='z-index: 99;top: 75%;display: none;'>
                                        </ul>
                                    </td>
                                    <td class="border-right p-2 text-13 align-top border-bottom border-top-0">
                                        <input type="text" autocomplete="off"
                                            class="border-0 pl-1 pr-2 py-1 w-100 warranty height-32 bg-input-guest-blue"
                                            name="product_id[0][warranty][]" data-index="0" value="">
                                    </td>
                                    <td class="border-right p-2 text-13 align-top border-bottom border-top-0">
                                        <input type="text" autocomplete="off"
                                            class="border-0 pl-1 pr-2 py-1 w-100 note_seri height-32 bg-input-guest-blue"
                                            name="product_id[0][note_seri][]" data-index="0" value="">
                                    </td>
                                    <td class="p-2 align-top border-bottom border-top-0 border-right">
                                        <svg class="delete-row" width="17" height="17" viewBox="0 0 17 17"
                                            fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="M13.1417 6.90625C13.4351 6.90625 13.673 7.1441 13.673 7.4375C13.673 7.47847 13.6682 7.5193 13.6589 7.55918L12.073 14.2992C11.8471 15.2591 10.9906 15.9375 10.0045 15.9375H6.99553C6.00943 15.9375 5.15288 15.2591 4.92702 14.2992L3.34113 7.55918C3.27393 7.27358 3.45098 6.98757 3.73658 6.92037C3.77645 6.91099 3.81729 6.90625 3.85826 6.90625H13.1417ZM9.03125 1.0625C10.4983 1.0625 11.6875 2.25175 11.6875 3.71875H13.8125C14.3993 3.71875 14.875 4.19445 14.875 4.78125V5.3125C14.875 5.6059 14.6371 5.84375 14.3438 5.84375H2.65625C2.36285 5.84375 2.125 5.6059 2.125 5.3125V4.78125C2.125 4.19445 2.6007 3.71875 3.1875 3.71875H5.3125C5.3125 2.25175 6.50175 1.0625 7.96875 1.0625H9.03125ZM9.03125 2.65625H7.96875C7.38195 2.65625 6.90625 3.13195 6.90625 3.71875H10.0938C10.0938 3.13195 9.61805 2.65625 9.03125 2.65625Z"
                                                fill="#6B6F76"></path>
                                        </svg>
                                    </td>
                                </tr>
                                <tr id="row-add-warranty" data-index="0" class="bg-white row-warranty"
                                    style="display: none" data-product-code="" data-product-id="">
                                    <td colspan="6"
                                        class="border-right p-2 text-13 align-top border-bottom border-top-0">
                                    <td class="border-right p-2 text-13 align-top border-bottom border-top-0">
                                        <button type="button" class="btn-add-warranty btn">
                                            +
                                        </button>
                                    </td>
                                    <td colspan="3"
                                        class="border-right p-2 text-13 align-top border-bottom border-top-0"></td>
                                </tr>
                            </tbody>
                        </table>
                        <input type="hidden" name="data-test" id="data-test">
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
    </div>
</form>
<script>
    var products = @json($products);
</script>

<script src="{{ asset('js/addproduct.js') }}"></script>
<script src="{{ asset('js/receiving.js') }}"></script>
<script src="{{ asset('js/barcode.js') }}"></script>
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
                        const date = response.warranty[0].serial_number.exports[0].export.date_create;
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
                    alert("Không thể lấy thông tin bảo hành. Vui lòng thử lại.");
                },
            });
        }
    });
    $(document).ready(function() {
        // Xử lý sự kiện thay đổi radio button
        $('input[type="radio"]').on('change', function() {
            let formType = $('input[name="form_type"]:checked').val();
            let contentWrapper = $('#content-wrapper');

            if (formType) {
                contentWrapper.removeClass('blur-wrapper');
                // Gọi hàm kiểm tra bảo hành
                checkWarrantyStatus(formType);
            } else {
                contentWrapper.addClass('blur-wrapper');
            }
        });

        // Hàm kiểm tra trạng thái bảo hành
        function checkWarrantyStatus(formType) {
            $('.name_warranty').each(function() {
                let warrantyInput = $(this);
                let serialData = warrantyInput.siblings('.id_seri').val(); // Lấy giá trị serial
                let warrantyId = warrantyInput.siblings('.id_warranty')
                    .val(); // Lấy giá trị warranty ID
                let checkIcon = warrantyInput.next('.check-icon'); // Lấy biểu tượng check
                // Gọi hàm kiểm tra bảo hành
                checkSerials(formType, serialData, warrantyId, checkIcon);
            });
        }
    });
</script>
