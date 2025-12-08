<div id="mySidenav" class="sidenav border margin-top-118">
    <div id="show_info_Guest" class="position-relative">
        <div class="bg-filter-search border-0 text-center border-custom">
            <p class="font-weight-bold text-uppercase info-chung--heading text-center">
                DANH SÁCH
            </p>
        </div>
        <div class="d-flex w-100 my-2">
            <div class="px-1">
                <p class="m-0 p-0 text-13-black">Từ ngày</p>
                <input type="text" class="w-100 form-control mr-1 bg-input-guest-blue" id="formatFrom"
                    placeholder="Chọn ngày">
                <input type="hidden" class="w-100 form-control mr-1" id="fromDate">
            </div>
            <div class="px-1">
                <p class="m-0 p-0 text-13-black">Đến ngày</p>
                <input type="text" class="w-100 form-control mr-1 bg-input-guest-blue" id="formatTo"
                    placeholder="Chọn ngày">
                <input type="hidden" class="w-100 form-control ml-1" id="toDate">
            </div>
        </div>
        <div class="w-100 my-2 px-1">
            @if ($name == 'NH')
                <div class="position-relative">
                    <p class="m-0 p-0 text-13-black">Nhà cung cấp</p>
                    <input type="text" placeholder="Chọn thông tin" readonly
                        class="w-100 bg-input-guest py-2 px-2 form-control text-13-black nameProviderMiniView bg-input-guest-blue"
                        autocomplete="off" id="inputProvider">
                    <input type="hidden" class="idProviderMiniView">
                    <div id="listGuestMiniView"
                        class="bg-white position-absolute rounded list-guest shadow p-1 z-index-block list-guest w-100"
                        style="z-index: 99;display: none;">
                        <ul class="m-0 p-0 scroll-data">
                            <div class="p-1">
                                <div class="position-relative">
                                    <input type="text" placeholder="Nhập nhà cung cấp"
                                        class="pr-4 w-100 input-search bg-input-guest" id="searchProviderMiniView">
                                    <span id="search-icon" class="search-icon">
                                        <i class="fas fa-search text-table" aria-hidden="true"></i>
                                    </span>
                                </div>
                            </div>
                            @if ($guestOrProvider)
                                @foreach ($guestOrProvider as $provider_value)
                                    <li class="p-2 align-items-center text-wrap border-top"
                                        data-id="{{ $provider_value->id }}">
                                        <a href="#" title="{{ $provider_value->provider_name }}" style="flex:2;"
                                            data-id="{{ $provider_value->id }}"
                                            data-name="{{ $provider_value->provider_name }}" name="search-info"
                                            class="search-info">
                                            <span class="text-13-black">{{ $provider_value->provider_name }}</span>
                                        </a>
                                    </li>
                                @endforeach
                            @endif
                        </ul>
                    </div>
                </div>
            @endif
            @if ($name == 'XH' || $name == 'TN' || $name == 'BG' || $name == 'TH')
                <div class="position-relative">
                    <p class="m-0 p-0 text-13-black">Khách hàng</p>
                    <input type="text" placeholder="Chọn thông tin" readonly
                        class="w-100 bg-input-guest py-2 px-2 form-control text-13-black nameProviderMiniView bg-input-guest-blue"
                        autocomplete="off" id="inputProvider">
                    <input type="hidden" class="idProviderMiniView">
                    <div id="listGuestMiniView"
                        class="bg-white position-absolute rounded list-guest shadow p-1 z-index-block list-guest w-100"
                        style="z-index: 99;display: none;">
                        <ul class="m-0 p-0 scroll-data">
                            <div class="p-1">
                                <div class="position-relative">
                                    <input type="text" placeholder="Nhập khách hàng"
                                        class="pr-4 w-100 input-search bg-input-guest" id="searchProviderMiniView">
                                    <span id="search-icon" class="search-icon">
                                        <i class="fas fa-search text-table" aria-hidden="true"></i>
                                    </span>
                                </div>
                            </div>
                            @if ($guestOrProvider)
                                @foreach ($guestOrProvider as $customer_value)
                                    <li class="p-2 align-items-center text-wrap border-top"
                                        data-id="{{ $customer_value->id }}">
                                        <a href="#" title="{{ $customer_value->customer_name }}" style="flex:2;"
                                            data-id="{{ $customer_value->id }}"
                                            data-name="{{ $customer_value->customer_name }}" name="search-info"
                                            class="search-info">
                                            <span class="text-13-black">{{ $customer_value->customer_name }}</span>
                                        </a>
                                    </li>
                                @endforeach
                            @endif
                        </ul>
                    </div>
                </div>
            @endif
            <div class="mt-2 position-relative">
                <p class="m-0 p-0 text-13-black">Người lập</p>
                <input type="text" placeholder="Chọn thông tin" readonly
                    class="w-100 bg-input-guest py-2 px-2 form-control text-13-black nameUserMiniView bg-input-guest-blue"
                    autocomplete="off" id="inputUser">
                <input type="hidden" class="idUserMiniView">
                <div id="listUserMiniView"
                    class="bg-white position-absolute rounded list-guest shadow p-1 z-index-block list-guest w-100"
                    style="z-index: 99;display: none;">
                    <ul class="m-0 p-0 scroll-data">
                        <div class="p-1">
                            <div class="position-relative">
                                <input type="text" placeholder="Nhập người lập"
                                    class="pr-4 w-100 input-search bg-input-guest" id="searchUserMiniView">
                                <span id="search-icon" class="search-icon">
                                    <i class="fas fa-search text-table" aria-hidden="true"></i>
                                </span>
                            </div>
                        </div>
                        @if ($users)
                            @foreach ($users as $user_value)
                                <li class="p-2 align-items-center text-wrap border-top"
                                    data-id="{{ $user_value->id }}">
                                    <a href="#" title="{{ $user_value->name }}" style="flex:2;"
                                        class="search-user" data-id="{{ $user_value->id }}"
                                        data-name="{{ $user_value->name }}">
                                        <span class="text-13-black">{{ $user_value->name }}</span>
                                    </a>
                                </li>
                            @endforeach
                        @endif
                    </ul>
                </div>
            </div>
        </div>
        <div class="d-flex w-100 my-2 justify-content-between align-items-center">
            <div class="m-0 p-0">
                <p class="m-0 p-0 text-13-black"></p>
            </div>
            <a href="#" class="custom-btn d-flex align-items-center h-100 mx-1" id="search-view-mini"
                data-page="{{ $name }}">
                <span>
                    <svg class="mx-1" width="16" height="16" viewBox="0 0 16 16" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M10.4614 7.38499C10.4614 9.08433 9.08384 10.4619 7.3845 10.4619C5.68517 10.4619 4.30758 9.08433 4.30758 7.38499C4.30758 5.68566 5.68517 4.30807 7.3845 4.30807C9.08384 4.30807 10.4614 5.68566 10.4614 7.38499ZM10.1515 11.4575C9.36298 11.9942 8.41039 12.3081 7.3845 12.3081C4.66556 12.3081 2.46143 10.1039 2.46143 7.38499C2.46143 4.66605 4.66556 2.46191 7.3845 2.46191C10.1034 2.46191 12.3076 4.66605 12.3076 7.38499C12.3076 8.41087 11.9937 9.36347 11.457 10.152L14.4987 13.1939C14.8592 13.5543 14.8592 14.1387 14.4987 14.4992C14.1382 14.8597 13.5539 14.8597 13.1934 14.4992L10.1515 11.4575Z"
                            fill="white" />
                    </svg>
                </span>
                <p class="m-0 p-0">Tìm kiếm</p>
            </a>
        </div>
        <div class="outerViewMini text-nowrap">
            <table class="table table-hover bg-white rounded">
                <thead>
                    <tr>
                        <th scope="col" class="height-52">
                            <span class="d-flex justify-content-start text-13-black font-weight-bold">
                                Mã phiếu
                            </span>
                        </th>
                        <th scope="col" class="height-52">
                            <span class="d-flex justify-content-start text-13-black font-weight-bold">
                                Ngày lập phiếu
                            </span>
                        </th>
                        @if ($name == 'TN' || $name == 'TH')
                            <th scope="col" class="height-52">
                                <span class="d-flex justify-content-start text-13-black font-weight-bold">
                                    Tình trạng
                                </span>
                            </th>
                        @endif
                        @if ($name == 'BG')
                            <th scope="col" class="height-52">
                                <span class="d-flex justify-content-start text-13-black font-weight-bold">
                                    Loại phiếu
                                </span>
                            </th>
                        @endif
                        @if ($name == 'NH')
                            <th scope="col" class="height-52">
                                <span class="d-flex justify-content-start text-13-black font-weight-bold">
                                    Nhà cung cấp
                                </span>
                            </th>
                        @else
                            <th scope="col" class="height-52">
                                <span class="d-flex justify-content-start text-13-black font-weight-bold">
                                    Khách hàng
                                </span>
                            </th>
                        @endif
                    </tr>
                </thead>
                <tbody class="tbody-detail-info">
                    <!-- Hiệu ứng load -->
                    <tr class="loading-row">
                        <td colspan="<?php echo $name == 'TN' || $name == 'BG' || $name == 'TH' ? 4 : 3; ?>" class="text-center">
                            <div class="spinner-border" role="status"></div>
                        </td>
                    </tr>
                    @if ($name == 'NH')
                        @foreach ($data as $item)
                            <tr class="position-relative detail-info height-30 data-row" style="display:none"
                                data-id="{{ $item->id }}" data-page="{{ $name }}">
                                <td class="text-13-black border-bottom border-top-0">
                                    {{ $item->import_code }}
                                </td>
                                <td class="text-13-black text-left border-bottom border-top-0">
                                    {{ date_format(new DateTime($item->date_create), 'd/m/Y') }}
                                </td>
                                <td class="text-13-black border-bottom max-width120 border-top-0">
                                    {{ $item->provider_name }}
                                </td>
                            </tr>
                        @endforeach
                    @endif
                    @if ($name == 'XH')
                        @foreach ($data as $item)
                            <tr class="position-relative detail-info height-30 data-row" style="display:none"
                                data-id="{{ $item->id }}" data-page="{{ $name }}">
                                <td class="text-13-black border-bottom border-top-0">
                                    {{ $item->export_code }}
                                </td>
                                <td class="text-13-black text-left border-bottom border-top-0">
                                    {{ date_format(new DateTime($item->date_create), 'd/m/Y') }}
                                </td>
                                <td class="text-13-black border-bottom max-width120 border-top-0">
                                    {{ $item->customer->customer_name ?? '' }}
                                </td>
                            </tr>
                        @endforeach
                    @endif
                    @if ($name == 'TN')
                        @foreach ($data as $item)
                            <tr class="position-relative detail-info height-30 data-row" style="display:none"
                                data-id="{{ $item->id }}" data-page="{{ $name }}">
                                <td class="text-13-black border-bottom border-top-0">
                                    {{ $item->form_code_receiving }}
                                </td>
                                <td class="text-13-black text-left border-bottom border-top-0">
                                    {{ date_format(new DateTime($item->date_created), 'd/m/Y') }}
                                </td>
                                <td class="text-13-black border-bottom border-top-0">
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
                                <td class="text-13-black border-bottom max-width120 border-top-0">
                                    {{ $item->customer->customer_name }}
                                </td>
                            </tr>
                        @endforeach
                    @endif
                    @if ($name == 'BG')
                        @foreach ($data as $item)
                            <tr class="position-relative detail-info height-30 data-row" style="display:none"
                                data-id="{{ $item->id }}" data-page="{{ $name }}">
                                <td class="text-13-black border-bottom border-top-0">
                                    {{ $item->quotation_code }}
                                </td>
                                <td class="text-13-black text-left border-bottom border-top-0">
                                    {{ date_format(new DateTime($item->quotation_date), 'd/m/Y') }}
                                </td>
                                <td class="text-13-black border-bottom border-top-0">
                                    @if ($item->reception->form_type == 1)
                                        Bảo hành
                                    @elseif($item->reception->form_type == 2)
                                        Dịch vụ
                                    @elseif($item->reception->form_type == 3)
                                        Bảo hành dịch vụ
                                    @endif
                                </td>
                                <td class="text-13-black border-bottom max-width120 border-top-0">
                                    {{ $item->customer->customer_name }}
                                </td>
                            </tr>
                        @endforeach
                    @endif
                    @if ($name == 'TH')
                        @foreach ($data as $item)
                            <tr class="position-relative detail-info height-30 data-row" style="display:none"
                                data-id="{{ $item->id }}" data-page="{{ $name }}">
                                <td class="text-13-black border-bottom border-top-0">
                                    {{ $item->return_code }}
                                </td>
                                <td class="text-13-black text-left border-bottom border-top-0">
                                    {{ date_format(new DateTime($item->date_created), 'd/m/Y') }}
                                </td>
                                <td class="text-13-black border-bottom border-top-0">
                                    @if ($item->status == 1)
                                        Hoàn thành
                                    @elseif($item->status == 2)
                                        Khách không đồng ý
                                    @endif
                                </td>
                                <td class="text-13-black border-bottom max-width120 border-top-0">
                                    {{ $item->customer->customer_name }}
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
<script src="{{ asset('/js/view-mini.js') }}"></script>
<script>
    $(document).ready(function() {
        flatpickr("#formatFrom", {
            locale: "vn",
            dateFormat: "d/m/Y",
            onChange: function(selectedDates) {
                // Lấy giá trị ngày đã chọn
                if (selectedDates.length > 0) {
                    const formattedDate = flatpickr.formatDate(
                        selectedDates[0],
                        "Y-m-d"
                    );
                    document.getElementById("fromDate").value =
                        formattedDate;
                }
            },
        });
        flatpickr("#formatTo", {
            locale: "vn",
            dateFormat: "d/m/Y",
            onChange: function(selectedDates) {
                // Lấy giá trị ngày đã chọn
                if (selectedDates.length > 0) {
                    const formattedDate = flatpickr.formatDate(
                        selectedDates[0],
                        "Y-m-d"
                    );
                    document.getElementById("toDate").value =
                        formattedDate;
                }
            },
        });
        // Hàm giả lập tải dữ liệu
        function loadData() {
            // Hiển thị hiệu ứng load
            $('.loading-row').show();
            $('.data-row').hide(); // Ẩn phần dữ liệu

            setTimeout(function() {
                // Ẩn hiệu ứng load
                $('.loading-row').hide();

                // Hiển thị dữ liệu
                $('.data-row').show();
            }, 500); // Thời gian trì hoãn
        }

        loadData(); // Gọi hàm load dữ liệu khi trang được tải
    });

    //
    $(document).ready(function() {
        // Hàm thêm dòng dữ liệu vào bảng
        function appendRows(data, page) {
            let maPhieu = null;
            let ten = null;
            let hienThiTinhTrang = null;
            let hienThiLoaiPhieu = null;

            const rows = data.map(detail => {
                if (page == 'NH') {
                    maPhieu = detail.import_code;
                    ten = detail.provider_name;
                    hienThiTinhTrang = "d-none";
                    hienThiLoaiPhieu = "d-none";
                }
                if (page == 'XH') {
                    maPhieu = detail.export_code;
                    ten = detail.customer_name;
                    hienThiTinhTrang = "d-none";
                    hienThiLoaiPhieu = "d-none";
                }
                if (page == 'TN') {
                    maPhieu = detail.form_code_receiving;
                    ten = detail.customer_name;
                    hienThiTinhTrang = "block";
                    hienThiLoaiPhieu = "d-none";
                }
                if (page == 'BG') {
                    maPhieu = detail.quotation_code;
                    ten = detail.customer_name;
                    hienThiLoaiPhieu = "block";
                    hienThiTinhTrang = "d-none";
                }
                if (page == 'TH') {
                    maPhieu = detail.return_code;
                    ten = detail.customer_name;
                    hienThiTinhTrang = "block";
                    hienThiLoaiPhieu = "d-none";
                }
                return `
                <tr class="position-relative detail-info height-52" data-id="${detail.id}" data-page="${page}">
                    <td class="text-13-black text-left border-top-0 border-bottom">${maPhieu}</td>
                    <td class="text-13-black text-left border-top-0 border-bottom">${detail.date_create}</td>
                    <td class="text-13-black text-left border-top-0 border-bottom ${hienThiTinhTrang}">${detail.status}</td>
                    <td class="text-13-black text-left border-top-0 border-bottom ${hienThiLoaiPhieu}">${detail.form_type}</td>
                    <td class="text-13-black text-left border-top-0 border-bottom max-width120">${ten}</td>
                </tr>
            `;
            }).join('');
            $('.tbody-detail-info').append(rows);
        }

        // Xử lý tìm kiếm
        $('#search-view-mini').on('click', function() {
            const page = this.dataset.page;
            const fromDate = $('#fromDate').val();
            const toDate = $('#toDate').val();
            const idGuest = $('.idProviderMiniView').val();
            const creator = $('.idUserMiniView').val();

            // Gọi Ajax để tìm kiếm
            $.ajax({
                url: '{{ route('searchMiniView') }}',
                type: 'GET',
                data: {
                    page,
                    fromDate,
                    toDate,
                    idGuest,
                    creator
                },
                success: function(data) {
                    // Xóa bảng cũ và thêm dữ liệu mới
                    $('.tbody-detail-info').empty();
                    if (data.length > 0) {
                        appendRows(data, page);

                        // Lưu dữ liệu tìm kiếm vào localStorage
                        const searchData = {
                            page,
                            fromDate,
                            toDate,
                            idGuest,
                            creator,
                            results: data
                        };
                        localStorage.setItem('searchData', JSON.stringify(searchData));
                    }
                },
            });
        });

        // Chuyển trang khi nhấn vào dòng chi tiết
        $(document).on('click', '.detail-info', function() {
            const id = this.dataset.id;
            const page = this.dataset.page;
            let url;

            if (page == 'NH') {
                url = "{{ route('imports.edit', ':id') }}".replace(':id', id); // Gán giá trị cho url
            }
            if (page == 'XH') {
                url = "{{ route('exports.edit', ':id') }}".replace(':id', id); // Gán giá trị cho url
            }
            if (page == 'TN') {
                url = "{{ route('receivings.edit', ':id') }}".replace(':id',
                    id); // Gán giá trị cho url
            }
            if (page == 'BG') {
                url = "{{ route('quotations.edit', ':id') }}".replace(':id',
                    id); // Gán giá trị cho url
            }
            if (page == 'TH') { 
                url = "{{ route('returnforms.edit', ':id') }}".replace(':id',
                    id); // Gán giá trị cho url
            }

            // Lưu trạng thái trang trước khi chuyển
            localStorage.setItem('currentDetailId', id);

            window.location.href = url;
        });


        // Lấy dữ liệu từ localStorage và khôi phục lại tìm kiếm
        const searchData = JSON.parse(localStorage.getItem('searchData'));

        if (searchData) {
            // Điền lại giá trị vào input tìm kiếm
            $('#formatFrom').val(searchData.fromDate ? moment(searchData.fromDate).format('DD/MM/YYYY') : '');
            $('#formatTo').val(searchData.toDate ? moment(searchData.toDate).format('DD/MM/YYYY') : '');
            $('#fromDate').val(searchData.fromDate);
            $('#toDate').val(searchData.toDate);
            $('.idProviderMiniView').val(searchData.idGuest);
            $('.idUserMiniView').val(searchData.creator);

            // Khôi phục danh sách tìm kiếm
            $('.tbody-detail-info').empty();
            if (searchData.results && searchData.results.length > 0) {
                appendRows(searchData.results, searchData.page);
            }
        }

        // Xóa dữ liệu khỏi localStorage nếu không cần giữ
        localStorage.removeItem('searchData');
    });
</script>
