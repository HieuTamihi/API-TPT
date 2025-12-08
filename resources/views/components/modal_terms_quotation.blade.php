<!-- Modal -->
<div class="modal fade" id="staticBackdrop" tabindex="-1" role="dialog" aria-labelledby="modal-id" aria-modal="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header justify-content-end">
                <div class="d-flex content__heading--right">
                    <div class="row m-0">
                        <a href="#">
                            <button type="button" data-dismiss="modal" data-modal-id="modal-id"
                                class="btn-destroy btn-destroy-modal btn-light mx-1 d-flex align-items-center h-100">
                                <svg class="mx-1" width="16" height="16" viewBox="0 0 16 16" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M8 15C11.866 15 15 11.866 15 8C15 4.13401 11.866 1 8 1C4.13401 1 1 4.13401 1 8C1 11.866 4.13401 15 8 15ZM6.03033 4.96967C5.73744 4.67678 5.26256 4.67678 4.96967 4.96967C4.67678 5.26256 4.67678 5.73744 4.96967 6.03033L6.93934 8L4.96967 9.96967C4.67678 10.2626 4.67678 10.7374 4.96967 11.0303C5.26256 11.3232 5.73744 11.3232 6.03033 11.0303L8 9.06066L9.96967 11.0303C10.2626 11.3232 10.7374 11.3232 11.0303 11.0303C11.3232 10.7374 11.3232 10.2626 11.0303 9.96967L9.06066 8L11.0303 6.03033C11.3232 5.73744 11.3232 5.26256 11.0303 4.96967C10.7374 4.67678 10.2626 4.67678 9.96967 4.96967L8 6.93934L6.03033 4.96967Z"
                                        fill="black"></path>
                                </svg>
                                <p class="m-0 p-0 text-dark">Hủy</p>
                            </button>
                        </a>
                        <button type="button" id="confirm"
                            class="submit-button custom-btn d-flex align-items-center h-100 ml-1">
                            <svg class="mx-1" xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                viewBox="0 0 16 16" fill="none">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M8 15C11.866 15 15 11.866 15 8C15 4.13401 11.866 1 8 1C4.13401 1 1 4.13401 1 8C1 11.866 4.13401 15 8 15ZM11.7836 6.42901C12.0858 6.08709 12.0695 5.55006 11.7472 5.22952C11.4248 4.90897 10.9186 4.9263 10.6164 5.26821L7.14921 9.19122L5.3315 7.4773C5.00127 7.16593 4.49561 7.19748 4.20208 7.54777C3.90855 7.89806 3.93829 8.43445 4.26852 8.74581L6.28032 10.6427C6.82041 11.152 7.64463 11.1122 8.13886 10.553L11.7836 6.42901Z"
                                    fill="white"></path>
                            </svg>
                            <p class="m-0 p-0">Xác nhận</p>
                        </button>
                    </div>
                </div>
            </div>
            <div class="content-wrapper2 px-0 py-0">
                <div class="border">
                    <div>
                        <div class="bg-filter-search border-0 d-flex justify-content-center align-items-center"
                            style="height:40px">
                            <p class="font-weight-bold text-uppercase mb-0" style="line-height: 40px;">
                                ĐIỀU KHOẢN THƯƠNG MẠI
                            </p>
                        </div>
                        <table class="info-serial w-100">
                            <tbody id="table-body">
                                <tr class="height-40">
                                    <td class="text-13-black border py-0 text-center">
                                        <textarea id="terms" cols="30" rows="10" class="w-100 h-auto text-danger">{{ trim($terms->content ?? "") }}</textarea>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $("#confirm").click(function() {
        let content = $("#terms").val();
        $.ajax({
            url: "/save-terms",
            type: "POST",
            data: {
                content: content,
                _token: $('meta[name="csrf-token"]').attr("content") // CSRF Token Laravel
            },
            success: function(response) {
                showAutoToast("success", response.msg);
                // Xóa nội dung cũ của .terms-content
                $(".terms-content").html("");

                // Nếu content rỗng, dùng nội dung mặc định
                if (content === "") {
                    $(".terms-content").html(`
                    <p class="m-0 text-header-print">*Giá trên bao gồm phí VAT</p>
                    <p class="m-0 text-header-print">*Thời gian bảo hành bo mạch: 03 tháng</p>
                    <p class="m-0 text-header-print">*Thời gian bảo hành ắc quy: 12 tháng</p>
                    <p class="m-0 text-header-print">*Thanh toán: Thanh toán bằng chuyển khoản 100% sau khi xác nhận đơn hàng</p>
                `);
                } else {
                    // Nếu có nội dung, thêm vào với xuống dòng <br>
                    $(".terms-content").html(content.replace(/\n/g, "<br>"));
                }
                $("#staticBackdrop").modal("hide");
            },
            error: function(xhr, status, error) {
                console.error("Lỗi:", error);
            }
        });
    });
</script>
