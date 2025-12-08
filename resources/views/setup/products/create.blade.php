@include('partials.header', ['activeGroup' => 'systemFirst', 'activeName' => 'products'])
<form action="{{ route('products.store') }}" method="POST">
    @csrf
    <div class="content-wrapper m-0 min-height--none" style="background: none;">
        <div class="content-header-fixed p-0 border-bottom-0">
            <div class="content__header--inner">
                <div class="content__heading--left opacity-0">
                    <span class="ml-4">Thiết lập ban đầu</span>
                    <span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M7.69269 13.9741C7.43577 13.7171 7.43577 13.3006 7.69269 13.0437L10.7363 10.0001L7.69269 6.95651C7.43577 6.69959 7.43577 6.28303 7.69269 6.02611C7.94962 5.76918 8.36617 5.76918 8.6231 6.02611L12.1319 9.53488C12.3888 9.7918 12.3888 10.2084 12.1319 10.4653L8.6231 13.9741C8.36617 14.231 7.94962 14.231 7.69269 13.9741Z"
                                fill="#26273B" fill-opacity="0.8" />
                        </svg>
                    </span>
                    <span class="nearLast-span">Hàng hóa</span>
                    <span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M7.69269 13.9741C7.43577 13.7171 7.43577 13.3006 7.69269 13.0437L10.7363 10.0001L7.69269 6.95651C7.43577 6.69959 7.43577 6.28303 7.69269 6.02611C7.94962 5.76918 8.36617 5.76918 8.6231 6.02611L12.1319 9.53488C12.3888 9.7918 12.3888 10.2084 12.1319 10.4653L8.6231 13.9741C8.36617 14.231 7.94962 14.231 7.69269 13.9741Z"
                                fill="#26273B" fill-opacity="0.8" />
                        </svg>
                    </span>
                    <span class="last-span">Thêm hàng hóa</span>
                </div>
                <div class="d-flex content__heading--right">
                    <a href="{{ route('products.index') }}">
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
                        <p class="m-0 p-0">Lưu hàng hóa</p>
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
                                    <p class="p-0 m-0 margin-left32 text-13">Nhóm</p>
                                </div>
                                <div
                                    class="border border-white w-100 border-left-0 border-right-0 border-top-0 px-3 text-13-black bg-input-guest-blue">
                                    <select name="group_id"
                                        class="form-control text-13-black bg-input-guest-blue border-0 p-0">
                                        <option value="0" class="bg-white">Chọn loại nhóm</option>
                                        @foreach ($groups as $item)
                                            <option value="{{ $item->id }}" class="bg-white">{{ $item->group_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="d-flex align-items-center height-60-mobile">
                                <div class="title-info py-2 border border-left-0 border-top-0">
                                    <p class="p-0 m-0 required-label text-danger margin-left32 text-13-red">Mã hàng</p>
                                </div>
                                <input type="text" placeholder="Nhập thông tin" name="product_code" autocomplete="off"
                                    required
                                    class="border border-white height-100 w-100 py-2 border-left-0 border-right-0 border-top-0 px-3 text-13-black bg-input-guest-blue">
                            </div>
                            <div class="d-flex align-items-center height-60-mobile">
                                <div class="title-info py-2 border border-left-0 border-top-0 height-100">
                                    <p class="p-0 m-0 required-label text-danger margin-left32 text-13-red">Tên hàng
                                    </p>
                                </div>
                                <input type="text" required placeholder="Nhập thông tin" name="product_name"
                                    autocomplete="off"
                                    class="border border-white height-100 w-100 py-2 border-left-0 border-right-0 border-top-0 px-3 text-13-black bg-input-guest-blue"
                                    maxlength="255">
                            </div>
                            <div class="d-flex align-items-center height-60-mobile option-radio">
                                <div class="title-info py-2 border border-left-0 border-top-0 height-100">
                                    <p class="p-0 m-0 margin-left32 text-13">Hãng</p>
                                </div>
                                <input type="text" placeholder="Nhập thông tin" name="brand" autocomplete="off"
                                    class="border border-white height-100 w-100 py-2 border-left-0 border-right-0 border-top-0 px-3 text-13-black bg-input-guest-blue">
                            </div>
                            <div class="d-flex align-items-center height-60-mobile option-radio">
                                <div class="title-info py-2 border border-left-0 border-top-0 height-100">
                                    <p class="p-0 m-0 margin-left32 text-13">Bảo hành</p>
                                </div>
                                <input type="number" placeholder="Nhập thông tin" name="warranty" autocomplete="off"
                                    class="border height-100 w-100 py-2 border-left-0 border-right-0 border-top-0 px-3 text-13-black bg-input-guest-blue"
                                    value="12"   min="0" step="1" required
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0,11);">
                            </div>

                            {{-- <table class="table table-hover bg-white rounded mt-2">
                                <thead class="border-custom">
                                    <tr>
                                        <th class="height-40 py-0 border-right-0">Linh kiện bảo hành</th>
                                        <th class="height-40 py-0 border-right-0">Tháng bảo hành</th>
                                        <th class="height-40 py-0 border-right-0"></th>
                                    </tr>
                                </thead>
                                <tbody id="add-warranty">
                                    <tr class="position-relative import-info height-40">
                                        <td
                                            class="text-13-black border-right border-bottom border-top-0 border-right-0 py-0">
                                            <input type="text" name="info[]" placeholder="Nhập thông tin" required
                                                class="border border-white height-100 w-100 py-2 border-right-0 border-top-0 px-3 text-13-black bg-input-guest-blue">
                                        </td>
                                        <td
                                            class="text-13-black border-right border-bottom border-top-0 border-right-0 py-0">
                                            <input type="number" name="warranty[]" placeholder="Nhập thông tin"
                                                value="12" required
                                                class="border border-white height-100 w-100 py-2 border-right-0 border-top-0 px-3 text-13-black bg-input-guest-blue">
                                        </td>
                                        <td
                                            class="text-13-black border-right border-bottom border-top-0 border-right-0 py-0">
                                            <button type="button" class="btn btn-sm btn-remove-warranty">
                                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path opacity="0.936" fill-rule="evenodd" clip-rule="evenodd"
                                                        d="M6.40625 0.968766C7.44813 0.958304 8.48981 0.968772 9.53125 1.00016C9.5625 1.03156 9.59375 1.06296 9.625 1.09436C9.65625 1.49151 9.66663 1.88921 9.65625 2.28746C10.7189 2.277 11.7814 2.28747 12.8438 2.31886C12.875 2.35025 12.9063 2.38165 12.9375 2.41305C12.9792 2.99913 12.9792 3.58522 12.9375 4.17131C12.9063 4.24457 12.8542 4.2969 12.7813 4.32829C12.6369 4.35948 12.4911 4.36995 12.3438 4.35969C12.3542 7.45762 12.3438 10.5555 12.3125 13.6533C12.1694 14.3414 11.7632 14.7914 11.0938 15.0034C9.01044 15.0453 6.92706 15.0453 4.84375 15.0034C4.17433 14.7914 3.76808 14.3414 3.625 13.6533C3.59375 10.5555 3.58333 7.45762 3.59375 4.35969C3.3794 4.3844 3.18148 4.34254 3 4.2341C2.95833 3.62708 2.95833 3.02007 3 2.41305C3.03125 2.38165 3.0625 2.35025 3.09375 2.31886C4.15605 2.28747 5.21855 2.277 6.28125 2.28746C6.27088 1.88921 6.28125 1.49151 6.3125 1.09436C6.35731 1.06018 6.38856 1.01832 6.40625 0.968766ZM6.96875 1.65951C7.63544 1.65951 8.30206 1.65951 8.96875 1.65951C8.96875 1.86882 8.96875 2.07814 8.96875 2.28746C8.30206 2.28746 7.63544 2.28746 6.96875 2.28746C6.96875 2.07814 6.96875 1.86882 6.96875 1.65951ZM3.65625 2.9782C6.53125 2.9782 9.40625 2.9782 12.2813 2.9782C12.2813 3.18752 12.2813 3.39684 12.2813 3.60615C9.40625 3.60615 6.53125 3.60615 3.65625 3.60615C3.65625 3.39684 3.65625 3.18752 3.65625 2.9782ZM4.34375 4.35969C6.76044 4.35969 9.17706 4.35969 11.5938 4.35969C11.6241 7.5032 11.5929 10.643 11.5 13.7789C11.3553 14.05 11.1366 14.2279 10.8438 14.3127C8.92706 14.3546 7.01044 14.3546 5.09375 14.3127C4.80095 14.2279 4.5822 14.05 4.4375 13.7789C4.34462 10.643 4.31337 7.5032 4.34375 4.35969Z"
                                                        fill="#6C6F74"></path>
                                                    <path opacity="0.891" fill-rule="evenodd" clip-rule="evenodd"
                                                        d="M5.78125 5.28118C6.0306 5.2259 6.20768 5.30924 6.3125 5.53118C6.35419 8.052 6.35419 10.5729 6.3125 13.0937C6.08333 13.427 5.85417 13.427 5.625 13.0937C5.58333 10.552 5.58333 8.01037 5.625 5.46868C5.69031 5.4141 5.7424 5.3516 5.78125 5.28118Z"
                                                        fill="#6C6F74"></path>
                                                    <path opacity="0.891" fill-rule="evenodd" clip-rule="evenodd"
                                                        d="M7.78125 5.28118C8.03063 5.2259 8.20769 5.30924 8.3125 5.53118C8.35419 8.052 8.35419 10.5729 8.3125 13.0937C8.08331 13.427 7.85419 13.427 7.625 13.0937C7.58331 10.552 7.58331 8.01037 7.625 5.46868C7.69031 5.4141 7.74238 5.3516 7.78125 5.28118Z"
                                                        fill="#6C6F74"></path>
                                                    <path opacity="0.891" fill-rule="evenodd" clip-rule="evenodd"
                                                        d="M9.78125 5.28118C10.0306 5.2259 10.2077 5.30924 10.3125 5.53118C10.3542 8.052 10.3542 10.5729 10.3125 13.0937C10.0833 13.427 9.85419 13.427 9.625 13.0937C9.58331 10.552 9.58331 8.01037 9.625 5.46868C9.69031 5.4141 9.74238 5.3516 9.78125 5.28118Z"
                                                        fill="#6C6F74"></path>
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table> --}}
                            {{-- <section class="content mt-2">
                                <div class="container-fluided">
                                    <div class="d-flex ml-4">
                                        <button type="button"
                                            class="btn-add-warranty d-flex align-items-center h-100 py-1 px-2 rounded activity mb-5"
                                            style="margin-right:10px">
                                            <svg class="mr-2" xmlns="http://www.w3.org/2000/svg" width="12" height="12"
                                                viewBox="0 0 18 18" fill="none">
                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M9 0C9.58186 -2.96028e-08 10.0536 0.471694 10.0536 1.05356L10.0536 16.9464C10.0536 17.5283 9.58186 18 9 18C8.41814 18 7.94644 17.5283 7.94644 16.9464V1.05356C7.94644 0.471694 8.41814 -2.96028e-08 9 0Z"
                                                    fill="#42526E"></path>
                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M18 9C18 9.58187 17.5283 10.0536 16.9464 10.0536H1.05356C0.471694 10.0536 -2.07219e-07 9.58187 0 9C-7.69672e-07 8.41814 0.471695 7.94644 1.05356 7.94644H16.9464C17.5283 7.94644 18 8.41814 18 9Z"
                                                    fill="#42526E"></path>
                                            </svg>
                                            <span class="text-table font-weight-bold">Thêm bảo hành</span>
                                        </button>
                                    </div>
                                </div>
                            </section> --}}
                        </div>
                    </div>
                </section>
            </section>
        </div>
    </div>
</form>
</div>
<script src="{{ asset('js/app.js') }}"></script>