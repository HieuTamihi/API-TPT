@include('partials.header', ['activeGroup' => 'reports', 'activeName' => 'export_import'])
@section('title', $title)
<div class="content-wrapper m-0 min-height--none p-0">
    <div class="content-header-fixed px-1">
        <div class="content__header--inner">
            <x-search-filter :keywords="request('keywords')" :filters="['Mã hàng', 'Tên hàng', 'Số lượng nhập', 'Số lượng xuất']" :filtersTime="['Ngày','Tháng', 'Quý', 'Năm']">
                <x-filter-text name="ma-hang" title="Mã hàng" />
                <x-filter-text name="ten-hang" title="Tên hàng" />
                <x-filter-compare name="so-luong-nhap" title="Số lượng nhập" />
                <x-filter-compare name="so-luong-xuat" title="Số lượng xuất" />
                @slot('slot1')
                <x-filter-date name="ngay" title="Khoảng thời gian" />
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
            <div class="row result-filter-rp_export_import margin-left20">
            </div>
            <div class="container-fluided">
                <div class="bg-filter-search text-center border-bottom-0">
                    <p class="font-weight-bold text-uppercase info-chung--heading text-center py-2">
                        BÁO CÁO HÀNG XUẤT NHẬP
                    </p>
                </div>
                <div class="col-12 p-0 m-0">
                    <div class="card">
                        <!-- /.card-header -->
                        <div class="outer2 table-responsive text-nowrap">
                            <table id="example2" class="table table-hover bg-white rounded">
                                <thead class="border-custom">
                                    <tr>
                                        <th class="height-40 py-0 border-right" scope="col">
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
                                        <th class="height-40 py-0 border-right" scope="col">
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
                                        <th class="height-40 py-0 border-right" scope="col">
                                            <span class="d-flex justify-content-start">
                                                <a href="#" class="sort-link btn-submit"
                                                    data-sort-by="product_import" data-sort-type="DESC">
                                                    <button class="btn-sort" type="submit">
                                                        <span class="text-14">Số lượng hàng nhập</span>
                                                    </button>
                                                </a>
                                                <div class="icon" id="icon-product_import"></div>
                                            </span>
                                        </th>
                                        <th class="height-40 py-0 border-right" scope="col">
                                            <span class="d-flex justify-content-start">
                                                <a href="#" class="sort-link btn-submit"
                                                    data-sort-by="product_export" data-sort-type="DESC">
                                                    <button class="btn-sort" type="submit">
                                                        <span class="text-14">Số lượng hàng xuất</span>
                                                    </button>
                                                </a>
                                                <div class="icon" id="icon-product_export"></div>
                                            </span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="tbody-rp_export_import">
                                    <?php $sumExport = 0;
                                    $sumImport = 0; ?>
                                    @foreach ($products as $item)
                                        <tr class="position-relative rp_export_import-info height-40">
                                            <input type="hidden" name="id-rp_export_import" class="id-rp_export_import"
                                                id="id-rp_export_import" value="{{ $item['product_id'] }}">
                                            <td class="text-13-black border-right product-code border-bottom py-0">
                                                {{ $item['product_code'] }}
                                            </td>
                                            <td
                                                class="text-13-black border border-left-0 product_name text-left border-bottom py-0 max-width180">
                                                {{ $item['product_name'] }}
                                            </td>
                                            <td
                                                class="text-13-black border border-left-0 product_import_{{ $item['product_id'] }} border-bottom py-0">
                                                {{ $item['total_import'] }}
                                            </td>
                                            <td
                                                class="text-13-black border border-left-0 product_export_{{ $item['product_id'] }} border-bottom py-0">
                                                {{ $item['total_export'] }}
                                            </td>
                                        </tr>
                                        <?php $sumExport += $item['total_export'];
                                        $sumImport += $item['total_import']; ?>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="footer-summary">
                            <table class="table-footer">
                                <tr>
                                    <td class="text-right" colspan="2"></td>
                                    <td class="text-danger total_import">Tổng số lượng hàng nhập:
                                        <span>{{ $sumImport }}</span>
                                    </td>
                                    <td class="text-danger total_export">Tổng số lượng hàng xuất:
                                        <span>{{ $sumExport }}</span>
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
            type_filter: $('#type-filter').val(),
            ma: getData('#ma-hang', this),
            ten: getData('#ten-hang', this),
            date: retrieveDateData(this, 'ngay'),
            so_luong_nhap: retrieveComparisonData(this, 'so-luong-nhap'),
            so_luong_xuat: retrieveComparisonData(this, 'so-luong-xuat'),
            sort: getSortData(buttonElement),
        };
        var nametable = "rp_export_import";
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
                url: "{{ route('reports.export_import') }}",
                data: formData,
                success: function(data) {
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
