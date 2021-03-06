<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', ['as' => 'home', 'uses' => 'HomeController@index']);

// logowanie uzytkownika
Route::get('Login', ['uses' => 'Auth\LoginController@index', 'as' => 'login']);
Route::post('Login', 'Auth\LoginController@signin');
// wylogowanie
Route::get('Logout', ['uses' => 'Auth\LoginController@signout', 'as' => 'logout']);

// rejestracja uzytkownika
Route::get('Register', ['uses' => 'Auth\RegisterController@index', 'as' => 'register']);
Route::post('Register', 'Auth\RegisterController@signup');

// przypominanie hasla
Route::controller('Password', 'Auth\PasswordController');


Route::group(['namespace' => 'Forum', 'prefix' => 'Forum', 'as' => 'forum.'], function () {
    // strona glowna forum
    Route::get('/', ['uses' => 'HomeController@index', 'as' => 'home']);
    Route::post('/Preview', ['uses' => 'HomeController@preview', 'as' => 'preview']);

    // formularz dodawania nowego watku na forum
    Route::get('{forum}/Submit', ['uses' => 'TopicController@submit', 'as' => 'topic.submit', 'middleware' => 'forum.access']);
    Route::post('{forum}/Submit', ['uses' => 'TopicController@save', 'middleware' => 'forum.access']);

    // dodawanie lub edycja posta na forum
    Route::get('{forum}/{topic}/Submit/{post?}', ['uses' => 'PostController@submit', 'as' => 'post.submit', 'middleware' => 'forum.access']);
    Route::post('{forum}/{topic}/Submit/{post?}', ['uses' => 'PostController@save', 'middleware' => 'forum.access']);

    // widok kategorii forum
    Route::get('{forum}', ['uses' => 'CategoryController@index', 'as' => 'category', 'middleware' => 'forum.access']);
    // widok wyswietlania watku. {topic}
    Route::get('{forum}/{topic}-{slug}', ['uses' => 'TopicController@index', 'as' => 'topic', 'middleware' => 'forum.access']);

    // usuwanie posta
    Route::post('Delete/{id}', ['uses' => 'PostController@delete', 'as' => 'delete', 'middleware' => 'auth']);

    // blokowanie watku
    Route::post('Lock/{id}', ['uses' => 'TopicController@lock', 'as' => 'lock', 'middleware' => 'auth']);

    // edycja/publikacja komentarza oraz jego usuniecie
    Route::post('Comment/{id?}', ['uses' => 'CommentController@save', 'as' => 'comment.save', 'middleware' => 'auth']);
    Route::get('Comment/{id}', ['uses' => 'CommentController@edit', 'middleware' => 'auth']);
    Route::post('Comment/Delete/{id}', ['uses' => 'CommentController@delete', 'as' => 'comment.delete', 'middleware' => 'auth']);
    // pokaz reszte komentarzy...
    Route::get('Comment/Show/{id}', ['uses' => 'CommentController@show', 'as' => 'comment.show']);

    Route::get('/Tag/{tag}', ['uses' => 'HomeController@tag', 'as' => 'tag']);
});

Route::get('Praca', ['uses' => 'Job\HomeController@index', 'as' => 'job.home']);

/*
 * Tymczasowe reguly
 */
Route::get('/Delphi', ['as' => 'page', 'uses' => 'Wiki\WikiController@category']);
Route::get('/Delphi/Lorem_ipsum', ['as' => 'article', 'uses' => 'Wiki\WikiController@article']);
Route::get('Praca/Lorem_ipsum', ['uses' => 'Job\OfferController@index', 'as' => 'job.offer']);

// Obsluga mikroblogow
Route::group(['namespace' => 'Microblog', 'prefix' => 'Mikroblogi', 'as' => 'microblog.'], function () {
    Route::get('/', ['uses' => 'HomeController@index', 'as' => 'home']);
    Route::post('Edit/{id?}', ['uses' => 'SubmitController@save', 'as' => 'save', 'middleware' => 'auth']);
    Route::get('Edit/{id}', ['uses' => 'SubmitController@edit', 'middleware' => 'auth']);

    Route::post('Upload', ['uses' => 'SubmitController@upload', 'as' => 'upload', 'middleware' => 'auth']);
    Route::get('View/{id}', ['uses' => 'ViewController@index', 'as' => 'view']);
    Route::post('Vote/{id}', ['uses' => 'VoteController@post', 'as' => 'vote', 'middleware' => 'auth']);
    Route::get('Vote/{id}', ['uses' => 'VoteController@voters', 'as' => 'voters']);
    Route::post('Watch/{id}', ['uses' => 'WatchController@post', 'as' => 'watch', 'middleware' => 'auth']);
    Route::post('Delete/{id}', ['uses' => 'SubmitController@delete', 'as' => 'delete', 'middleware' => 'auth']);

    // edycja/publikacja komentarza oraz jego usuniecie
    Route::post('Comment/{id?}', ['uses' => 'CommentController@save', 'as' => 'comment.save', 'middleware' => 'auth']);
    Route::get('Comment/{id}', ['uses' => 'CommentController@edit', 'middleware' => 'auth']);
    Route::post('Comment/Delete/{id}', ['uses' => 'CommentController@delete', 'as' => 'comment.delete', 'middleware' => 'auth']);
    // pokaz reszte komentarzy...
    Route::get('Comment/Show/{id}', ['uses' => 'CommentController@show', 'as' => 'comment.show']);

    Route::get('/Mine', ['uses' => 'HomeController@mine', 'as' => 'mine']);
    Route::get('/{tag}', ['uses' => 'HomeController@tag', 'as' => 'tag']);
});

// Obsluga modulu pastebin
Route::get('Pastebin', ['uses' => 'Pastebin\HomeController@index', 'as' => 'pastebin.home']);
Route::get('Pastebin/{id}', ['uses' => 'Pastebin\HomeController@show', 'as' => 'pastebin.show'])->where('id', '\d+');
Route::post('Pastebin', ['uses' => 'Pastebin\HomeController@save']);

Route::group(['namespace' => 'User', 'prefix' => 'User', 'middleware' => 'auth', 'as' => 'user.'], function () {

    // strona glowna panelu uzytkownika
    Route::get('/', ['uses' => 'HomeController@index', 'as' => 'home']);
    // dodawanie i usuwanie zdjecia uzytkownika
    Route::post('Photo/Upload', ['uses' => 'HomeController@upload', 'as' => 'photo.upload']);
    Route::post('Photo/Delete', ['uses' => 'HomeController@delete', 'as' => 'photo.delete']);

    // ustawienia uzytkownika
    Route::get('Settings', ['uses' => 'SettingsController@index', 'as' => 'settings']);
    Route::post('Settings', 'SettingsController@save');

    Route::get('Visits', ['uses' => 'VisitsController@index', 'as' => 'visits']);
    Route::post('Visits', 'VisitsController@save');

    Route::get('Alerts', ['uses' => 'AlertsController@index', 'as' => 'alerts']);
    Route::get('Alerts/Settings', ['uses' => 'AlertsController@settings', 'as' => 'alerts.settings']);
    Route::post('Alerts/Settings', 'AlertsController@save');
    Route::get('Alerts/Ajax', 'AlertsController@ajax');
    Route::post('Alerts/Mark/{id?}', 'AlertsController@markAsRead');
    Route::post('Alerts/Delete/{id}', 'AlertsController@delete');

    Route::get('Pm', ['uses' => 'PmController@index', 'as' => 'pm']);
    Route::get('Pm/Show/{id}', ['uses' => 'PmController@show', 'as' => 'pm.show']);
    Route::get('Pm/Submit', ['uses' => 'PmController@submit', 'as' => 'pm.submit']);
    Route::post('Pm/Submit', 'PmController@save');
    Route::post('Pm/Delete/{id}', 'PmController@delete')->name('pm.delete');
    Route::post('Pm/Preview', 'PmController@preview')->name('pm.preview');
    Route::get('Pm/Ajax', 'PmController@ajax')->name('pm.ajax');

    Route::get('Favorites', ['uses' => 'FavoritesController@index', 'as' => 'favorites']);
    Route::post('Favorites', 'FavoritesController@save');

    Route::get('Profiles', ['uses' => 'ProfilesController@index', 'as' => 'profiles']);
    Route::post('Profiles', 'ProfilesController@save');

    Route::get('Rates', ['uses' => 'RatesController@index', 'as' => 'rates']);
    Route::post('Rates', 'RatesController@save');

    Route::get('Stats', ['uses' => 'StatsController@index', 'as' => 'stats']);
    Route::post('Stats', 'StatsController@save');

    Route::get('Accepts', ['uses' => 'AcceptsController@index', 'as' => 'accepts']);
    Route::post('Accepts', 'AcceptsController@save');

    Route::get('Skills', ['uses' => 'SkillsController@index', 'as' => 'skills']);
    Route::post('Skills', 'SkillsController@save');
    Route::post('Skills/Order', ['uses' => 'SkillsController@order', 'as' => 'skills.order']);
    Route::post('Skills/{id}', ['uses' => 'SkillsController@delete', 'as' => 'skills.delete']);

    Route::get('Security', ['uses' => 'SecurityController@index', 'as' => 'security']);
    Route::post('Security', 'SecurityController@save');

    Route::get('Password', ['uses' => 'PasswordController@index', 'as' => 'password']);
    Route::post('Password', 'PasswordController@save');

    Route::get('Forum', ['uses' => 'ForumController@index', 'as' => 'forum']);
    Route::post('Forum', 'ForumController@save');

    // Generowanie linka potwierdzajacego autentycznosc adresu e-mail
    Route::get('Confirm', ['uses' => 'ConfirmController@index', 'as' => 'confirm']);
    Route::post('Confirm', ['uses' => 'ConfirmController@send']);
});

// ta regula nie moze sprawdzac czy user jest zalogowany, czy nie. user moze potwierdzic adres e-mail
// niekoniecznie bedac zalogowanym
Route::get('User/Confirm/Email', ['uses' => 'User\ConfirmController@email', 'as' => 'user.email']);
// wizytowka usera. komponent ktory pojawia sie po naprowadzenia kursora nad login usera
Route::get('User/Vcard/{id}', ['uses' => 'User\VcardController@index', 'as' => 'user.vcard']);
// zadanie AJAX z lista loginow (podpowiedzi)
Route::get('User/Prompt', ['uses' => 'User\PromptController@index', 'as' => 'user.prompt']);

// dostep do panelu administracyjnego
Route::group(['namespace' => 'Adm', 'middleware' => ['auth', 'adm'], 'prefix' => 'Adm'], function () {
    Route::get('/', 'HomeController@index');
});

Route::get('Profile/{user}', ['uses' => 'Profile\HomeController@index', 'as' => 'profile']);
Route::get('Tag/Prompt', ['uses' => 'Tag\PromptController@index', 'as' => 'tag.prompt']);
Route::get('Tag/Validate', ['uses' => 'Tag\PromptController@valid', 'as' => 'tag.validate']);
Route::get('Flag', ['uses' => 'FlagController@index', 'as' => 'flag', 'middleware' => 'auth']);
Route::post('Flag', ['uses' => 'FlagController@save', 'middleware' => 'auth']);

Route::get('/{slug}', function ($slug) {
    echo "404 $slug";

})->where('slug', '.*');