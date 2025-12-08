<div class="print-content">
    <div class="print-section" style="display: none;">
        <div class="container-fluid border-dark border p-5 pb-6">
            <div class="header">
                <div class="d-flex justify-content-between">
                    <div class="">
                        <h3 class="text-header-print font-weight-bold m-0">CÔNG TY TNHH TM DV THIÊN PHÁT TIẾN</h3>
                        <p class="m-0 p-0">196 Quách Đình Bảo, P.Phú Thạnh, Q.Tân Phú, TP. Hồ Chí Minh</p>
                        <p class="m-0 p-0">DT: 028 7777 8988 - MST: 0311999088</p>
                        <p class="m-0 p-0">Email: info@thienphattien.com</p>
                        <p class="m-0 p-0">Zalo: 098 346 8473</p>
                        <p class="m-0 p-0">Mã phiếu:
                            <span class="font-weight-bold">{{ $receiving->form_code_receiving }}</span>
                        </p>
                    </div>
                    <div style="width:250px;">
                        <img src="{{ asset('images/logo-tpt-print.png') }}" class="w-100" alt="">
                    </div>
                </div>
            </div>
            <div class="title text-center">
                <h2 class="font-weight-bold m-0">PHIẾU TIẾP NHẬN</h2>
            </div>
            <p class="info text-center m-0 font-weight-bold font-italic">
                Ngày @php echo date("d"); @endphp tháng @php echo date("m"); @endphp năm @php echo date("Y"); @endphp
            </p>
            <p class="info">Khách hàng:
                @php
                    echo $receiving->customer->customer_name == null
                        ? '<span class="dotted-line"></span>'
                        : $receiving->customer->customer_name;
                @endphp
            </p>
            <p class="info">Địa chỉ:
                {{ $receiving->address }}
            </p>
            <p class="info">
                <span class="mr-1">Người liên hệ:</span>
                {{ $receiving->contact_person }}
                <span class="ml-6 pl-6">
                    SĐT liên hệ: {{ $receiving->phone }}
                </span>
            </p>
            <table class="table">
                <thead>
                    <tr>
                        <th class="border border-dark">STT</th>
                        <th class="border border-dark">Mã hàng</th>
                        <th class="border border-dark">Hãng</th>
                        <th class="border border-dark">Số lượng</th>
                        <th class="border border-dark">S/N</th>
                        <th class="border border-dark">Tình trạng tiếp nhận</th>
                        <th class="border border-dark">Ghi chú</th>
                    </tr>
                </thead>
                <tbody>
                    @php $stt = 0; @endphp
                    @foreach ($receivedProducts as $productId => $products)
                        @foreach ($products as $item)
                            @php $stt++; @endphp
                            <tr>
                                <td class="border border-dark">{{ $stt }}</td>
                                <td class="border border-dark">{{ $item->product->product_code }}</td>
                                <td class="border border-dark">{{ $item->product->brand }}</td>
                                <td class="border border-dark">1</td>
                                <td class="border border-dark">{{ $item->serial->serial_code ?? '' }}</td>
                                <td class="border border-dark">{{ $item->warrantyReceived[0]->state_recei ?? '' }}</td>
                                <td class="border border-dark">{{ $item->warrantyReceived[0]->note ?? '' }}</td>
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
            <div class="d-flex justify-content-between">
                <div class="">
                    Bảo hành: <input type="checkbox" class="input-checkbox-customer"
                        {{ $receiving->form_type == 1 ? 'checked' : '' }}>
                </div>
                <div class="">
                    Dịch vụ: <input type="checkbox" class="input-checkbox-customer"
                        {{ $receiving->form_type == 2 ? 'checked' : '' }}>
                </div>
                <div class="">
                    Bảo hành dịch vụ: <input type="checkbox" class="input-checkbox-customer"
                        {{ $receiving->form_type == 3 ? 'checked' : '' }}>
                </div>
            </div>
            <p class="info font-italic font-weight-bold pt-2">
                Ghi chú: Khi nhận UPS gửi bảo hành, sửa chữa, quý khách cần đem theo phiếu tiếp nhận này. Các trường hợp
                làm
                mất, làm rách phiếu sẽ không được giải quyết. Xin cảm ơn.
            </p>
            <div class="footer d-flex justify-content-between" style="padding-left: 6rem; padding-right: 6rem;">
                <div class="sign text-center">
                    <p class="font-weight-bold m-0">Khách hàng</p>
                    <span class="d-block">(Ký và ghi rõ họ tên)</span>
                </div>
                <div class="sign text-center">
                    <p class="font-weight-bold m-0">Người lập phiếu</p>
                    <span class="d-block">(Ký và ghi rõ họ tên)</span>
                </div>
            </div>
            <div class="pb-5"></div>
        </div>
    </div>
</div>
<script>
    document.getElementById("printButton").addEventListener("click", function() {
        // Kích hoạt in
        window.print();
    });
</script>
