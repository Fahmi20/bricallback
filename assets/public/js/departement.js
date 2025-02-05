$(document).ready(function () {
	// Inisialisasi DataTables untuk tabel push notifikasi
	var tablePushNotifications = $("#table_push_notifications").DataTable({
		processing: true,
		serverSide: true,
		ajax: {
			type: "GET",
			url: "../Backend/notifikasi", // Sesuaikan dengan endpoint controller yang benar untuk mengambil data push notifikasi
		},
		columnDefs: [
			{
				searchable: false,
				orderable: false,
				targets: 0,
			},
		],
		columns: [
			{
				render: function (data, type, row) {
					return `<div style="text-align:center;">${row.partnerServiceId}</div>`;
				},
			},
			{
				render: function (data, type, row) {
					return `<div style="text-align:center;">${row.customerNo}</div>`;
				},
			},
			{
				render: function (data, type, row) {
					return `<div style="text-align:center;">${row.virtualAccountNo}</div>`;
				},
			},
			{
				render: function (data, type, row) {
					return `<div style="text-align:center;">${row.paymentRequestId}</div>`;
				},
			},
			{
				render: function (data, type, row) {
					return `<div style="text-align:center;">${row.trxDateTime}</div>`;
				},
			},
			{
				render: function (data, type, row) {
					return `<div style="text-align:center;">${row.paymentAmount}</div>`;
				},
			},
			{
				render: function (data, type, row) {
					return `<div style="text-align:center;">${row.terminalId}</div>`;
				},
			},
			{
				render: function (data, type, row) {
					return `<div style="text-align:center;">${row.bankId}</div>`;
				},
			},
			{
				render: function (data, type, row) {
					return `<div style="text-align:center;">${row.status}</div>`;
				},
			},
		],
	});

	// Inisialisasi SweetAlert untuk notifikasi
	const Toast = Swal.mixin({
		toast: true,
		position: "top-end",
		showConfirmButton: false,
		timer: 3000,
		timerProgressBar: true,
	});
});
