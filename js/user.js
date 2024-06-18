let currentPhoto = 0;

function buyUserContacts(user_id){
    
    if (payment_active == 'false'){        
        swal({
            title: "Посмотреть контактные данные?",
            // text: "С вашего счета будут списаны " + vacancy_price + " монет",
            icon: "warning",
            buttons: {
                cancel: {
                    text: "Отмена",
                    value: null,
                    visible: true,
                    className: "",
                    closeModal: true,
                },
                confirm: {
                    text: "Ок",
                    value: true,
                    visible: true,
                    className: "",
                    closeModal: false
                }
            },
            dangerMode: true,
        })
        .then((confirm) => {
            if (confirm) {
                $.ajax({
                    method: "POST",
                    url: "/lk/api.php",
                    data: {
                        "action": "OpenUserContacts",
                        "object_id": user_id,
                        "token": ctoken
                    },
                    success: (response) => {
                        console.log(response);
                        swal("Контакты успешно открыты", {
                            icon: "success",
                            buttons: false
                        });
                        setTimeout(() => {window.location = "";}, 1000);
                    },
                    error: (response) => {
                        swal("Произошла ошибка", "Попробуйте повторить позднее", {
                            icon: "error"
                        });
                    }
                })
            }
        });                
    }
    else {
        if(current_balance < vacancy_price){        
            swal("Недостаточно средств", "На вашем балансе не хватает " + (vacancy_price - current_balance) + " монет", {
                icon: "error",
                buttons: {
                    cancel: {
                        text: "Отмена",
                        value: null,
                        visible: true,
                        className: "",
                        closeModal: true,
                    },
                    confirm: {
                        text: "Пополнить счет",
                        value: true,
                        visible: true,
                        className: "",
                        closeModal: false
                    }
                }
            }).then((confirm) => {
                if(confirm){
                    window.location = "/lk/buy.php";
                }
            });
            return;
        }
        swal({
            title: "Подтверждение покупки",
            text: "С вашего счета будут списаны " + vacancy_price + " монет",
            icon: "warning",
            buttons: {
                cancel: {
                    text: "Отмена",
                    value: null,
                    visible: true,
                    className: "",
                    closeModal: true,
                },
                confirm: {
                    text: "Оплатить",
                    value: true,
                    visible: true,
                    className: "",
                    closeModal: false
                }
            },
            dangerMode: true,
        })
        .then((confirm) => {
            if (confirm) {
                $.ajax({
                    method: "POST",
                    url: "/lk/api.php",
                    data: {
                        "action": "buyUserContacts",
                        "object_id": user_id,
                        "token": ctoken
                    },
                    success: (response) => {
                        console.log(response);
                        swal("Успешно оплачено", {
                            icon: "success",
                            buttons: false
                        });
                        setTimeout(() => {window.location = "";}, 1000);
                    },
                    error: (response) => {
                        swal("Произошла ошибка", "Попробуйте повторить позднее", {
                            icon: "error"
                        });
                    }
                })
            }
        });
    }
}

function showPopup(index){       
	if(index > (photos.length - 1)) index = 0;
    $("#popup_img")[0].src = "/img/avatars/" + photos[index];    
    currentPhoto = index;
    $("#modal_photo_id")[0].textContent = "Фото " + (currentPhoto + 1) + " из " + photos.length;
    $("#popup").css("display", "flex");
    $("#popup").animate({"opacity": 1});
}

function hidePopup(){
    if($("#popup").css("opacity") == "1"){
        $("#popup").animate({"opacity": 0}, () => {
            $("#popup").css("display", "none");
        });
    }
    if($("#popupFilter").css("opacity") == "1"){
        $("#popupFilter").animate({"opacity": 0}, () => {
            $("#popupFilter").css("display", "none");
        });
    }
}

function nextPhoto(){
	currentPhoto++;
	if(currentPhoto > (photos.length - 1)) currentPhoto = 0;
	$("#popup_img")[0].src = "/img/avatars/" + photos[currentPhoto];
	$("#modal_photo_id")[0].textContent = "Фото " + (currentPhoto + 1) + " из " + photos.length;
}

function previousPhoto(){
	currentPhoto--;
	if(currentPhoto < 0) currentPhoto = (photos.length - 1);
	$("#popup_img")[0].src = "/img/avatars/" + photos[currentPhoto];
	$("#modal_photo_id")[0].textContent = "Фото " + (currentPhoto + 1) + " из " + photos.length;
}

// for filter photo
function showPopupFilter(index, name, photosFilter){

    if (photosFilter[photosFilter.length - 1].includes('/')) photosFilter.splice(photosFilter.length - 1, photosFilter.length - 1);
                     
    if(index > (photosFilter.length - 1)) index = 0;
    $("#popup_imgFilter")[0].src = "/img/filter_photos/" + photosFilter[index];
    currPhoto = index;
    $("#modal_photo_idFilter")[0].textContent = "Фото " + (currPhoto + 1) + " из " + photosFilter.length;
    $("#popupFilter").css("display", "flex");
    $("#popupFilter").animate({"opacity": 1});
    $("#txtHeader")[0].innerHTML = "Фотографии " + name;        
}

function nextPhotoFilter(){
    if (photosFilter[photosFilter.length - 1].includes('/')) photosFilter.splice(photosFilter.length - 1, photosFilter.length - 1);
	currPhoto++;
	if(currPhoto > (photosFilter.length - 1)) currPhoto = 0;
	$("#popup_imgFilter")[0].src = "/img/filter_photos/" + photosFilter[currPhoto];
	$("#modal_photo_idFilter")[0].textContent = "Фото " + (currPhoto + 1) + " из " + photosFilter.length;
}

function previousPhotoFilter(){
    if (photosFilter[photosFilter.length - 1].includes('/')) photosFilter.splice(photosFilter.length - 1, photosFilter.length - 1);
	currPhoto--;
	if(currPhoto < 0) currPhoto = (photosFilter.length - 1);
	$("#popup_imgFilter")[0].src = "/img/filter_photos/" + photosFilter[currPhoto];
	$("#modal_photo_idFilter")[0].textContent = "Фото " + (currPhoto + 1) + " из " + photosFilter.length;
}

$(document).keyup(function(e) {
    if (e.key === "Escape") { // escape key maps to keycode `27`
	    hidePopup();
	}
});