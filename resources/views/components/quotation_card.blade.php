<div class="print-content">
    <div class="print-section" style="display: none;">
        <div class="container-fluid border-dark border p-5 pb-6">
            <div class="header">
                <div class="d-flex justify-content-between">
                    <div class="">
                        <h4>CÔNG TY TNHH TM DV THIÊN PHÁT TIẾN</h4>
                        <p class="m-0 p-0">Địa chỉ: 196 Quách Đình Bảo, Phường Phú Thạnh, Quận Tân Phú, TP HCM</p>
                        <p class="m-0 p-0">Điện thoại: 0867551488</p>
                        <p class="m-0 p-0">Website: www.thienphattien.com</p>
                        <p class="m-0 p-0">Email: Info@thienphattien.com</p>
                    </div>
                    <div style="width:250px;">
                        <img src="{{ asset('images/logo-tpt-print.png') }}" class="w-100" alt="">
                    </div>
                </div>
            </div>
            <div class="title text-center">
                <h2 class="font-weight-bold m-0">PHIẾU BÁO GIÁ</h2>
            </div>
            <table class="info-table">
                <tr>
                    <th>Kính gửi</th>
                    <td>{{ $quotation->customer->customer_name }}</td>
                    <th>Người gửi</th>
                    <td>{{ Auth::user()->name }}</td>
                </tr>
                <tr>
                    <th>Người nhận</th>
                    <td>{{ $quotation->contact_person }}</td>
                    <th>Email</th>
                    <td>{{ Auth::user()->email }}</td>
                </tr>
                <tr>
                    <th>Điện thoại</th>
                    <td>{{ $quotation->phone }}</td>
                    <th>Điện thoại</th>
                    <td>{{ Auth::user()->phone }}</td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td>{{ $quotation->customer->email }}</td>
                    <th>Ngày gửi</th>
                    <td>{{ date('d/m/Y') }}</td>
                </tr>
                <tr>
                    <th>Địa chỉ</th>
                    <td>{{ $quotation->address }}</td>
                    <th>Mã phiếu</th>
                    <td>{{ $quotation->quotation_code }}</td>
                </tr>
                <tr>
                    <td colspan="4">Cảm ơn Quý khách đã liên hệ với Cty TNHH Thiên Phát Tiến, công ty chúng tôi xin
                        trân
                        trọng gửi đến quý khách hàng bảng chào giá thiết bị như sau:</td>
                </tr>
            </table>
            <table class="table mt-3">
                <thead>
                    <tr>
                        <th class="border border-dark py-0 bg-quotation text-center">STT</th>
                        <th class="border border-dark py-0 bg-quotation">Thông tin hàng hóa/dịch vụ</th>
                        <th class="border border-dark py-0 bg-quotation text-center">ĐVT</th>
                        <th class="border border-dark py-0 bg-quotation text-center">Hãng</th>
                        <th class="border border-dark py-0 bg-quotation text-center">Số lượng</th>
                        <th class="border border-dark py-0 bg-quotation text-center">Thuế</th>
                        <th class="border border-dark py-0 bg-quotation text-center">Đơn giá</th>
                        <th class="border border-dark py-0 bg-quotation text-center">Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    @php $stt = 0; @endphp
                    @foreach ($quotationServices as $id => $item)
                        @php $stt++; @endphp
                        <tr>
                            <td class="border border-dark text-center">{{ $stt }}</td>
                            <td class="border border-dark">{{ $item->service_name }}</td>
                            <td class="border border-dark text-center">{{ $item->unit }}</td>
                            <td class="border border-dark text-center">{{ $item->brand }}</td>
                            <td class="border border-dark text-center">{{ $item->quantity }}</td>
                            <td class="border border-dark text-center">{{ number_format($item->tax_rate) }} %</td>
                            <td class="border border-dark text-right">
                                {{ number_format($item->unit_price, 0, '', '.') }}
                            </td>
                            <td class="border border-dark text-right">
                                {{ number_format($item->quantity * $item->unit_price, 0, '', '.') }}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <td colspan="7" class="border border-dark text-right font-weight-bold text-16 py-2">Tổng</td>
                        <td class="border border-dark text-right" id="print-sum"></td>
                    </tr>
                    <tr>
                        <td colspan="7" class="border border-dark text-right font-weight-bold text-16 py-2">VAT 8%
                        </td>
                        <td class="border border-dark text-right" id="print-vat-8"></td>
                    </tr>
                    <tr>
                        <td colspan="7" class="border border-dark text-right font-weight-bold text-16 py-2">VAT 10%
                        </td>
                        <td class="border border-dark text-right" id="print-vat-10"></td>
                    </tr>
                    <tr>
                        <td colspan="7"
                            class="border border-dark text-right font-weight-bold text-16 py-2 text-header-print">TỔNG
                            BAO
                            GỒM VAT</td>
                        <td class="border border-dark text-right" id="sum-vat"></td>
                    </tr>
                </tbody>
            </table>
            <div class="item">
                <h5 class="text-uppercase font-weight-bold m-0"><u>Điều khoản thương mại:</u></h5>
                @if (!empty($terms) && !empty($terms->content))
                    <div class="terms-content text-header-print">
                        {!! nl2br(e($terms->content)) !!}
                    </div>
                @else
                    <div class="terms-content text-header-print">
                        <p class="m-0 text-header-print">*Giá trên bao gồm phí VAT</p>
                        <p class="m-0 text-header-print">*Thời gian bảo hành bo mạch: 03 tháng</p>
                        <p class="m-0 text-header-print">*Thời gian bảo hành ắc quy: 12 tháng</p>
                        <p class="m-0 text-header-print">*Thanh toán: Thanh toán bằng chuyển khoản 100% sau khi xác nhận
                            đơn
                            hàng</p>
                    </div>
                @endif
                <p class="m-0">*Thông tin chuyển khoản:</p>
                <h5 class="text-uppercase font-weight-bold">CÔNG TY TNHH TM DV THIÊN PHÁT TIẾN</h5>
                <p class="m-0">- Số tài khoản: 147703659 mở tại Ngân Hàng ACB - Phòng Giao Dịch Nguyễn Sơn</p>
                <p class="m-0">- Số tài khoản: 0421000465858 mở tại Ngân Hàng VCB, Chi Nhánh Phú Thọ, Tp HCM</p>
                <p class="m-0">* Để biết thêm chi tiết, xin Quý Khách vui lòng liên hệ với:
                    <span class="text-header-print">{{ Auth::user()->name }} - {{ Auth::user()->phone }}</span>
                </p>
                <p class="m-0">* Lưu ý : Hiệu lực báo giá trong vòng 15 ngày</p>
                <p class="m-0">Rất mong nhận được sự ủng hộ và hợp tác của Quý khách</p>
            </div>
            <div class="footer d-flex justify-content-between px-5 mt-2">
                <div class="sign text-center">
                    <p class="font-weight-bold m-0">Đại diện kinh doanh</p>
                    <span class="d-block">(Ký và ghi rõ họ tên)</span>
                </div>
                <div class="sign text-center">
                    <p class="font-weight-bold m-0">Xác nhận báo giá</p>
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
