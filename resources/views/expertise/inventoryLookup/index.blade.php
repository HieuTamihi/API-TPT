@include('partials.header', ['activeGroup' => 'manageProfess', 'activeName' => 'inventoryLookup'])
@section('title', $title)
<div class="content-wrapper m-0 min-height--none p-0">
    <div class="content-header-fixed px-1">
        <div class="content__header--inner" style="position: relative; display: flex; align-items: center;">
            <x-search-filter :keywords="request('keywords')" :filters="[
                'Mã hàng',
                'Tên hàng',
                'Hãng',
                'Serial',
                'Nhà cung cấp',
                'Ngày nhập hàng',
                'Thời gian tồn kho',
                'Trạng thái',
            ]">
                <x-filter-text name="ma-hang" title="Mã hàng" />
                <x-filter-text name="ten-hang" title="Tên hàng" />
                <x-filter-text name="serial" title="Serial" />
                <x-filter-text name="hang" title="Hãng" />
                <x-filter-checkbox :dataa='$providers' name="nha-cung-cap" title="Nhà cung cấp" button="nha-cung-cap"
                    namedisplay="provider_name" />
                <x-filter-date name="ngay-nhap-hang" title="Ngày nhập hàng" />
                <x-filter-status name="trang-thai" title="Trạng thái" :filters="[
                    ['key' => '1', 'value' => 'Tới hạn bào trì', 'color' => '#858585'],
                    ['key' => '0', 'value' => '', 'color' => '#08AA36BF'],
                ]" />
                <x-filter-compare name="thoi-gian-ton-kho" title="Thời gian tồn kho" />
            </x-search-filter>
            <div class="d-flex content__heading--right">
                <div class="row m-0">
                    <div class="toggle-container" style="">
                        <button class="custom-btn-1 mr-1" id="toggleSummary">
                            Hiển thị bảng tổng hợp
                        </button>
                    </div>
                </div>
                <button class="m-0 btn-outline-primary" id="exportBtn">Export Excel</button>

            </div>


        </div>
    </div>
    <div class="content margin-top-127">
        <section class="content">
            <div class="container-fluided">
                <div class="row result-filter-inven-lookup margin-left20 my-1">
                </div>
                <div id="summaryTable" class="col-12 p-0 m-0" style="display: none;">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Tổng hợp tồn kho</h3>
                        </div>
                        <div class="outer2 table-responsive text-nowrap">
                            <table id="summaryTableData" class="table table-hover bg-white rounded"
                                style="font-size: 14px;">
                                <thead class="border-custom">
                                    <tr class="height-40 py-0 border-right-0" scope="col">
                                        <th class="height-40 py-0 border-right-0" scope="col">
                                            <span class="d-flex justify-content-start">
                                                <a href="#" class="sort-link btn-submit"
                                                    data-sort-by="product_code" data-sort-type="DESC">
                                                    <button class="btn-sort" type="submit">
                                                        <span class="text-14">Mã hàng</span>
                                                    </button>
                                                </a>
                                                <div class="icon" id="icon-product_code"></div>
                                            </span>
                                        </th>
                                        <th class="height-40 py-0 border-right-0" scope="col">
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
                                        <th class="height-40 py-0 border-right-0" scope="col">
                                            <span class="d-flex justify-content-start">
                                                <a href="#" class="sort-link btn-submit" data-sort-by="brand"
                                                    data-sort-type="DESC">
                                                    <button class="btn-sort" type="submit">
                                                        <span class="text-14">Hãng</span>
                                                    </button>
                                                </a>
                                                <div class="icon" id="icon-brand"></div>
                                            </span>
                                        </th>
                                        <th>Số lượng</th>
                                    </tr>
                                </thead>
                                <tbody id="summaryTableBody">
                                    <!-- Dữ liệu sẽ được thêm bằng Ajax -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-12 p-0 m-0">
                    <div class="card">
                        <div class="outer2 table-responsive text-nowrap">
                            <table id="example2" class="table table-hover bg-white rounded">
                                <thead class="border-custom">
                                    <tr>
                                        <th class="height-40 py-0 border-right-0" scope="col">
                                            <span class="d-flex justify-content-start">
                                                <a href="#" class="sort-link btn-submit"
                                                    data-sort-by="product_code" data-sort-type="DESC">
                                                    <button class="btn-sort" type="submit">
                                                        <span class="text-14">Mã hàng</span>
                                                    </button>
                                                </a>
                                                <div class="icon" id="icon-product_code"></div>
                                            </span>
                                        </th>
                                        <th class="height-40 py-0 border-right-0" scope="col">
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
                                        <th class="height-40 py-0 border-right-0" scope="col">
                                            <span class="d-flex justify-content-start">
                                                <a href="#" class="sort-link btn-submit" data-sort-by="brand"
                                                    data-sort-type="DESC">
                                                    <button class="btn-sort" type="submit">
                                                        <span class="text-14">Hãng</span>
                                                    </button>
                                                </a>
                                                <div class="icon" id="icon-brand"></div>
                                            </span>
                                        </th>
                                        <th class="height-40 py-0 border-right-0" scope="col">
                                            <span class="d-flex justify-content-start">
                                                <a href="#" class="sort-link btn-submit"
                                                    data-sort-by="sericode" data-sort-type="DESC">
                                                    <button class="btn-sort" type="submit">
                                                        <span class="text-14">S/N</span>
                                                    </button>
                                                </a>
                                                <div class="icon" id="icon-sericode"></div>
                                            </span>
                                        </th>
                                        <th class="height-40 py-0 border-right-0" scope="col">
                                            <span class="d-flex justify-content-start">
                                                <a href="#" class="sort-link btn-submit"
                                                    data-sort-by="providername" data-sort-type="DESC">
                                                    <button class="btn-sort" type="submit">
                                                        <span class="text-14">Nhà cung cấp</span>
                                                    </button>
                                                </a>
                                                <div class="icon" id="icon-providername"></div>
                                            </span>
                                        </th>
                                        <th class="height-40 py-0 border-right-0" scope="col">
                                            <span class="d-flex justify-content-start">
                                                <a href="#" class="sort-link btn-submit"
                                                    data-sort-by="import_date" data-sort-type="DESC">
                                                    <button class="btn-sort" type="submit">
                                                        <span class="text-14">Ngày nhập hàng</span>
                                                    </button>
                                                </a>
                                                <div class="icon" id="icon-import_date"></div>
                                            </span>
                                        </th>
                                        @if (!auth()->user()->hasAnyRole(['Quản lý kho', 'Bảo hành']))
                                            <th class="height-40 py-0 border-right-0" scope="col">
                                                <span class="d-flex justify-content-start">
                                                    <a href="#" class="sort-link btn-submit"
                                                        data-sort-by="warehouse_id" data-sort-type="DESC">
                                                        <button class="btn-sort" type="submit">
                                                            <span class="text-14">Kho</span>
                                                        </button>
                                                    </a>
                                                    <div class="icon" id="icon-warehouse_id"></div>
                                                </span>
                                            </th>
                                        @endif
                                        <th class="height-40 py-0 border-right-0" scope="col">
                                            <span class="d-flex justify-content-start">
                                                <a href="#" class="sort-link btn-submit"
                                                    data-sort-by="storage_duration" data-sort-type="DESC">
                                                    <button class="btn-sort" type="submit">
                                                        <span class="text-14">Thời gian tồn kho</span>
                                                    </button>
                                                </a>
                                                <div class="icon" id="icon-storage_duration"></div>
                                            </span>
                                        </th>
                                        <th class="height-40 py-0 border-right-0" scope="col">
                                            <span class="d-flex justify-content-start">
                                                <a href="#" class="sort-link btn-submit" data-sort-by="status"
                                                    data-sort-type="DESC">
                                                    <button class="btn-sort" type="submit">
                                                        <span class="text-14">Trạng thái</span>
                                                    </button>
                                                </a>
                                                <div class="icon" id="icon-status"></div>
                                            </span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="tbody-inven-lookup">
                                    @foreach ($inventory as $item)
                                        <tr class="position-relative inven-lookup-info height-40">
                                            <input type="hidden" name="id-inven-lookup" class="id-inven-lookup"
                                                id="id-inven-lookup" value="{{ $item->id }}">
                                            <td
                                                class="text-13-black border-right border-bottom border-top-0 border-right-0 py-0">
                                                {{ $item->product->product_code }}
                                            </td>
                                            <td
                                                class="text-13-black border border-left-0 border-bottom border-top-0 border-right-0 py-0 max-width180">
                                                {{ $item->product->product_name }}
                                            </td>
                                            <td
                                                class="text-13-black border border-left-0 border-bottom border-top-0 border-right-0 py-0">
                                                {{ $item->product->brand }}
                                            </td>
                                            <td
                                                class="text-13-black border border-left-0 border-bottom border-top-0 border-right-0 py-0">
                                                @if ($item->serialNumber)
                                                    <a href="{{ route('inventoryLookup.edit', $item->id) }}">
                                                        {{ $item->serialNumber->serial_code ?? '' }}
                                                        @if (!empty($item->serialNumber) && $item->serialNumber->status == 5)
                                                            <span class="text-13-black">(Hàng mượn)</span>
                                                        @endif
                                                    </a>
                                                @endif
                                            </td>
                                            <td
                                                class="text-13-black border border-left-0 border-bottom border-top-0 border-right-0 py-0 max-width180">
                                                @if ($item->provider)
                                                <span class="truncate-1line" title="{{ $item->provider->provider_name }}">{{ $item->provider->provider_name }}</span>
                                                @endif
                                            </td>
                                            <td
                                                class="text-13-black border border-left-0 border-bottom border-top-0 border-right-0 py-0">
                                                {{ date_format(new DateTime($item->import_date), 'd/m/Y') }}
                                            </td>
                                            @if (!auth()->user()->hasAnyRole(['Quản lý kho', 'Bảo hành']))
                                                <td
                                                    class="text-13-black border border-left-0 border-bottom border-top-0 border-right-0 py-0">
                                                    @if ($item->warehouse)
                                                        {{ $item->warehouse->warehouse_name }}
                                                    @endif
                                                </td>
                                            @endif
                                            <td
                                                class="text-13-black border border-left-0 border-bottom border-top-0 border-right-0 py-0">
                                                {{ $item->storage_duration }} ngày
                                            </td>
                                            <td
                                                class="text-13-black border border-left-0 border-bottom border-top-0 border-right-0 py-0">
                                                @if ($item->status == '1')
                                                    <span class="text-danger">Tới hạn bảo trì</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <!-- Pagination -->
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div class="pagination-info opacity-0">
                                Hiển thị {{ $inventory->firstItem() ?? 0 }} đến {{ $inventory->lastItem() ?? 0 }} trong tổng số {{ $inventory->total() }} kết quả
                            </div>
                            <div class="pagination-links">
                                {{ $inventory->links() }}
                            </div>
                        </div>
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
        // Thu thập dữ liệu từ form
        var formData = {
            search: $('#search').val(),
            ma: getData('#ma-hang', this),
            ten: getData('#ten-hang', this),
            brand: getData('#hang', this),
            sn: getData('#serial', this),
            date: retrieveDateData(this, 'ngay-nhap-hang'),
            provider: getStatusData(this, 'nha-cung-cap'),
            status: getStatusData(this, 'trang-thai'),
            time_inven: retrieveComparisonData(this, 'thoi-gian-ton-kho'),
            sort: deepeningSort(getSortData(buttonElement)), // Điều chỉnh nếu cần để sort bảng tổng hợp
            page: 1 // Reset về trang 1 khi filter
        };
        // Ẩn tùy chọn nếu cần
        if (!$(e.target).closest('li, input[type="checkbox"]').length) {
            $('#' + $(this).data('button-name') + '-options').hide();
        }
        // Gọi hàm AJAX cho bảng chi tiết
        var route = "{{ route('filter-inven-lookup') }}";
        var nametable = 'inven-lookup';
        handleAjaxRequest(formData, route, nametable);

        // Nếu bảng tổng hợp đang hiển thị, cập nhật nó
        if ($('#summaryTable').is(':visible')) {
            loadSummaryData(formData);
        }
    });

    exportTableToExcel("#exportBtn", "#example2", "ton_kho.xlsx");

    // Toggle bảng tổng hợp
    $(document).on('click', '#toggleSummary', function() {
        var $summaryTable = $('#summaryTable');
        var $mainTable = $('#example2').closest('.col-12');
        var formData = {
            search: $('#search').val(),
            ma: getData('#ma-hang', this),
            ten: getData('#ten-hang', this),
            brand: getData('#hang', this),
            sn: getData('#serial', this),
            date: retrieveDateData(this, 'ngay-nhap-hang'),
            provider: getStatusData(this, 'nha-cung-cap'),
            status: getStatusData(this, 'trang-thai'),
            time_inven: retrieveComparisonData(this, 'thoi-gian-ton-kho')
        };

        if ($summaryTable.is(':visible')) {
            $summaryTable.hide();
            $mainTable.show();
            $(this).text('Hiển thị bảng tổng hợp');
        } else {
            $summaryTable.show();
            $mainTable.hide();
            $(this).text('Hiển thị bảng chi tiết');
            loadSummaryData(formData); // Truyền formData khi toggle
        }
    });

    // Hàm tải dữ liệu tổng hợp qua Ajax
    function loadSummaryData(formData = {}) {
        $.ajax({
            url: "{{ route('inventoryLookup.summary') }}",
            method: "GET",
            data: formData, // Truyền dữ liệu lọc
            cache: true,
            dataType: "json",
            beforeSend: function() {
                $('#summaryTableBody').html('<tr><td colspan="4">Đang tải dữ liệu...</td></tr>');
            },
            success: function(response) {
                var tbody = $('#summaryTableBody');
                tbody.empty();

                if (response.data?.length) {
                    let rows = response.data.map(item => `
                        <tr class="position-relative inven-lookup-info height-40">
                            <td class="text-13-black border-right border-bottom border-top-0 border-right-0 py-0">${item.product_code || ''}</td>
                            <td class="text-13-black border border-left-0 border-bottom border-top-0 border-right-0 py-0 max-width180 note-text">${item.product_name || ''}</td>
                            <td class="text-13-black border border-left-0 border-bottom border-top-0 border-right-0 py-0">${item.brand || ''}</td>
                            <td class="text-13-black border-right border-bottom border-top-0 border-right-0 py-0">${item.quantity || 0}</td>
                        </tr>
                    `).join('');
                    tbody.append(rows);
                } else {
                    tbody.append('<tr><td colspan="4">Không có dữ liệu</td></tr>');
                }
            },
            error: function(xhr) {
                console.log('Lỗi:', xhr.responseText);
                $('#summaryTableBody').html('<tr><td colspan="4">Lỗi khi tải dữ liệu</td></tr>');
            }
        });
    }

    // Hàm deepeningSort để xử lý sort nếu cần (giả định)
    function deepeningSort(sortData) {
        if (sortData && sortData.sort_by && sortData.sort_type) {
            return {
                sort_by: sortData.sort_by,
                sort_type: sortData.sort_type
            };
        }
        return {};
    }

    // Xử lý pagination links
    $(document).on('click', '.pagination a', function(e) {
        e.preventDefault();
        var url = $(this).attr('href');
        var page = url.split('page=')[1];
        
        // Thu thập dữ liệu filter hiện tại
        var formData = {
            search: $('#search').val(),
            ma: getData('#ma-hang', document),
            ten: getData('#ten-hang', document),
            brand: getData('#hang', document),
            sn: getData('#serial', document),
            date: retrieveDateData(document, 'ngay-nhap-hang'),
            provider: getStatusData(document, 'nha-cung-cap'),
            status: getStatusData(document, 'trang-thai'),
            time_inven: retrieveComparisonData(document, 'thoi-gian-ton-kho'),
            page: page
        };
        
        // Gọi AJAX với trang mới
        var route = "{{ route('filter-inven-lookup') }}";
        var nametable = 'inven-lookup';
        handleAjaxRequest(formData, route, nametable);
    });
</script>
