function setOnline(){
    $.ajax({
        method: "POST",
        url: "/lk/api.php",
        data: {
            "action": "setOnline",
            "token": ctoken
        }, 
        success: (resp) => {
            console.log(resp);
        }
    });
}

$(document).ready(() => {
    setOnline();
    setInterval(setOnline, 60000); 
});