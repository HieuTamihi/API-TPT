@include('partials.header', ['activeGroup' => 'manageProfess', 'activeName' => 'returnforms'])
<form id="form-submit" action="{{ route('returnforms.store') }}" method="POST">
    @csrf
    <div class="content-wrapper--2Column m-0 min-height--none">
        <div class="content-header-fixed-report-1 pt-2">
            <div class="content__header--inner pl-4">
                <div class="content__heading--left d-flex opacity-1">
                    <div class="d-flex mb-2 mr-2 p-1 border rounded" style="order: 0;">
                        <span class="text text-13-black m-0" style="flex: 2;">Chọn phiếu tiếp nhận :</span>
                        <div class="form-check form-check-inline mr-1">
                            <select class="form-check-input border-0 text text-13-black" name="reception_id" required
                                id="reception">
                                <option value="">Chưa chọn phiếu</option>
                                @foreach ($receivings as $item)
                                    <option value="{{ $item->id }}"
                                        {{ $item->id == request()->query('recei') ? 'selected' : '' }}>
                                        {{ $item->form_code_receiving }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="d-flex mb-2 mr-2 p-1 border rounded" style="order: 0;">
                        <span class="text text-13-black m-0" style="flex: 2;">Loại phiếu :</span>
                        <div class="form-check form-check-inline mr-1">
                            <select class="form-check-input border-0 text text-13-black select-nodropdown"
                                name="form_type" id="form_type" disabled>
                                <option value="0">Chưa chọn</option>
                                <option value="1">Bảo hành</option>
                                <option value="2">Dịch vụ</option>
                                <option value="3">Bảo hành dịch vụ</option>
                            </select>
                        </div>
                    </div>
                    <div class="d-flex mb-2 mr-2 p-1 border rounded" style="order: 0;">
                        <span class="text text-13-black m-0" style="flex: 2;">Trạng thái :</span>
                        <div class="form-check form-check-inline mr-1">
                            <select class="form-check-input border-0 text text-13-black" name="status" required
                                id="status">
                                <option value="1">Hoàn thành</option>
                                <option value="2">Không đồng ý</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="d-flex content__heading--right">
                    <div class="row m-0">
                        <a href="{{ route('returnforms.index') }}">
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
                        {{-- <button id="sideGuest" type="button" class="btn-option border-0 mx-1">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <rect x="16" width="16" height="16" rx="5" transform="rotate(90 16 0)"
                                    fill="#ECEEFA"></rect>
                                <path
                                    d="M15 11C15 13.2091 13.2091 15 11 15L5 15C2.7909 15 1 13.2091 1 11L1 5C1 2.79086 2.7909 1 5 1L11 1C13.2091 1 15 2.79086 15 5L15 11ZM10 13.5L10 2.5L5 2.5C3.6193 2.5 2.5 3.61929 2.5 5L2.5 11C2.5 12.3807 3.6193 13.5 5 13.5H10Z"
                                    fill="#26273B" fill-opacity="0.8"></path>
                            </svg>
                        </button> --}}
                    </div>
                </div>
            </div>
        </div>
        <div class="content-wrapper2 px-0 py-0 margin-top-118">
            <div id="contextMenuPBH" class="dropdown-menu"
                style="display: none; background: #ffffff; position: absolute; width:13%;  padding: 3px 10px;  box-shadow: 0 0 10px -3px rgba(0, 0, 0, .3); border: 1px solid #ccc;">
                <a class="dropdown-item text-13-black" href="#" data-option="donhang">Tạo phiếu xuất kho</a>
            </div>
            {{-- Thông tin khách hàng --}}
            <div class="" id="main" style="margin-right: 10px !important;">

                <div class="border">
                    <div class="info-form">
                        <div class="bg-filter-search border-0 text-center">
                            <p class="font-weight-bold text-uppercase info-chung--heading text-center">
                                THÔNG TIN PHIẾU TRẢ HÀNG
                            </p>
                        </div>
                        <div class="d-flex w-100">
                            <div
                                class="d-flex w-100 justify-content-between py-2 px-3 border border-bottom-0 border-right-0 align-items-center text-left text-nowrap position-relative height-44">
                                <span class="text-13-black text-nowrap mr-3 required-label" style="width: 180px;">Mã
                                    phiếu</span>
                                <input type="text" id="return_code" name="return_code" style="flex:2;"
                                    placeholder="Nhập thông tin" value="{{ $quoteNumber }}"
                                    class="text-13-black w-50 border-0 bg-input-guest date_picker bg-input-guest-blue py-2 px-2">
                            </div>
                            <div
                                class="d-flex w-100 justify-content-between py-2 px-3 border border-bottom-0 border-right-0 align-items-center text-left text-nowrap position-relative height-44">
                                <span class="text-13-black btn-click font-weight-bold" style="width: 180px;">Khách
                                    hàng</span>
                                <input placeholder="Nhập thông tin" autocomplete="off" required id="customer_name"
                                    readonly class="text-13-black w-100 border-0 bg-input-guest py-2 px-2"
                                    style="flex:2;" />
                                <input type="hidden" name="customer_id" id="customer_id">
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
                                    style=" flex:2;" />
                                <input type="hidden" value="{{ now()->format('Y-m-d') }}" name="date_created"
                                    id="hiddenDateCreate">
                            </div>
                            <div
                                class="d-flex w-100 justify-content-between py-2 px-3 border border-bottom-0 border-right-0 align-items-center text-left text-nowrap position-relative height-44">
                                <span class="text-13-black btn-click" style="width: 180px;"> Người liên hệ </span>
                                <input name="contact_person" id="contact_person" placeholder="Nhập thông tin"
                                    autocomplete="off"
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
                                    name="phone_number" id="phone_number" />
                            </div>

                        </div>
                        <div class="d-flex w-100">
                            <div
                                class="d-flex w-100 justify-content-between py-2 px-3 border border-bottom-0 border-right-0 align-items-center text-left text-nowrap position-relative height-44">
                                <span class="text-13-black text-nowrap mr-3" style="width: 100px;">Địa chỉ</span>
                                <input placeholder="Nhập thông tin" name="address" id="address"
                                    class="text-13-black w-50 border-0 bg-input-guest bg-input-guest-blue py-2 px-2"style="flex:2;" />
                            </div>
                            <div
                                class="d-flex w-100 justify-content-between py-2 px-3 border border-bottom-0 border-right-0 align-items-center text-left text-nowrap position-relative height-44">
                            </div>
                            <div
                                class="d-flex w-100 justify-content-between py-2 px-3 border border-bottom-0 border-right-0 align-items-center text-left text-nowrap position-relative height-44">
                                <span class="text-13-black text-nowrap mr-3" style="width: 180px;">Phương thức trả
                                    hàng</span>
                                <select
                                    class="text-13-black w-50 border-0 addr bg-input-guest addr bg-input-guest-blue py-2 px-2"
                                    style="flex:2;" name="return_method" id="return_method">
                                    <option value="1">Khách nhận trực tiếp</option>
                                    <option value="2">Chuyển phát nhanh</option>
                                    <option value="3">Gửi chành xe</option>
                                </select>
                            </div>
                        </div>
                        <div class="d-flex w-100">
                            <div
                                class="d-flex w-100 justify-content-between py-2 px-3 border border-bottom-0 border-right-0 align-items-center text-left text-nowrap position-relative height-44">
                                <span class="text-13-black text-nowrap mr-3" style="width: 100px;">Ghi chú</span>
                                <input name="notes" placeholder="Nhập thông tin" autocomplete="off" id="notes"
                                    class="text-13-black w-50 border-0 addr bg-input-guest addr bg-input-guest-blue py-2 px-2"style="flex:2;" />
                            </div>
                            <div
                                class="d-flex w-100 justify-content-between py-2 px-3 border border-bottom-0 border-right-0 align-items-center text-left text-nowrap position-relative height-44">
                            </div>
                            <div
                                class="d-flex w-100 justify-content-between py-2 px-3 border border-bottom-0 border-right-0 align-items-center text-left text-nowrap position-relative height-44">
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
                    <section class="content" style="overflow-x:visible">
                        <table class="table" id="inputcontent">
                            <thead>
                                <tr style="height:44px;">
                                    <th class="border-right px-2 p-0 pl-4 col-product-code">
                                        <span class="text-table text-secondary">Mã hàng</span>
                                    </th>
                                    <th class="border-right px-2 p-0 text-left col-product-name">
                                        <span class="text-table text-secondary">Tên Hàng</span>
                                    </th>
                                    <th class="border-right px-2 p-0 text-left col-brand">
                                        <span class="text-table text-secondary">Hãng</span>
                                    </th>
                                    <th class="border-right px-2 p-0 text-right col-quantity">
                                        <span class="text-table text-secondary">Số lượng</span>
                                    </th>
                                    <th class="border-right px-2 p-0 text-right col-serial-number">
                                        <span class="text-table text-secondary">Serial Number</span>
                                    </th>
                                    <th class="border-right note px-2 p-0 text-left col-replacement-code">
                                        <span class="text-table text-secondary">Mã hàng đổi</span>
                                    </th>
                                    <th class="border-right note px-2 p-0 text-left col-replacement-serial">
                                        <span class="text-table text-secondary">Serial Number đổi</span>
                                    </th>
                                    <th class="border-right note px-2 p-0 text-left col-extra-warranty">
                                        <span class="text-table text-secondary">Thông tin</span>
                                    </th>
                                    <th class="border-right note px-2 p-0 text-left col-extra-warranty">
                                        <span class="text-table text-secondary">Bảo hành thêm</span>
                                    </th>
                                    <th class="border-right note px-2 p-0 text-left col-note">
                                        <span class="text-table text-secondary">Ghi chú</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="tbody-data">
                            </tbody>
                        </table>
                    </section>
                </div>
            </div>
        </div>
    </div>
</form>
<script src="{{ asset('js/returnform.js') }}"></script>
<script>
    $(document).ready(function() {
        // Trích xuất tham số từ URL
        const getQueryParameter = name => new URLSearchParams(window.location.search).get(name);
        const receiParam = getQueryParameter('recei');
        const fetchData = (selectedId) => {
            $.ajax({
                url: '{{ route('getReceiving') }}',
                type: 'GET',
                data: {
                    selectedId: selectedId,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    const formType = response.data.form_type;
                    const hideReplacement = formType === 2 || formType === 3 ? "d-none" : "";
                    const hideExtraWarranty = formType === 1 || formType === 3 ? "d-none" : "";

                    // Thêm hoặc xóa lớp "d-none" cho các cột dựa trên logic
                    $(".col-replacement-code, .col-replacement-serial")
                        .toggleClass("d-none", hideReplacement === "d-none");
                    $(".col-extra-warranty")
                        .toggleClass("d-none", hideExtraWarranty === "d-none");
                    $('#form_type').val(formType).change();
                    populateTableRows(response.product, "#tbody-data", response.productData,
                        formType);
                    $('#customer_name').val(response.data.customer.customer_name);
                    $('#customer_id').val(response.data.customer_id);
                    $('#contact_person').val(response.data.contact_person);
                    $('#phone_number').val(response.data.phone);
                    $('#address').val(response.data.address);
                }
            });
        };
        if (receiParam) fetchData(receiParam);

        // Xử lý thay đổi khi chọn reception
        $('#reception').change(function() {
            const selectedId = $(this).val();
            if (selectedId != 0) fetchData(selectedId);
        });
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
    });
    $(document).ready(function() {
        let isChecking = false;

        $("#btn-get-unique-products").click(function(e) {
            let isValid = true;
            let isCheckingAny = false;

            $("#tbody-data .replacement_serial_number_id").each(function() {
                const $input = $(this);
                const checking = $input.data("checking");
                const $checkIcon = $input.siblings(".check-icon");
                const iconText = $checkIcon.text().trim();

                if (checking === true) {
                    isCheckingAny = true;
                    return false;
                }

                if (iconText === "✖") {
                    isValid = false;
                    return false;
                }
            });

            if (isCheckingAny) {
                e.preventDefault();
                showAutoToast("warning", "Vui lòng chờ kiểm tra số serial...");
                return;
            }

            if (!isValid) {
                e.preventDefault();
                showAutoToast("warning", "Dữ liệu không hợp lệ!");
                return;
            }
        });

        function checkSerial($input) {
            const serialNumber = $input.val().trim();

            // Nếu chưa nhập gì thì không kiểm tra
            if (serialNumber === "") {
                const $checkIcon = $input.siblings(".check-icon");
                $checkIcon.text("").css("color", "").attr("title", "");
                $input.data("checking", false);
                return;
            }

            const $row = $input.closest("tr");
            const replacementCode = $row.find("input.replacement_code").val();
            const $checkIcon = $input.siblings(".check-icon");

            $input.data("checking", true);
            isChecking = true;

            // Kiểm tra trùng lặp
            let isDuplicate = false;
            $(".replacement_serial_number_id").each(function() {
                const otherValue = $(this).val().trim();
                if ($(this)[0] !== $input[0] && otherValue === serialNumber && serialNumber !== "") {
                    isDuplicate = true;
                    return false;
                }
            });

            if (isDuplicate) {
                $checkIcon.text("✖").css("color", "red").attr("title", "Serial bị trùng lặp");
                $input.data("checking", false);
                isChecking = false;
                return;
            }

            if (!replacementCode) {
                $checkIcon.text("✖").css("color", "red").attr("title", "Chưa chọn mã sản phẩm thay thế");
                $input.data("checking", false);
                isChecking = false;
                return;
            }

            // Gọi AJAX kiểm tra
            $.ajax({
                url: '{{ route('checkSNReplace') }}',
                type: "GET",
                data: {
                    serialNumber: serialNumber,
                    product_id: replacementCode,
                    _token: $('meta[name="csrf-token"]').attr("content"),
                },
                success: function(response) {
                    if (response.status === "success") {
                        $checkIcon.text("✔").css("color", "green").attr("title", response.message);
                    } else {
                        $checkIcon.text("✖").css("color", "red").attr("title", response.message);
                    }
                },
                error: function() {
                    $checkIcon.text("?").css("color", "orange").attr("title", "Lỗi kết nối server");
                },
                complete: function() {
                    $input.data("checking", false);
                    isChecking = false;
                },
            });
        }

        // Sự kiện paste
        $(document).on("paste", ".replacement_serial_number_id", function() {
            const $input = $(this);
            setTimeout(() => {
                $input.val($input.val().trim());
                checkSerial($input);
            }, 0);
        });

        // Sự kiện change / click / blur (KHÔNG dùng search-item!)
        $(document).on("change click blur", ".replacement_serial_number_id", function() {
            checkSerial($(this));
        });

        // Sự kiện click vào item trong danh sách
        $(document).on("mousedown", ".search-item", function(e) {
            const $item = $(this);
            const $container = $item.closest(".search-container");
            const $input = $container.find(".search-input");
            const $row = $item.closest("tr");

            const code = $item.data("code");
            const replace_id = $item.data("replace_id");

            $input.val(code);
            $row.find("input.replacement_code").val(replace_id);

            $container.find(".search-list").removeClass("active");

            // Bắt buộc kiểm tra lại các serial khi mã đổi thay đổi
            $row.find(".replacement_serial_number_id").each(function() {
                const $serialInput = $(this);
                const $checkIcon = $serialInput.siblings(".check-icon");

                $checkIcon.text("").css("color", "transparent");
                $serialInput.data("checking", false);

                if ($serialInput.val().trim() !== "") {
                    checkSerial($serialInput);
                }
            });
        });
    });
</script>
