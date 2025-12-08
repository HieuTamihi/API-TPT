@include('partials.header', ['activeGroup' => 'systemFirst', 'activeName' => 'customers'])
@section('title', $title)
<div class="content editGuest min-height--none">
    <div class="content-header-fixed p-0">
        <div class="content__header--inner">
            <section class="content-header--options border-0 p-0"></section>
            <div class="d-flex content__heading--right">
                <div class="row m-0">
                    <a href="{{ route('customers.index') }}" class="activity" data-name1="KH" data-des="Trở về">
                        <button type="button" class="btn-save-print d-flex align-items-center h-100 rounded"
                            style="margin-right:10px;">
                            <svg class="mx-1" xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                viewBox="0 0 16 16" fill="none">
                                <path
                                    d="M5.6738 11.4801C5.939 11.7983 6.41191 11.8413 6.73012 11.5761C7.04833 11.311 7.09132 10.838 6.82615 10.5198L5.3513 8.75H12.25C12.6642 8.75 13 8.41421 13 8C13 7.58579 12.6642 7.25 12.25 7.25L5.3512 7.25L6.82615 5.4801C7.09132 5.1619 7.04833 4.689 6.73012 4.4238C6.41191 4.1586 5.939 4.2016 5.6738 4.5198L3.1738 7.51984C2.942 7.79798 2.942 8.20198 3.1738 8.48012L5.6738 11.4801Z"
                                    fill="#6D7075" />
                            </svg>
                            <p class="m-0 p-0 text-13-black">Trở về</p>
                        </button>
                    </a>
                    <a class="activity" data-name1="KH" data-des="Xem trang sửa"
                        href="{{ route('customers.edit', ['customer' => $customer->id]) }}">
                        <button type="button" class="custom-btn d-flex align-items-center h-100 mx-1">
                            <svg class="mx-1" xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                viewBox="0 0 16 16" fill="none">
                                <path
                                    d="M4.75 2.00007C2.67893 2.00007 1 3.679 1 5.75007V11.25C1 13.3211 2.67893 15 4.75 15H10.2501C12.3212 15 14.0001 13.3211 14.0001 11.25V8.00007C14.0001 7.58586 13.6643 7.25007 13.2501 7.25007C12.8359 7.25007 12.5001 7.58586 12.5001 8.00007V11.25C12.5001 12.4927 11.4927 13.5 10.2501 13.5H4.75C3.50736 13.5 2.5 12.4927 2.5 11.25V5.75007C2.5 4.50743 3.50736 3.50007 4.75 3.50007H7C7.41421 3.50007 7.75 3.16428 7.75 2.75007C7.75 2.33586 7.41421 2.00007 7 2.00007H4.75Z"
                                    fill="white" />
                                <path
                                    d="M12.1339 5.19461L10.7197 3.7804L6.52812 7.97196C5.77185 8.72823 5.25635 9.69144 5.0466 10.7402C5.03144 10.816 5.09828 10.8828 5.17409 10.8677C6.22285 10.6579 7.18606 10.1424 7.94233 9.38618L12.1339 5.19461Z"
                                    fill="white" />
                                <path
                                    d="M13.4559 1.45679C13.2663 1.39356 13.0571 1.44293 12.9158 1.58431L11.7803 2.71974L13.1945 4.13395L14.33 2.99852C14.4714 2.85714 14.5207 2.64802 14.4575 2.45834C14.2999 1.98547 13.9288 1.61441 13.4559 1.45679Z"
                                    fill="white" />
                            </svg>
                            <p class="p-0 m-0">Sửa</p>
                        </button>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="content editGuest" style="margin-top: 8.5rem;">
        <div class="content tab-pane in active">
            <div class="content-info">
                <div class="d-flex align-items-center height-60-mobile">
                    <div class="title-info py-2 border border-left-0 height-100">
                        <p class="p-0 m-0 margin-left32 text-14">Nhóm khách hàng</p>
                    </div>
                    <input type="text" readonly value="{{ $customer->group_name }}"
                        class="border w-100 py-2 border-left-0 border-right-0 px-3 text-13-black height-100">
                </div>
                <div class="d-flex align-items-center height-60-mobile">
                    <div class="title-info py-2 border border-left-0 height-100">
                        <p class="p-0 m-0 margin-left32 text-14">Mã khách hàng</p>
                    </div>
                    <input type="text" required readonly value="{{ $customer->customer_code }}"
                        class="border w-100 py-2 border-left-0 border-right-0 px-3 text-13-black height-100">
                </div>
                <div class="d-flex align-items-center height-60-mobile">
                    <div class="title-info py-2 border border-left-0 height-100">
                        <p class="p-0 m-0 margin-left32 text-14">Tên khách hàng</p>
                    </div>
                    <input type="text" required readonly value="{{ $customer->customer_name }}"
                        class="border w-100 py-2 border-left-0 border-right-0 px-3 text-13-black height-100">
                </div>
                <div class="d-flex align-items-center height-60-mobile">
                    <div class="title-info py-2 border border-top-0 border-left-0 height-100">
                        <p class="p-0 m-0  margin-left32 text-14">Địa chỉ</p>
                    </div>
                    <input type="text" required value="{{ $customer->address }}" readonly
                        class="border border-top-0 w-100 py-2 border-left-0 border-right-0 px-3 text-13-black height-100">
                </div>
                <div class="d-flex  align-items-center height-60-mobile">
                    <div class="title-info height-100 py-2 border border-top-0 border-left-0">
                        <p class="p-0 m-0 margin-left32 text-14">Điện thoại</p>
                    </div>
                    <input type="text" value="{{ $customer->phone }}" readonly
                        class="border border-top-0 w-100 py-2 border-left-0 border-right-0 px-3 text-13-black height-100">
                </div>
                <div class="d-flex  align-items-center height-60-mobile">
                    <div class="title-info height-100 py-2 border border-top-0 border-left-0">
                        <p class="p-0 m-0 margin-left32 text-14">Email</p>
                    </div>
                    <input type="text" value="{{ $customer->email }}" readonly
                        class="border border-top-0 w-100 py-2 border-left-0 border-right-0 px-3 text-13-black height-100">
                </div>
                <div class="d-flex align-items-center height-60-mobile">
                    <div class="title-info height-100 py-2 border border-top-0 border-left-0">
                        <p class="p-0 m-0 margin-left32 text-14">Mã số thuế</p>
                    </div>
                    <input type="text" value="{{ $customer->tax_code }}" readonly
                        class="border border-top-0 w-100 py-2 border-left-0 border-right-0 px-3 text-14 height-100">
                </div>
                <div class="d-flex align-items-center height-60-mobile">
                    <div class="title-info height-100 py-2 border border-top-0 border-left-0">
                        <p class="p-0 m-0 margin-left32 text-14">Ghi chú</p>
                    </div>
                    <input type="text" value="{{ $customer->note }}" readonly
                        class="border border-top-0 w-100 py-2 border-left-0 border-right-0 px-3 text-14 height-100">
                </div>
            </div>
        </div>
    </div>
</div>
