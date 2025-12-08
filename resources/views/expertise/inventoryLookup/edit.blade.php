@include('partials.header', ['activeGroup' => 'manageProfess', 'activeName' => 'inventoryLookup'])
@section('title', $title)
<form id="form-submit" action="{{ route('inventoryLookup.update', $inventoryLookup->id) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="content-wrapper--2Column m-0 min-height--none pr-2">
        <div class="content-header-fixed-report-1 pt-2">
            <div class="content__header--inner">
                <div class="content__heading--left opacity-0"></div>
                <div class="d-flex content__heading--right">
                    <div class="row m-0">
                        <div class="dropdown mx-1">
                            <a href="{{ route('inventoryLookup.index') }}">
                                <button type="button"
                                    class="btn-save-print rounded mx-1 py-1 px-2 d-flex align-items-center h-100">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        viewBox="0 0 16 16" fill="none">
                                        <path
                                            d="M5.6738 11.4801C5.939 11.7983 6.41191 11.8413 6.73012 11.5761C7.04833 11.311 7.09132 10.838 6.82615 10.5198L5.3513 8.75H12.25C12.6642 8.75 13 8.41421 13 8C13 7.58579 12.6642 7.25 12.25 7.25L5.3512 7.25L6.82615 5.4801C7.09132 5.1619 7.04833 4.689 6.73012 4.4238C6.41191 4.1586 5.939 4.2016 5.6738 4.5198L3.1738 7.51984C2.942 7.79798 2.942 8.20198 3.1738 8.48012L5.6738 11.4801Z"
                                            fill="black" />
                                    </svg>
                                    <span class="text-button text-dark ml-2 font-weight-bold">Trở về</span>
                                </button>
                            </a>
                        </div>
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
        <div class="content-wrapper2 px-0 py-0 margin-top-118">
            {{-- Thông tin khách hàng --}}
            <div class="border">
                <div>
                    <div class="bg-filter-search border-0 text-center">
                        <p class="font-weight-bold text-uppercase info-chung--heading text-center">
                            THÔNG TIN HÀNG BẢO TRÌ ĐỊNH KỲ
                        </p>
                    </div>
                    <div class="container-fluided">
                        <section class="content">
                            <table class="table mb-0" id="inputcontent">
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
                                            <span class="text-table text-13-black font-weight-bold">Serial Number</span>
                                        </th>
                                        <th class="border-right px-2 p-0" style="width: 10%;">
                                            <span class="text-table text-13-black font-weight-bold">Ngày nhập
                                                hàng</span>
                                        </th>
                                        <th class="border-right note px-2 p-0 text-left" style="width: 15%;">
                                            <span class="text-table text-13-black font-weight-bold">Thời gian tồn
                                                kho</span>
                                        </th>
                                        <th class="border-right note px-2 p-0 text-left" style="width: 15%;">
                                            <span class="text-table text-13-black font-weight-bold">Ngày bảo trì</span>
                                        </th>
                                        <th class="border-right note px-2 p-0 text-left border-right-0" style="width: 15%;">
                                            <span class="text-table text-13-black font-weight-bold">Ghi chú</span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="text-13-black border-right border-top-0">
                                            {{ $inventoryLookup->product->product_code }}
                                        </td>
                                        <td class="text-13-black border-right border-top-0">
                                            {{ $inventoryLookup->product->product_name }}
                                        </td>
                                        <td class="text-13-black border-right border-top-0">
                                            {{ $inventoryLookup->product->brand }}
                                        </td>
                                        <td class="text-13-black border-right border-top-0">
                                            {{ $inventoryLookup->serialNumber->serial_code }}
                                        </td>
                                        <td class="text-13-black border-right border-top-0">
                                            {{ date_format(new DateTime($inventoryLookup->import_date), 'd/m/Y') }}
                                        </td>
                                        <td class="text-13-black border-right border-top-0">
                                            {{ $inventoryLookup->storage_duration }} ngày
                                        </td>
                                        <td class="text-13-black border-right border-top-0">
                                            <input type="date" id="dateCreate"
                                                class="text-13-black w-100 border-0 bg-input-guest bg-input-guest-blue py-2 px-2">
                                            <input type="hidden" name="warranty_date" value="{{ date('Y-m-d') }}"
                                                id="hiddenDateCreate">
                                        </td>
                                        <td class="text-13-black border-right border-top-0 border-right-0">
                                            <input type="text" name="note" autocomplete="off"
                                                class="text-13-black w-100 border-0 bg-input-guest bg-input-guest-blue py-2 px-2">
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </section>
                    </div>
                </div>
            </div>
            {{-- Thông tin sản phẩm --}}
            <div class="content">
                <div id="title--fixed" class="bg-filter-search text-center border-0">
                    <p class="font-weight-bold text-uppercase info-chung--heading text-center">LỊCH SỬ BẢO TRÌ ĐỊNH KỲ
                    </p>
                </div>
                <div class="container-fluided">
                    <section class="content">
                        <table class="table" id="inputcontent">
                            <thead>
                                <tr style="height:44px;">
                                    <th class="border-right px-2 p-0" style="width: 8%">
                                        <span class="text-table text-13-black font-weight-bold pl-3">Serial
                                            Number</span>
                                    </th>
                                    <th class="border-right px-2 p-0 text-left" style="width: 15%; z-index:99;">
                                        <span class="text-table text-13-black font-weight-bold">Ngày nhập hàng</span>
                                    </th>
                                    <th class="border-right px-2 p-0 text-left" style="width: 8%;">
                                        <span class="text-table text-13-black font-weight-bold">Thời gian tồn
                                            kho</span>
                                    </th>
                                    <th class="border-right px-2 p-0" style="width: 8%;">
                                        <span class="text-table text-13-black font-weight-bold">Ngày bảo trì</span>
                                    </th>
                                    <th class="border-right px-2 p-0" style="width: 10%;">
                                        <span class="text-table text-13-black font-weight-bold">Ghi chú</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($histories as $item_history)
                                    <tr>
                                        <td class="text-13-black border-right border-bottom border-top-0">
                                            {{ $item_history->inventoryLookup->serialNumber->serial_code }}
                                        </td>
                                        <td class="text-13-black border-right border-bottom border-top-0">
                                            {{ date_format(new DateTime($item_history->inventoryLookup->import_date), 'd/m/Y') }}
                                        </td>
                                        <td class="text-13-black border-right border-bottom border-top-0">
                                            {{ $item_history->storage_duration }} ngày
                                        </td>
                                        <td class="text-13-black border-right border-bottom border-top-0">
                                            {{ date_format(new DateTime($item_history->warranty_date), 'd/m/Y') }}
                                        </td>
                                        <td class="text-13-black border-right border-bottom border-top-0">
                                            {{ $item_history->note }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </section>
                </div>
            </div>
        </div>
    </div>
</form>
<script>
    flatpickr("#dateCreate", {
        locale: "vn",
        dateFormat: "d/m/Y",
        defaultDate: "today",
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
