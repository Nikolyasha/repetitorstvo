var xhr = new XMLHttpRequest();
xhr.open('GET', '/right_banner.frame', false);
xhr.send();
try{
    document.getElementById("right_banner").innerHTML = xhr.responseText;
} catch(err){}

xhr = new XMLHttpRequest();
xhr.open('GET', '/top_banner.frame', false);
xhr.send();
try{
    document.getElementById("top_banner").innerHTML = xhr.responseText;
} catch(err){}