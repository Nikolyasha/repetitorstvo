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

$(document).keyup(function(e) {
    if (e.key === "Escape") { // escape key maps to keycode `27`
	    hidePopup();
	}
});