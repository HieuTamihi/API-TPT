function populateTableRows(products, tableSelector, dataProduct, type) {
    // Xóa tất cả các dòng hiện có trước khi thêm dữ liệu mới
    $(tableSelector).find(".row-product").remove();
    console.log(type);

    // Xác định các class ẩn dựa trên 'type'
    const hideReplacement = type === 2 || type === 3 ? "d-none" : "";
    const hideExtraWarranty = type === 1 || type === 3 ? "d-none" : "";
    const hideAll = type === 3 ? "d-none" : "";
    if (type === 2) {
        // Lặp qua danh sách sản phẩm
        products.forEach((product, index) => {
            // Kiểm tra nếu sản phẩm có bảo hành
            let hasWarranty =
                product.warranty_received &&
                product.warranty_received.length > 0;

            product.warranty_received.forEach((warranty, wIndex) => {
                let isFirstRow = wIndex === 0; // Kiểm tra nếu là dòng đầu tiên của sản phẩm

                let row = `
                <tr class="row-product bg-white">
                    ${
                        isFirstRow
                            ? `
                    <td class="border-right p-2 text-13 align-top border-bottom border-top-0 pl-4" rowspan="${
                        product.warranty_received.length
                    }">
                        <input type="hidden" name="return[${index}][product_id]" value="${
                                  product.product_id || ""
                              }">
                        <input type="text" autocomplete="off" class="border-0 pl-1 pr-2 py-1 w-100 product_code height-32" readonly 
                        name="return[${index}][product_code]" value="${
                                  product.product?.product_code || ""
                              }">
                    </td>
                    <td class="border-right p-2 text-13 align-top border-bottom border-top-0" rowspan="${
                        product.warranty_received.length
                    }">
                        <input type="text" autocomplete="off" class="border-0 pl-1 pr-2 py-1 w-100 product_name height-32" readonly 
                        name="return[${index}][product_name]" value="${
                                  product.product?.product_name || ""
                              }">
                    </td>
                    <td class="border-right p-2 text-13 align-top border-bottom border-top-0" rowspan="${
                        product.warranty_received.length
                    }">
                        <input type="text" autocomplete="off" class="border-0 pl-1 pr-2 py-1 w-100 brand height-32" readonly 
                        name="return[${index}][brand]" value="${
                                  product.product?.brand || ""
                              }">
                    </td>
                    <td class="border-right p-2 text-13 align-top border-bottom border-top-0" rowspan="${
                        product.warranty_received.length
                    }">
                        <input type="text" autocomplete="off" class="border-0 pl-1 pr-2 py-1 w-100 quantity height-32" readonly 
                        name="return[${index}][quantity]" value="${
                                  product.quantity || ""
                              }">
                    </td>
                    <td class="border-right p-2 text-13 align-top border-bottom border-top-0" rowspan="${
                        product.warranty_received.length
                    }">
                        <input type="hidden" name="return[${index}][serial_id]" value="${
                                  product.serial?.id || ""
                              }">
                        <input type="text" autocomplete="off" class="border-0 pl-1 pr-2 py-1 w-100 serial_code height-32" readonly 
                        name="return[${index}][serial_code]" value="${
                                  product.serial?.serial_code || ""
                              }">
                    </td>
                    `
                            : ""
                    }

                    <td class="border-right p-2 text-13 align-top border-bottom border-top-0 ${hideExtraWarranty}">
                        <input type="text" autocomplete="off" class="border-0 pl-1 pr-2 py-1 w-100 extra_warranty height-32" readonly 
                        name="return[${index}][warranty][${wIndex}][name_warranty]" value="${
                    warranty.name_warranty || ""
                }">
                    </td>

                    <td class="border-right p-2 text-13 align-top border-bottom border-top-0 ${hideExtraWarranty}">
                        <input type="number" min="0" max="100" autocomplete="off" class="border-0 pl-1 pr-2 py-1 w-100 extra_warranty height-32 bg-input-guest-blue" 
                        name="return[${index}][warranty][${wIndex}][extra_warranty]">
                    </td>

                    <td class="border-right p-2 text-13 align-top border-bottom border-top-0">
                        <input type="text" autocomplete="off" class="border-0 pl-1 pr-2 py-1 w-100 note height-32 bg-input-guest-blue" 
                        name="return[${index}][warranty][${wIndex}][note]">
                    </td>
                </tr>
            `;

                // Thêm dòng vào bảng
                $(tableSelector).append(row);
            });

            // Nếu không có bảo hành, vẫn cần tạo 1 dòng cho sản phẩm
            if (!hasWarranty) {
                let row = `
                <tr class="row-product bg-white">
                    <td class="border-right p-2 text-13 align-top border-bottom border-top-0 pl-4">
                        <input type="hidden" name="return[${index}][product_id]" value="${
                    product.product_id || ""
                }">
                        <input type="text" autocomplete="off" class="border-0 pl-1 pr-2 py-1 w-100 product_code height-32" readonly 
                        name="return[${index}][product_code]" value="${
                    product.product?.product_code || ""
                }">
                    </td>
                    <td class="border-right p-2 text-13 align-top border-bottom border-top-0">
                        <input type="text" autocomplete="off" class="border-0 pl-1 pr-2 py-1 w-100 product_name height-32" readonly 
                        name="return[${index}][product_name]" value="${
                    product.product?.product_name || ""
                }">
                    </td>
                    <td class="border-right p-2 text-13 align-top border-bottom border-top-0">
                        <input type="text" autocomplete="off" class="border-0 pl-1 pr-2 py-1 w-100 brand height-32" readonly 
                        name="return[${index}][brand]" value="${
                    product.product?.brand || ""
                }">
                    </td>
                    <td class="border-right p-2 text-13 align-top border-bottom border-top-0">
                        <input type="text" autocomplete="off" class="border-0 pl-1 pr-2 py-1 w-100 quantity height-32" readonly 
                        name="return[${index}][quantity]" value="${
                    product.quantity || ""
                }">
                    </td>
                    <td class="border-right p-2 text-13 align-top border-bottom border-top-0">
                        <input type="text" autocomplete="off" class="border-0 pl-1 pr-2 py-1 w-100 serial_code height-32" readonly 
                        name="return[${index}][serial_code]" value="${
                    product.serial?.serial_code || ""
                }">
                    </td>
                    <td class="border-right p-2 text-13 align-top border-bottom border-top-0">
                        <input type="text" autocomplete="off" class="border-0 pl-1 pr-2 py-1 w-100 note height-32 bg-input-guest-blue" 
                        name="return[${index}][note]">
                    </td>
                </tr>
            `;
                $(tableSelector).append(row);
            }
        });
    } else {
        products.forEach((product, index) => {
            // Construct the HTML row
            let row = `
            <tr class="row-product bg-white">
                <td class="border-right p-2 text-13 align-top border-bottom border-top-0 pl-4">
                    <input type="hidden" name="return[${index}][product_id]" value="${
                product.product_id || ""
            }">
                    <input type="text" autocomplete="off" class="border-0 pl-1 pr-2 py-1 w-100 product_code height-32" readonly name="return[${index}][product_code]" value="${
                product.product?.product_code || ""
            }">
                </td>
                <td class="border-right p-2 text-13 align-top border-bottom border-top-0">
                    <input type="text" autocomplete="off" class="border-0 pl-1 pr-2 py-1 w-100 product_name height-32" readonly name="return[${index}][product_name]" value="${
                product.product?.product_name || ""
            }">
                </td>
                <td class="border-right p-2 text-13 align-top border-bottom border-top-0">
                    <input type="text" autocomplete="off" class="border-0 pl-1 pr-2 py-1 w-100 brand height-32" readonly name="return[${index}][brand]" value="${
                product.product?.brand || ""
            }">
                </td>
                <td class="border-right p-2 text-13 align-top border-bottom border-top-0">
                    <input type="text" autocomplete="off" class="border-0 pl-1 pr-2 py-1 w-100 quantity height-32" readonly name="return[${index}][quantity]" value="${
                product.quantity || ""
            }">
                </td>
                <td class="border-right p-2 text-13 align-top border-bottom border-top-0">
                 <input type="hidden" autocomplete="off"class="border-0 pl-1 pr-2 py-1 w-100 serial_id height-32" readonly name="return[${index}][serial_id]" value="${
                product.serial?.id || ""
            }">
                    <input type="text" autocomplete="off" class="border-0 pl-1 pr-2 py-1 w-100 serial_code height-32" readonly name="return[${index}][serial_code]" value="${
                product.serial?.serial_code || ""
            }">
                </td>
                <td class="border-right p-2 text-13 align-top border-bottom border-top-0 ${hideReplacement}">
                    <input type="hidden" min="0" autocomplete="off" class="border-0 pl-1 pr-2 py-1 w-100 replacement_code height-32 bg-input-guest-blue" id="replacement_code_${index}" name="return[${index}][replacement_code]" value="">
                    <div class="search-container">
                        <input type="text" class="search-input border-0 pl-1 pr-2 py-1 w-100 serial_code height-32 bg-input-guest-blue" placeholder="Chọn mã hàng" />
                        <ul class="search-list border rounded">
                            ${dataProduct
                                .map(
                                    (item) => `
                                <li class="search-item p-2 border-bottom" data-id="${index}" data-replace_id="${item.id}" data-code="${item.product_code}">
                                    ${item.product_code}
                                </li>
                            `
                                )
                                .join("")}
                        </ul>
                    </div>
                </td>
                <td class="border-right p-2 text-13 align-top border-bottom border-top-0 position-relative ${hideReplacement}">
                    <div class="replacement-list">
                        <div class="replacement-item d-flex align-items-center mb-1 position-relative">
                            <input type="text" min="0" autocomplete="off" class="border-0 pl-1 pr-2 py-1 w-100 replacement_serial_number_id height-32 bg-input-guest-blue" name="return[${index}][replacement_serial_number_id][]">
                            <span class="check-icon right-45"></span>
                            <button type="button" class="btn btn-sm btn-outline-danger remove-replacement ml-2">×</button>
                        </div>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-primary add-replacement">+ Thêm serial</button>
                </td>
                <td class="border-right p-2 text-13 align-top border-bottom border-top-0 ${hideExtraWarranty}">
                    <input type="number" min="0" max="100" autocomplete="off" class="border-0 pl-1 pr-2 py-1 w-100 extra_warranty height-32 bg-input-guest-blue" name="return[${index}][extra_warranty]">
                </td>
            
                <td class="border-right p-2 text-13 align-top border-bottom border-top-0">
                    <input type="text" autocomplete="off" class="border-0 pl-1 pr-2 py-1 w-100 note height-32 bg-input-guest-blue" name="return[${index}][note]">
                </td>
            </tr>
           products.forEach((product.warranty_received, index) => {
        `;
            // Append the row to the table
            $(tableSelector).append(row);
        });
    }
}

$(document).ready(function () {
    // Hiển thị danh sách khi focus vào input
    $(document).on("focus", ".search-input", function () {
        const $input = $(this);
        const $searchList = $input.next(".search-list");

        // Hiển thị danh sách nếu có mục
        if ($searchList.find(".search-item").length > 0) {
            $searchList.addClass("active"); // Hiển thị danh sách
        }
    });

    $(document).on("blur", ".search-input", function () {
        const $searchList = $(this).next(".search-list");
        // Đợi 100ms để không ảnh hưởng click
        setTimeout(() => {
            $searchList.removeClass("active");
        }, 100);
    });

    // Lọc danh sách khi nhập vào input
    $(document).on("input", ".search-input", function () {
        const filter = $(this).val().toLowerCase(); // Chuyển giá trị nhập vào thành chữ thường
        const $searchList = $(this).next(".search-list");

        // Hiển thị tất cả mục nếu input rỗng
        if (filter === "") {
            $searchList.find(".search-item").show(); // Hiển thị lại toàn bộ mục
        } else {
            // Lọc các item trong danh sách
            $searchList.find(".search-item").each(function () {
                const text = $(this).text().toLowerCase(); // Chuyển text của item thành chữ thường
                if (text.includes(filter)) {
                    $(this).show(); // Hiển thị item nếu khớp
                } else {
                    $(this).hide(); // Ẩn item nếu không khớp
                }
            });
        }

        // Hiển thị hoặc ẩn danh sách dựa trên kết quả lọc
        if ($searchList.find(".search-item:visible").length > 0) {
            $searchList.addClass("active");
        } else {
            // $searchList.removeClass("active");
        }
    });

    // Bắt sự kiện thêm mới
    $(document).on("click", ".add-replacement", function () {
        const $td = $(this).closest("td");
        const $template = $td.find(".replacement-item").first().clone();

        // Reset lại input và icon
        $template.find("input").val("");
        $template
            .find(".check-icon")
            .text("")
            .css("color", "")
            .attr("title", ""); // XÓA title nếu có

        // Append vào danh sách
        $td.find(".replacement-list").append($template);

        // Cập nhật lại trạng thái nút xoá
        updateRemoveButtons($td.find(".replacement-list"));
    });

    // Bắt sự kiện xóa
    $(document).on("click", ".remove-replacement", function () {
        const $item = $(this).closest(".replacement-item");
        // Nếu chỉ còn 1 thì không xóa (bạn muốn luôn có ít nhất 1 ô)
        if ($item.siblings(".replacement-item").length > 0) {
            $item.remove();
        } else {
            // Nếu muốn clear luôn ô cuối cùng
            $item.find("input").val("");
            $item.find(".check-icon").text("").css("color", "");
        }
    });
});
// $(document).ready(function () {
//     $("#btn-get-unique-products").click(function (e) {
//         // Giả lập click ra ngoài trước khi thực hiện hành động chính
//         let isValid = true;
//         $("#tbody-data .check-icon").each(function () {
//             if ($(this).text().trim() === "✖") {
//                 isValid = false;
//                 return false; // Dừng vòng lặp
//             }
//         });
//         if (!isValid) {
//             e.preventDefault();
//             showAutoToast("warning", "Dữ liệu không hợp lệ!");
//         }
//     });
// });
