@include('partials.header', ['activeGroup' => 'manageProfess', 'activeName' => 'quotations'])
<form id="form-submit" action="{{ route('quotations.store') }}" method="POST">
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
                </div>
                <div class="d-flex content__heading--right">
                    <div class="row m-0">
                        <a href="{{ route('quotations.index') }}">
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
                            THÔNG TIN PHIẾU BÁO GIÁ
                        </p>
                    </div>
                    <div class="d-flex w-100">
                        <div
                            class="d-flex w-100 justify-content-between py-2 px-3 border border-bottom-0 border-right-0 align-items-center text-left text-nowrap position-relative height-44">
                            <span class="text-13-black text-nowrap mr-3 required-label" style="width: 180px;">Mã
                                phiếu</span>
                            <input type="text" id="quotation_code" name="quotation_code" style="flex:2;"
                                placeholder="Nhập thông tin" value="{{ $quoteNumber }}"
                                class="text-13-black w-50 border-0 bg-input-guest date_picker bg-input-guest-blue py-2 px-2">
                        </div>
                        <div
                            class="d-flex w-100 justify-content-between py-2 px-3 border border-bottom-0 border-right-0 align-items-center text-left text-nowrap position-relative height-44">
                            <span class="text-13-black btn-click font-weight-bold" style="width: 180px;">Khách hàng</span>
                            <input autocomplete="off" required id="customer_name" readonly
                                class="text-13-black w-100 border-0 bg-input-guest py-2 px-2" style="flex:2;" />
                            <input type="hidden" name="customer_id" id="customer_id">
                        </div>
                        <div
                            class="d-flex w-100 justify-content-between py-2 px-3 border border-bottom-0 border-right-0 align-items-center text-left text-nowrap position-relative height-44">
                            <span class="text-13-black text-nowrap mr-3" style="width: 180px;">Người lập phiếu</span>
                            <input type="hidden" name="user_id" value="{{ Auth::user()->id }}">
                            <input class="text-13-black w-50 border-0 bg-input-guest py-2 px-2" autocomplete="off"
                                placeholder="Nhập thông tin" style="flex:2;" name=""
                                value="{{ Auth::user()->name }}" readonly />
                        </div>
                    </div>
                    <div class="d-flex w-100">
                        <div 
                            class="d-flex w-100 justify-content-between py-2 px-3 border border-bottom-0 border-right-0 align-items-center text-left text-nowrap position-relative height-44">
                            <span class="text-13-black text-nowrap mr-3 required-label" style="width: 180px;">Ngày lập
                                phiếu</span>
                            <input placeholder="Nhập thông tin" autocomplete="off" type="date" id="dateCreate"
                                class="text-13-black w-50 border-0 bg-input-guest bg-input-guest-blue py-2 px-2"
                                style=" flex:2;" value="" />
                            <input type="hidden" value="{{ now()->format('Y-m-d') }}" name="quotation_date"
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
                                autocomplete="off" placeholder="Nhập thông tin" style="flex:2;" name="phone"
                                id="phone" />
                        </div>

                    </div>
                    <div class="d-flex w-100">
                        <div
                            class="d-flex w-100 justify-content-between py-2 px-3 border border-bottom-0 border-right-0 align-items-center text-left text-nowrap position-relative height-44">
                            <span class="text-13-black text-nowrap mr-3" style="width: 180px;">Địa chỉ</span>
                            <input placeholder="Nhập thông tin" name="address" id="address"
                                class="text-13-black w-50 border-0 bg-input-guest bg-input-guest-blue py-2 px-2"style="flex:2;" />
                        </div>
                    </div>
                    <div class="d-flex w-100">
                        <div
                            class="d-flex w-100 justify-content-between py-2 px-3 border border-bottom-0 border-right-0 align-items-center text-left text-nowrap position-relative height-44">
                            <span class="text-13-black text-nowrap mr-3" style="width: 180px;">Ghi chú</span>
                            <input name="notes" placeholder="Nhập thông tin" autocomplete="off" id="notes"
                                class="text-13-black w-50 border-0 addr bg-input-guest addr bg-input-guest-blue py-2 px-2"style="flex:2;" />
                        </div>
                    </div>
                </div>
            </div>
            </div>
            {{-- Thông tin khách hàng --}}
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
                    <p class="font-weight-bold text-uppercase info-chung--heading text-center">THÔNG TIN HÀNG HOÁ/DỊCH
                        VỤ
                    </p>
                </div>
                <div class="container-fluided">
                    <section class="content">
                        <table class="table" id="inputcontent">
                            <thead>
                                <tr style="height:44px;">
                                    <th class="border-right px-2 p-0 pl-4" style="width:5%;">
                                        <span class="text-table text-secondary">STT</span>
                                    </th>
                                    <th class="border-right px-2 p-0 text-left" style="width:25%;">
                                        <span class="text-table text-secondary">Thông tin hàng hoá/dịch vụ</span>
                                    </th>
                                    <th class="border-right px-2 p-0 text-left" style="width:10%;">
                                        <span class="text-table text-secondary">ĐVT</span>
                                    </th>
                                    <th class="border-right px-2 p-0 text-right" style="width:10%;">
                                        <span class="text-table text-secondary">Hãng</span>
                                    </th>
                                    <th class="border-right px-2 p-0 text-right" style="width:5%;">
                                        <span class="text-table text-secondary">Số lượng</span>
                                    </th>
                                    <th class="border-right px-2 p-0 text-right" style="width:8%;">
                                        <span class="text-table text-secondary">Đơn giá</span>
                                    </th>
                                    <th class="border-right note px-2 p-0 text-left" style="width:5%;">
                                        <span class="text-table text-secondary">Thuế</span>
                                    </th>
                                    <th class="border-right note px-2 p-0 text-left" style="width:10%;">
                                        <span class="text-table text-secondary">Thành tiền</span>
                                    </th>
                                    <th class="border-right note px-2 p-0 text-left" style="width:15%;">
                                        <span class="text-table text-secondary">Ghi chú</span>
                                    </th>
                                    <th class="" style="width:10%;"></th>
                                </tr>
                            </thead>
                            <tbody id="tbody-data">
                                <tr class="row-product bg-white">
                                    <td class="border-right p-2 text-13 align-top border-bottom border-top-0 pl-4">
                                    </td>
                                    <td class="border-right p-2 text-13 align-top border-bottom border-top-0">
                                        <input type="text" autocomplete="off"
                                            class="border-0 pl-1 pr-2 py-1 w-100 service_name height-32 bg-input-guest-blue"
                                            name="services[0][service_name]" value="" required>
                                    </td>
                                    <td class="border-right p-2 text-13 align-top border-bottom border-top-0">
                                        <input type="text" autocomplete="off"
                                            class="border-0 pl-1 pr-2 py-1 w-100 unit height-32 bg-input-guest-blue"
                                            name="services[0][unit]" value="">
                                    </td>
                                    <td class="border-right p-2 text-13 align-top border-bottom border-top-0">
                                        <input type="text" autocomplete="off"
                                            class="border-0 pl-1 pr-2 py-1 w-100 brand height-32 bg-input-guest-blue"
                                            name="services[0][brand]" value="">
                                    </td>
                                    <td class="border-right p-2 text-13 align-top border-bottom border-top-0">
                                        <input type="number" min="1" autocomplete="off"
                                            class="border-0 pl-1 pr-2 py-1 w-100 quantity height-32 bg-input-guest-blue"
                                            name="services[0][quantity]">
                                    </td>
                                    <td class="border-right p-2 text-13 align-top border-bottom border-top-0">
                                        <input type="number" step="0.01" min="0" autocomplete="off"
                                            class="border-0 pl-1 pr-2 py-1 w-100 unit_price height-32 bg-input-guest-blue"
                                            name="services[0][unit_price]">
                                    </td>
                                    <td class="border-right p-2 text-13 align-top border-bottom border-top-0">
                                        <select
                                            class="border-0 pl-1 pr-2 py-1 w-100 height-32 bg-input-guest-blue tax_rate"
                                            name="services[0][tax_rate]">
                                            <option value="10">10%</option>
                                            <option value="8">8%</option>
                                            <option value="0">KCT</option>
                                        </select>
                                    </td>
                                    <td class="border-right p-2 text-13 align-top border-bottom border-top-0">
                                        <input type="text" step="0.01" min="0" readonly
                                            class="border-0 pl-1 pr-2 py-1 w-100 total height-32"
                                            name="services[0][total]" value="0">
                                    </td>
                                    <td class="border-right p-2 text-13 align-top border-bottom border-top-0">
                                        <input type="text" autocomplete="off"
                                            class="border-0 pl-1 pr-2 py-1 w-100 note height-32 bg-input-guest-blue"
                                            name="services[0][note]" value="">
                                    </td>
                                    <td class="p-2 align-top activity border-bottom border-top-0 border-right">
                                        <button type="button" class="delete-row btn btn-sm"> <svg class="delete-row"
                                                width="17" height="17" viewBox="0 0 17 17" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M13.1417 6.90625C13.4351 6.90625 13.673 7.1441 13.673 7.4375C13.673 7.47847 13.6682 7.5193 13.6589 7.55918L12.073 14.2992C11.8471 15.2591 10.9906 15.9375 10.0045 15.9375H6.99553C6.00943 15.9375 5.15288 15.2591 4.92702 14.2992L3.34113 7.55918C3.27393 7.27358 3.45098 6.98757 3.73658 6.92037C3.77645 6.91099 3.81729 6.90625 3.85826 6.90625H13.1417ZM9.03125 1.0625C10.4983 1.0625 11.6875 2.25175 11.6875 3.71875H13.8125C14.3993 3.71875 14.875 4.19445 14.875 4.78125V5.3125C14.875 5.6059 14.6371 5.84375 14.3438 5.84375H2.65625C2.36285 5.84375 2.125 5.6059 2.125 5.3125V4.78125C2.125 4.19445 2.6007 3.71875 3.1875 3.71875H5.3125C5.3125 2.25175 6.50175 1.0625 7.96875 1.0625H9.03125ZM9.03125 2.65625H7.96875C7.38195 2.65625 6.90625 3.13195 6.90625 3.71875H10.0938C10.0938 3.13195 9.61805 2.65625 9.03125 2.65625Z"
                                                    fill="#6B6F76"></path>
                                            </svg></button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <input type="hidden" name="data-test" id="data-test">
                        <section class="content mt-2">
                            <div class="container-fluided">
                                <div class="d-flex ml-4">
                                    <button type="button" data-modal-id="modal-id" data-toggle="modal"
                                        data-target="#modal-id"
                                        class="btn-save-print d-flex align-items-center h-100 py-1 px-2 rounded activity"
                                        id="btn-add-row" style="margin-right:10px">
                                        <svg class="mr-2" xmlns="http://www.w3.org/2000/svg" width="12"
                                            height="12" viewBox="0 0 18 18" fill="none">
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="M9 0C9.58186 -2.96028e-08 10.0536 0.471694 10.0536 1.05356L10.0536 16.9464C10.0536 17.5283 9.58186 18 9 18C8.41814 18 7.94644 17.5283 7.94644 16.9464V1.05356C7.94644 0.471694 8.41814 -2.96028e-08 9 0Z"
                                                fill="#42526E" />
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="M18 9C18 9.58187 17.5283 10.0536 16.9464 10.0536H1.05356C0.471694 10.0536 -2.07219e-07 9.58187 0 9C-7.69672e-07 8.41814 0.471695 7.94644 1.05356 7.94644H16.9464C17.5283 7.94644 18 8.41814 18 9Z"
                                                fill="#42526E" />
                                        </svg>
                                        <span class="text-table">Thêm dòng</span>
                                    </button>
                                </div>
                            </div>
                        </section>
                        <div class="content">
                            <div class="row" style="width:95%;">
                                <div class="position-relative col-lg-6 px-0"></div>
                                <div class="position-relative col-lg-3 col-md-7 col-sm-12 margin-left180">
                                    <div class="m-3 ">
                                        <div class="d-flex justify-content-between">
                                            <span class="text-14-black">Giá trị trước thuế:</span>
                                            <span id="total-amount-sum" class="text-14-black">0</span>
                                        </div>
                                        <div class="d-flex justify-content-between mt-2 align-items-center">
                                            <span class="text-14-black">VAT 8%</span>
                                            <span id="product-tax8" class="text-14-black">0</span>
                                        </div>
                                        <div class="d-flex justify-content-between mt-2 align-items-center">
                                            <span class="text-14-black">VAT 10%</span>
                                            <span id="product-tax10" class="text-14-black">0</span>
                                        </div>
                                        <div class="d-flex justify-content-between mt-2">
                                            <span class="text-20-bold">Tổng cộng:</span>
                                            <span id="grand-total" class="text-20-bold text-right">
                                                0
                                            </span>
                                            <input type="hidden" name="total_amount" value="0" id="total">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>
</form>
<script src="{{ asset('js/quotation.js') }}"></script>
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
                    $('#customer_name').val(response.data.customer.customer_name);
                    $('#customer_id').val(response.data.customer_id);
                    $('#contact_person').val(response.data.contact_person);
                    $('#phone').val(response.data.phone);
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
</script>
