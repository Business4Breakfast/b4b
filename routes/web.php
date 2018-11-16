<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/


//Route::get('send_test_email', function(){
//    Mail::raw('Sending emails with Mailgun and Laravel is easy!', function($message)
//    {
//        $message->subject('Mailgun and Laravel are awesome!');
//        $message->from('no-reply@website_name.com', 'Website Name');
//        $message->to('marian@patak.sk');
//    });
//});


// routovanie obrazkov pre files zo storage
//Route::get('storage/{filename}', function ($filename)
//{
//    return Image::make(storage_path('public/' . $filename))->response();
//});

//Global routes
Route::get('lang/{lang}', ['as'=>'lang.switch', 'uses'=>'LanguageController@switchLang']);

Route::post('ajax/function', 'AjaxHelperController@getFunction')->name('ajax.helper.function')->middleware('auth');

Route::post('ajax/backend-menu-update', 'AjaxController@backendMenuUpdate')->name('ajax.backend-menu');
Route::post('ajax/check-vat-registration', 'AjaxController@checkVatRegistration')->name('ajax.vat-registration');
Route::post('ajax/get-company-data', 'AjaxController@getCompanyData')->name('ajax.get-company-data');
Route::post('ajax/get-club-data', 'AjaxController@getClubData')->name('ajax.get-club-data');
Route::post('ajax/get-user-data', 'AjaxController@getUserData')->name('ajax.get-user-data');
Route::post('ajax/get-guest-data', 'AjaxController@getGuestData')->name('ajax.get-guest-data')->middleware('auth');
Route::post('ajax/get-counties-in-district', 'AjaxController@getCountiesInDistrict')->name('ajax.get-counties-in-district');
Route::post('ajax/get-user-data-search', 'AjaxController@getUserDataSearch')->name('ajax.get-user-data-search');
Route::post('ajax/get-user-detail', 'AjaxController@getUserDetail')->name('ajax.get-user-detail');
Route::post('ajax/save-attendance-status', 'AjaxController@saveAttendanceStatus')->name('ajax.save-attendance-status');


// pozvanka hosta od clena
Route::get('/invite-guest/{user}', 'Events\EventInvitationController@userSendInvitationToGuest')->name('invite.user.guest');
// odoslanie zakladneho formulara pozvanky
Route::post('/invite-guest/store', 'Events\EventInvitationController@invitationStore')->name('invite.guest.store');

// pozvanka existujúceho hosta od clena
Route::get('/invite-exist-guest/{user}', 'Events\EventInvitationController@userSendInvitationToExistGuest')->name('invite.user.exist.guest');
// pozvanka clenov na udalost
Route::get('/invite-member/{event}', 'Events\EventInvitationController@eventSendInvitationToMember')->name('invite.event.member');


Route::post('/invite-guest-exist/store', 'Events\EventInvitationController@invitationExistStore')->name('invite.guest.exist.store');

Route::get('/invite-response/{attend}', 'Events\EventInvitationController@invitationResponse')->name('invite.guest.response');
Route::post('/invite-attendance-resend', 'Events\EventInvitationController@resendInvitationFromAttendanceStore')->name('invite.attendance.resend.store');


Auth::routes();

Route::get('/hGTfdaeAq7896jqA8JHd87126swdA', 'Developer\CronController@checkRequest')->name('cron.request');
Route::get('/cron', 'Developer\TestControler@cron')->name('cron');


//Route::get('/', 'Dashboard\IndexController@index')->name("main");
Route::get('/', 'Dashboard\IndexController@index')->name("dashboard.index");
Route::post('/', 'Dashboard\IndexController@index')->name("dashboard.search");


Route::group(['namespace' => 'Developer', 'prefix' => 'developer', 'as' => 'developer.', 'middleware' => ['auth']], function() {

    Route::get('/', 'BugReportController@index')->name('developer');

    Route::get('/test', 'TestControler@index')->name('test');

    // menu
    Route::post('/menu-store', 'MenuController@menuStore')->name('menu-add');
    Route::post('/menu-save', 'MenuController@menuSave')->name('menu-save');
    Route::post('/menu-delete', 'MenuController@menuDelete')->name('menu-delete');

    Route::get('/menu', 'MenuController@menu')->name('menu');
    Route::get('/menu-add', 'MenuController@menuAdd')->name('menu-add');

    Route::get('/menu-edit/{id}', 'MenuController@menuEdit')->name('menu-edit');
    Route::get('/menu-reorder', 'MenuController@menuReorder')->name('menu-reorder');


    Route::get('/role', 'RoleController@role')->name('role');
    Route::get('/role/add', 'RoleController@add')->name('role.add');
    Route::get('/role/edit/{id}', 'RoleController@edit')->name('role.edit');
    Route::post('/role/save', 'RoleController@save')->name('role.save');
    Route::post('/role/create', 'RoleController@create')->name('role.create');
    Route::post('/role/destroy', 'RoleController@destroy')->name('role.destroy');
    Route::get('/role/permissions/{id_role}', 'RoleController@permissionsGet')->name('role.permissions');
    Route::post('/role/permission/save', 'RoleController@permissionsSave')->name('role.permissions.save');

    Route::get('/permission', 'PermissionController@permission')->name('permission');
    Route::get('/permission/add', 'PermissionController@add')->name('permission.add');
    Route::get('/permission/edit/{id}', 'PermissionController@edit')->name('permission.edit');
    Route::post('/permission/save', 'PermissionController@save')->name('permission.save');
    Route::post('/permission/create', 'PermissionController@create')->name('permission.create');
    Route::post('/permission/destroy', 'PermissionController@destroy')->name('permission.destroy');

    // bug report
    Route::resource('/bug-report', 'BugReportController');

    //system log
    Route::resource('/sys-log', 'SystemLogController');

    // email notification
    Route::resource('/email-notifications', 'EmailNotificationController');
    Route::get('/email-notifications-send', 'EmailNotificationController@sendEmail')->name('email.notification.send');

    //route for artiaan command
    Route::get('artisan/{action}', 'ArtisanCommandController@getAction')->name('artisan.action');

});



Route::group(['namespace' => 'Setting', 'prefix' => 'setting', 'as' => 'setting.', 'middleware' => ['auth']], function() {

    Route::get('/user/profile/', 'UserController@profileLogged')->name('user.profile');
    Route::get('/user/profile-edit/{id_user}', 'UserController@profileEdit')->name('user.profile.edit');

    // zmena nalogovaneho usera pre superadmina
    Route::post('/change-user-login', 'UserController@changeUserLogin')->name('change.user.login');

    Route::resource('/user', 'UserController');
    Route::get('/user/permissions/{id_user}', 'UserController@permissionsGet')->name('user.permissions');
    Route::post('/user/permission/save', 'UserController@permissionsSave')->name('user.permissions.save');

    Route::put('/user/profile/save', 'UserController@profileSave')->name('user.profile.save');


    Route::get('/profile/profile/', 'ProfileController@profileLogged')->name('profile.profile');
    Route::resource('/profile', 'ProfileController');
    Route::get('/profile/permissions/{id_user}', 'ProfileController@permissionsGet')->name('profile.permissions');
    Route::post('/profile/permission/save', 'ProfileController@permissionsSave')->name('profile.permissions.save');

    Route::resource('/company', 'CompanyController');
    Route::resource('/membership', 'MembershipController');
    Route::resource('/club', 'ClubController');
    Route::resource('/club-member', 'ClubMembersController');
    Route::resource('/club-breakfast', 'ClubBreakfastController');
    Route::resource('/member-stats', 'MemberStatsController');

    Route::get('/membership/payment/{id_user}', 'MembershipController@payment')->name('membership.payment');
    Route::post('/membership/payment/store', 'MembershipController@paymentStore')->name('membership.payment.store');

    //predlzenie clenstva
    Route::get('/membership/renewal/{membership}', 'MembershipController@renewalMembership')->name('membership.renewal');
    Route::post('/membership/renewal/store', 'MembershipController@renewalMembershipStore')->name('membership.renewal.store');


    Route::get('/club-breakfast/ticket/{breakfast}', 'ClubBreakfastController@ticket')->name('club-breakfast-ticket');
    Route::get('/club-breakfast/attendance/{breakfast}', 'ClubBreakfastController@attendance')->name('club-breakfast-attendance');


    //ciselníky
    Route::resource('/interest', 'InterestController');
    Route::put('/interest/active/{industry}', 'InterestController@active')->name('interest.active');

    Route::resource('/industry', 'IndustryController');
    Route::put('/industry/active/{industry}', 'IndustryController@active')->name('industry.active');

    Route::resource('/event-type', 'EventTypeListController');
    Route::put('/event-type/active/{id}', 'EventTypeListController@active')->name('event-type.active');

    Route::resource('/event-type-text', 'EventTypeTextListController');
    Route::put('/event-type-text/active/{id}', 'EventTypetextListController@active')->name('event-type-text.active');


    Route::resource('/event-activity', 'EventActivityListController');
    Route::put('/event-activity/active/{id}', 'EventActivityListController@active')->name('event-activity.active');

    Route::resource('/reference-ticket', 'ReferencesTicketController');
    Route::put('/reference-ticket/active/{industry}', 'ReferencesTicketController@active')->name('reference-ticket.active');

    // statusy crm
    Route::resource('/crm-status', 'CrmStatusListController');
    Route::put('/reference-ticket/active/{industry}', 'CrmStatusListController@active')->name('crm-status.active');

    // statusy prijate faktury
    Route::resource('/invoice-income-type', 'InvoiceIncomeTypeListController');
    Route::put('/invoice-income-type/active/{type}', 'InvoiceIncomeTypeListController@active')->name('invoice-income-type.active');

    // statusy pokladny
    Route::resource('/cash-register-type', 'CashRegisterTypeListController');
    Route::put('/cash-register-type/active/{type}', 'CashRegisterTypeListController@active')->name('cash-register-type.active');

    Route::resource('/user-test','userTestController');
});



Route::group(['namespace' => 'Finance', 'prefix' => 'finance', 'as' => 'finance.', 'middleware' => ['auth']], function() {

    Route::get('/invoice-check', 'InvoiceController@readBankStatement');
    Route::resource('/invoice', 'InvoiceController');
    Route::get('/invoice/print/{invoice}', 'InvoiceController@print')->name('invoice.print');
    Route::get('/invoice/pay/{invoice}', 'InvoiceController@payment')->name('invoice.pay');

    // pokladna
    Route::resource('/cash-register', 'CashRegisterController');
    // prijate faktury
    Route::resource('/invoice-income', 'InvoiceIncomeController');

    Route::resource('/invoice-payment', 'InvoicePaymentController');

    //ciselniky
    Route::resource('/invoice-text', 'InvoiceTextController');
    Route::put('/invoice-text/active/{invoice}', 'InvoiceTextController@active')->name('invoice-text.active');

});


Route::group(['namespace' => 'Guests', 'prefix' => 'guests', 'as' => 'guests.', 'middleware' => ['auth']], function() {

    Route::resource('/guest-listings', 'GuestListingController');
    // hostia podla filtra kluby a stavy
    Route::resource('/guest-listings-filter', 'GuestListingFilterController');


});


Route::group(['namespace' => 'Events', 'prefix' => 'events', 'as' => 'events.', 'middleware' => ['auth']], function() {

    Route::resource('/listing', 'EventController');
    Route::get('/duplicate/{event}', 'EventController@duplicate')->name('listing-duplicate');

    Route::post('/activity', 'EventController@activityStore')->name('activity.store');
    Route::delete('/activity/{activity}', 'EventController@activityDestroy')->name('activity.destroy');
    Route::delete('/attendance-destroy/{activity}', 'EventController@attendanceDestroy')->name('attendance.destroy');

    // zobrazenie nahladu pozvanky
    Route::get('/inviting-prewiew/{event}', 'EventController@invitingPrewiew')->name('inviting.preview');

    // zobrazenie nahladu pozvanky
    Route::resource('/template', 'EventCustomTemplateController');

    // zobrazenie nahladu pozvanky
    Route::resource('/ticket-setting', 'EventTicketSettingController');

    //invitation event detail
    Route::get('/invitation/{event}', 'EventInvitationController@invitationEventDetail')->name('invitation.detail');
    Route::post('/invitation/store', 'EventInvitationController@invitationEventDetailStore')->name('invitation.detail.store');

    //print
    Route::get('/print/attendance/{attendance}/{type}', 'EventPrintController@attendanceList')->name('print.attendance');

    //uzavierka
    Route::resource('/balance', 'EventBalanceController');
//    //ustrizky
    Route::resource('/reference', 'EventReferenceController');

    // statistiky
    Route::resource('/stats', 'EventStatsController');

    // fotografie z eventu  upload
    Route::post('/upload/images', 'EventUploadImagesController@uploadImagesBlueImp')->name('upload.images.blue-imp');

    // presmerovanie menu
    Route::get('events', function () {
        return redirect()->route('events.listing.index');
    });

});



Route::group(['namespace' => 'Manual', 'prefix' => 'manual', 'as' => 'manual.', 'middleware' => ['auth']], function() {

    Route::resource('/files-type', 'FileTypeController');
    Route::resource('/files', 'FileController');

    // download file from nanual
    Route::get('download-file/{file}', 'FileController@getFileToDownload')->name('download.file');

    Route::resource('/faq', 'FaqController');
    Route::put('/faq/active/{faq}', 'FaqController@active')->name('faq.active');

});



Route::group(['namespace' => 'Invitations', 'prefix' => 'invitations', 'as' => 'invitations.', 'middleware' => ['auth']], function() {

    //hostia novy
    Route::get('event/guest-new/{event}', 'InvitationController@invitationEventGuestNew')->name('event.guest-new');
    Route::post('event/guest-new/{event}', 'InvitationController@invitationEventGuestNewStore')->name('event.guest-new.store');
    Route::post('event/guest-new-step2/{event}', 'InvitationController@invitationEventGuestNewStore2')->name('event.guest-new-step2.store');

    Route::get('event/guest-new-step3/{event}', 'InvitationController@invitationEventGuestNew3')->name('event.guest-new-step3');
    Route::post('event/guest-new-step3/{event}', 'InvitationController@invitationEventGuestNewStore3')->name('event.guest-new-step3.store');

    //hostia
    Route::get('event-guest/{event}', 'InvitationController@invitationEventGuest')->name('event.guest');
    Route::post('event/guest', 'InvitationController@invitationEventGuestStore')->name('event.guest.store');
    //clenovia
    Route::get('event-member/{event}', 'InvitationController@invitationEventMember')->name('event.member');
    Route::post('event-member/member', 'InvitationController@invitationEventMemberStore')->name('event.member.store');
    //clenovia inych klubov
    Route::get('event-member-all/{event}', 'InvitationController@invitationEventMemberAll')->name('event.member-all');
    Route::post('event/member-all', 'InvitationController@invitationEventMemberAllStore')->name('event.member-all.store');

});


Route::group(['namespace' => 'Api', 'prefix' => 'api', 'as' => 'api.', 'middleware' => ['web']], function() {

    Route::get('club/{club}', 'ApiController@clubDetail')->name('club.detail');
    Route::get('clubs', 'ApiController@clubsListings')->name('clubs.listings');
    Route::get('price-ticket', 'ApiController@priceTickets')->name('clubs.tickets');
    Route::get('price-ticket-total', 'ApiController@priceTicketsTotal')->name('clubs.tickets.total');


});

// not authentification web external registrazion guest from email and events application
Route::group(['namespace' => 'External', 'prefix' => 'ext', 'as' => 'ext.', 'middleware' => ['web']], function() {

    // zakladny formular email a phone
    Route::get('invite-guest/{user}', 'InvitationGuestController@inviteGuestStep1')->name('invite.guest.step1');
    // odoslanie zakladneho formulara pozvanky
    Route::post('invite-guest-step1/store', 'InvitationGuestController@inviteGuestStoreStep1')->name('invite.guest.step1.store');
    // odoslanie formulara ak host existuje, zapiseme do prezencky posleme pozvankz
    Route::post('invite-guest-step2/store', 'InvitationGuestController@inviteGuestStoreStep2')->name('invite.guest.step2.store');
    // odoslanie formulara ak host neexistuje a vytvárame noveho usera zapiseme do prezencky a posleme pozvanku
    Route::post('invite-guest-step22/store', 'InvitationGuestController@inviteGuestStoreStep22')->name('invite.guest.step22.store');
    // odoslanie formulara ak host existuje, ak existuej zhoda v mene
    Route::post('invite-guest-step3/store', 'InvitationGuestController@inviteGuestStoreStep3')->name('invite.guest.step3.store');
    // event moznost kupenia listlku cez online payment
    Route::get('ticket-event/{event}', 'EventTicketBuyController@showFormBuyTicket')->name('ticket.event');
    // zobrazenie info o evente pre external
    Route::get('info-event/{event}', 'EventExternalInfoController@showDetailInfo')->name('event.info');
    // prihlaska za clena
    Route::get('application-form-member/{guest}', 'EventExternalInfoController@applicationFormMember')->name('application.form.member');
    // prihlaska za clena
    Route::post('application-form-member', 'EventExternalInfoController@applicationFormMemberStore')->name('application.form.member.store');
});



Route::resource('/franchisor', 'FranchisorController');

Route::resource('applications', 'Applications\ApplicationsController');

