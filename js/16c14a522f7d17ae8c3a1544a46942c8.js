function createFilter(){
    let form = document.forms[0];

    if(form.filter_name.value.length < 1 || form.filter_display_name.value.length < 1){
        swal("Укажите имя фильтра");
        return;
    }
    if(form.filter_type.selectedIndex < 0){
        swal("Укажите тип фильтра");
        return;
    }
    if(form.filter_object_type.selectedIndex < 0){
        swal("Укажите тип объекта фильтра");
        return;
    }
    if(form.filter_type.selectedIndex != 0 && form.filter_options.value.length < 1){
        swal("Укажите опции объекта");
        return;
    }

    swal({
        title: "Подтверждение",
        text: "Подтвердите создание нового фильтра",
        icon: "info",
        buttons: ["Отмена", "Да"],
        confirmButtonClass: "btn-success",
        closeOnConfirm: false
    }).then(function(result){
        if(result){
            swal("Подтверждение получено", "Фильтр будет создан через секунду", "success");
            setTimeout(function () {
                form.submit();
            }, 1000);
        }
    });
}

function editFilter(){
    let form = document.forms[0];

    if(form.filter_name.value.length < 1 || form.filter_display_name.value.length < 1){
        swal("Укажите имя фильтра");
        return;
    }
    if(form.filter_type.selectedIndex < 0){
        swal("Укажите тип фильтра");
        return;
    }
    if(form.filter_object_type.selectedIndex < 0){
        swal("Укажите тип объекта фильтра");
        return;
    }
    if(form.filter_type.selectedIndex != 0 && form.filter_options.value.length < 1){
        swal("Укажите опции объекта");
        return;
    }

    swal({
        title: "Подтверждение",
        text: "Сохранить изменения?",
        icon: "info",
        buttons: ["Отмена", "Да"],
        closeOnConfirm: false
    }).then(function(result){
        if(!result) return;
        swal("Подтверждение получено", "", "success");
        setTimeout(function () {
            form.submit();
        }, 1000);
    });
}

function removeFilter(filter_id){
    swal({
        title: "Вы уверены?",
        text: "Вы действительно хотите удалить этот фильтр? Эта операция необратима",
        icon: "warning",
        showCancelButton: true,
        dangerMode: true,
        buttons: ["Отмена", "Да, удалить"],
        closeOnConfirm: false,
        showLoaderOnConfirm: true
    }).then( () => {
        let request = new XMLHttpRequest();
        request.open("POST", "filters.php", true);
        request.setRequestHeader("X-Requested-With", "XMLHttpRequest");
        request.send('{"action": "remove", "filter_id": "' + (+filter_id) + '", "token": "' + ctoken + '"}');
        request.onreadystatechange = () => {
            if(request.readyState == 4 && request.status == 200) {
                table.row($("#filter_" + filter_id)).remove().draw();
                let i = 0;
                Array.prototype.slice.call(document.getElementsByTagName("table")[0].getElementsByTagName("tr")).forEach(el => {
                    if(i != 0){
                        el.children[0].textContent = i;
                    }
                    i++;
                });
                swal("Успех", "Фильтр успешно удален", "success");
            }
            if(request.readyState == 4 && (request.status == 400 || request.status == 403)) {
                swal("Ошибка", "Операция не удалась", "error");
            }
        }
    });
}

function createVacancyDurationChangeHandler(){
    let status = ["none", "none", "none"];
    let validator = [false, false, false];
    switch(+document.getElementById("vacancy_duration").value){
        case 0:
            status[0] = "flex";
            validator[0] = "true";
            break;
        case 1:
            status[1] = "flex";
            validator[1] = "true";
            break;
        case 2:
            status[2] = "flex";
            validator[2] = "true";
            break;
        default:
            break;
    }
    console.log(status);
    document.getElementById("longtime_work").style.display = status[0];
    document.getElementById("shorttime_work").style.display = status[1];
    document.getElementById("shorttime_work").getElementsByTagName("input")[0].required = validator[1];
    document.getElementById("shorttime_work").getElementsByTagName("input")[1].required = validator[1];
    document.getElementById("onetime_work").style.display = status[2];
    document.getElementById("onetime_work").getElementsByTagName("input")[0].required = validator[2];
}

function verifryForm(){
    let form = document.forms[0];

    if(form.vacancy_name.value.length < 3){
        swal("Слишком короткое название вакансии");
        return;
    }

    if(form.vacancy_workplace_count.value.length < 1){
        swal("Введите количество вакантных мест");
        return;
    }

    if(form.vacancy_duration.selectedIndex == 0){
        let week_days = Array.prototype.slice.call(document.getElementById("longtime_work").getElementsByTagName("input"));
        let selected = false;
        week_days.forEach(el => {
            if(el.checked) selected = true;
        });
        if(!selected){
            swal("Необходимо выбрать минимум один рабочий день недели!");
            return;
        }
    }
    else if(form.vacancy_duration.selectedIndex == 1){
        if(form.vacancy_date_start.value.length == 0 || form.vacancy_date_end.value.length == 0){
            swal("Необходимо указать временной промежуток вакансии");
            return;
        }
    }
    else{
        if(form.vacancy_date.value.length == 0){
            swal("Необходимо указать дату события вакансии");
            return;
        }
    }

    if(form.vacancy_salary_per_hour.value.length == 0 && form.vacancy_salary_per_day.value.length == 0 && form.vacancy_salary_per_month.value.length == 0){
        swal("Необходимо указать зарплату");
        return;
    }

    if(form.vacancy_salary_per_hour.value > 1000000 || form.vacancy_salary_per_hour.value < 0 || form.vacancy_salary_per_day.value > 1000000 || form.vacancy_salary_per_day.value < 0 || form.vacancy_salary_per_month.value > 1000000 || form.vacancy_salary_per_month.value < 0){
        swal("Зарплата указана некорректно");
        return;
    }

    if(form.vacancy_desc_min.value.length == 0){
        swal("Необходимо указать краткое описание вакансии");
        return;
    }

    if(form.vacancy_desc.value.length == 0){
        swal("Необходимо указать полное описание вакансии");
        return;
    }

    if(form.vacancy_contacts.value.length == 0){
        swal("Необходимо указать контакты вакансии");
        return;
    }
    swal({
        title: "Сохранить изменения?",
        text: "С вашего счета будут списаны монеты за создание вакансии",
        icon: "info",
        buttons: ["Отмена", "Да"],
        closeOnConfirm: false
    }).then(function(result){
        if(!result) return;
        swal("Отправлено", "Внесенные изменения будут сохранены", "success");
        setTimeout(function () {
            form.submit();
        }, 1000);
    });
}

function removeVacancy(id){
    swal({
        title: "Вы уверены?",
        text: "Вы действительно хотите удалить эту вакансию? Эта операция необратима",
        icon: "error",
        showCancelButton: true,
        dangerMode: true,
        buttons: ["Отмена", "Да, удалить"],
        closeOnConfirm: false,
        showLoaderOnConfirm: true
    }).then(function(result){
        if(!result) return;
        let request = new XMLHttpRequest();
        request.open("POST", "/admin/edit_vacancy.php", true);
        request.setRequestHeader("X-Requested-With", "XMLHttpRequest");
        request.send('{"remove": "' + id + '", "token": "' + ctoken + '"}');
        request.onreadystatechange = () => {
            if(request.readyState == 4 && request.status == 200) {
                swal("Успех", "Вакансия «" + vacancy_name + "» успешно удалена ", "success");
                window.location = "/admin";
            }
            if(request.readyState == 4 && (request.status == 400 || request.status == 403)) {
                swal("Ошибка", "Операция не удалась", "error");
            }
        }
    });
}

function removeElement(id, table){
    swal({
        title: "Вы уверены?",
        text: "Вы действительно хотите удалить этот элемент? Это может повлечь за собой плохие последствия",
        icon: "error",
        showCancelButton: true,
        dangerMode: true,
        buttons: ["Отмена", "Да, удалить"],
        closeOnConfirm: false,
        showLoaderOnConfirm: true
    }).then(function(result){
        if(!result) return;
        let request = new XMLHttpRequest();
        request.open("POST", "/admin/lists.php", true);
        request.setRequestHeader("X-Requested-With", "XMLHttpRequest");
        request.send('{"remove": "' + id + '", "table": "' + table + '", "token": "' + ctoken + '"}');
        request.onreadystatechange = () => {
            if(request.readyState == 4 && request.status == 200) {
                swal("Успех", "Элемент успешно удален", "success");
                window.location = "/admin/lists.php?edit=" + table;
            }
            if(request.readyState == 4 && (request.status == 400 || request.status == 403)) {
                swal("Ошибка", "Операция не удалась", "error");
            }
        }
    });
}

// function postPage(){
//     document.getElementById("html_editor").value = 
// }

function previewPage(){
    swal({
        title: "Подготовка предпросмотра", 
        text: "Пожалуйста, подождите", 
        icon: "info",
        buttons: false
    });
    $.ajax({
        method: "POST",
        url: "/admin/edit_page.php",
        data: {
            "action": "preview",
            "token": ctoken,
            "page_name": document.getElementsByName("page_title")[0].value,
            "page_content": CKEDITOR.instances.html_editor.getData()
        },
        success: (response) => {
            if(response == "OK"){
                window.open("/_preview.html", '_blank').focus();
                swal.close();
            }
            else{
                swal("Ошибка предпросмотра", "Создать страницу предпросмотра не удалось", "error");
            }
        }
    })
}

function postPage(){
    document.getElementById("html_editor").value = CKEDITOR.instances.html_editor.getData();
    document.getElementById("page_editor").submit();
}

function setUserStatus(status, id){
    swal({
        title: "Подтверждение",
        text: "Вы действительно хотите " + (status ? 'заблокировать' : 'разблокировать') + " этого пользователя?",
        icon: "info",
        showCancelButton: true,
        dangerMode: (status ? true : false),
        buttons: ["Отмена", "Да"],
        closeOnConfirm: false,
        showLoaderOnConfirm: true
    }).then(function(result){
        if(!result) return;
        let request = new XMLHttpRequest();
        request.open("POST", "/admin/users.php", true);
        request.setRequestHeader("X-Requested-With", "XMLHttpRequest");
        request.send('{"action": "' + (status ? 'block' : 'unlock') + '", "user": "' + id + '", "token": "' + ctoken + '"}');
        request.onreadystatechange = () => {
            if(request.readyState == 4 && request.status == 200) {
                swal("Успех", "", "success");
                window.location = "/admin/users.php";
            }
            if(request.readyState == 4 && (request.status == 400 || request.status == 403)) {
                swal("Ошибка", "Операция не удалась", "error");
            }
        }
    });
}

function activateUser(id){
    swal({
        title: "Подтверждение",
        text: "Вы действительно хотите активировать этого пользователя?",
        icon: "info",
        showCancelButton: true,
        dangerMode: false,
        buttons: ["Отмена", "Да"],
        closeOnConfirm: false,
        showLoaderOnConfirm: true
    }).then(function(result){
        if(!result) return;
        let request = new XMLHttpRequest();
        request.open("POST", "/admin/users.php", true);
        request.setRequestHeader("X-Requested-With", "XMLHttpRequest");
        request.send('{"action": "activate", "user": "' + id + '", "token": "' + ctoken + '"}');
        request.onreadystatechange = () => {
            if(request.readyState == 4 && request.status == 200) {
                swal("Успех", "", "success");
                window.location = "/admin/users.php";
            }
            if(request.readyState == 4 && (request.status == 400 || request.status == 403)) {
                swal("Ошибка", "Операция не удалась", "error");
            }
        }
    });
}