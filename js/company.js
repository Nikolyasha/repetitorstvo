function buyCompanyContacts(company_id){
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
                        "action": "OpenCompanyContacts",
                        "object_id": company_id,
                        "token": ctoken
                    },
                    success: (response) => {
                        console.log(response);
                        swal("Контакты успешно открыты", {
                            icon: "success",
                            buttons: false
                        });
                        setTimeout(() => {window.location = window.location + "?contacts";}, 1000);
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
        if(current_balance < company_price){
            swal("Недостаточно средств", "На вашем балансе не хватает " + (company_price - current_balance) + " монет", {
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
            text: "С вашего счета будут списаны " + company_price + " монет",
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
                        "action": "buyCompanyContacts",
                        "object_id": company_id,
                        "token": ctoken
                    },
                    success: (response) => {
                        console.log(response);
                        swal("Успешно оплачено", {
                            icon: "success",
                            buttons: false
                        });
                        setTimeout(() => {window.location = window.location + "?contacts";}, 1000);
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

function showCompanyDescTab(){
    $(".company_desc_contact")[0].style.display = "none";
    $(".company_desc_text_block")[0].style.display = "block";
    $(".company_info__button")[0].style.background = "#EAE8E8";
    $(".company_info__button")[1].style.background = "#F2F2F2";
}

function showCompanyContactTab(){
    $(".company_desc_contact")[0].style.display = "block";
    $(".company_desc_text_block")[0].style.display = "none";
    $(".company_info__button")[0].style.background = "#F2F2F2";
    $(".company_info__button")[1].style.background = "#EAE8E8";
}

function showFullDescription(){
    $(".company_desc_text")[0].style.maxHeight = "800px";
    $("#company_desc_readmore_button")[0].style.display = "none";
    $("#company_desc_hide_button")[0].style.display = "block";
}

function hideFullDescription(){
    $(".company_desc_text")[0].style.maxHeight = "150px";
    $("#company_desc_readmore_button")[0].style.display = "block";
    $("#company_desc_hide_button")[0].style.display = "none";
}

$(document).ready(() => {
    if($(".company_desc_text")[0].clientHeight >= 150){
        $(".company_desc_text")[0].style.maxHeight = "150px";
    }
    else{
        $("#company_desc_readmore_button")[0].style.display = "none";
    }
    
});


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