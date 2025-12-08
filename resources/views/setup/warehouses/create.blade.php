@include('partials.header', ['activeGroup' => 'systemFirst', 'activeName' => 'warehouses'])
<form action="{{ route('warehouses.store') }}" method="POST">
    @csrf
    <div class="content-wrapper m-0 min-height--none" style="background: none;">
        <div class="content-header-fixed p-0 border-bottom-0">
            <div class="content__header--inner">
                <div class="content__heading--left opacity-0">
                </div>
                <div class="d-flex content__heading--right">
                    <a href="{{ route('warehouses.index') }}">
                        <button type="button"
                            class="btn-destroy btn-light mx-1 d-flex align-items-center h-100 rounded">
                            <svg class="mx-1" xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                viewBox="0 0 14 14" fill="none">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M7 14C10.866 14 14 10.866 14 7C14 3.13401 10.866 0 7 0C3.13401 0 0 3.13401 0 7C0 10.866 3.13401 14 7 14ZM5.03033 3.96967C4.73744 3.67678 4.26256 3.67678 3.96967 3.96967C3.67678 4.26256 3.67678 4.73744 3.96967 5.03033L5.93934 7L3.96967 8.96967C3.67678 9.26256 3.67678 9.73744 3.96967 10.0303C4.26256 10.3232 4.73744 10.3232 5.03033 10.0303L7 8.06066L8.96967 10.0303C9.26256 10.3232 9.73744 10.3232 10.0303 10.0303C10.3232 9.73744 10.3232 9.26256 10.0303 8.96967L8.06066 7L10.0303 5.03033C10.3232 4.73744 10.3232 4.26256 10.0303 3.96967C9.73744 3.67678 9.26256 3.67678 8.96967 3.96967L7 5.93934L5.03033 3.96967Z"
                                    fill="black" />
                            </svg>
                            <p class="m-0 p-0 text-13-black">Hủy</p>
                        </button>
                    </a>
                    <button type="submit" class="custom-btn mx-1 d-flex align-items-center h-100 mr-1">
                        <svg class="mx-1" width="18" height="18" viewBox="0 0 16 16" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M6.75 1V6.75C6.75 7.5297 7.34489 8.17045 8.10554 8.24313L8.25 8.25H14V13C14 14.1046 13.1046 15 12 15H4C2.89543 15 2 14.1046 2 13V3C2 1.89543 2.89543 1 4 1H6.75ZM8 1L14 7.03022H9C8.44772 7.03022 8 6.5825 8 6.03022V1Z"
                                fill="white"></path>
                        </svg>
                        <p class="m-0 p-0">Lưu kho</p>
                    </button>

                </div>
            </div>
        </div>
        <div class="content margin-top-38" style="background: none;">
            <section class="content">
                <section class="container-fluided">
                    <div class="info-chung">
                        <div class="content-info">
                            <div class="d-flex align-items-center height-60-mobile">
                                <div class="title-info py-2 border border-left-0 height-100">
                                    <p class="p-0 m-0 margin-left32 text-13">Mã kho</p>
                                </div>
                                <input type="text" placeholder="Nhập thông tin" name="warehouse_code"
                                    autocomplete="off"
                                    class="border height-100 w-100 py-2 border-left-0 border-right-0 px-3 text-13-black bg-input-guest-blue">
                            </div>
                            <div class="d-flex align-items-center height-60-mobile">
                                <div class="title-info py-2 border border-left-0 border-top-0 height-100">
                                    <p class="p-0 m-0 required-label text-danger margin-left32 text-13-red">Tên kho
                                    </p>
                                </div>
                                <input type="text" required placeholder="Nhập thông tin" name="warehouse_name"
                                    autocomplete="off"
                                    class="border height-100 w-100 py-2 border-left-0 border-right-0 border-top-0 px-3 text-13-black bg-input-guest-blue"
                                    maxlength="255">
                            </div>
                            <div class="d-flex align-items-center height-60-mobile option-radio">
                                <div class="title-info py-2 border border-left-0 border-top-0 height-100">
                                    <p class="p-0 m-0 margin-left32 text-13">Địa chỉ</p>
                                </div>
                                <input type="text" placeholder="Nhập thông tin" name="address" autocomplete="off"
                                    class="border height-100 w-100 py-2 border-left-0 border-right-0 border-top-0 px-3 text-13-black bg-input-guest-blue">
                            </div>
                        </div>
                    </div>
                </section>
            </section>
        </div>
    </div>
</form>
</div>
