$(document).ready(function () {
    // сделать кликабельным блок с текстом https://habr.com/post/38208/
    $(".pane-list li").click(function () {
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

    // появилось окно добавления новой задачи
    $('#dialogAddTask').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget)
        var recipient = button.data('whatever')
        var modal = $(this)
        // выставляем значения в диалоговом окне
        modal.find('.modal-body input').val(recipient);
        // приоритет - 'средний' по умолчанию
        modal.find('#select-prio').val(2);
        // статус - 'в работе' по умолчанию
        modal.find('#select-status').val(1);
    })

    // обработчик добавления новой/редактирования существующей задачи
    $("#btn-submit").click(function () {

        var dataToSend = {};
        dataToSend['name'] = $('.modal-body input').val();
        dataToSend['prio'] = $('#select-prio').val();
        dataToSend['status'] = $('#select-status').val();
        dataToSend['tags'] = $('#tags-list').val();
        var textCmd = "command=add_task&param=" + $.toJSON(dataToSend);

        //alert("отпр. данные: " + textCmd);
        $(".btn-refresh").remove();
        $(this).toggleClass("active");

        $.getJSON("req_ajax.php", textCmd, function (data) {
            // alert("Прибыли данные: " + 'dd');
            // data = json_encode(data);
            // alert("Прибыли данные: " + data);
        });
        // alert("Прибыли данные: " + 'dd2');
    });
    // обработчик удаления выбранной задачи
    $(".btn-del").click(function () {
    });
});
