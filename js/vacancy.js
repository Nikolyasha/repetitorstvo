// for filter photo
function showPopupFilter(index, name, photosFilter){

    // photosFilter = photosFilter.split(",");
    

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
    // alert("AAA)"+photosFilter);
    // photosFilter = photosFilter.split(",");
    if (photosFilter[photosFilter.length - 1].includes('/')) photosFilter.splice(photosFilter.length - 1, photosFilter.length - 1);
    currPhoto++;
    if(currPhoto > (photosFilter.length - 1)) currPhoto = 0;
    $("#popup_imgFilter")[0].src = "/img/filter_photos/" + photosFilter[currPhoto];
    $("#modal_photo_idFilter")[0].textContent = "Фото " + (currPhoto + 1) + " из " + photosFilter.length;
}

function previousPhotoFilter(){
    // photosFilter = photosFilter.split(",");
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