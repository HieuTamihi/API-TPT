$(document).ready(function () {
    function toggleListGuest(input, list, filterInput) {
        input.on("click", function () {
            list.show();
        });

        $(document).click(function (event) {
            if (
                !$(event.target).closest(input).length &&
                !$(event.target).closest(filterInput).length
            ) {
                list.hide();
            }
        });

        var applyFilter = function () {
            var value = filterInput.val().toUpperCase();
            list.find("li").each(function () {
                var text = $(this).find("a").text().toUpperCase();
                $(this).toggle(text.indexOf(value) > -1);
            });
        };

        input.on("keyup", applyFilter);
        filterInput.on("keyup", applyFilter);
    }

    //nhà cung cấp
    toggleListGuest(
        $("#provider_name"),
        $("#listProvider"),
        $("#searchProvider")
    );
    //Kho chuyển
    toggleListGuest(
        $("#warehouse_name"),
        $("#listWarehouse"),
        $("#searchWarehouse")
    );
    //Kho nhận
    toggleListGuest(
        $("#warehouse_receive_name"),
        $("#listWarehouseReceive"),
        $("#searchWarehouseReceive")
    );
    //Người lập
    toggleListGuest($("#user_name"), $("#listUser"), $("#searchUser"));
    //lấy tên nhà cung cấp và id
    $('a[name="search-info"]').on("click", function () {
        const dataId = $(this).attr("id");
        const dataName = $(this).data("name");
        const phone = $(this).data("phone");
        const address = $(this).data("address");
        const contact = $(this).data("contact");
        $("#provider_id").val(dataId);
        $("#provider_name").val(dataName);
        $('[name="phone"]').val(phone);
        $('[name="contact_person"]').val(contact);
        $('[name="address"]').val(address);
    });
    //lấy người lập
    $('a[name="create-info"]').on("click", function () {
        const dataId = $(this).attr("id");
        const dataName = $(this).data("name");
        $("#user_id").val(dataId);
        $("#user_name").val(dataName);
    });
    //lấy kho chuyển
    $('a[name="warehouse-info"]').on("click", function () {
        const dataId = $(this).attr("id");
        const dataName = $(this).data("name");
        $("#warehouse_id").val(dataId);
        $("#warehouse_name").val(dataName);
        $('.delete-row').click();
    });
    //lấy kho nhận
    $('a[name="warehouse-receive-info"]').on("click", function () {
        const dataId = $(this).attr("id");
        const dataName = $(this).data("name");
        $("#warehouse_receive_id").val(dataId);
        $("#warehouse_receive_name").val(dataName);
        $('.delete-row').click();
    });
});
function formatDate(dateString) {
    const date = new Date(dateString);
    const day = String(date.getDate()).padStart(2, "0");
    const month = String(date.getMonth() + 1).padStart(2, "0");
    const year = date.getFullYear();

    return `${day}/${month}/${year}`;
}
let name_modal = $("#name_modal").val();
if (name_modal == "NH" || name_modal == "XH") {
    //format định dạng
    flatpickr("#dateCreate", {
        locale: "vn",
        dateFormat: "d/m/Y",
        defaultDate: "today",
        onChange: function (selectedDates) {
            // Lấy giá trị ngày đã chọn
            if (selectedDates.length > 0) {
                const formattedDate = flatpickr.formatDate(
                    selectedDates[0],
                    "Y-m-d"
                );
                document.getElementById("hiddenDateCreate").value =
                    formattedDate;
            }
        },
    });
} else if (name_modal == "CNH" || name_modal == "CXH") {
    //format định dạng
    flatpickr("#dateCreate", {
        locale: "vn",
        dateFormat: "d/m/Y",
        onChange: function (selectedDates) {
            // Lấy giá trị ngày đã chọn
            if (selectedDates.length > 0) {
                const formattedDate = flatpickr.formatDate(
                    selectedDates[0],
                    "Y-m-d"
                );
                document.getElementById("hiddenDateCreate").value =
                    formattedDate;
            }
        },
    });
}
