function setFavoriteVacancy(event, vacancy_id){
    if(event.classList.contains("unactive_favorite")){
        $.ajax({
            url: "/lk/favorite.php",
            method: "POST",
            data : {
                action: "addFavorite",
                vacancy_id: vacancy_id,
                token: ctoken
            },
            success: (response) => {
                event.classList.add("active_favorite");
                event.classList.remove("unactive_favorite");
            }
        });
    }
    else{
        $.ajax({
            url: "/lk/favorite.php",
            method: "POST",
            data : {
                action: "removeFavorite",
                vacancy_id: vacancy_id,
                token: ctoken
            },
            success: (response) => {
                event.classList.add("unactive_favorite");
                event.classList.remove("active_favorite");
            }
        });
    }
}

function setFavoriteAnket(event, anket_id){
    if(event.classList.contains("unactive_favorite")){
        $.ajax({
            url: "/lk/favorite.php",
            method: "POST",
            data : {
                action: "addFavorite",
                anket_id: anket_id,
                token: ctoken
            },
            success: (response) => {
                event.classList.add("active_favorite");
                event.classList.remove("unactive_favorite");
            }
        });
    }
    else{
        $.ajax({
            url: "/lk/favorite.php",
            method: "POST",
            data : {
                action: "removeFavorite",
                anket_id: anket_id,
                token: ctoken
            },
            success: (response) => {
                event.classList.add("unactive_favorite");
                event.classList.remove("active_favorite");
            }
        });
    }
}