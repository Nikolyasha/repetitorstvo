let form = document.forms[0];
function checkPass(){
    if(!form.checkValidity()){
        swal("Форма заполнена некорректно");
        return;
    }
    form.submit();
}
function sleep(milliseconds) {
    const date = Date.now();
    let currentDate = null;
    do {
      currentDate = Date.now();
    } while (currentDate - date < milliseconds);
  }
function bm_removePhoto(event){
    swal({
        title: "Вы уверены?",
        text: "Вы действительно хотите удалить эту фотографию?",
        icon: "error",
        showCancelButton: true,
        dangerMode: true,
        buttons: ["Отмена", "Удалить"],
        closeOnConfirm: false,
        showLoaderOnConfirm: true
    }).then(function(result){
        if(!result) return;
        console.log(event.parentElement.parentElement.parentElement);
        $.ajax({
            url: "/admin/edit_anket.php",
            method: "POST",
            data: {
                "id": current_user_id,
                "action": "remove_photo",
                "photo_id": +event.parentElement.parentElement.parentElement.attributes['photo-id'].value,
                "token": ctoken
            },
            success: () => {
                let photoList = [];
                event.parentElement.parentElement.parentElement.parentElement.removeChild(event.parentElement.parentElement.parentElement);
                Array.prototype.slice.call(
                    document.getElementById("uploaded_files").getElementsByTagName("li")
                ).forEach(el => {
                    try{
                        photoList.push(el.attributes['photo-id'].value);
                    } catch(e){}
                });
                form.photos.value = photoList.join(";");
                swal.close();    
            }
        })
    });
}

$('#bm_anket_photos_input').change(function() {

    // Валидация
    let exit = false;
    let bm_files = Array.prototype.slice.call($("#bm_anket_photos_input")[0].files);
    if(form.photos.value.split(",").length >= max_photos){
        alert("Вы достигли ограничения файлов");
        swal("Вы достигли ограничения файлов", "Можно загрузить до " + max_photos + " фотографий", "warning");
        $('#bm_anket_photos')[0].reset();
        return;
    }
    bm_files.forEach(file => {
        if(file['type'].split("/")[0] != "image"){
            swal("Файл '" + file['name'] + "' не является изображением", "Загружайте только изображения", "warning");
            $('#bm_anket_photos')[0].reset();
            exit = true;
            return;
        }
        if(file['size'] > 5242880){
            swal("Файл '" + file['name'] + "' больше 5 МБ", "Размер загружаемого файла не должен привышать 5 мб", "warning");
            $('#bm_anket_photos')[0].reset();
            exit = true;
            return;
        }
    });

    if(exit) return;

    // Вывод статуса загрузки
    let bm_task_files = [];
    let bm_file_list = $("#uploaded_files")[0];
    let bm_last_file_id = 1;
    if(bm_file_list.childElementCount > 0)
        bm_last_file_id = (+bm_file_list.children[bm_file_list.children.length-1].attributes['photo-id'].value) + 1;
    bm_files.forEach(file => {
        $("#uploaded_files")[0].innerHTML += '<li class="bm_uploaded_files__item" id="bm_file_id' + bm_last_file_id + '" photo-id=0>' +
                                                    '<div class="bm_file_uploading">' + file['name'] + '</div>' + 
                                                '</li>';
        bm_task_files.push({"id": 'bm_file_id' + bm_last_file_id, "name": file['name']});
        bm_last_file_id++;
    });
    $('#bm_anket_photos').ajaxSubmit({
        type: 'POST',
        url: '/admin/edit_anket.php',
        success: function(response) {
            for(let i = 0; bm_task_files.length > i; i++){
                let bm_file_label = $("#" + bm_task_files[i]['id'])[0];
                try{
                    if(response['error'] != undefined){
                        bm_file_label.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="red" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/><path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/></svg>';
                        bm_file_label.innerHTML += '<div class="bm_uploaded_files__item_name">' + bm_task_files[i]['name'] + ' (' + response['error'] + ')</div>';
                        swal("Ошибка загрузки файла", response['error'], "error");
                    }
                    else if(response[i]['result'] == "OK"){
                        if(form.photos.value == "")
                            form.photos.value += response[i]['file'];
                        else
                            form.photos.value += "," + response[i]['file'];
                        bm_file_label.innerHTML = '<a target="_blank" href="/img/avatars/' + response[i]['file'] + '"><img src="/img/avatars/' + response[i]['file'] + '?' + new Date().getTime() + '" class="bm_mini_photo"></a><div class="bm_uploaded_files__item_info"><div class="bm_uploaded_files__item_name">' + bm_task_files[i]['name'] + '</div><div style="margin-left: 5px;"><span onclick="bm_removePhoto(this);" class="bm_rm_button">Удалить</span></div></div>';
                        bm_file_label.setAttribute("photo-id", response[i]['photo_id']);
                        // bm_file_label.innerHTML += '<div class="bm_uploaded_files__item_name">' + bm_task_files[i]['name'] + '</div><div style="margin-left: 10px;"><span onclick="bm_removePhoto(this);" class="bm_rm_button">Удалить</span></div>';
                    }
                    else{
                        bm_file_label.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="red" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/><path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/></svg>';
                        bm_file_label.innerHTML += '<div class="bm_uploaded_files__item_name">' + bm_task_files[i]['name'] + ' (' + response[i]['result'] + ')</div>';
                        swal("Ошибка загрузки файла", response[i]['result'], "error");
                    }
                }
                catch(e){
                    bm_file_label.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="red" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/><path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/></svg>';
                    bm_file_label.innerHTML += '<div class="bm_uploaded_files__item_name">' + bm_task_files[i]['name'] + '</div>';
                }
            }
            $('#bm_anket_photos')[0].reset();
        }
    });
});