
function getVisibleTableData(tableSelector) {
    let data = [];

    $(tableSelector).find("tr").each(function () {
        let rowData = [];
        $(this).find("th:visible, td:visible").each(function () { // Chỉ lấy ô hiển thị
            rowData.push($(this).text().trim());
        });
        if (rowData.length > 0) {
            data.push(rowData);
        }
    });

    return data;
}

function exportTableToExcel(buttonSelector, tableSelector, filename = "data.xlsx") {
    $(buttonSelector).click(function () {
        let tableData = getVisibleTableData(tableSelector);
        if (tableData.length === 0) {
            alert("Không có dữ liệu để xuất!");
            return;
        }

        let ws = XLSX.utils.aoa_to_sheet(tableData);
        let wb = XLSX.utils.book_new();

        XLSX.utils.book_append_sheet(wb, ws, "Sheet1");
        XLSX.writeFile(wb, filename);
    });
}
