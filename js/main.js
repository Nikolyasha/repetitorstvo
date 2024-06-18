function renderError(text, force = false){
    document.getElementById("error_place").innerHTML = "<hr>" +
        "<div class='row m-t-20 login_error'><p>" +
            "<b>Ошибка:</b> " + text +
        "</p></div>";
    if(force){
        swal("Ошибка", text, "error");
    }
}

function hidePeopleBlock(){
    if($('.people__row').height() == 0)
        $('.people__row').animate({"height": 272});
    else
        $('.people__row').animate({"height": 0});
}

//#region Вакансии
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
        let row = $("#vacancy_" + id)[0];
        let vacancy_name = row.getElementsByTagName("td")[1].textContent;
        row.getElementsByTagName("td")[8].innerHTML = 
            '<button class="btn btn-success btn-icon disabled"><i class="icofont icofont-eye-alt"></i></button>' + 
            '<button class="btn btn-warning btn-icon disabled"><i class="ti-pencil-alt"></i></button>' +
            '<button class="btn btn-danger btn-icon disabled"><i class="ti-trash"></i></button>';
        row.style.background = "#EEE";
        row.style.color = "#AAA";

        let request = new XMLHttpRequest();
        request.open("POST", "vacancies.php", true);
        request.setRequestHeader("X-Requested-With", "XMLHttpRequest");
        request.send('{"remove": "' + id + '", "token": "' + ctoken + '"}');
        request.onreadystatechange = () => {//Call a function when the state changes.
            if(request.readyState == 4 && request.status == 200) {
                table.row($("#vacancy_" + id)).remove().draw();
                let i = 0;
                Array.prototype.slice.call(document.getElementsByTagName("table")[0].getElementsByTagName("tr")).forEach(el => {
                    if(i != 0){
                        el.children[0].textContent = i;
                    }
                    i++;
                });
                swal("Успех", "Вакансия «" + vacancy_name + "» успешно удалена ", "success");
            }
            if(request.readyState == 4 && (request.status == 400 || request.status == 403)) {
                row.getElementsByTagName("td")[4].innerHTML = '<b style="color: red;">ОШИБКА</b>';
                swal("Ошибка", "Операция не удалась", "error");
            }
        }
    });
}

//Создание вакансии
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

    let titleMsg = "Создать вакансию?";
    let textMsg = "создание";
    
    if (action == 'edit') {
        titleMsg = "Сохранить изменения?";
        textMsg = "редактирование";
    }

    if (payment_active == 'false') {
        swal({
            title: titleMsg,
            // title: "Сохранить изменения?",
            //text: "С вашего счета будут списаны монеты за создание вакансии",
            icon: "info",
            showCancelButton: true,
            buttons: ["Отмена", "Да"],
            closeOnConfirm: false
        }).then(function(result){
            if(!result) return;
            // swal("Отправлено", "Внесенные изменения будут сохранены", "success");
            setTimeout(function () {
                form.submit();
            }, 1000);
        });
    }
    else {
        if (action == 'edit' && edit_payment == 'false') {
            swal({
                title: titleMsg,
                // title: "Сохранить изменения?",
                //text: "С вашего счета будут списаны монеты за создание вакансии",
                icon: "info",
                showCancelButton: true,
                buttons: ["Отмена", "Да"],
                closeOnConfirm: false
            }).then(function(result){
                if(!result) return;
                // swal("Отправлено", "Внесенные изменения будут сохранены", "success");
                setTimeout(function () {
                    form.submit();
                }, 1000);
            });
        }
        else {
            swal({
                title: titleMsg,
                // title: "Сохранить изменения?",
                text: "С вашего счета будут списаны " + vacancy_price + " монет за " + textMsg + " вакансии",
                icon: "info",
                showCancelButton: true,
                buttons: ["Отмена", "Да"],
                closeOnConfirm: false
            }).then(function(result){
                if(!result) return;
                // swal("Отправлено", "Внесенные изменения будут сохранены", "success");
                setTimeout(function () {
                    form.submit();
                }, 1000);
            });
        }
    }
    
}

function removeImg(value) {
    value.parentElement.remove();

    let id_remove = value.parentElement.children[0].value;    
    document.getElementById(id_remove).value = id_remove;
    
}

function preview(id) {
    
    let imageContainer = document.getElementById("imgForm_"+id);
    let imgI = document.getElementById("imgInp_"+id);

    for (i of imgI.files){
        
        let reader = new FileReader();                              

        reader.onload = function() {            
            if (i.size > 5242880) {
                swal("Файл '" + i.name + "' больше 5 МБ", "Размер загружаемого файла не должен привышать 5 мб", "warning");
                return;
            }
            else if(i.type.split("/")[0] != "image" || i.type.split("/")[1] == "gif"){
                swal("Файл '" + i.name + "' не является изображением", "Загружайте только изображения", "warning");                
                return;
            }
            else if (document.querySelectorAll('#photo_'+id).length == max_photos) {
                swal("Вы достигли ограничения файлов", "Можно загрузить до " + max_photos + " фотографий", "warning");
                return;
            }
            else {                            
            
                imageContainer.innerHTML += '<div id="photo_'+id+'"><img src="' + reader.result +'" class="mini_photo"><span>'+i.name+'</span><span onclick="removeImg(this)" class="bm_rm_button">Удалить</span> <input type="hidden" name="extra_photos_add_'+id+'[]" value="'+reader.result+'"/> </div>';
            }
            
        }        
        
        reader.readAsDataURL(i);
        
    }
    
}

function viewVideo(url) {

    videojs('my-video').ready(function() {
        
        var myPlayer = this;
        
        myPlayer.src({ type: 'video/youtube', src: url });
        myPlayer.controls(true);
        
        const videoId = YouTubeVideoId(url);
        myPlayer.poster('https://img.youtube.com/vi/'+videoId+'/maxresdefault.jpg');
        
    });
    
}

//#endregion

//#region  Офферы
function rejectOffer(offer_id, vacancy_id){
    swal({
        title: "Вы уверены?",
        text: "Вы действительно хотите отклонить это предложение? Отправитель не увидит причины отказа",
        icon: "warning",
        showCancelButton: true,
        dangerMode: true,
        buttons: ["Отмена", "Да, отклонить"],
        closeOnConfirm: false,
        showLoaderOnConfirm: true
    }).then(function(result){
        if(!result) return;
        let row = $("#offer_" + offer_id)[0];
        let user_page_link = row.getElementsByTagName("td")[row.getElementsByTagName("td").length-1].getElementsByTagName("a")[0].href;
        user_page_link = user_page_link.split("/")[user_page_link.split("/").length-1];
        row.getElementsByTagName("td")[row.getElementsByTagName("td").length-2].innerHTML = '<b style="color: darkred;">Отклонен</b>';
        row.getElementsByTagName("td")[row.getElementsByTagName("td").length-1].innerHTML = 
            '<a target="blank" href="/anket/' + user_page_link + '"><button class="btn btn-info btn-icon"><i class="icofont icofont-user-alt-3"></i></button></a> ' +
            '<a><button class="btn btn-success btn-icon disabled"><i class="icofont icofont-check-circled"></i></button></a> ' +
            '<a><button class="btn btn-danger btn-icon disabled"><i class="icofont icofont-ui-delete"></i></button></a>';

        let request = new XMLHttpRequest();
        request.open("POST", "offers.php", true);
        request.setRequestHeader("X-Requested-With", "XMLHttpRequest");
        request.send('{"action": "reject", "offer_id": "' + offer_id + '", "vacancy_id": ' + vacancy_id + ', "token": "' + ctoken + '"}');
        request.onreadystatechange = () => {
            if(request.readyState == 4 && request.status == 200) {
                row.getElementsByTagName("td")[row.getElementsByTagName("td").length-1].getElementsByTagName("a")[2].innerHTML =  
                    '<button class="btn btn-danger btn-icon"><i class="icofont icofont-ui-delete"></i></button> ';
                swal("Успех", "Отклик успешно отклонен", "success");
            }
            if(request.readyState == 4 && (request.status == 400 || request.status == 403)) {
                row.getElementsByTagName("td")[row.getElementsByTagName("td").length-2].innerHTML = '<b style="color: red;">ОШИБКА</b>';
                swal("Ошибка", "Операция не удалась", "error");
            }
        }
    });
}

function removeOffer(offer_id, vacancy_id, is_user = false){
    swal({
        title: "Вы уверены?",
        text: "Вы действительно хотите удалить это предложение? Эта операция необратима",
        icon: "warning",
        showCancelButton: true,
        dangerMode: true,
        buttons: ["Отмена", "Да, удалить"],
        closeOnConfirm: false,
        showLoaderOnConfirm: true
    }).then(function(result){
        if(!result) return;
        let row = $("#offer_" + offer_id)[0];
        let request = new XMLHttpRequest();
        request.open("POST", "offers.php", true);
        request.setRequestHeader("X-Requested-With", "XMLHttpRequest");
        request.send('{"action": "remove' + (is_user ? "_user" : "") + '", "offer_id": "' + (+offer_id) + '", "vacancy_id": "' + (+vacancy_id) + '", "token": "' + ctoken + '"}');
        request.onreadystatechange = () => {
            if(request.readyState == 4 && request.status == 200) {
                table.row($("#offer_" + offer_id)).remove().draw();
                let i = 0;
                Array.prototype.slice.call(document.getElementsByTagName("table")[0].getElementsByTagName("tr")).forEach(el => {
                    if(i != 0){
                        el.children[0].textContent = i;
                    }
                    i++;
                });
                swal("Успех", "Отклик успешно удален", "success");
            }
            if(request.readyState == 4 && (request.status == 400 || request.status == 403)) {
                row.getElementsByTagName("td")[row.getElementsByTagName("td").length-2].innerHTML = '<b style="color: red;">ОШИБКА</b>';
                swal("Ошибка", "Операция не удалась", "error");
            }
        }
    });
}

function sendOfferReply(){
    let form = document.forms[0];

    if(+form.offer_status.value < 1){
        swal("Вы не выбрали решение");
        return;
    }
    if(form.offer_reply.value.length < 1 && +form.offer_status.value == 1){
        swal("Вам нужно написать ответ");
        return;
    }

    swal({
        title: "Отправить ответ?",
        text: "Отменить эту операцию невозможно",
        icon: "info",
        showCancelButton: true,
        confirmButtonClass: "btn-success",
        buttons: ["Отмена", "Да"],
        closeOnConfirm: false
    }).then(function(result){
        if(!result) return;
        swal("Отправлено", "Ваш ответ был отправлен пользователю", "success");
        setTimeout(function () {
            form.submit();
        }, 1000);
    });
}

function cancelOfferReply(){
    swal({
        title: "Отменить операцию?",
        text: "Все изменения будут утерянны",
        icon: "error",
        showCancelButton: true,
        dangerMode: true,
        buttons: ["Отмена", "Да"],
        closeOnConfirm: false
    }).then(function(result){
        if(!result) return;
        if(redirect.length > 0){
            swal("Отменено", "Вы будете отправлены на прошлую страницу", "success");
            setTimeout(function () {
                window.location.replace(redirect);
            }, 1000);
        }
        else{
            swal("Отменено", "Вы будете отправлены на список предложений", "success");
            setTimeout(function () {
                window.location.replace("offers.php");
            }, 1000);
        }
    });
}

function sendOfferRequest(){
    let form = document.forms[0];

    if(form.offer_request.value.length < 1){
        swal("Вам нужно написать ответ");
        return;
    }

    swal({
        title: "Отправить запрос?",
        text: offerPrice > 0 ? "С вашего счета будет списано " + offerPrice + " монет(ы)" : "Отменить эту операцию невозможно",
        icon: "info",
        showCancelButton: true,
        confirmButtonClass: "btn-success",
        buttons: ["Отмена", "Да, подтвердить"],
        closeOnConfirm: false
    }).then(function(result){
        if(!result) return;
        swal("Отправлено", "Ваш запрос был отправлен работодателю", "success");
        setTimeout(function () {
            form.submit();
        }, 1000);
    });
}

function cancelOfferRequest(){
    swal({
        title: "Отменить операцию?",
        text: "Все изменения будут утерянны",
        icon: "error",
        showCancelButton: true,
        dangerMode: true,
        buttons: ["Отмена", "Да"],
        closeOnConfirm: false
    }).then(function(result){
        if(!result) return;
        if(document.getElementById("redirect").value.length > 0){
            swal("Отменено", "Вы будете отправлены на прошлую страницу", "success");
            setTimeout(function () {
                window.location.replace(document.getElementById("redirect").value);
            }, 1000);
        }
        else{
            swal("Отменено", "Вы будете отправлены на список предложений", "success");
            setTimeout(function () {
                window.location.replace("/lk/requests.php");
            }, 1000);
        }
    });
}

function openCompanyResponse(e){
    if (!e)
        e = window.event;
    let sender = e.srcElement || e.target;
    if($(sender.parentElement).height() != 18){
        $(sender.parentElement).animate({"height": 18});
        sender.textContent = "Ваш ответ: ...";
    }
    else{
        $(sender.parentElement).css('height', 'auto');
        let autoHeight = $(sender.parentElement).height();
        $(sender.parentElement).height(18).animate({height: autoHeight});
        sender.textContent = "Ваш ответ:";
    }
}

function openUserRequest(e){
    if (!e)
        e = window.event;
    let sender = e.srcElement || e.target;
    if($(sender.parentElement).height() != 18){
        $(sender.parentElement).animate({"height": 18});
        sender.textContent = "Ваше сообщение: ...";
    }
    else{
        $(sender.parentElement).css('height', 'auto');
        let autoHeight = $(sender.parentElement).height();
        $(sender.parentElement).height(18).animate({height: autoHeight});
        sender.textContent = "Ваше сообщение:";
    }
}

//#endregion