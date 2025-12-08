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
                        <p class="m-0 p-0">Mã phiếu: <span class="font-weight-bold">{{ $export->export_code }}</span>
                        </p>
                    </div>
                    <div style="width:250px;">
                        <img src="{{ asset('images/logo-tpt-print.png') }}" class="w-100" alt="">
                    </div>
                </div>
            </div>
            <div class="title text-center">
                <h2 class="font-weight-bold m-0">PHIẾU BẢO HÀNH</h2>
            </div>
            <p class="info text-center m-0 font-weight-bold font-italic">
                Ngày @php echo date("d"); @endphp tháng @php echo date("m"); @endphp năm @php echo date("Y"); @endphp
            </p>
            <p class="info m-0">Khách hàng:
                {{ $export->customer->customer_name ?? '' }}
            </p>
            <p class="info m-0">Địa chỉ:
                @php
                    echo $export->address == null ? '<span class="dotted-line"></span>' : $export->address;
                @endphp
            </p>
            <table class="table">
                <thead>
                    <tr>
                        <th class="border border-dark">STT</th>
                        <th class="border border-dark">Mã hàng</th>
                        <th class="border border-dark">Hãng</th>
                        <th class="border border-dark">Số lượng</th>
                        <th class="border border-dark">S/N</th>
                        <th class="border border-dark">Bảo hành</th>
                        <th class="border border-dark">Ghi chú</th>
                    </tr>
                </thead>
                <tbody>
                    @php $stt = 0; @endphp
                    @foreach ($productExports as $productId => $products)
                        @foreach ($products as $item)
                            @php $stt++; @endphp
                            <tr>
                                <td class="border border-dark">{{ $stt }}</td>
                                <td class="border border-dark">{{ $item->product->product_code }}</td>
                                <td class="border border-dark">{{ $item->product->brand }}</td>
                                <td class="border border-dark">{{ $item->quantity }}</td>
                                <td class="border border-dark">{{ $item->serialNumber ? $item->serialNumber->serial_code : '' }}</td>
                                <td class="border border-dark max-width180">
                                    @php
                                        // Kiểm tra nếu warranty là chuỗi và chuyển nó thành mảng
                                        if (is_string($item->warranty)) {
                                            $warrantyArray = json_decode($item->warranty, true); // Chuyển chuỗi JSON thành mảng
                                        } else {
                                            $warrantyArray = $item->warranty; // Nếu warranty đã là mảng, không cần chuyển
                                        }
                                    @endphp

                                    @foreach ($warrantyArray as $warranty)
                                        @php
                                            $name = $warranty[0] ?? 'Không có tên';
                                            $months = $warranty[1] ?? 'Không có thông tin tháng';
                                        @endphp
                                        {{ $name }}: {{ $months }} tháng
                                        @if (!$loop->last)
                                            ;
                                        @endif
                                    @endforeach
                                </td>
                                <td class="border border-dark">{{ $item->note }}</td>
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
            <span class="info font-italic font-weight-bold">Ghi chú:</span>
            @php
                echo $export->note == null ? '<span class="dotted-line"></span>' : $export->note;
            @endphp
            <div class="footer d-flex justify-content-between">
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
