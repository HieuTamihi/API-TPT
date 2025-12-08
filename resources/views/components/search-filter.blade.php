<div class="d-flex align-items-center ml-3">
    <form action="" method="get" id="search-filter" class="p-0 m-0">
        <div class="position-relative ml-1">
            <input type="text" placeholder="Tìm kiếm" id="search" name="keywords" style="outline: none;"
                class="pr-4 w-100 input-search text-13" value="{{ request()->keywords }}" />
            <span id="search-icon" class="search-icon">
                <i class="fas fa-search btn-submit"></i>
            </span>
            <input class="btn-submit" type="submit" id="hidden-submit" name="hidden-submit" style="display: none;" />
        </div>
    </form>
    <div class="dropdown mx-2 filter-all">
        <button class="btn-filter_search" data-toggle="dropdown">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                <path
                    d="M12.9548 3H10.0457C9.74445 3 9.50024 3.24421 9.50024 3.54545V6.45455C9.50024 6.75579 9.74445 7 10.0457 7H12.9548C13.256 7 13.5002 6.75579 13.5002 6.45455V3.54545C13.5002 3.24421 13.256 3 12.9548 3Z"
                    fill="#6D7075"></path>
                <path
                    d="M6.45455 3H3.54545C3.24421 3 3 3.24421 3 3.54545V6.45455C3 6.75579 3.24421 7 3.54545 7H6.45455C6.75579 7 7 6.75579 7 6.45455V3.54545C7 3.24421 6.75579 3 6.45455 3Z"
                    fill="#6D7075"></path>
                <path
                    d="M6.45455 9.50024H3.54545C3.24421 9.50024 3 9.74445 3 10.0457V12.9548C3 13.256 3.24421 13.5002 3.54545 13.5002H6.45455C6.75579 13.5002 7 13.256 7 12.9548V10.0457C7 9.74445 6.75579 9.50024 6.45455 9.50024Z"
                    fill="#6D7075"></path>
                <path
                    d="M12.9548 9.50024H10.0457C9.74445 9.50024 9.50024 9.74445 9.50024 10.0457V12.9548C9.50024 13.256 9.74445 13.5002 10.0457 13.5002H12.9548C13.256 13.5002 13.5002 13.256 13.5002 12.9548V10.0457C13.5002 9.74445 13.256 9.50024 12.9548 9.50024Z"
                    fill="#6D7075"></path>
            </svg>
            <span class="text-btnIner">Bộ lọc</span>
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd"
                    d="M5.42342 6.92342C5.65466 6.69219 6.02956 6.69219 6.26079 6.92342L9 9.66264L11.7392 6.92342C11.9704 6.69219 12.3453 6.69219 12.5766 6.92342C12.8078 7.15466 12.8078 7.52956 12.5766 7.76079L9.41868 10.9187C9.18745 11.1499 8.81255 11.1499 8.58132 10.9187L5.42342 7.76079C5.19219 7.52956 5.19219 7.15466 5.42342 6.92342Z"
                    fill="#6B6F76" />
            </svg>
        </button>
        <div class="dropdown-menu" id="dropdown-menu" aria-labelledby="dropdownMenuButton" style="z-index:">
            <div class="search-container px-2">
                <input type="text" placeholder="Tìm kiếm" id="myInput" class="text-13" onkeyup="filterFunction()"
                    style="outline: none;">
                <span class="search-icon mr-2">
                    <i class="fas fa-search"></i>
                </span>
            </div>
            <div class="scrollbar" id="scrollbar">
                @foreach ($filters as $filter)
                    <button class="dropdown-item btndropdown border-bottom btn-filter"
                        id="btn-{{ \Illuminate\Support\Str::slug($filter) }}"
                        data-button="{{ \Illuminate\Support\Str::slug($filter) }}" type="button">
                        {{ $filter }}
                    </button>
                @endforeach
            </div>
            @if (!empty($filtersTime))
                <li class="dropdown-submenu">
                    <div class="dropdown-item btn-filter border-bottom test" type="button">
                        Thời gian
                    </div>
                    <ul class="dropdown-menu">
                        @foreach ($filtersTime as $filter)
                            <button class="dropdown-item btndropdown border-bottom btn-filter"
                                id="btn-{{ \Illuminate\Support\Str::slug($filter) }}"
                                data-button="{{ \Illuminate\Support\Str::slug($filter) }}" type="button">
                                {{ $filter }}
                            </button>
                        @endforeach
                    </ul>
                </li>
            @endif
        </div>
        {{ $slot }}
        @if (!empty($filtersTime))
            {{ $slot1 }}
        @endif
    </div>
</div>
<script>
    $(document).ready(function() {
        $('.dropdown-submenu .test').on("click", function(e) {
            $(this).next('ul').toggle();
            e.stopPropagation();
            e.preventDefault();
        });
    });
</script>
