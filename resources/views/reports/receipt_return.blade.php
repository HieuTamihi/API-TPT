@include('partials.header', ['activeGroup' => 'reports', 'activeName' => 'receipt_return'])
@section('title', $title)
<div class="content-wrapper m-0 min-height--none p-0">
    <div class="content-header-fixed px-1">
        <div class="content__header--inner">
            <x-search-filter :keywords="request('keywords')" :filters="['Mã hàng', 'Tên hàng', 'Hàng tiếp nhận', 'Hàng đã trả']" :filtersTime="['Ngày','Tháng', 'Quý', 'Năm']">
                <x-filter-text name="ma-hang" title="Mã hàng" />
                <x-filter-text name="ten-hang" title="Tên hàng" />
                <x-filter-compare name="hang-tiep-nhan" title="Hàng tiếp nhận" />
                <x-filter-compare name="hang-da-tra" title="Hàng đã trả" />
                @slot('slot1')
                    <x-filter-date name="ngay" title="Ngày" />
                    <x-filter-month name="thang" title="Tháng" />
                    <x-filter-month name="quy" title="Quý" />
                    <x-filter-month name="nam" title="Năm" />
                @endslot
            </x-search-filter>
            <button class="m-0 btn-outline-primary" id="exportBtn">Export Excel</button>
        </div>
    </div>
    <div class="content margin-top-86">

        <section class="content report-content">
            <div class="row result-filter-rp_receipt_return margin-left20">
            </div>
            <div class="container-fluided">
                <div class="bg-filter-search text-center border border-bottom-0">
                    <p class="font-weight-bold text-uppercase info-chung--heading text-center py-2">
                        BÁO CÁO HÀNG TIẾP NHẬN - TRẢ HÀNG
                    </p>
                </div>
                <div class="col-12 p-0 m-0">
                    <div class="card">
                        <!-- /.card-header -->
                        <div class="outer2 table-responsive text-nowrap">
                            <table id="example2" class="table table-hover bg-white rounded">
                                <thead class="border-custom">
                                    <tr>
                                        <th class="height-40 py-0 border" scope="col">
                                            <span class="d-flex justify-content-start">
                                                <a href="#" class="sort-link btn-submit" data-sort-by="product_code"
                                                    data-sort-type="DESC">
                                                    <button class="btn-sort" type="submit">
                                                        <span class="text-14">Mã hàng</span>
                                                    </button>
                                                </a>
                                                <div class="icon" id="icon-product_code"></div>
                                            </span>
                                        </th>
                                        <th class="height-40 py-0 border" scope="col">
                                            <span class="d-flex justify-content-start">
                                                <a href="#" class="sort-link btn-submit"
                                                    data-sort-by="product_name" data-sort-type="DESC">
                                                    <button class="btn-sort" type="submit">
                                                        <span class="text-14">Tên hàng</span>
                                                    </button>
                                                </a>
                                                <div class="icon" id="icon-product_name"></div>
                                            </span>
                                        </th>
                                        <th class="height-40 py-0 border" scope="col">
                                            <span class="d-flex justify-content-start">
                                                <a href="#" class="sort-link btn-submit"
                                                    data-sort-by="product_import" data-sort-type="DESC">
                                                    <button class="btn-sort" type="submit">
                                                        <span class="text-14">Số lượng hàng tiếp nhận</span>
                                                    </button>
                                                </a>
                                                <div class="icon" id="icon-product_import"></div>
                                            </span>
                                        </th>
                                        <th class="height-40 py-0 border" scope="col">
                                            <span class="d-flex justify-content-start">
                                                <a href="#" class="sort-link btn-submit"
                                                    data-sort-by="product_export" data-sort-type="DESC">
                                                    <button class="btn-sort" type="submit">
                                                        <span class="text-14">Số lượng hàng đã trả</span>
                                                    </button>
                                                </a>
                                                <div class="icon" id="icon-product_export"></div>
                                            </span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="tbody-rp_receipt_return">
                                    <?php $sumReceive = 0;
                                    $sumReturn = 0; ?>
                                    @foreach ($products as $item)
                                        <tr class="position-relative rp_receipt_return-info height-40">
                                            <input type="hidden" name="id-rp_receipt_return"
                                                class="id-rp_receipt_return" id="id-rp_receipt_return"
                                                value="{{ $item['product_id'] }}">
                                            <td class="text-13-black border-right product-code border-bottom py-0">
                                                {{ $item['product_code'] }}
                                            </td>
                                            <td
                                                class="text-13-black border border-left-0 product_name text-left border-bottom py-0 max-width180">
                                                {{ $item['product_name'] }}
                                            </td>
                                            <td
                                                class="text-13-black border border-left-0 product_import_{{ $item['product_id'] }} border-bottom py-0">
                                                {{ $item['total_receive'] }}
                                            </td>
                                            <td
                                                class="text-13-black border border-left-0 product_export_{{ $item['product_id'] }} border-bottom py-0">
                                                {{ $item['total_return'] }}
                                            </td>
                                        </tr>
                                        <?php $sumReceive += $item['total_receive'];
                                        $sumReturn += $item['total_return']; ?>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="footer-summary">
                                <table class="table-footer">
                                    <tr>
                                        <td class="text-right" colspan="2"></td>
                                        <td class="text-danger">Tổng số lượng hàng tiếp nhận:
                                            <span class="total_import">{{ $sumReceive }}</span>
                                        </td>
                                        <td class="text-danger">Tổng số lượng hàng đã trả:
                                            <span class="total_export">{{ $sumReturn }}</span>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <input type="hidden" id="type-filter" value="month">
                        <input type="hidden" id="time-filter"
                            value="{{ \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d H:i:s') }}">
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
<script src="{{ asset('js/filter.js') }}"></script>
<script src="{{ asset('js/exports_excel.js') }}"></script>

<script>
    $(document).on('click', '.btn-submit', function(e) {
        if (!$(e.target).is('input[type="checkbox"]')) e.preventDefault();
        var buttonElement = this;
        var buttonElement1 = $(this);
        var buttonName = buttonElement1.data('button-name');
        const {
            month,
            quarter,
            year
        } = setFilterValues(buttonName);
        // Thu thập dữ liệu từ form
        var formData = {
            search: $('#search').val(),
            type: buttonName,
            month: month,
            quarter: quarter,
            year: year,
            date: retrieveDateData(this, 'ngay'),
            type_filter: $('#type-filter').val(),
            ma: getData('#ma-hang', this),
            ten: getData('#ten-hang', this),
            so_luong_nhap: retrieveComparisonData(this, 'hang-tiep-nhan'),
            so_luong_xuat: retrieveComparisonData(this, 'hang-da-tra'),
            sort: getSortData(buttonElement),
        };
        console.log(formData);
        var nametable = "rp_receipt_return";
        // Ẩn tùy chọn nếu cần
        if (!$(e.target).closest('li, input[type="checkbox"]').length) {
            $('#' + $(this).data('button-name') + '-options').hide();
            // Thiết lập CSRF token nếu cần
            $.ajaxSetup({
                headers: {
                    csrftoken: "{{ csrf_token() }}",
                },
            });
            // Gọi hàm AJAX
            $.ajax({
                type: "get",
                url: "{{ route('reports.receipt_return') }}",
                data: formData,
                success: function(data) {
                    console.log(data);
                    updateFilters(
                        data,
                        filters,
                        `.result-filter-${nametable}`,
                        `.tbody-${nametable}`,
                        `.${nametable}-info`,
                        `.id-${nametable}`,
                        nametable
                    );
                    var totalImport = 0;
                    var totalExport = 0;
                    $.each(data.data, function(index, item) {
                        $('.product_import_' + item.id).text(item.product_import);
                        $('.product_export_' + item.id).text(item.product_export);
                        totalImport += item.product_import;
                        totalExport += item.product_export;
                    });
                    $('.total_import').text(totalImport);
                    $('.total_export').text(totalExport);
                },
                error: function(xhr, status, error) {
                    // console.error("AJAX Error:", status, error);
                    // alert("Đã xảy ra lỗi, vui lòng thử lại.");
                },
            });
        }
        if (!$(e.target).closest('li, input[type="checkbox"]').length) {
            $('#' + $(this).data('button-name') + '-options').hide();
        }
    });
    exportTableToExcel("#exportBtn", "#example2", "bao_cao_hang_xuat_nhap.xlsx");
</script>
