$(document).ready(function () {
    // Khi nhấn vào report-dropdown
    $(".report-dropdown").on("click", function (e) {
        e.preventDefault(); // Ngăn chặn reload trang

        const targetId = $(this).data("target"); // Lấy ID từ data-target
        const $target = $("#" + targetId);

        // Ẩn tất cả các block khác và chỉ toggle block mục tiêu
        $(".block-optionss").not($target).hide();
        $target.toggle();
    });

    // Khi nhấn nút "Hủy" trong block
    $(".block-optionss").on("click", ".cancel", function () {
        $(this).closest(".block-optionss").hide();
    });

    // Xử lý nhấp bên ngoài block để ẩn block
    $(document).on("click", function (e) {
        // Kiểm tra nếu nhấp không thuộc vào block hoặc report-dropdown
        if (!$(e.target).closest(".block-optionss, .report-dropdown").length) {
            $(".block-optionss").hide(); // Ẩn tất cả các block
        }
    });
});
