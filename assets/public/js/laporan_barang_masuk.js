$(function () {
	var show = document.getElementById("transaksi_masuk");
	show.style.display = "none";

	const Toast = Swal.mixin({
		toast: true,
		position: "top-end",
		showConfirmButton: false,
		timer: 3000,
		timerProgressBar: true,
	});

	// Inisialisasi Datepicker
	$("#startDate")
		.datepicker({
			format: "yyyy-mm-dd", // Format datepicker yyyy-mm-dd
			autoclose: true,
		})
		.datepicker("setDate", new Date()); // Set default hari ini

	$("#search").click(function () {
		var startDate = $("#startDate").val(); // Ambil tanggal input
		console.log("startDate:", startDate); // Debugging log

		if (!startDate) {
			Toast.fire({
				icon: "error",
				title: "Semua field harus diisi",
			});
			return;
		}

		// Validasi apakah startDate berada dalam 60 hari terakhir atau lebih awal
		if (!isValidDateInLast60Days(startDate)) {
			Toast.fire({
				icon: "error",
				title: "Start Date harus dalam 60 hari terakhir atau lebih awal",
			});
			return;
		}

		// AJAX request ke backend
		show.style.display = "none";
		$("#table_transaksi_masuk").empty();

		$.ajax({
			url: "../Backend/get_report_va_controller",
			type: "POST",
			dataType: "JSON",
			data: {
				startDate: startDate, // Kirim tanggal dalam format yyyy-mm-dd
			},
			success: function (response) {
				console.log(response);

				if (
					response.responseCode === "2003500" &&
					response.virtualAccountData.length > 0
				) {
					var no = 1;
					$.each(response.virtualAccountData, function (index, item) {
						var row = `
                            <tr>
                                <td style="text-align:center">${no}</td>
                                <td style="text-align:center">${item.partnerServiceId.trim()}</td>
                                <td style="text-align:center">${item.customerNo}</td>
                                <td style="text-align:center">${item.virtualAccountNo.trim()}</td>
                                <td style="text-align:center">${item.trxId}</td>
                                <td style="text-align:center">${item.totalAmount.value} ${item.totalAmount.currency}</td>
                                <td style="text-align:center">${getFormattedDate(item.trxDateTime, "dd-mm-yyyy")}</td>
                                <td style="text-align:center">${item.virtualAccountName}</td>
                                <td style="text-align:center">${item.additionalInfo.channel}</td>
                                <td style="text-align:center">${item.additionalInfo.description}</td>
                                <td style="text-align:center">${item.additionalInfo.sourceAccountVa}</td>
                                <td style="text-align:center">${item.additionalInfo.tellerId}</td>
                            </tr>
                        `;
						$("#table_transaksi_masuk").append(row);
						no++;
					});
					show.style.display = "block";
				} else {
					var emptyRow = `
                        <tr>
                            <td colspan="12" style="text-align:center">Data Transaksi Tidak Ditemukan</td>
                        </tr>
                    `;
					$("#table_transaksi_masuk").html(emptyRow);
					show.style.display = "block";
				}
			},
			error: function () {
				Toast.fire({
					icon: "error",
					title: "Gagal mengambil data",
				});
				var errorRow = `
                    <tr>
                        <td colspan="12" style="text-align:center">Error retrieving data</td>
                    </tr>
                `;
				$("#table_transaksi_masuk").html(errorRow);
				show.style.display = "block";
			},
		});
	});
});

// Fungsi untuk format tanggal dalam berbagai format
function getFormattedDate(date, format = "yyyy-mm-dd") {
	var dateObj = new Date(date);
	let year = dateObj.getFullYear();
	let month = (1 + dateObj.getMonth()).toString().padStart(2, "0");
	let day = dateObj.getDate().toString().padStart(2, "0");

	if (format === "dd-mm-yyyy") {
		return day + "-" + month + "-" + year;
	} else if (format === "yyyy-mm-dd") {
		return year + "-" + month + "-" + day;
	}
	return date;
}

// Fungsi untuk mengecek apakah tanggal berada dalam 60 hari terakhir
function isValidDateInLast60Days(dateString) {
	var selectedDate = new Date(dateString);
	var today = new Date();
	var past60Days = new Date();
	past60Days.setDate(today.getDate() - 60); // Set tanggal 60 hari ke belakang

	return selectedDate <= today && selectedDate >= past60Days;
}
