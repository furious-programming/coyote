var elixir = require('laravel-elixir');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Less
 | file for our application, as well as publishing vendor resources.
 |
 */

elixir(function(mix) {

    var base = [
        'jquery-last.min.js',
        'bootstrap/tooltip.js',
        'bootstrap/dropdown.js',// menu jest na kazdej stronie (dla uzytkownikow zalogowanych)
        'bootstrap/collapse.js', // to musi sie znajdowac na kazdej podstronie (zwijanie menu dla urzadzen mobilnych)
        'components/state.js',
        'components/declination.js',
        'components/date.js',
        'components/notifications.js',
        'components/session.js',
        'components/vcard.js',
        'main.js'
    ];

    mix.scripts(base, 'public/js/main.js')
        // strona glowna
       //.scripts(['bootstrap/tab.js', 'vendor/perfect-scrollbar.js'], 'public/js/homepage.js')
        // forum
       .scripts(['vendor/animate-colors.js', 'bootstrap/modal.js', 'pages/forum.js'], 'public/js/forum.js')
        // mikroblogi
       .scripts(['vendor/animate-colors.js', 'pages/microblog.js'], 'public/js/microblog.js')
        // strona profilu uzytkownika
       //.scripts(['pages/profile.js'], 'public/js/profile.js')
        // komponent uzywany przy publikowaniu tekstu. laczy ze soba pluginy, np. dynamicznie zmieniajace
        // rozmiar pola textarea, czy tez podpowiadajacy login uzytkownika w tekscie
       .scripts(['components/prompt.js', 'components/autogrow.js', 'components/fast-submit.js'], 'public/js/posting.js')
       .scripts(['components/wikieditor.js'], 'public/js/wikieditor.js')
        // auto complete. uzywany m.in. w podczas pisania wiadomosci, czy tez ustalania umiejetnosci
       .scripts(['components/auto-complete.js'], 'public/js/auto-complete.js')
        // komponent z mozliwoscia wyboru tagow
       .scripts(['components/tags.js'], 'public/js/tags.js')
        // komponent bootstrapa umozliwiajacy wyswietlanie statycznych dymkow
       .scripts(['bootstrap/popover.js'], 'public/js/popover.js')
        // uzywane na niewielu stronach. tam gdzie trzeba przelaczac sie miedzy zakladkami
       .scripts(['bootstrap/tab.js'], 'public/js/tab.js')
        // okna modalne, tj. wyswietlanie komunikatow - np. zapytanie czy na pewno usunac post
       .scripts(['bootstrap/modal.js'], 'public/js/modal.js')
       .scripts(['vendor/perfect-scrollbar.js'], 'public/js/perfect-scrollbar.js')
       .scripts(['vendor/animate-colors.js'], 'public/js/animate-colors.js')
       .scripts(['vendor/ui-resizer.js'], 'public/js/ui-resizer.js')
       .scripts(['vendor/jquery-ui.js'], 'public/js/jquery-ui.js');

    mix.sass('main.scss')
       .sass('pages/auth.scss')
       .sass('pages/homepage.scss')
       .sass('pages/microblog.scss')
       .sass('pages/forum.scss')
       .sass('pages/wiki.scss')
       .sass('pages/user.scss')
       .sass('pages/profile.scss')
       .sass('pages/job.scss')
       .sass('vendor/wikieditor.scss');
});

