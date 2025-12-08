@include('partials.header', ['activeGroup' => 'manageProfess', 'activeName' => 'warrantyLookup'])
@section('title', $title)
<div class="content-wrapper m-0 min-height--none p-0">
    <div class="content-header-fixed px-1">
        <div class="content__header--inner">
            <x-search-filter :keywords="request('keywords')" :filters="[
                'Mã hàng',
                // 'Tên hàng',
                'Hãng',
                'Serial',
                'Khách hàng',
                'Ngày xuất hàng',
                'Bảo hành',
                'Hạn bảo hành',
                'Ngày kích hoạt BHDV',
                'Bảo hành dịch vụ',
                'Tình trạng',
            ]">
                <x-filter-text name="ma-hang" title="Mã hàng" />
                <x-filter-text name="ten-hang" title="Tên hàng" />
                <x-filter-text name="serial" title="Serial" />
                <x-filter-text name="hang" title="Hãng" />
                <x-filter-checkbox :dataa='$customers' name="khach-hang" title="Khách hàng" button="khach-hang"
                    namedisplay="customer_name" />
                <x-filter-date name="ngay-xuat-hang" title="Ngày xuất hàng" />
                <x-filter-date name="ngay-kich-hoat-bhdv" title="Ngày kích hoạt BHDV" />
                <x-filter-status name="tinh-trang" title="Tình trạng" :filters="[
                    ['key' => '0', 'value' => 'Còn bảo hành', 'color' => '#858585'],
                    ['key' => '1', 'value' => 'Hết bảo hành', 'color' => '#08AA36BF'],
                ]" />
                <x-filter-compare name="bao-hanh" title="Bảo hành" />
                <x-filter-compare name="han-bao-hanh" title="Hạn bảo hành" />
                <x-filter-compare name="bao-hanh-dich-vu" title="Bảo hành dịch vụ" />
            </x-search-filter>
            <button class="m-0 btn-outline-primary" id="exportBtn">Export Excel</button>
        </div>
    </div>
    <div class="content margin-top-127">
        <section class="content">
            <div class="container-fluided">
                <div class="row result-filter-warran-lookup margin-left20 my-1">
                </div>
                <div class="col-12 p-0 m-0">
                    <div class="card">
                        <!-- /.card-header -->
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
                                                <a href="#" class="sort-link btn-submit" data-sort-by="sericode"
                                                    data-sort-type="DESC">
                                                    <button class="btn-sort" type="submit">
                                                        <span class="text-14">Serial Number</span>
                                                    </button>
                                                </a>
                                                <div class="icon" id="icon-sericode"></div>
                                            </span>
                                        </th>
                                        <th class="height-40 py-0 border-right-0" scope="col">
                                            <span class="d-flex justify-content-start">
                                                <a href="#" class="sort-link btn-submit"
                                                    data-sort-by="customername" data-sort-type="DESC">
                                                    <button class="btn-sort" type="submit">
                                                        <span class="text-14">Khách hàng</span>
                                                    </button>
                                                </a>
                                                <div class="icon" id="icon-customername"></div>
                                            </span>
                                        </th>
                                        <th class="height-40 py-0 border-right-0" scope="col">
                                            <span class="d-flex justify-content-start">
                                                <a href="#" class="sort-link btn-submit"
                                                    data-sort-by="export_return_date" data-sort-type="DESC">
                                                    <button class="btn-sort" type="submit">
                                                        <span class="text-14">Ngày bán</span>
                                                    </button>
                                                </a>
                                                <div class="icon" id="icon-export_return_date"></div>
                                            </span>
                                        </th>
                                        <th class="height-40 py-0 border-right-0" scope="col">
                                            <span class="d-flex justify-content-start">
                                                <a href="#" class="sort-link btn-submit"
                                                    data-sort-by="name_warranty" data-sort-type="DESC">
                                                    <button class="btn-sort" type="submit">
                                                        <span class="text-14">Bảo hành</span>
                                                    </button>
                                                </a>
                                                <div class="icon" id="icon-name_warranty"></div>
                                            </span>
                                        </th>
                                        <th class="height-40 py-0 border-right-0" scope="col">
                                            <span class="d-flex justify-content-start">
                                                <a href="#" class="sort-link btn-submit"
                                                    data-sort-by="name_warranty" data-sort-type="DESC">
                                                    <button class="btn-sort" type="submit">
                                                        <span class="text-14">Hạn bảo hành</span>
                                                    </button>
                                                </a>
                                                <div class="icon" id="icon-name_warranty"></div>
                                            </span>
                                        </th>
                                        <th class="height-40 py-0 border-right-0" scope="col">
                                            <span class="d-flex justify-content-start">
                                                <a href="#" class="sort-link btn-submit"
                                                    data-sort-by="return_date" data-sort-type="DESC">
                                                    <button class="btn-sort" type="submit">
                                                        <span class="text-14">Ngày kích hoạt BH dịch vụ</span>
                                                    </button>
                                                </a>
                                                <div class="icon" id="icon-return_date"></div>
                                            </span>
                                        </th>
                                        <th class="height-40 py-0 border-right-0" scope="col">
                                            <span class="d-flex justify-content-start">
                                                <a href="#" class="sort-link btn-submit"
                                                    data-sort-by="warranty_extra" data-sort-type="DESC">
                                                    <button class="btn-sort" type="submit">
                                                        <span class="text-14">Bảo hành dịch vụ</span>
                                                    </button>
                                                </a>
                                                <div class="icon" id="icon-warranty_extra"></div>
                                            </span>
                                        </th>
                                        <th class="height-40 py-0 border-right-0" scope="col">
                                            <span class="d-flex justify-content-start">
                                                <a href="#" class="sort-link btn-submit" data-sort-by="status"
                                                    data-sort-type="DESC">
                                                    <button class="btn-sort" type="submit">
                                                        <span class="text-14">Tình trạng</span>
                                                    </button>
                                                </a>
                                                <div class="icon" id="icon-status"></div>
                                            </span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="tbody-warran-lookup">
                                    @foreach ($warranty as $item)
                                        @php
                                            $firstHistory = $item->warrantyHistories->first();
                                            $branchId = $firstHistory?->receiving?->branch_id ?? 0;
                                            $formType = $firstHistory?->receiving?->form_type ?? 0;
                                            $currentDate = new DateTime();
                                            $purchaseDate = new DateTime($item->export_return_date);
                                            preg_match('/\d+/', $item->warranty, $matches);
                                            $warrantyPeriod = isset($matches[0]) ? (int) $matches[0] : 0;

                                            $expireDate = (clone $purchaseDate)->modify("+$warrantyPeriod months");
                                            $isExpired = $currentDate > $expireDate;
                                            $interval = $expireDate->diff($currentDate);
                                            $years = $interval->y;
                                            $months = $interval->m;
                                            $days = $interval->d;
                                        @endphp
                                        <tr class="position-relative warran-lookup-info height-40">
                                            <input type="hidden" name="id-warran-lookup" class="id-warran-lookup"
                                                id="id-warran-lookup" value="{{ $item->sn_id }}">
                                            <td
                                                class="text-13-black border-right border-bottom border-top-0 border-right-0 py-0">
                                                {{ $item->product->product_code }}
                                            </td>
                                            <td
                                                class="text-13-black border border-left-0 border-bottom border-top-0 border-right-0 py-0">
                                                {{ $item->product->brand }}
                                            </td>
                                            <td
                                                class="text-13-black border border-left-0 border-bottom border-top-0 border-right-0 py-0">
                                                @if ($item->serialNumber)
                                                    <a
                                                        href="{{ route('warrantyLookup.edit', $item->serialNumber->id) }}">
                                                        {{ $item->serialNumber->serial_code }}
                                                    </a>
                                                @endif
                                            </td>
                                            <td
                                                class="text-13-black border border-left-0 border-bottom border-top-0 border-right-0 py-0 max-width180">
                                                <span class="truncate-1line" title="{{ $item->customer->customer_name ?? '' }}">{{ $item->customer->customer_name ?? '' }}</span>
                                            </td>
                                            <td
                                                class="text-13-black border border-left-0 border-bottom border-top-0 border-right-0 py-0">
                                                {{ $item->export_return_date ? date_format(new DateTime($item->export_return_date), 'd/m/Y') : '' }}
                                            </td>
                                            <td
                                                class="text-13-black border border-left-0 border-bottom border-top-0 border-right-0 py-0 max-width180">
                                                {{ $item->name_warranty }}
                                            </td>
                                            <td
                                                class="text-13-black border border-left-0 border-bottom border-top-0 border-right-0 py-0 max-width180">
                                                @if ($isExpired)
                                                    <span class="text-danger">
                                                        Hết bảo hành ({{ $years > 0 ? "$years năm" : '' }}
                                                        {{ $months }} tháng {{ $days }} ngày trước)
                                                    </span>
                                                @else
                                                    <span class="text-success">
                                                        Còn {{ $years > 0 ? "$years năm" : '' }} {{ $months }}
                                                        tháng {{ $days }} ngày
                                                    </span>
                                                @endif
                                            </td>
                                            <td
                                                class="text-13-black border border-left-0 border-bottom border-top-0 border-right-0 py-0">
                                                {{ $item->name_expire_date ? ($item->return_date ? date('d/m/Y', strtotime($item->return_date)) : '') : '' }}
                                            </td>
                                            <td
                                                class="text-13-black border border-left-0 border-bottom border-top-0 border-right-0 py-0">
                                                {{ $item->name_expire_date }}
                                            </td>
                                            <td
                                                class="text-13-black border border-left-0 border-bottom border-top-0 border-right-0 py-0 max-width180">
                                                @if ($branchId == 2 && $formType == 3)
                                                @else
                                                    {{ $item->status_string }}
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
                                Hiển thị {{ $warranty->firstItem() ?? 0 }} đến {{ $warranty->lastItem() ?? 0 }} trong tổng số {{ $warranty->total() }} kết quả
                            </div>
                            <div class="pagination-links">
                                {{ $warranty->links() }}
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
            sn: getData('#serial', this),
            brand: getData('#hang', this),
            date: retrieveDateData(this, 'ngay-xuat-hang'),
            customer: getStatusData(this, 'khach-hang'),
            status: getStatusData(this, 'tinh-trang'),
            bao_hanh: retrieveComparisonData(this, 'bao-hanh'),
            han_bao_hanh: retrieveComparisonData(this, 'han-bao-hanh'),
            bao_hanh_dich_vu: retrieveComparisonData(this, 'bao-hanh-dich-vu'),
            date_expired: retrieveDateData(this, 'ngay-kich-hoat-bhdv'),
            sort: getSortData(buttonElement),
            page: 1 // Reset về trang 1 khi filter
        };
        // Ẩn tùy chọn nếu cần
        if (!$(e.target).closest('li, input[type="checkbox"]').length) {
            $('#' + $(this).data('button-name') + '-options').hide();
        }
        // Gọi hàm AJAX
        var route = "{{ route('filter-warran-lookup') }}"; // Thay route phù hợp
        var nametable = 'warran-lookup'; // Thay tên bảng phù hợp
        handleAjaxRequest(formData, route, nametable);
    });
    
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
            sn: getData('#serial', document),
            brand: getData('#hang', document),
            date: retrieveDateData(document, 'ngay-xuat-hang'),
            customer: getStatusData(document, 'khach-hang'),
            status: getStatusData(document, 'tinh-trang'),
            bao_hanh: retrieveComparisonData(document, 'bao-hanh'),
            han_bao_hanh: retrieveComparisonData(document, 'han-bao-hanh'),
            bao_hanh_dich_vu: retrieveComparisonData(document, 'bao-hanh-dich-vu'),
            date_expired: retrieveDateData(document, 'ngay-kich-hoat-bhdv'),
            page: page
        };
        
        // Gọi AJAX với trang mới
        var route = "{{ route('filter-warran-lookup') }}";
        var nametable = 'warran-lookup';
        handleAjaxRequest(formData, route, nametable);
    });
    
    exportTableToExcel("#exportBtn", "#example2", "bao_hanh.xlsx");
</script>
