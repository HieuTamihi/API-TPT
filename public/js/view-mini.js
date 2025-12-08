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

    toggleListGuest(
        $("#inputProvider"),
        $("#listGuestMiniView"),
        $("#searchProviderMiniView")
    );
    toggleListGuest(
        $("#inputUser"),
        $("#listUserMiniView"),
        $("#searchUserMiniView")
    );
    //lấy tên nhà cung cấp và id
    $('a[name="search-info"]').on("click", function () {
        const dataName = $(this).data("name");
        const data_id = $(this).data("id");
        $("#inputProvider").val(dataName);
        $(".idProviderMiniView").val(data_id);
    });
    $(".search-user").on("click", function () {
        const dataName = $(this).data("name");
        const data_id = $(this).data("id");
        $("#inputUser").val(dataName);
        $(".idUserMiniView").val(data_id);
    });
});
