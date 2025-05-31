
/* FUNCIONES GLOBALES COOKIES */

window.getCookie = function(name) {
    const value = "; " + document.cookie;
    const parts = value.split("; " + name + "=");
    if (parts.length === 2) return parts.pop().split(";").shift();
}

window.setCookie = function(name, value, expiresSeconds) {
    let expires = "";
    if (expiresSeconds) {
        const date = new Date();
        date.setTime(date.getTime() + expiresSeconds * 1000);
        expires = "; expires=" + date.toUTCString();
    }
    document.cookie = name + "=" + value + expires + "; path=/";
}