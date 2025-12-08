@include('partials.header', ['activeGroup' => 'manageProfess', 'activeName' => 'warrantyLookup'])
@section('title', $title)
<div class="content-wrapper--2Column m-0 min-height--none pr-2">
    <div class="content-header-fixed-report-1 pt-2">
        <div class="content__header--inner">
            <div class="content__heading--left opacity-0"></div>
            <div class="d-flex content__heading--right">
                <div class="row m-0">
                    <div class="dropdown mx-1">
                        <a href="{{ route('warrantyLookup.index') }}">
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
                        THÔNG TIN HÀNG BẢO HÀNH
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
                                    <th class="px-2 p-0 text-left" style="width: 8%;">
                                        <span class="text-table text-13-black font-weight-bold">Hãng</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-13-black border-right border-bottom-0 border-top-0">
                                        {{ $warrantyLookup->product->product_code }}
                                    </td>
                                    <td class="text-13-black border-right border-bottom-0 border-top-0">
                                        {{ $warrantyLookup->product->product_name }}
                                    </td>
                                    <td class="text-13-black border-bottom-0 border-top-0">
                                        {{ $warrantyLookup->product->brand }}
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
            <div id="title--fixed" class="bg-filter-search text-center border border-bottom-0 border-top-0">
                <p class="font-weight-bold text-uppercase info-chung--heading text-center">LỊCH SỬ BẢO HÀNH
                </p>
            </div>
            <div class="container-fluided">
                <section class="content">
                    <table class="table" id="inputcontent">
                        <thead>
                            <tr style="height:44px;">
                                <th class="border-right px-2 p-0">
                                    <span class="text-table text-13-black font-weight-bold">Phiếu tiếp
                                        nhận</span>
                                </th>
                                <th class="border-right px-2 p-0 text-left">
                                    <span class="text-table text-13-black font-weight-bold">Ngày lập phiếu</span>
                                </th>
                                <th class="border-right px-2 p-0 text-left">
                                    <span class="text-table text-13-black font-weight-bold">Phiếu trả hàng</span>
                                </th>
                                <th class="border-right px-2 p-0">
                                    <span class="text-table text-13-black font-weight-bold">Ngày trả hàng</span>
                                </th>
                                <th class="border-right px-2 p-0">
                                    <span class="text-table text-13-black font-weight-bold">Loại phiếu</span>
                                </th>
                                <th class="border-right px-2 p-0">
                                    <span class="text-table text-13-black font-weight-bold">Serial Number</span>
                                </th>
                                <th class="border-right px-2 p-0">
                                    <span class="text-table text-13-black font-weight-bold">Mã hàng đổi</span>
                                </th>
                                <th class="border-right px-2 p-0">
                                    <span class="text-table text-13-black font-weight-bold">Serial Number đổi</span>
                                </th>
                                <th class="border-right px-2 p-0">
                                    <span class="text-table text-13-black font-weight-bold">Thông tin</span>
                                </th>
                                <th class="border-right px-2 p-0">
                                    <span class="text-table text-13-black font-weight-bold">Bảo hành dịch vụ
                                        (tháng)</span>
                                </th>
                                <th class="border-right px-2 p-0">
                                    <span class="text-table text-13-black font-weight-bold">Ghi chú</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $groupedHistories = $warrantyHistory->groupBy(function ($item) {
                                    return $item->return_id .
                                        '-' .
                                        $item->warrantyLookup->sn_id .
                                        '-' .
                                        $item->productReturn->replacement_code;
                                });
                            @endphp
                            @foreach ($groupedHistories as $group)
                                @foreach ($group as $index => $item)
                                    <tr class="position-relative inven-lookup-info height-40">
                                        <input type="hidden" name="id-inven-lookup" class="id-inven-lookup"
                                            id="id-inven-lookup" value="{{ $item->id }}">
                                        @if ($index === 0)
                                            <td class="text-13-black border-right border-bottom border-top-0 py-0">
                                                <a href="{{ route('receivings.edit', $item->receiving->id) }}">
                                                    {{ $item->receiving->form_code_receiving }}
                                                </a>
                                            </td>
                                            <td
                                                class="text-13-black border border-left-0 border-bottom border-top-0 py-0">
                                                {{ date_format(new DateTime($item->receiving->date_created), 'd/m/Y') }}
                                            </td>
                                            <td
                                                class="text-13-black border border-left-0 border-bottom border-top-0 py-0">
                                                <a href="{{ route('returnforms.edit', $item->returnForm->id) }}">
                                                    {{ $item->returnForm->return_code }}
                                                </a>
                                            </td>
                                            <td
                                                class="text-13-black border border-left-0 border-bottom border-top-0 py-0">
                                                {{ date_format(new DateTime($item->returnForm->date_created), 'd/m/Y') }}
                                            </td>
                                            <td
                                                class="text-13-black border border-left-0 border-bottom border-top-0 py-0">
                                                @if ($item->receiving->form_type == 1)
                                                    Bảo hành
                                                @elseif($item->receiving->form_type == 2)
                                                    Dịch vụ
                                                @elseif($item->receiving->form_type == 3)
                                                    Bảo hành dịch vụ
                                                @endif
                                            </td>
                                            <td
                                                class="text-13-black border border-left-0 border-bottom border-top-0 py-0">
                                                {{ $item->productReturn->serialNumber->serial_code ?? '' }}
                                            </td>
                                            <td
                                                class="text-13-black border border-left-0 border-bottom border-top-0 py-0">
                                                {{ $item->productReturn->product_replace->product_code ?? '' }}
                                            </td>
                                        @else
                                            {{-- Dòng sau: để trống các cột trùng --}}
                                            <td class="border-right border-bottom border-top-0 py-0"></td>
                                            <td class="border border-left-0 border-bottom border-top-0 py-0"></td>
                                            <td class="border border-left-0 border-bottom border-top-0 py-0"></td>
                                            <td class="border border-left-0 border-bottom border-top-0 py-0"></td>
                                            <td class="border border-left-0 border-bottom border-top-0 py-0"></td>
                                            <td class="border border-left-0 border-bottom border-top-0 py-0"></td>
                                            <td class="border border-left-0 border-bottom border-top-0 py-0"></td>
                                        @endif
                                        <td class="text-13-black border border-left-0 border-bottom border-top-0 py-0">
                                            {{ $item->productReturn->replacementSerialNumber->serial_code ?? '' }}
                                        </td>
                                        <td class="text-13-black border border-left-0 border-bottom border-top-0 py-0">
                                            {{ $item->productReturn->warranties->name_warranty ?? null }}
                                        </td>
                                        <td class="text-13-black border border-left-0 border-bottom border-top-0 py-0">
                                            {{ $item->productReturn->extra_warranty ?? '' }}
                                        </td>
                                        <td class="text-13-black border border-left-0 border-bottom border-top-0 py-0">
                                            {{ $item->productReturn->notes ?? '' }}
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </section>
            </div>
        </div>
    </div>
</div>
