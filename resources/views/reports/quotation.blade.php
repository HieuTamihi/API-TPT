@include('partials.header', ['activeGroup' => 'reports', 'activeName' => 'quotations_report'])
@section('title', $title)
<div class="content-wrapper m-0 min-height--none p-0">
    <div class="content-header-fixed px-1">
        <div class="content__header--inner">
            <x-search-filter :keywords="request('keywords')" :filters="[
                'Mã phiếu',
                'Khách hàng',
                'Ngày lập phiếu',
                'Tổng tiền',
                'Phiếu tiếp nhận',
                'Tình trạng',
                'Ghi chú',
            ]" :filtersTime="['Tháng', 'Quý', 'Năm', 'Ngày lập phiếu']">
                <x-filter-text name="ma-phieu" title="Mã phiếu" />
                <x-filter-status name="tinh-trang" title="Tình trạng" :filters="[
                    ['key' => '3', 'value' => 'Hoàn thành', 'color' => '#08AA36BF'],
                    ['key' => '4', 'value' => 'Khách không đồng ý', 'color' => '#dc3545'],
                ]" />
                <x-filter-checkbox :dataa='$customers' name="khach-hang" title="Khách hàng" button="khach-hang"
                    namedisplay="customer_name" />

                <x-filter-text name="phieu-tiep-nhan" title="Phiếu tiếp nhận" />
                <x-filter-text name="ghi-chu" title="Ghi chú" />
                <x-filter-compare name="tong-tien" title="Tổng tiền" />
                @slot('slot1')
                    <x-filter-date name="ngay-lap-phieu" title="Ngày lập phiếu" />
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
            <div class="row result-filter-rp_quotation margin-left20">
            </div>
            <div class="container-fluided">
                <div class="bg-filter-search text-center border border-bottom-0">
                    <p class="font-weight-bold text-uppercase info-chung--heading text-center py-2">
                        BÁO CÁO PHIẾU BÁO GIÁ
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
                                                <a href="#" class="sort-link btn-submit"
                                                    data-sort-by="quotation_code" data-sort-type="DESC">
                                                    <button class="btn-sort" type="submit">
                                                        <span class="text-14">Mã phiếu</span>
                                                    </button>
                                                </a>
                                                <div class="icon" id="icon-quotation_code"></div>
                                            </span>
                                        </th>
                                        <th class="height-40 py-0 border" scope="col">
                                            <span class="d-flex justify-content-start">
                                                <a href="#" class="sort-link btn-submit"
                                                    data-sort-by="customer_name" data-sort-type="DESC">
                                                    <button class="btn-sort" type="submit">
                                                        <span class="text-14">Khách hàng</span>
                                                    </button>
                                                </a>
                                                <div class="icon" id="icon-customer_name"></div>
                                            </span>
                                        </th>
                                        <th class="height-40 py-0 border" scope="col">
                                            <span class="d-flex justify-content-start">
                                                <a href="#" class="sort-link btn-submit"
                                                    data-sort-by="quotation_date" data-sort-type="DESC">
                                                    <button class="btn-sort" type="submit">
                                                        <span class="text-14">Ngày lập phiếu</span>
                                                    </button>
                                                </a>
                                                <div class="icon" id="icon-quotation_date"></div>
                                            </span>
                                        </th>
                                        <th class="height-40 py-0 border" scope="col">
                                            <span class="d-flex justify-content-start">
                                                <a href="#" class="sort-link btn-submit"
                                                    data-sort-by="total_amount" data-sort-type="DESC">
                                                    <button class="btn-sort" type="submit">
                                                        <span class="text-14">Tổng tiền</span>
                                                    </button>
                                                </a>
                                                <div class="icon" id="icon-total_amount"></div>
                                            </span>
                                        </th>
                                        <th class="height-40 py-0 border" scope="col">
                                            <span class="d-flex justify-content-start">
                                                <a href="#" class="sort-link btn-submit"
                                                    data-sort-by="form_code_receiving" data-sort-type="DESC">
                                                    <button class="btn-sort" type="submit">
                                                        <span class="text-14">Phiếu tiếp nhận</span>
                                                    </button>
                                                </a>
                                                <div class="icon" id="icon-form_code_receiving"></div>
                                            </span>
                                        </th>
                                        <th class="height-40 py-0 border" scope="col">
                                            <span class="d-flex justify-content-start">
                                                <a href="#" class="sort-link btn-submit"
                                                    data-sort-by="status_recei" data-sort-type="DESC">
                                                    <button class="btn-sort" type="submit">
                                                        <span class="text-14">Tình trạng</span>
                                                    </button>
                                                </a>
                                                <div class="icon" id="icon-status_recei"></div>
                                            </span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="tbody-rp_quotation">
                                    @php
                                        $completed = 0;
                                        $cancel = 0;
                                        $totalCompoleted = 0;
                                        $totalCancel = 0;
                                        $totalAmount = 0;
                                    @endphp
                                    @foreach ($quotations as $item)
                                        @if ($item->status == 1)
                                            @php
                                                $completed++;
                                                $totalCompoleted += $item->total_amount;
                                            @endphp
                                        @elseif($item->status == 2)
                                            @php
                                                $cancel++;
                                                $totalCancel += $item->total_amount;
                                            @endphp
                                        @endif
                                        @php
                                            $totalAmount += $item->total_amount;
                                        @endphp
                                        <tr class="position-relative rp_quotation-info height-40">
                                            <input type="hidden" name="id-rp_quotation" class="id-rp_quotation"
                                                id="id-rp_quotation" value="{{ $item->id }}">
                                            <td class="text-13-black border-right border-bottom py-0">
                                                {{ $item->quotation_code }}
                                            </td>
                                            <td
                                                class="text-13-black border border-left-0 text-left border-bottom py-0 max-width180">
                                                {{ $item->customer->customer_name }}
                                            </td>
                                            <td class="text-13-black border border-left-0 border-bottom py-0">
                                                {{ date_format(new DateTime($item->quotation_date), 'd/m/Y') }}
                                            </td>
                                            <td class="text-13-black border border-left-0 border-bottom py-0">
                                                {{ number_format($item->total_amount) }}
                                            </td>
                                            <td class="text-13-black border border-left-0 border-bottom py-0">
                                                {{ $item->reception->form_code_receiving }}
                                            </td>
                                            <td class="text-13-black border border-left-0 border-bottom py-0">
                                                @if ($item->reception->status == 1)
                                                    Tiếp nhận
                                                @elseif($item->reception->status == 2)
                                                    Xử lý
                                                @elseif($item->reception->status == 3)
                                                    Hoàn thành
                                                @elseif($item->reception->status == 4)
                                                    Khách không đồng ý
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="footer-summary">
                                <table class="table-footer" style="table-layout: auto;">
                                    <tr>
                                        <td class="text-right" colspan="1"></td>
                                        <td class="text-danger">Phiếu báo giá:
                                            <span class="statusCounts-3">{{ count($quotations) }}</span>
                                        </td>
                                        <td class="text-danger">Phiếu hoàn thành:
                                            <span class="statusCounts-3">{{ $completed }}</span>
                                        </td>
                                        <td class="text-danger">Tổng tiền phiếu hoàn thành:
                                            <span class="totalAmounts-3">{{ number_format($totalCompoleted) }}</span>
                                        </td>
                                        <td class="text-danger">Phiếu khách từ chối:
                                            <span class="statusCounts-4">{{ $cancel }}</span>
                                        </td>
                                        <td class="text-danger">Tổng tiền toàn bộ phiếu báo giá:
                                            <span class="totalAmounts-4">{{ number_format($totalAmount) }}</span>
                                        </td>
                                    </tr>
                                </table>
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
            ma: getData('#ma-phieu', this),
            receiving_code: getData('#phieu-tiep-nhan', this),
            tong_tien: retrieveComparisonData(this, 'tong-tien'),
            status: getStatusData(this, 'tinh-trang'),
            date: retrieveDateData(this, 'ngay-lap-phieu'),
            customer: getStatusData(this, 'khach-hang'),
            sort: getSortData(buttonElement),
        };
        console.log(formData);
        var nametable = "rp_quotation";
        // Ẩn tùy chọn nếu cần

        // Thiết lập CSRF token nếu cần
        $.ajaxSetup({
            headers: {
                csrftoken: "{{ csrf_token() }}",
            },
        });
        // Gọi hàm AJAX
        $.ajax({
            type: "get",
            url: "{{ route('reports.quotation') }}",
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
                const responseData = data.countTotal;

                // Các trạng thái cần kiểm tra
                const statuses = ["3", "4"];
                statuses.forEach(function(status) {
                    const count = responseData.statusCounts?.[status] ??
                        0; // Giá trị mặc định là 0 nếu không tồn tại
                    $(`.statusCounts-${status}`).text(count);
                });
                // Duyệt qua các trạng thái để gắn dữ liệu cho `totalAmounts`
                statuses.forEach(function(status) {
                    const total = responseData.totalAmounts?.[status] ??
                        0; // Giá trị mặc định là 0 nếu không tồn tại
                    $(`.totalAmounts-${status}`).text(
                        new Intl.NumberFormat('vi-VN', {
                            style: 'decimal'
                        }).format(total)
                    );
                });
            },
            error: function(xhr, status, error) {},
        });

        if (!$(e.target).closest('li, input[type="checkbox"]').length) {
            $('#' + $(this).data('button-name') + '-options').hide();
        }
    });
    exportTableToExcel("#exportBtn", "#example2", "bao_cao_hang_xuat_nhap.xlsx");
</script>
