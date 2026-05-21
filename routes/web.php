<?php
// routes/web.php
/** @var Core\Router $router */

$router->get('/', function() {
    header("Location: " . ($_ENV['APP_URL'] ?? '') . "/login");
    exit;
});

// Authentication
$router->get('/login', 'AuthController@showLogin');
$router->post('/login', 'AuthController@login');
$router->get('/logout', 'AuthController@logout');
$router->get('/cookie-policy', 'AuthController@cookiePolicy');

// Public Attendance (license verified - students scan QR)
$router->group(['middleware' => ['LicenseMiddleware']], function($router) {
    $router->get('/attendance/checkin/{sessionId}', 'AttendanceController@checkinPage');
    $router->post('/attendance/checkin/{sessionId}', 'AttendanceController@checkinSubmit');

    // Student Portal (license verified)
    $router->get('/portal/{room_code}', 'ClassroomController@portal');
    $router->post('/portal/{room_code}/login', 'ClassroomController@loginStudent');
    $router->get('/portal/{room_code}/logout', 'ClassroomController@logoutStudent');
});

// License Activation (Public)
$router->get('/license/activate', 'LicenseController@activate');
$router->post('/license/submit', 'LicenseController@submit');

// Protected Routes
$router->group(['middleware' => ['AuthMiddleware', 'CsrfMiddleware', 'LicenseMiddleware']], function($router) {
    
    // Admin Routes
    $router->group(['middleware' => ['AdminMiddleware']], function($router) {
        $router->get('/admin/dashboard', 'DashboardController@index');
        $router->get('/admin/apps', 'DashboardController@appCenter');

        // Student Management
        $router->get('/admin/students', 'StudentController@index');
        $router->get('/admin/students/template', 'StudentController@downloadTemplate');
        $router->post('/admin/students/import', 'StudentController@importCSV');
        $router->post('/admin/students/store', 'StudentController@store');
        $router->get('/admin/students/edit/{id}', 'StudentController@edit');
        $router->post('/admin/students/update/{id}', 'StudentController@update');
        $router->post('/admin/students/delete/{id}', 'StudentController@delete');
        $router->get('/admin/students/export', 'StudentController@export');

        // Classroom Management
        $router->get('/admin/classrooms', 'ClassroomController@index');
        $router->post('/admin/classrooms/create', 'ClassroomController@store');
        $router->get('/admin/classrooms/edit/{id}', 'ClassroomController@edit');
        $router->post('/admin/classrooms/update/{id}', 'ClassroomController@update');
        $router->post('/admin/classrooms/update-settings/{id}', 'ClassroomController@updateSettings');
        $router->post('/admin/classrooms/delete/{id}', 'ClassroomController@delete');
        $router->get('/admin/classrooms/show/{id}', 'ClassroomController@show');
        $router->post('/admin/classrooms/add-student/{id}', 'ClassroomController@addStudent');
        $router->post('/admin/classrooms/remove-student/{id}', 'ClassroomController@removeStudent');
        $router->get('/admin/classrooms/search-students/{id}', 'ClassroomController@searchStudents');
        $router->get('/admin/classrooms/class-levels', 'ClassroomController@getClassLevels');
        $router->get('/admin/classrooms/student-assignments/{classroomId}/{studentId}', 'ClassroomController@getStudentAssignments');

        // Attendance Routes
        $router->post('/admin/attendance/start/{classroomId}', 'AttendanceController@start');
        $router->post('/admin/attendance/stop/{sessionId}', 'AttendanceController@stop');
        $router->post('/admin/attendance/check/{sessionId}', 'AttendanceController@check');
        $router->post('/admin/attendance/scan/{sessionId}', 'AttendanceController@scan');
        $router->get('/admin/attendance/session/{id}', 'AttendanceController@session');
        $router->post('/admin/attendance/delete/{id}', 'AttendanceController@deleteSession');

        // Assignment Management
        $router->get('/admin/assignments', 'AssignmentController@index');
        $router->get('/admin/assignments/classroom/{id}', 'AssignmentController@classroom');
        $router->post('/admin/assignments/store', 'AssignmentController@store');
        $router->post('/admin/assignments/delete/{id}', 'AssignmentController@delete');
        $router->get('/admin/assignments/grading/{id}', 'AssignmentController@grading');
        $router->post('/admin/assignments/save-grade', 'AssignmentController@saveGrade');
        $router->post('/admin/assignments/scan-student-code', 'AssignmentController@scanStudentCode');

        // Profile Routes
        $router->get('/admin/profile', 'ProfileController@index');
        $router->post('/admin/profile/update', 'ProfileController@update');
        $router->post('/admin/profile/password', 'ProfileController@changePassword');
        $router->post('/admin/profile/avatar', 'ProfileController@uploadAvatar');

        // Report Routes (Phase 7)
        $router->get('/admin/reports', 'ReportController@index');
        $router->get('/admin/reports/classroom/{id}', 'ReportController@classroom');

        // Student Card Routes (Phase 7)
        $router->get('/admin/cards', 'StudentCardController@index');
        $router->get('/admin/cards/settings', 'StudentCardController@settings');
        $router->post('/admin/cards/settings', 'StudentCardController@updateSettings');
        $router->get('/admin/cards/select/{id}', 'StudentCardController@select');
        $router->post('/admin/cards/generate', 'StudentCardController@generate');
        $router->post('/admin/students/upload-photo', 'StudentCardController@uploadPhoto');
    });

    // Student Routes (if any)
    // $router->get('/student/dashboard', 'StudentPortalController@index');
});
