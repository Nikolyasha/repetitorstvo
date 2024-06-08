const re = /^(([^<>()[\]\.,;:\s@\"]+(\.[^<>()[\]\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()[\]\.,;:\s@\"]+\.)+[^<>()[\]\.,;:\s@\"]{2,})$/i;

function verifryRegForm(){
    let form = document.forms[0];

    if(form.passwd.value.length < 6){
        renderError("Пароль должен быть не короче 6 символов");
        return;
    }
    if(form.passwd.value != form.passwd_confirm.value){
        renderError("Пароли не совпадают");
        return;
    }
    if(form.name.value == 0){
        renderError("Введите ваше имя");
        return;
    }
    if(form.name.value.trim().split(" ").length < 2){
        renderError("Введите имя и фамилию");
        return;
    }
    if(!re.test(form.mail.value)){
        renderError("Введите корректную почту");
        return;
    }
    
    if(form.confirm_politics.checked){
        form.submit();
    }
    else{
        renderError("Вы должны принять пользовательское соглашение");
        return;
    }
}

function sendActivation(token){
    document.getElementById("send_email").onclick = () => { swal("Вы уже запрашивали повторную отправку письма"); };
    document.getElementById("send_email").textContent = "Отправка письма...";
    var req = new XMLHttpRequest();
    req.open("POST", "/activation.php?retoken=" + token);
    req.addEventListener("readystatechange", () => {
        document.getElementById("send_email").textContent = "Ошибка";
        if(req.responseText == "OK"){
            document.getElementById("send_email").textContent = "Письмо успешно отправленно";
            swal("Успех", "Письмо успешно отправленно", "success");
        } else {
            swal("Ошибка", "Письмо не может быть отправленно, свяжитесь с администрацией", "error");
        }
    });
    req.send();
}