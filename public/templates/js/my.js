$(document).ready(function() {
	// сделать кликабельным блок с текстом https://habr.com/post/38208/
	$(".pane-list li").click(function() {
		window.location = $(this).find("a").attr("href");
		return false;
	});
	// $.ajax({
	// url:"req_ajax.php",
	// type:"POST",
	// success:function(result){ //роль играет только этот блок
	// $("#ins1").html(result)
	// }
	// });

	// обработчик добавления новой задачи
	$(".btn-add").click(function() {

		$(".btn-refresh").remove();
		$(this).toggleClass("active");
		// alert("Прибыли данные: " + 'dd1');
		// var jqxhr = $.getJSON("req_ajax.php")
		// .success(function(data) { alert("Успешное выполнение");
		// $("#ins1").html(data)})
		// .error(function() { alert("Ошибка выполнения"); })
		// .complete(function() { alert("Завершение выполнения"); });

		$.getJSON("req_ajax.php", "command=add&name=имя1&status=0", function(data) {
			// alert("Прибыли данные: " + 'dd');
			alert("Прибыли данные: " + data);
		});
		// alert("Прибыли данные: " + 'dd2');
	});
	// обработчик удаления выбранной задачи
	$(".btn-del").click(function() {
	});
});
