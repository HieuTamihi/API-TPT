@include('partials.header', ['activeGroup' => 'systemFirst', 'activeName' => 'providers'])
@section('title', $title)
<form action="{{ route('providers.store') }}" method="POST">
    @csrf
    <div class="content-wrapper m-0 min-height--none">
        <div class="content-header-fixed p-0">
            <div class="content__header--inner">
                <div class="content__heading--left opacity-0">
                    <span class="ml-4">Thiết lập ban đầu</span>
                    <span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20"
                            fill="none">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M7.69269 13.9741C7.43577 13.7171 7.43577 13.3006 7.69269 13.0437L10.7363 10.0001L7.69269 6.95651C7.43577 6.69959 7.43577 6.28303 7.69269 6.02611C7.94962 5.76918 8.36617 5.76918 8.6231 6.02611L12.1319 9.53488C12.3888 9.7918 12.3888 10.2084 12.1319 10.4653L8.6231 13.9741C8.36617 14.231 7.94962 14.231 7.69269 13.9741Z"
                                fill="#26273B" fill-opacity="0.8" />
                        </svg>
                    </span>
                    <span class="nearLast-span">
                        <a class="text-dark" href="{{ route('providers.index') }}">
                            Nhà cung cấp
                        </a>
                    </span>
                    <span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20"
                            fill="none">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M7.69269 13.9741C7.43577 13.7171 7.43577 13.3006 7.69269 13.0437L10.7363 10.0001L7.69269 6.95651C7.43577 6.69959 7.43577 6.28303 7.69269 6.02611C7.94962 5.76918 8.36617 5.76918 8.6231 6.02611L12.1319 9.53488C12.3888 9.7918 12.3888 10.2084 12.1319 10.4653L8.6231 13.9741C8.36617 14.231 7.94962 14.231 7.69269 13.9741Z"
                                fill="#26273B" fill-opacity="0.8" />
                        </svg>
                    </span>
                    <span class="last-span">Tạo nhà cung cấp</span>
                </div>
                <div class="d-flex content__heading--right">
                    <div class="row m-0">
                        <a href="{{ route('providers.index') }}">
                            <button class="btn-destroy btn-light mx-1 d-flex align-items-center h-100" type="button">
                                <svg class="mx-1" xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                    viewBox="0 0 14 14" fill="none">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M7 14C10.866 14 14 10.866 14 7C14 3.13401 10.866 0 7 0C3.13401 0 0 3.13401 0 7C0 10.866 3.13401 14 7 14ZM5.03033 3.96967C4.73744 3.67678 4.26256 3.67678 3.96967 3.96967C3.67678 4.26256 3.67678 4.73744 3.96967 5.03033L5.93934 7L3.96967 8.96967C3.67678 9.26256 3.67678 9.73744 3.96967 10.0303C4.26256 10.3232 4.73744 10.3232 5.03033 10.0303L7 8.06066L8.96967 10.0303C9.26256 10.3232 9.73744 10.3232 10.0303 10.0303C10.3232 9.73744 10.3232 9.26256 10.0303 8.96967L8.06066 7L10.0303 5.03033C10.3232 4.73744 10.3232 4.26256 10.0303 3.96967C9.73744 3.67678 9.26256 3.67678 8.96967 3.96967L7 5.93934L5.03033 3.96967Z"
                                        fill="black" />
                                </svg>
                                <p class="p-0 m-0 text-dark">Hủy</p>
                            </button>
                        </a>

                        <button type="submit" class="custom-btn d-flex align-items-center h-100"
                            style="margin-right:10px">
                            <svg class="mx-1" xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                viewBox="0 0 16 16" fill="none">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M6.75 1V6.75C6.75 7.5297 7.34489 8.17045 8.10554 8.24313L8.25 8.25H14V13C14 14.1046 13.1046 15 12 15H4C2.89543 15 2 14.1046 2 13V3C2 1.89543 2.89543 1 4 1H6.75ZM8 1L14 7.03022H9C8.44772 7.03022 8 6.5825 8 6.03022V1Z"
                                    fill="white" />
                            </svg>
                            <p class="m-0 p-0 text-btnIner-primary">Lưu nhà cung cấp</p>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="content" style="margin-top: 8.7rem;">
            <section class="content">
                <div class="container-fluided">
                    <div class="info-chung">
                        <div class="content-info">
                            <div class="d-flex align-items-center height-60-mobile">
                                <div class="title-info py-2 border border-top-0 border-left-0 height-100">
                                    <p class="p-0 m-0 margin-left32 text-13">Nhóm</p>
                                </div>
                                <div
                                    class="border border-white border-top-0 w-100 py-2 border-left-0 border-right-0 px-3 text-13-black height-100 bg-input-guest-blue">
                                    <select name="category_id" id=""
                                        class="border border-top-0 w-100 border-left-0 border-right-0 text-13-black height-100 bg-input-guest-blue">
                                        <option value="0" class="bg-white">Chọn nhóm nhà cung cấp</option>
                                        @foreach ($category as $item)
                                            <option value="{{ $item->id }}" class="bg-white">{{ $item->group_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="d-flex align-items-center height-60-mobile">
                                <div class="title-info py-2 border border-top-0 border-left-0 height-100">
                                    <p class="p-0 m-0 margin-left32 text-13-red required-label">Mã nhà cung cấp</p>
                                </div>
                                <input type="text" placeholder="Nhập thông tin" name="provider_code" required
                                    autocomplete="off"
                                    class="border border-white border-top-0 w-100 py-2 border-left-0 border-right-0 px-3 text-13-black height-100 bg-input-guest-blue">
                            </div>

                            <div class="d-flex align-items-center height-60-mobile">
                                <div class="title-info py-2 border border-left-0 height-100 border-top-0">
                                    <p class="p-0 m-0 required-label margin-left32 text-13-red">Tên nhà cung cấp</p>
                                </div>
                                <input type="text" required placeholder="Nhập thông tin" name="provider_name"
                                    autocomplete="off"
                                    class="border border-white border-top-0 w-100 py-2 border-left-0 border-right-0 px-3 text-13-black height-100 bg-input-guest-blue">
                            </div>

                            <div class="d-flex align-items-center height-60-mobile">
                                <div class="title-info py-2 border border-left-0 height-100 border-top-0">
                                    <p class="p-0 m-0 margin-left32 text-13">Địa chỉ</p>
                                </div>
                                <input type="text" placeholder="Nhập thông tin" name="address" autocomplete="off"
                                    class="border border-white border-top-0 w-100 py-2 border-left-0 border-right-0 px-3 text-13-black height-100 bg-input-guest-blue">
                            </div>
                            <div class="d-flex  align-items-center height-60-mobile">
                                <div class="title-info height-100 py-2 border border-top-0 border-left-0">
                                    <p class="p-0 m-0 margin-left32 text-13">Người liên hệ</p>
                                </div>
                                <input type="text" placeholder="Nhập thông tin" name="contact_person"
                                    autocomplete="off"
                                    class="border border-white border-top-0 w-100 py-2 border-left-0 border-right-0 px-3 text-13-black height-100 bg-input-guest-blue">
                            </div>
                            <div class="d-flex align-items-center height-60-mobile">
                                <div class="title-info py-2 border border-left-0 height-100 border-top-0">
                                    <p class="p-0 m-0 margin-left32 text-13">Điện thoại</p>
                                </div>
                                <input type="number" placeholder="Nhập thông tin" name="phone" autocomplete="off"
                                    class="border border-white w-100 py-2 border-left-0 border-right-0 px-3 text-13-black height-100 bg-input-guest-blue border-top-0" 
                                     min="0" step="1"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0,11);">
                            </div>

                            <div class="d-flex align-items-center height-60-mobile">
                                <div class="title-info py-2 border border-top-0 border-left-0 height-100">
                                    <p class="p-0 m-0 margin-left32 text-13">Email</p>
                                </div>
                                <input type="email" placeholder="Nhập thông tin" name="email" autocomplete="off"
                                    class="border border-white border-top-0 w-100 py-2 border-left-0 border-right-0 px-3 text-13-black height-100 bg-input-guest-blue">
                            </div>
                            <div class="d-flex align-items-center height-60-mobile">
                                <div class="title-info height-100 py-2 border border-top-0 border-left-0">
                                    <p class="p-0 m-0 margin-left32 text-13">Mã số thuế</p>
                                </div>
                                <input type="text" placeholder="Nhập thông tin" name="tax_code"
                                    oninput="validateInput(this)" autocomplete="off"
                                    class="border border-white border-top-0 w-100 py-2 border-left-0 border-right-0 px-3 text-13-black height-100 bg-input-guest-blue">
                            </div>
                            <div class="d-flex align-items-center height-60-mobile">
                                <div class="title-info height-100 py-2 border border-top-0 border-left-0">
                                    <p class="p-0 m-0 margin-left32 text-13">Ghi chú</p>
                                </div>
                                <input type="text" placeholder="Nhập thông tin" name="note" autocomplete="off"
                                    class="border border-white border-top-0 w-100 py-2 border-left-0 border-right-0 px-3 text-13-black height-100 bg-input-guest-blue">
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>

</form>
<script>
    function validateInput(input) {
        // Loại bỏ tất cả các ký tự ngoại trừ số và dấu "-"
        input.value = input.value.replace(/[^0-9-]/g, '');

        // Loại bỏ các dấu "-" liên tiếp
        input.value = input.value.replace(/-{2,}/g, '');
    }
</script>
