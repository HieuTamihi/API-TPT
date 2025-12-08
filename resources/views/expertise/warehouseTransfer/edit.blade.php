@include('partials.header', ['activeGroup' => 'manageProfess', 'activeName' => 'warehouseTransfer'])
@section('title', $title)
<form id="form-submit" action="{{ route('warehouseTransfer.update', $warehouseTransfer->id) }}" method="POST">
    @csrf
    @method('PUT')
    <input type="hidden" value="{{ $warehouseTransfer->id }}" id="warehouseTransferId">
    <div class="content-wrapper--2Column m-0 min-height--none pr-2">
        <div class="content-header-fixed-report-1 pt-2">
            <div class="content__header--inner">
                <div class="content__heading--left opacity-0"></div>
                <div class="d-flex content__heading--right">
                    <div class="row m-0">
                        <a href="{{ route('warehouseTransfer.index') }}">
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
                    </div>
                </div>
            </div>
        </div>
        <div class="content-wrapper2 px-0 py-0 margin-top-118">
            {{-- Thông tin khách hàng --}}
            <div class="border">
                <div>
                    <div class="bg-filter-search border-0 text-center">
                        <p class="font-weight-bold text-uppercase info-chung--heading text-center">
                            THÔNG TIN PHIẾU CHUYỂN KHO
                        </p>
                    </div>
                    <div class="row">
                        <div class="col-md-4 m-0 p-0">
                            <div
                                class="d-flex w-100 justify-content-between py-2 px-3 border border-bottom-0 border-right-0 align-items-center text-left text-nowrap position-relative height-44">
                                <span class="text-13-black text-nowrap mr-3 font-weight-bold"
                                    style="width: 180px;">Mã phiếu</span>
                                <input type="text" name="code" style="flex:2;" placeholder="Nhập thông tin" readonly
                                    required value="{{ $warehouseTransfer->code }}"
                                    class="text-13-black w-50 border-0 bg-input-guest date_picker py-2 px-2">
                            </div>
                            <div
                                class="d-flex w-100 justify-content-between py-2 px-3 border border-bottom-0 border-right-0 align-items-center text-left text-nowrap position-relative height-44">
                                <span class="text-13-black text-nowrap mr-3 required-label font-weight-bold"
                                    style="width: 180px;">Ngày lập phiếu</span>
                                <input placeholder="Nhập thông tin" autocomplete="off" required type="text"
                                    id="dateCreate"
                                    value="{{ date_format(new DateTime($warehouseTransfer->transfer_date), 'd/m/Y') }}"
                                    class="text-13-black w-50 border-0 bg-input-guest bg-input-guest-blue py-2 px-2"
                                    style=" flex:2;" />
                                <input type="hidden" value="{{ $warehouseTransfer->transfer_date }}"
                                    name="transfer_date" id="hiddenDateCreate">
                            </div>
                        </div>
                        <div class="col-md-4 m-0 p-0">
                            <div
                                class="d-flex w-100 justify-content-between py-2 px-3 border border-bottom-0 border-right-0 align-items-center text-left text-nowrap position-relative height-44">
                                <span class="text-13-black text-nowrap mr-3 font-weight-bold"
                                    style="width: 180px;">Người lập phiếu</span>
                                <input autocomplete="off" placeholder="Nhập thông tin" required id="user_name" readonly
                                    class="text-13-black w-50 border-0 bg-input-guest py-2 px-2" style="flex:2;"
                                    value="{{ $warehouseTransfer->user->name }}" />
                                <input type="hidden" name="user_id" id="user_id"
                                    value="{{ $warehouseTransfer->user_id }}">
                            </div>
                            <div
                                class="d-flex w-100 justify-content-between py-2 px-3 border border-bottom-0 border-right-0 align-items-center text-left text-nowrap position-relative height-44">
                                <span class="text-13-black btn-click font-weight-bold" style="width: 195px;">Ghi chú</span>
                                <input placeholder="Nhập thông tin" autocomplete="off" name="note"
                                    value="{{ $warehouseTransfer->note }}"
                                    class="text-13-black w-100 border-0 bg-input-guest bg-input-guest-blue py-2 px-2"
                                    style="flex:2;" />
                            </div>
                        </div>
                        <div class="col-md-4 m-0 p-0">
                            <div style="width: 99%;"
                                class="d-flex justify-content-between py-2 px-3 border border-bottom-0 border-right-0 align-items-center text-left text-nowrap position-relative height-44">
                                <span class="text-13-black text-nowrap mr-3 font-weight-bold"
                                    style="width: 180px;">Kho
                                    xuất</span>
                                <input id="warehouse_name" placeholder="Nhập thông tin" type="text" required
                                    onkeypress="return false;" autocomplete="off" readonly
                                    value="{{ $warehouseTransfer->fromWarehouse->warehouse_name }}"
                                    class="text-13-black w-50 border-0 bg-input-guest py-2 px-2"
                                    style="flex:2;" />
                                <input type="hidden" name="from_warehouse_id" id="warehouse_id"
                                    value="{{ $warehouseTransfer->from_warehouse_id }}">
                                {{-- <div class="">
                                    <div id="listWarehouse"
                                        class="bg-white position-absolute rounded list-guest shadow p-1 z-index-block"
                                        style="z-index: 99;display: none;">
                                        <div class="p-1">
                                            <div class="position-relative">
                                                <input type="text" placeholder="Nhập thông tin" autocomplete="off"
                                                    class="pr-4 w-100 input-search bg-input-guest"
                                                    id="searchWarehouse">
                                                <span id="search-icon" class="search-icon">
                                                    <i class="fas fa-search text-table" aria-hidden="true"></i>
                                                </span>
                                            </div>
                                        </div>
                                        <ul class="m-0 p-0 scroll-data">
                                            @foreach ($warehouse as $warehouse_value)
                                                <li class="p-2 align-items-center text-wrap border-top"
                                                    data-id="{{ $warehouse_value->id }}">
                                                    <a href="#" title="{{ $warehouse_value->warehouse_name }}"
                                                        style="flex:2;" id="{{ $warehouse_value->id }}"
                                                        data-name="{{ $warehouse_value->warehouse_name }}"
                                                        name="warehouse-info" class="warehouse-info">
                                                        <span
                                                            class="text-13-black">{{ $warehouse_value->warehouse_name }}</span>
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div> --}}
                            </div>
                            <div style="width: 99%;"
                                class="d-flex justify-content-between py-2 px-3 border border-bottom-0 border-right-0 align-items-center text-left text-nowrap position-relative height-44">
                                <span class="text-13-black text-nowrap mr-3 font-weight-bold"
                                    style="width: 180px;">Kho
                                    nhận</span>
                                <input id="warehouse_receive_name" placeholder="Nhập thông tin" type="text"
                                    required onkeypress="return false;" autocomplete="off" readonly
                                    value="{{ $warehouseTransfer->toWarehouse->warehouse_name }}"
                                    class="text-13-black w-50 border-0 bg-input-guest py-2 px-2"
                                    style="flex:2;" />
                                <input type="hidden" name="to_warehouse_id" id="warehouse_receive_id"
                                    value="{{ $warehouseTransfer->to_warehouse_id }}">
                                {{-- <div class="">
                                    <div id="listWarehouseReceive"
                                        class="bg-white position-absolute rounded list-guest shadow p-1 z-index-block"
                                        style="z-index: 99;display: none;">
                                        <div class="p-1">
                                            <div class="position-relative">
                                                <input type="text" placeholder="Nhập thông tin" autocomplete="off"
                                                    class="pr-4 w-100 input-search bg-input-guest"
                                                    id="searchWarehouseReceive">
                                                <span id="search-icon" class="search-icon">
                                                    <i class="fas fa-search text-table" aria-hidden="true"></i>
                                                </span>
                                            </div>
                                        </div>
                                        <ul class="m-0 p-0 scroll-data">
                                            @foreach ($warehouse as $warehouse_value)
                                                <li class="p-2 align-items-center text-wrap border-top"
                                                    data-id="{{ $warehouse_value->id }}">
                                                    <a href="#" title="{{ $warehouse_value->warehouse_name }}"
                                                        style="flex:2;" id="{{ $warehouse_value->id }}"
                                                        data-name="{{ $warehouse_value->warehouse_name }}"
                                                        name="warehouse-receive-info" class="warehouse-receive-info">
                                                        <span
                                                            class="text-13-black">{{ $warehouse_value->warehouse_name }}</span>
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div> --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{-- Thông tin sản phẩm --}}
            <div class="content report-content">
                <div id="title--fixed" class="bg-filter-search text-center border border-bottom-0 border-top-0">
                    <p class="font-weight-bold text-uppercase info-chung--heading text-center">THÔNG TIN HÀNG</p>
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
                                        <span class="text-table text-13-black font-weight-bold">Serial Number</span>
                                    </th>
                                    <th class="border-right px-2 p-0" style="width: 10%; display: none;"
                                        id="title-borrow">
                                        <span class="text-table text-13-black font-weight-bold">Serial
                                            Number mượn</span>
                                    </th>
                                    <th class="border-right note px-2 p-0 text-left" style="width: 15%;">
                                        <span class="text-table text-13-black font-weight-bold">Ghi chú</span>
                                    </th>
                                    {{-- <th class="border-right" style="width: 5%;"></th> --}}
                                </tr>
                            </thead>
                            <tbody id="tbody-product-data">
                                @foreach ($productWarehouse as $productId => $products)
                                    @php
                                        // Lấy thông tin sản phẩm của product_id
                                        $product = $products->first()->product;
                                    @endphp
                                    @foreach ($products as $item)
                                        <tr id="serials-data" class="row-product bg-white" data-index="1"
                                            data-product-code="{{ $product->product_code }}"
                                            data-product-id="{{ $item->product->id }}">
                                            <td
                                                class="border-right p-2 text-13 align-top border-bottom border-top-0 d-none">
                                                <input type="text" autocomplete="off"
                                                    class="border-0 pl-1 pr-2 py-1 w-100 product_id height-32"
                                                    readonly="" name="product_id[]"
                                                    value="{{ $item->product->id }}">
                                            </td>
                                            <td
                                                class="border-right p-2 text-13 align-top border-bottom border-top-0 pl-4">
                                                <input type="text" autocomplete="off"
                                                    class="border-0 pl-1 pr-2 py-1 w-100 product_code height-32"
                                                    readonly="" name="product_code"
                                                    value="{{ $item->product->product_code }}">
                                            </td>
                                            <td class="border-right p-2 text-13 align-top border-bottom border-top-0">
                                                <input type="text" autocomplete="off"
                                                    class="border-0 pl-1 pr-2 py-1 w-100 product_name height-32"
                                                    readonly="" name="product_name"
                                                    value="{{ $item->product->product_name }}">
                                            </td>
                                            <td class="border-right p-2 text-13 align-top border-bottom border-top-0">
                                                <input type="text" autocomplete="off"
                                                    class="border-0 pl-1 pr-2 py-1 w-100 brand height-32"
                                                    readonly="" name="brand"
                                                    value="{{ $item->product->brand }}">
                                            </td>
                                            <td class="border-right p-2 text-13 align-top border-bottom border-top-0">
                                                <input type="text" autocomplete="off"
                                                    class="border-0 pl-1 pr-2 py-1 w-100 height-32" readonly=""
                                                    name="" value="1">
                                            </td>
                                            <td class="border-right p-2 text-13 align-top border-bottom border-top-0">
                                                <input type="text" autocomplete="off"
                                                    class="border-0 pl-1 pr-2 py-1 w-100 serial height-32" readonly
                                                    name="serial[]" value="{{ $item->serialNumber->serial_code }}">
                                            </td>
                                            @if ($warehouseTransfer->from_warehouse_id == 2)
                                                <td
                                                    class="border-right p-2 text-13 align-top border-bottom border-top-0">
                                                    <input type="text" autocomplete="off"
                                                        class="border-0 pl-1 pr-2 py-1 w-100 serial_borrow height-32"
                                                        readonly name="serial_borrow_input[]"
                                                        value="{{ $item->serialNumberBorrow->serial_code }}">
                                                </td>
                                            @endif
                                            <td class="border-right p-2 text-13 align-top border-bottom border-top-0">
                                                <input type="text" autocomplete="off"
                                                    class="border-0 pl-1 pr-2 py-1 w-100 note_seri height-32 bg-input-guest-blue"
                                                    name="note_seri[]" value="{{ $item->note }}">
                                            </td>
                                            {{-- <td class="p-2 align-top activity border-bottom border-top-0 border-right text-center">
                                                <svg class="delete-row" width="17" height="17"
                                                    viewBox="0 0 17 17" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                                        d="M13.1417 6.90625C13.4351 6.90625 13.673 7.1441 13.673 7.4375C13.673 7.47847 13.6682 7.5193 13.6589 7.55918L12.073 14.2992C11.8471 15.2591 10.9906 15.9375 10.0045 15.9375H6.99553C6.00943 15.9375 5.15288 15.2591 4.92702 14.2992L3.34113 7.55918C3.27393 7.27358 3.45098 6.98757 3.73658 6.92037C3.77645 6.91099 3.81729 6.90625 3.85826 6.90625H13.1417ZM9.03125 1.0625C10.4983 1.0625 11.6875 2.25175 11.6875 3.71875H13.8125C14.3993 3.71875 14.875 4.19445 14.875 4.78125V5.3125C14.875 5.6059 14.6371 5.84375 14.3438 5.84375H2.65625C2.36285 5.84375 2.125 5.6059 2.125 5.3125V4.78125C2.125 4.19445 2.6007 3.71875 3.1875 3.71875H5.3125C5.3125 2.25175 6.50175 1.0625 7.96875 1.0625H9.03125ZM9.03125 2.65625H7.96875C7.38195 2.65625 6.90625 3.13195 6.90625 3.71875H10.0938C10.0938 3.13195 9.61805 2.65625 9.03125 2.65625Z"
                                                        fill="#6B6F76"></path>
                                                </svg>
                                            </td> --}}
                                        </tr>
                                    @endforeach
                                    {{-- Nút thêm --}}
                                    {{-- <tr id="add-row-product" class="bg-white" data-product-code="SP1"
                                        data-product-id="{{ $item->product->id }}">
                                        <td colspan="{{ $warehouseTransfer->from_warehouse_id == 2 ? 8 : 7 }}"
                                            class="border-right p-2 text-13 align-top border-bottom border-top-0 pl-4">
                                            <button type="button" class="save-info-product btn"
                                                data-product-id="{{ $item->product->id }}"
                                                data-product-code="{{ $item->product->product_code }}"
                                                data-product-name="{{ $item->product->product_name }}"
                                                data-product-brand="{{ $item->product->brand }}">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    viewBox="0 0 16 16" fill="none">
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
                                    </tr> --}}
                                @endforeach
                            </tbody>
                        </table>
                        <input type="hidden" name="data-test" id="data-test">
                        {{-- <section class="content mt-2">
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
                        </section> --}}
                        <x-add-product-modal :id="'modal-id'" title="Thêm sản phẩm" :data-product="$productAll"
                            name="CPCK" />
                    </section>
                </div>
            </div>
        </div>
    </div>
</form>
<script>
    $(document).ready(function() {
        if ($("#warehouse_id").val() !== "1") {
            $("#title-borrow").show();
        }
    });
    //format định dạng
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
</script>
<script src="{{ asset('js/addproduct.js') }}"></script>
<script src="{{ asset('js/imports.js') }}"></script>
