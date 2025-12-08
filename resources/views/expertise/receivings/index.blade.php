@include('partials.header', ['activeGroup' => 'manageProfess', 'activeName' => 'receivings'])
@section('title', $title)
<div class="content-wrapper m-0 min-height--none p-0">
    <div class="content-header-fixed px-1">
        <div class="content__header--inner">
            <x-search-filter :keywords="request('keywords')" :filters="[
                'Mã phiếu',
                'Khách hàng',
                'Serial',
                'Mã sản phẩm',
                'Tên sản phẩm',
                'Ngày lập phiếu',
                'Ngày đóng phiếu',
                'Loại phiếu',
                // 'Hàng tiếp nhận',
                'Tình trạng',
                'Trạng thái',
                'Ghi chú',
            ]">
                <x-filter-text name="ma-phieu" title="Mã phiếu" />
                <x-filter-status name="loai-phieu" title="Loại phiếu" :filters="[
                    ['key' => '1', 'value' => 'Bảo hành', 'color' => '#858585'],
                    ['key' => '2', 'value' => 'Dịch vụ', 'color' => '#08AA36BF'],
                    ['key' => '3', 'value' => 'Bảo hành dịch vụ', 'color' => '#E8B600'],
                ]" />
                <x-filter-status name="hang-tiep-nhan" title="Hàng tiếp nhận" :filters="[
                    ['key' => '1', 'value' => 'Nội bộ', 'color' => '#858585'],
                    ['key' => '2', 'value' => 'Bên ngoài', 'color' => '#08AA36BF'],
                ]" />
                <x-filter-status name="tinh-trang" title="Tình trạng" :filters="[
                    ['key' => '1', 'value' => 'Tiếp nhận', 'color' => '#858585'],
                    ['key' => '2', 'value' => 'Xử lý', 'color' => '#08AA36BF'],
                    ['key' => '3', 'value' => 'Hoàn thành', 'color' => '#08AA36BF'],
                    ['key' => '4', 'value' => 'Khách không đồng ý', 'color' => '#08AA36BF'],
                ]" />
                <x-filter-status name="trang-thai" title="Trạng thái" :filters="[
                    ['key' => '2', 'value' => 'Quá hạn', 'color' => '#858585'],
                    ['key' => '1', 'value' => 'Chưa xử lý', 'color' => '#08AA36BF'],
                    ['key' => '0', 'value' => 'Blank', 'color' => '#08AA36BF'],
                ]" />
                <x-filter-checkbox :dataa='$customers' name="khach-hang" title="Khách hàng" button="khach-hang"
                    namedisplay="customer_name" />
                <x-filter-date name="ngay-lap-phieu" title="Ngày lập phiếu" />
                <x-filter-date name="ngay-dong-phieu" title="Ngày đóng phiếu" />
                <x-filter-text name="ghi-chu" title="Ghi chú" />
                <x-filter-text name="serial" title="Serial" />
                <x-filter-text name="ma-san-pham" title="Mã sản phẩm" />
                <x-filter-text name="ten-san-pham" title="Tên sản phẩm" />
            </x-search-filter>
            <div class="d-flex content__heading--right">
                <button class="m-0 btn-outline-primary" id="exportBtn">Export Excel</button>
                @unlessrole('Kế toán')
                    <div class="row m-0">
                        <a href="{{ route('receivings.create') }}" class="activity mr-3" data-name1="KH" data-des="Tạo mới">
                            <button type="button" class="custom-btn mx-1 d-flex align-items-center h-100">
                                <svg class="mr-1" width="12" height="12" viewBox="0 0 18 18" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M9 0C9.58186 -2.96028e-08 10.0536 0.471694 10.0536 1.05356L10.0536 16.9464C10.0536 17.5283 9.58186 18 9 18C8.41814 18 7.94644 17.5283 7.94644 16.9464V1.05356C7.94644 0.471694 8.41814 -2.96028e-08 9 0Z"
                                        fill="white" />
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M18 9C18 9.58187 17.5283 10.0536 16.9464 10.0536H1.05356C0.471694 10.0536 -2.07219e-07 9.58187 0 9C-7.69672e-07 8.41814 0.471695 7.94644 1.05356 7.94644H16.9464C17.5283 7.94644 18 8.41814 18 9Z"
                                        fill="white" />
                                </svg>
                                <p class="m-0 ml-1">Tạo mới</p>
                            </button>
                        </a>
                    </div>
                @endunlessrole
            </div>
        </div>
    </div>
    <div class="content margin-top-127">
        <section class="content">
            <div class="container-fluided">
                <div class="row result-filter-data margin-left20 my-1">
                </div>
                <div class="col-12 p-0 m-0">
                    <div class="card" style="overflow-x: visible;">
                        <!-- /.card-header -->
                        <div class="outer2 table-responsive text-nowrap">
                            <table id="example2" class="table table-hover bg-white rounded">
                                <thead class="border-custom">
                                    <tr>
                                        <th class="height-40 py-0 border-right-0" scope="col">
                                            <span class="d-flex justify-content-start">
                                                <a href="#" class="sort-link btn-submit"
                                                    data-sort-by="form_code_receiving" data-sort-type="DESC">
                                                    <button class="btn-sort" type="submit">
                                                        <span class="text-14">Mã phiếu</span>
                                                    </button>
                                                </a>
                                                <div class="icon" id="icon-form_code_receiving"></div>
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
                                                    data-sort-by="date_created" data-sort-type="DESC">
                                                    <button class="btn-sort" type="submit">
                                                        <span class="text-14">Ngày lập phiếu</span>
                                                    </button>
                                                </a>
                                                <div class="icon" id="icon-date_created"></div>
                                            </span>
                                        </th>
                                        <th class="height-40 py-0 border-right-0" scope="col">
                                            <span class="d-flex justify-content-start">
                                                <a href="#" class="sort-link btn-submit" data-sort-by="closed_at"
                                                    data-sort-type="DESC">
                                                    <button class="btn-sort" type="submit">
                                                        <span class="text-14">Ngày đóng phiếu</span>
                                                    </button>
                                                </a>
                                                <div class="icon" id="icon-closed_at"></div>
                                            </span>
                                        </th>
                                        <th class="height-40 py-0 border-right-0" scope="col">
                                            <span class="d-flex justify-content-start">
                                                <a href="#" class="sort-link btn-submit"
                                                    data-sort-by="form_type" data-sort-type="DESC">
                                                    <button class="btn-sort" type="submit">
                                                        <span class="text-14">Loại phiếu</span>
                                                    </button>
                                                </a>
                                                <div class="icon" id="icon-form_type"></div>
                                            </span>
                                        </th>
                                        {{-- <th class="height-40 py-0 border-right-0" scope="col">
                                            <span class="d-flex justify-content-start">
                                                <a href="#" class="sort-link btn-submit"
                                                    data-sort-by="branch_id" data-sort-type="DESC">
                                                    <button class="btn-sort" type="submit">
                                                        <span class="text-14">Hàng tiếp nhận</span>
                                                    </button>
                                                </a>
                                                <div class="icon" id="icon-branch_id"></div>
                                            </span>
                                        </th> --}}
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
                                        <th class="height-40 py-0 border-right-0" scope="col">
                                            <span class="d-flex justify-content-start">
                                                <a href="#" class="sort-link btn-submit" data-sort-by="state"
                                                    data-sort-type="DESC">
                                                    <button class="btn-sort" type="submit">
                                                        <span class="text-14">Trạng thái</span>
                                                    </button>
                                                </a>
                                                <div class="icon" id="icon-state"></div>
                                            </span>
                                        </th>
                                        <th class="height-40 py-0 border-right-0" scope="col">
                                            <span class="d-flex justify-content-start">
                                                <a href="#" class="sort-link btn-submit" data-sort-by="note"
                                                    data-sort-type="DESC">
                                                    <button class="btn-sort" type="submit">
                                                        <span class="text-14">Ghi chú</span>
                                                    </button>
                                                </a>
                                                <div class="icon" id="icon-note"></div>
                                            </span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="tbody-data">
                                    @foreach ($receivings as $item)
                                        <tr data-create-return-url="{{ route('returnforms.create') }}"
                                            data-edit-return-url="{{ route('returnforms.edit', ':id') }}"
                                            data-create-quote-url="{{ route('quotations.create') }}"
                                            data-edit-quote-url="{{ route('quotations.edit', ':id') }}"
                                            class="position-relative data-info row-data height-40 @if ($item->state == 1) bg-custom-yl @elseif($item->state == 2) bg-custom-pink @else bg-white @endif">
                                            <input type="hidden" name="id-data" class="id-data" id="id-data"
                                                value="{{ $item->id }}" data-status="{{ $item->status }}"
                                                data-has-return="{{ $item->returnForms->id ?? 0 }}"
                                                data-has-quote="{{ $item->quotation->id ?? 0 }}">
                                            <td
                                                class="text-13-black border-right border-bottom border-top-0 border-right-0 py-0">
                                                <a
                                                    href="{{ route('receivings.edit', $item->id) }}">{{ $item->form_code_receiving }}</a>
                                            </td>
                                            <td
                                                class="text-13-black border-right border-bottom border-top-0 border-right-0 py-0 max-width180">
                                                <span class="truncate-1line" title="{{ $item->customer->customer_name }}">{{ $item->customer->customer_name }}</span>
                                            </td>
                                            <td
                                                class="text-13-black border border-left-0 border-bottom border-top-0 border-right-0 py-0">
                                                {{ date_format(new DateTime($item->date_created), 'd/m/Y') }}
                                            </td>
                                            <td
                                                class="text-13-black border border-left-0 border-bottom border-top-0 border-right-0 py-0">
                                                {{ $item->closed_at ? date_format(new DateTime($item->closed_at), 'd/m/Y') : '' }}
                                            </td>
                                            <td
                                                class="text-13-black border border-left-0 border-bottom border-top-0 border-right-0 py-0">
                                                @if ($item->form_type == 1)
                                                    Bảo hành
                                                @elseif($item->form_type == 2)
                                                    Dịch vụ
                                                @elseif($item->form_type == 3)
                                                    Bảo hành dịch vụ
                                                @endif
                                            </td>
                                            {{-- <td
                                                class="text-13-black border border-left-0 border-bottom border-top-0 border-right-0 py-0">
                                                @if ($item->branch_id == 1)
                                                    Nội bộ
                                                @elseif($item->branch_id == 2)
                                                    Bên ngoài
                                                @endif
                                            </td> --}}
                                            <td
                                                class="text-13-black border border-left-0 border-bottom border-top-0 border-right-0 py-0 status-text{{ $item->id }}">
                                                @if ($item->status == 1)
                                                    Tiếp nhận
                                                @elseif($item->status == 2)
                                                    Xử lý
                                                @elseif($item->status == 3)
                                                    Hoàn thành
                                                @elseif($item->status == 4)
                                                    Khách không đồng ý
                                                @endif
                                            </td>
                                            <td
                                                class="text-13-black border border-left-0 border-bottom border-top-0 border-right-0 py-0 state-text{{ $item->id }}">
                                                @if ($item->state == 1)
                                                    Chưa xử lý
                                                @elseif($item->state == 2)
                                                    Quá hạn
                                                @endif
                                            </td>
                                            <td
                                                class="text-13-black border border-left-0 border-bottom border-top-0 border-right-0 py-0 note-text">
                                                {{ $item->notes }}
                                            </td>
                                            <td class="position-absolute m-0 p-0 bg-hover-icon icon-center">
                                                <div class="d-flex w-100">
                                                    <a href="#">
                                                        <div class="rounded">
                                                            <form
                                                                onclick="return confirm('Bạn có chắc chắn muốn xóa?')"
                                                                action="{{ route('receivings.destroy', $item->id) }}"
                                                                method="POST" class="d-inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-sm">
                                                                    <svg width="16" height="16"
                                                                        viewBox="0 0 16 16" fill="none"
                                                                        xmlns="http://www.w3.org/2000/svg">
                                                                        <path opacity="0.936" fill-rule="evenodd"
                                                                            clip-rule="evenodd"
                                                                            d="M6.40625 0.968766C7.44813 0.958304 8.48981 0.968772 9.53125 1.00016C9.5625 1.03156 9.59375 1.06296 9.625 1.09436C9.65625 1.49151 9.66663 1.88921 9.65625 2.28746C10.7189 2.277 11.7814 2.28747 12.8438 2.31886C12.875 2.35025 12.9063 2.38165 12.9375 2.41305C12.9792 2.99913 12.9792 3.58522 12.9375 4.17131C12.9063 4.24457 12.8542 4.2969 12.7813 4.32829C12.6369 4.35948 12.4911 4.36995 12.3438 4.35969C12.3542 7.45762 12.3438 10.5555 12.3125 13.6533C12.1694 14.3414 11.7632 14.7914 11.0938 15.0034C9.01044 15.0453 6.92706 15.0453 4.84375 15.0034C4.17433 14.7914 3.76808 14.3414 3.625 13.6533C3.59375 10.5555 3.58333 7.45762 3.59375 4.35969C3.3794 4.3844 3.18148 4.34254 3 4.2341C2.95833 3.62708 2.95833 3.02007 3 2.41305C3.03125 2.38165 3.0625 2.35025 3.09375 2.31886C4.15605 2.28747 5.21855 2.277 6.28125 2.28746C6.27088 1.88921 6.28125 1.49151 6.3125 1.09436C6.35731 1.06018 6.38856 1.01832 6.40625 0.968766ZM6.96875 1.65951C7.63544 1.65951 8.30206 1.65951 8.96875 1.65951C8.96875 1.86882 8.96875 2.07814 8.96875 2.28746C8.30206 2.28746 7.63544 2.28746 6.96875 2.28746C6.96875 2.07814 6.96875 1.86882 6.96875 1.65951ZM3.65625 2.9782C6.53125 2.9782 9.40625 2.9782 12.2813 2.9782C12.2813 3.18752 12.2813 3.39684 12.2813 3.60615C9.40625 3.60615 6.53125 3.60615 3.65625 3.60615C3.65625 3.39684 3.65625 3.18752 3.65625 2.9782ZM4.34375 4.35969C6.76044 4.35969 9.17706 4.35969 11.5938 4.35969C11.6241 7.5032 11.5929 10.643 11.5 13.7789C11.3553 14.05 11.1366 14.2279 10.8438 14.3127C8.92706 14.3546 7.01044 14.3546 5.09375 14.3127C4.80095 14.2279 4.5822 14.05 4.4375 13.7789C4.34462 10.643 4.31337 7.5032 4.34375 4.35969Z"
                                                                            fill="#6C6F74" />
                                                                        <path opacity="0.891" fill-rule="evenodd"
                                                                            clip-rule="evenodd"
                                                                            d="M5.78125 5.28118C6.0306 5.2259 6.20768 5.30924 6.3125 5.53118C6.35419 8.052 6.35419 10.5729 6.3125 13.0937C6.08333 13.427 5.85417 13.427 5.625 13.0937C5.58333 10.552 5.58333 8.01037 5.625 5.46868C5.69031 5.4141 5.7424 5.3516 5.78125 5.28118Z"
                                                                            fill="#6C6F74" />
                                                                        <path opacity="0.891" fill-rule="evenodd"
                                                                            clip-rule="evenodd"
                                                                            d="M7.78125 5.28118C8.03063 5.2259 8.20769 5.30924 8.3125 5.53118C8.35419 8.052 8.35419 10.5729 8.3125 13.0937C8.08331 13.427 7.85419 13.427 7.625 13.0937C7.58331 10.552 7.58331 8.01037 7.625 5.46868C7.69031 5.4141 7.74238 5.3516 7.78125 5.28118Z"
                                                                            fill="#6C6F74" />
                                                                        <path opacity="0.891" fill-rule="evenodd"
                                                                            clip-rule="evenodd"
                                                                            d="M9.78125 5.28118C10.0306 5.2259 10.2077 5.30924 10.3125 5.53118C10.3542 8.052 10.3542 10.5729 10.3125 13.0937C10.0833 13.427 9.85419 13.427 9.625 13.0937C9.58331 10.552 9.58331 8.01037 9.625 5.46868C9.69031 5.4141 9.74238 5.3516 9.78125 5.28118Z"
                                                                            fill="#6C6F74" />
                                                                    </svg>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <ul class="option-button p-2">
                                <li style="position: relative;"> <!-- Đảm bảo cha có position: relative -->
                                    <button class="option-btn btn status" data-action="change-status">Chuyển tình
                                        trạng</button>
                                    <!-- Danh sách tình trạng -->
                                    <ul class="status-list"
                                        style="display: none; position: absolute; background: white; border: 1px solid #ccc; padding: 10px; list-style: none;">
                                    </ul>
                                </li>
                                <li>
                                    <button class="option-btn btn return-form" data-action="return-form"></button>
                                </li>
                                <li>
                                    <button class="option-btn btn quotation" data-action="quotation"></button>
                                </li>
                            </ul>

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
            ma: getData('#ma-phieu', this),
            note: getData('#ghi-chu', this),
            serial: getData('#serial', this),
            product_name: getData('#ten-san-pham', this),
            product_code: getData('#ma-san-pham', this),
            date: retrieveDateData(this, 'ngay-lap-phieu'),
            closed_at: retrieveDateData(this, 'ngay-dong-phieu'),
            customer: getStatusData(this, 'khach-hang'),
            form_type: getStatusData(this, 'loai-phieu'),
            // brand_type: getStatusData(this, 'hang-tiep-nhan'),
            status: getStatusData(this, 'tinh-trang'),
            state: getStatusData(this, 'trang-thai'),
            sort: getSortData(buttonElement)
        };
        // Ẩn tùy chọn nếu cần
        if (!$(e.target).closest('li, input[type="checkbox"]').length) {
            $('#' + $(this).data('button-name') + '-options').hide();
        }
        // Gọi hàm AJAX
        var route = "{{ route('filter-receivings') }}"; // Thay route phù hợp
        var nametable = 'data'; // Thay tên bảng phù hợp
        handleAjaxRequest(formData, route, nametable);
    });
    $(document).ready(function() {
        $('.status').on('click', function(e) {
            e.stopPropagation();
            const $statusList = $(this).siblings('.status-list');
            $('.status-list').not($statusList).hide();
            $statusList.toggle();
        });

        $('.status-list').on('click', 'li', function(e) {
            e.stopPropagation();
            const statusId = $(this).data('status');
            const recei = $(this).data('recei');
            const returndata = $(this).data('return');
            const statusText = $(this).text();
            const $td = $(`.status-text${recei}`);
            const $state = $(`.state-text${recei}`);
            $.ajax({
                url: '/update-status',
                method: 'POST',
                data: {
                    status: statusId,
                    recei: recei,
                    returndata: returndata,
                    _token: $('meta[name="csrf-token"]').attr('content'),
                },
                success: function(response) {
                    // console.log(response);
                    if (response.status === 'success') {
                        $td.text(statusText);
                        $state.text('');
                        $('input.id-data[value="' + response.id + '"]').data('status',
                            statusId);
                        if (statusId != 1) {
                            $('input.id-data[value="' + response.id + '"]').closest('tr')
                                .removeClass('bg-custom-yl bg-custom-pink');
                        }
                        showAutoToast("success", 'Cập nhật trạng thái thành công.');
                    } else {
                        showAutoToast("warning", 'Cập nhật trạng thái không thành công.');
                    }
                },
                error: function() {
                    showAutoToast("warning",
                        'Không thể cập nhật trạng thái. Vui lòng thử lại.');
                },
            });

            $(this).closest('.status-list').hide();
            const optionButton = $(this).closest('.option-button');
            optionButton.hide()
        });

        // Ẩn danh sách khi click ra ngoài
        $(document).on('click', function() {
            $('.status-list').hide();
        });
    });


    $(document).ready(function() {
        $(document).on('contextmenu', '.row-data', function(e) {
            e.preventDefault();
            const $row = $(this);
            const $optionButton = $('.option-button');
            const $statusList = $optionButton.find('.status-list');
            const dataRecei = $row.find('.id-data').val();
            const hasReturn = $row.find('.id-data').data('has-return');
            const hasQuote = $row.find('.id-data').data('has-quote');
            const dataStatus = $row.find('.id-data').data('status');

            const urls = {
                createReturn: `${$row.data('create-return-url')}?recei=${dataRecei}`,
                editReturn: $row.data('edit-return-url').replace(':id', hasReturn),
                createQuote: `${$row.data('create-quote-url')}?recei=${dataRecei}`,
                editQuote: $row.data('edit-quote-url').replace(':id', hasQuote),
            };

            // Kiểm tra nếu dataStatus = 1 thì ẩn các nút tạo và sửa
            if (dataStatus === 1) {
                $optionButton.find('.return-form, .quotation').addClass('d-none');
            } else {
                // Hiển thị và cập nhật nút tạo/sửa khi dataStatus không phải là 1
                $optionButton.find('.return-form, .quotation').removeClass('d-none');
                updateButtonText($optionButton.find('.return-form'), hasReturn, 'Tạo phiếu trả hàng',
                    'Sửa phiếu trả hàng', urls.createReturn, urls.editReturn);
                updateButtonText($optionButton.find('.quotation'), hasQuote, 'Tạo phiếu báo giá',
                    'Sửa phiếu báo giá', urls.createQuote, urls.editQuote);
            }

            const statusData = hasReturn !== 0 ? [{
                    status: 3,
                    label: 'Hoàn thành'
                },
                {
                    status: 4,
                    label: 'Không đồng ý'
                }
            ] : [{
                    status: 1,
                    label: 'Tiếp nhận'
                },
                {
                    status: 2,
                    label: 'Xử lý'
                }
            ];

            $statusList.empty(); // Xóa danh sách cũ
            statusData.forEach(({
                status,
                label
            }) => {
                $statusList.append(`
                <li data-return="${hasReturn}" data-recei="${dataRecei}" data-status="${status}">
                    ${label}
                </li>
            `);
            });

            const {
                clientX: x,
                clientY: y
            } = e;
            $optionButton.css({
                top: `${y}px`,
                left: `${x}px`,
                position: 'fixed',
                zIndex: 1000,
            }).show();
        });

        $(document).on('click', function(e) {
            if (!$(e.target).closest('.row-data').length && !$(e.target).closest('.option-button')
                .length) {
                $('.option-button').hide();
            }
        });

        $('.option-btn').on('click', function() {
            const url = $(this).data('url');
            if (url) window.open(url, '_blank');
        });

        function updateButtonText($button, hasItem, createText, editText, createUrl, editUrl) {
            if (hasItem === 0) {
                $button.text(createText).data('url', createUrl);
            } else {
                $button.text(editText).data('url', editUrl);
            }
        }
    });
    exportTableToExcel("#exportBtn", "#example2", "phieu_tiep_nhan.xlsx");
</script>
