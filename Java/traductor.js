(function() {
    const script = document.createElement('script');
    script.src = "https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit";
    script.async = true;
    document.body.appendChild(script);
})();

function googleTranslateElementInit() {
    new google.translate.TranslateElement({
        pageLanguage: 'es',
        includedLanguages: 'es,ca,eu,gl,en,fr,it,pt,de,ar,ru',
        layout: google.translate.TranslateElement.InlineLayout.SIMPLE,
        autoDisplay: false
    }, 'google_translate_element');
}

function setGoogleTranslateLang(lang) {
    if (!lang) return;
    document.cookie = "googtrans=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
    const cookieValue = `/es/${lang}`;
    let cookieString = `googtrans=${cookieValue}; path=/; SameSite=Lax; max-age=86400`;
    if (location.hostname !== 'localhost' && location.hostname !== '127.0.0.1') {
        cookieString += `; domain=.${location.hostname}`;
    }
    document.cookie = cookieString;
    window.location.reload();
}

document.addEventListener("DOMContentLoaded", () => {
    const select = document.getElementById("custom-language-select");
    if (!select) return;
    const match = document.cookie.match(/googtrans=\/es\/([a-z]{2})/);
    if (match) select.value = match[1];
    select.addEventListener("change", () => {
        const lang = select.value.trim();
        if (lang) setGoogleTranslateLang(lang);
    });
});