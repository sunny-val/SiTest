$(document)
    .ready(
        function () {

            // id выбранной строки
            var selected_uuid = 0;

            // вывод окна с ошибкой
            function showAlert(err_text) {
                var content =
                    ' \
                  <div class="alert alert_err alert-danger alert-dismissible fade show" \
                      role="alert"> \
                      <strong>Ошибка!</strong> <span>'
                        + err_text
                        + '</span> \
                      <button type="button" class="close" data-dismiss="alert" \
                          aria-label="Close"> \
                          <span aria-hidden="true">&times;</span> \
                      </button> \
                  </div>';

                $(".alert-err").html(content);

            }
            // удаление окна с ошибкой
            function closeAlert(err_text) {
                $('.alert_err').hide();
            }

            // сделать кнопки редактирования и удаления неактивными
            $(".btn-edit").prop("disabled", true);
            $(".btn-del").prop("disabled", true);

            // сделать кликабельной таблицу с задачами
            // кроме первой строки - .tasklist tr:not(:first-child)
            $('body').on('click', '.tasklist tr', function () {
                // убрать выделение всех задач
                $(".tasklist tr").removeClass("selected");
                // ищем id выбранной задачи
                selected_uuid = $(this).attr("uuid");
                if (selected_uuid) {
                    var attr = '[uuid = "' + selected_uuid + '"]';
                    // выделить выбраную задачу
                    $(attr).addClass("selected");
                    // сделать кнопки редактирования и удаления активными
                    $(".btn-edit").prop("disabled", false);
                    $(".btn-del").prop("disabled", false);
                }
                return false;
            });

            // появилось окно добавления новой задачи
            $('#dialogAddTask').on('show.bs.modal', function (event) {

                closeAlert();
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

            // появилось окно редактирования задачи
            $('#dialogEditTask').on('show.bs.modal', function (event) {

                closeAlert();
                var modal = $(this);
                // ищем параметры задачи
                var attr = '[uuid = "' + selected_uuid + '"]';
                // 
                // showAlert("Не выбрана задача для редактирования");
                // $('#dialogEditTask').modal('hide');
                val = td = $(attr).children();
                if (td.length != 5) {
                    showAlert('Неверные параметры задачи');
                    a = $('#dialogEditTask').modal('hide');
                    b = a.hide();
                    return;
                }
                // выставляем значения в диалоговом окне
                $('#task-edit-name').html(td[0].textContent);
                // приоритет - 'средний' по умолчанию
                modal.find('#select-prio-edit').val(td[4].textContent);
                // статус - 'в работе' по умолчанию
                modal.find('#select-status-edit').val(td[2].textContent);
                // теги
                modal.find('#tags-list-edit').val(td[3].textContent);
            })

            function getTextStatus(status_int) {
                switch (status_int) {
                case 1:
                    return "в работе";
                case 2:
                    return "завершена";
                }
                return "неизвестно";
            }

            // обработчик получения списка всех задач
            $(".btn-refresh").click(
                function () {
                    closeAlert();
                    $.getJSON("req_ajax.php", "command=get_all_tasks",
                        function (data) {

                            // $().alert('close');

                            if (!'err' in data)
                                showAlert("Неизвестная Ошибка");

                            if (data['err'] && data['err'].length > 0) {
                                showAlert(data['err']);

                            } else {
                                var tasks = data['task'];
                                var tasks_html = '';
                                if (data['task'] !== undefined) {
                                    for ( var key in tasks) {
                                        var task = tasks[key];
                                        tasks_html += "<tr uuid=" + task['uuid'] + ">\n";
                                        tasks_html += "  <td>" + task['name'] + "</td>\n";
                                        tasks_html +=
                                            "  <td>" + getTextStatus(Number(task['status']))
                                                + "</td>\n";
                                        tasks_html +=
                                            "  <td hidden=''>" + task['status'] + "</td>\n";
                                        tasks_html += "  <td>" + task['tags'] + "</td>\n";
                                        tasks_html +=
                                            "  <td hidden=''>" + task['priority'] + "</td>\n";
                                        tasks_html += "</tr>";
                                    }
                                }
                                $(".tasklist").html(tasks_html);
                                // сделать кнопку редактирования
                                // неактивной, т.к.
                                // выделения нет
                                $(".btn-edit").prop("disabled", true);
                                $(".btn-del").prop("disabled", true);

                            }
                            // alert("Прибыли данные: " +
                            // data['task'].length);

                            ;
                            // data = json_encode(data);
                            // alert("Прибыли данные: " + data);
                        });
                });
            // обработчик добавления новой задачи
            $(".btn-submit").click(function () {
                var dataToSend = {};
                dataToSend['name'] = $('.modal-body input').val();
                dataToSend['prio'] = $('#select-prio').val();
                dataToSend['status'] = $('#select-status').val();
                dataToSend['tags'] = $('#tags-list').val();

                var textCmd = "command=add_task&param=" + $.toJSON(dataToSend);

                // alert("отпр. данные: " + textCmd);
                // $(".btn-refresh").remove();
                // $(this).toggleClass("active");

                $.getJSON("req_ajax.php", textCmd, function (data) {
                    if (!'err' in data)
                        showAlert("Неизвестная Ошибка");
                    else if (data['err'] && data['err'].length > 0)
                        showAlert(data['err']);
                    else {
                        // успешно прошли изменения
                        // нажать кнопку 'получить список задач'
                        $(".btn-refresh").click();
                    }
                });
            });
            // обработчик редактирования существующей задачи
            $(".btn-submit-edit").click(function () {
                var dataToSend = {};
                dataToSend['name'] = $('#task-edit-name').text();
                dataToSend['prio'] = $('#select-prio-edit').val();
                dataToSend['status'] = $('#select-status-edit').val();
                dataToSend['tags'] = $('#tags-list-edit').val();
                dataToSend['uuid'] = selected_uuid;
                var textCmd = "command=edit_task&param=" + $.toJSON(dataToSend);

                $.getJSON("req_ajax.php", textCmd, function (data) {
                    if (!'err' in data)
                        showAlert("Неизвестная Ошибка");
                    else if (data['err'] && data['err'].length > 0)
                        showAlert(data['err']);
                    else {
                        // успешно прошли изменения
                        // нажать кнопку 'получить список задач'
                        $(".btn-refresh").click();
                    }
                });
            });
            // обработчик удаления выбранной задачи
            $(".btn-del").click(function () {
                var dataToSend = {};
                dataToSend['uuid'] = selected_uuid;
                var textCmd = "command=del_task&param=" + $.toJSON(dataToSend);

                $.getJSON("req_ajax.php", textCmd, function (data) {
                    if (!'err' in data)
                        showAlert("Неизвестная Ошибка");
                    else if (data['err'] && data['err'].length > 0)
                        showAlert(data['err']);
                    else {
                        // успешно прошли изменения
                        // нажать кнопку 'получить список задач'
                        $(".btn-refresh").click();
                    }
                });
            });

            // нажать кнопку 'получить список задач'
            $(".btn-refresh").click();
        });
