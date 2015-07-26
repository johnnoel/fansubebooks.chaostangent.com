WebFontConfig = {
    custom: {
        families: ['Vegur:n7'],
        urls: ['/css/fonts.css']
    }
};

(function(d) {
    var wf = d.createElement('script'), s = d.scripts[0];
    wf.src = 'https://ajax.googleapis.com/ajax/libs/webfont/1.5.18/webfont.js';
    s.parentNode.insertBefore(wf, s);
})(document);
