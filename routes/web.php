<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\SocialController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;

// Social authentication routes (works for both login and registration)
Route::get('login/{provider}', [SocialController::class, 'redirectToSocial'])->name('social.redirect');
Route::get('login/{provider}/callback', [SocialController::class, 'handleSocialCallback'])->name('social.callback');
Route::get('register/{provider}', [SocialController::class, 'redirectToSocial'])->name('social.register');
Route::get('register/{provider}/callback', [SocialController::class, 'handleSocialCallback'])->name('social.register.callback');

// Show form to request password reset
Route::get('password/reset', [ForgotPasswordController::class, 'showForm'])->name('password.request');

// Send reset link email
Route::post('password/email', [ForgotPasswordController::class, 'sendEmail'])->name('password.email');

// Show form to reset password
Route::get('password/reset/{token}', [ResetPasswordController::class, 'showForm'])->name('password.reset');

// Update password
Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');


// Public routes
Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

// Protected routes
Route::middleware('auth')->group(function () {
    // Logout route - must be authenticated to logout
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/logout', [LoginController::class, 'logout']); // Also handle GET requests for logout
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/about', [DashboardController::class, 'about'])->name('about');
    Route::put('/profile', [DashboardController::class, 'updateProfile'])->name('profile.update');
        // Notifications API
        Route::get('/notifications/unread-count', [\App\Http\Controllers\NotificationController::class, 'unreadCount'])->name('notifications.unread-count');
    
    // API routes for dynamic content loading
    Route::get('/api/branches/{branchId}/categories', [DashboardController::class, 'getCategoriesByBranch'])->name('api.branches.categories');
    Route::get('/api/categories/{categoryId}/services', [DashboardController::class, 'getServicesByCategory'])->name('api.categories.services');
    Route::get('/api/branches/{branchId}/services', [DashboardController::class, 'getServicesByBranch'])->name('api.branches.services');
    
    
    // Patient routes
    Route::middleware('role:patient')->group(function () {
        Route::get('/patient/history', [App\Http\Controllers\PatientController::class, 'history'])->name('patient.history');
            Route::get('/patient/history/filter', [App\Http\Controllers\PatientController::class, 'historyAjax'])->name('patient.history.filter');
        
        // Notifications routes
        Route::get('/notifications', [App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
        Route::post('/notifications/{notification}/mark-read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
        Route::post('/notifications/mark-all-read', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
        
        // Consultation routes
        Route::get('/consultations', [App\Http\Controllers\PatientConsultationController::class, 'index'])->name('consultations.index');
        Route::get('/consultations/create', [App\Http\Controllers\PatientConsultationController::class, 'create'])->name('consultations.create');
        Route::get('/consultations/medical', [App\Http\Controllers\PatientConsultationController::class, 'medicalConsultation'])->name('consultations.medical');
        Route::post('/consultations', [App\Http\Controllers\PatientConsultationController::class, 'store'])->name('consultations.store');
        Route::get('/consultations/available-slots', [App\Http\Controllers\PatientConsultationController::class, 'getAvailableTimeSlots'])->name('consultations.available-slots');
        Route::get('/consultations/{consultation}', [App\Http\Controllers\PatientConsultationController::class, 'show'])->name('consultations.show');
        Route::post('/consultations/{consultation}/cancel', [App\Http\Controllers\PatientConsultationController::class, 'cancel'])->name('consultations.cancel');
        
        // Personal Information routes
        Route::get('/personal-information', [App\Http\Controllers\PersonalInformationController::class, 'index'])->name('personal-information.index');
        Route::get('/personal-information/select', [App\Http\Controllers\PersonalInformationController::class, 'select'])->name('personal-information.select');
        Route::get('/personal-information/create', [App\Http\Controllers\PersonalInformationController::class, 'create'])->name('personal-information.create');
        Route::get('/patient-information-sheet', [App\Http\Controllers\PersonalInformationController::class, 'patientInformationSheet'])->name('patient-information-sheet');
        Route::post('/patient-information-sheet', [App\Http\Controllers\PersonalInformationController::class, 'storePatientInformationSheet'])->name('patient-information-sheet.store');
        Route::get('/add-patient-information', [App\Http\Controllers\PersonalInformationController::class, 'addPatientInformation'])->name('add-patient-information');
        Route::post('/add-patient-information', [App\Http\Controllers\PersonalInformationController::class, 'storeAddPatientInformation'])->name('add-patient-information.store');
        Route::get('/edit-patient-information/{personalInformation}', [App\Http\Controllers\PersonalInformationController::class, 'editPatientInformation'])->name('edit-patient-information');
        Route::put('/edit-patient-information/{personalInformation}', [App\Http\Controllers\PersonalInformationController::class, 'updatePatientInformation'])->name('edit-patient-information.update');
        Route::get('/personal-information/{personalInformation}/edit', [App\Http\Controllers\PersonalInformationController::class, 'edit'])->name('personal-information.edit');
        Route::post('/personal-information', [App\Http\Controllers\PersonalInformationController::class, 'store'])->name('personal-information.store');
        Route::put('/personal-information/{personalInformation}', [App\Http\Controllers\PersonalInformationController::class, 'update'])->name('personal-information.update');
        Route::delete('/personal-information/{personalInformation}', [App\Http\Controllers\PersonalInformationController::class, 'destroy'])->name('personal-information.destroy');
        Route::post('/personal-information/{personalInformation}/set-default', [App\Http\Controllers\PersonalInformationController::class, 'setDefault'])->name('personal-information.set-default');
        
        // Medical Information routes
        Route::get('/medical-information/select', [App\Http\Controllers\MedicalInformationController::class, 'select'])->name('medical-information.select');
        Route::get('/medical-information/create', [App\Http\Controllers\MedicalInformationController::class, 'create'])->name('medical-information.create');
        Route::get('/medical-information/{medicalInformation}/edit', [App\Http\Controllers\MedicalInformationController::class, 'edit'])->name('medical-information.edit');
        Route::get('/medical-information/{medicalInformation}', [App\Http\Controllers\MedicalInformationController::class, 'show'])->name('medical-information.show');
        Route::post('/medical-information', [App\Http\Controllers\MedicalInformationController::class, 'store'])->name('medical-information.store');
        Route::put('/medical-information/{medicalInformation}', [App\Http\Controllers\MedicalInformationController::class, 'update'])->name('medical-information.update');
        Route::delete('/medical-information/{medicalInformation}', [App\Http\Controllers\MedicalInformationController::class, 'destroy'])->name('medical-information.destroy');
        
        // Emergency Contact routes
        Route::get('/emergency-contact/select', [App\Http\Controllers\EmergencyContactController::class, 'select'])->name('emergency-contact.select');
        Route::get('/emergency-contact/create', [App\Http\Controllers\EmergencyContactController::class, 'create'])->name('emergency-contact.create');
        Route::get('/emergency-contact/{emergencyContact}/edit', [App\Http\Controllers\EmergencyContactController::class, 'edit'])->name('emergency-contact.edit');
        Route::get('/emergency-contact/{emergencyContact}', [App\Http\Controllers\EmergencyContactController::class, 'show'])->name('emergency-contact.show');
        Route::post('/emergency-contact', [App\Http\Controllers\EmergencyContactController::class, 'store'])->name('emergency-contact.store');
        Route::put('/emergency-contact/{emergencyContact}', [App\Http\Controllers\EmergencyContactController::class, 'update'])->name('emergency-contact.update');
        Route::delete('/emergency-contact/{emergencyContact}', [App\Http\Controllers\EmergencyContactController::class, 'destroy'])->name('emergency-contact.destroy');
        
        // Service routes
        Route::get('/services', [App\Http\Controllers\ServiceController::class, 'index'])->name('services.index');
        Route::get('/services/{service}', [App\Http\Controllers\ServiceController::class, 'show'])->name('services.show');
        
        // Cart routes
        Route::get('/cart', [App\Http\Controllers\CartController::class, 'index'])->name('cart.index');
        Route::post('/cart/add', [App\Http\Controllers\CartController::class, 'add'])->name('cart.add');
        Route::patch('/cart/{cart}', [App\Http\Controllers\CartController::class, 'update'])->name('cart.update');
        Route::delete('/cart/{cart}', [App\Http\Controllers\CartController::class, 'remove'])->name('cart.remove');
        Route::delete('/cart', [App\Http\Controllers\CartController::class, 'clear'])->name('cart.clear');
        Route::get('/cart/count', [App\Http\Controllers\CartController::class, 'count'])->name('cart.count');
        
        // Search routes
        Route::get('/search', [App\Http\Controllers\SearchController::class, 'search'])->name('search');
    });
    
    // Doctor routes (require doctor role)
    Route::prefix('doctor')->middleware('role:doctor')->group(function () {
        Route::get('/', [App\Http\Controllers\Doctor\DashboardController::class, 'index'])->name('doctor.dashboard');
        
        // Admin reports routes
        Route::get('/admin/{adminId}/reports', [App\Http\Controllers\Doctor\DashboardController::class, 'getAdminReports'])->name('doctor.admin.reports');
        Route::get('/appointment/{appointmentId}/patient-details', [App\Http\Controllers\Doctor\DashboardController::class, 'getPatientDetails'])->name('doctor.patient.details');
        Route::get('/admin/{adminId}/download-report', [App\Http\Controllers\Doctor\DashboardController::class, 'downloadAdminReport'])->name('doctor.admin.download-report');
        
        // Branch routes
        Route::get('/branches', [App\Http\Controllers\Doctor\DashboardController::class, 'branches'])->name('doctor.branches');
        Route::get('/branches/create', [App\Http\Controllers\Doctor\DashboardController::class, 'createBranch'])->name('doctor.branches.create');
        Route::post('/branches', [App\Http\Controllers\Doctor\DashboardController::class, 'storeBranch'])->name('doctor.branches.store');
        Route::get('/branches/{branch}/edit', [App\Http\Controllers\Doctor\DashboardController::class, 'editBranch'])->name('doctor.branches.edit');
        Route::put('/branches/{branch}', [App\Http\Controllers\Doctor\DashboardController::class, 'updateBranch'])->name('doctor.branches.update');
        Route::delete('/branches/{branch}', [App\Http\Controllers\Doctor\DashboardController::class, 'destroyBranch'])->name('doctor.branches.destroy');
        
        // Category routes
        Route::get('/categories', [App\Http\Controllers\Doctor\DashboardController::class, 'categories'])->name('doctor.categories');
        Route::get('/categories/create', [App\Http\Controllers\Doctor\DashboardController::class, 'createCategory'])->name('doctor.categories.create');
        Route::get('/categories/{category}/edit', [App\Http\Controllers\Doctor\DashboardController::class, 'editCategory'])->name('doctor.categories.edit');
        Route::post('/categories', [App\Http\Controllers\Doctor\DashboardController::class, 'storeCategory'])->name('doctor.categories.store');
        Route::put('/categories/{category}', [App\Http\Controllers\Doctor\DashboardController::class, 'updateCategory'])->name('doctor.categories.update');
        Route::delete('/categories/{category}', [App\Http\Controllers\Doctor\DashboardController::class, 'destroyCategory'])->name('doctor.categories.destroy');
        
        // Service routes
        Route::get('/services', [App\Http\Controllers\Doctor\DashboardController::class, 'services'])->name('doctor.services');
        Route::get('/services/create', [App\Http\Controllers\Doctor\DashboardController::class, 'createService'])->name('doctor.services.create');
        Route::post('/services', [App\Http\Controllers\Doctor\DashboardController::class, 'storeService'])->name('doctor.services.store');
        Route::get('/services/{service}/edit', [App\Http\Controllers\Doctor\DashboardController::class, 'editService'])->name('doctor.services.edit');
        Route::put('/services/{service}', [App\Http\Controllers\Doctor\DashboardController::class, 'updateService'])->name('doctor.services.update');
        Route::delete('/services/{service}', [App\Http\Controllers\Doctor\DashboardController::class, 'destroyService'])->name('doctor.services.destroy');
        
        // Time Slot routes
        Route::get('/slots', [App\Http\Controllers\Doctor\DashboardController::class, 'timeSlots'])->name('doctor.slots');
        Route::get('/slots/create', [App\Http\Controllers\Doctor\DashboardController::class, 'createTimeSlot'])->name('doctor.slots.create');
        Route::post('/slots', [App\Http\Controllers\Doctor\DashboardController::class, 'storeTimeSlot'])->name('doctor.slots.store');
        Route::get('/slots/{slot}/edit', [App\Http\Controllers\Doctor\DashboardController::class, 'editTimeSlot'])->name('doctor.slots.edit');
        Route::put('/slots/{slot}', [App\Http\Controllers\Doctor\DashboardController::class, 'updateTimeSlot'])->name('doctor.slots.update');
        Route::delete('/slots/{slot}', [App\Http\Controllers\Doctor\DashboardController::class, 'destroyTimeSlot'])->name('doctor.slots.destroy');
        Route::post('/slots/appointments/{appointment}/accept', [App\Http\Controllers\Doctor\DashboardController::class, 'acceptAppointment'])->name('doctor.slots.accept');
        Route::post('/slots/appointments/{appointment}/reject', [App\Http\Controllers\Doctor\DashboardController::class, 'rejectAppointment'])->name('doctor.slots.reject');
        
        // All Appointments (All Branches) routes
        Route::get('/all-appointments', [App\Http\Controllers\Doctor\DashboardController::class, 'allAppointments'])->name('doctor.all-appointments');
        Route::get('/all-appointments/{appointment}/patient-info', [App\Http\Controllers\Doctor\DashboardController::class, 'getPatientInfo'])->name('doctor.all-appointments.patient-info');
        Route::delete('/all-appointments/{appointment}', [App\Http\Controllers\Doctor\DashboardController::class, 'deleteAllAppointment'])->name('doctor.all-appointments.delete');
        Route::post('/all-appointments/{appointment}/result', [App\Http\Controllers\Doctor\DashboardController::class, 'storeAllAppointmentResult'])->name('doctor.all-appointments.result');
        
        // Promotion routes (new comprehensive system)
        Route::get('/promos', [App\Http\Controllers\Doctor\PromoController::class, 'index'])->name('doctor.promos.index');
        Route::get('/promos/create', [App\Http\Controllers\Doctor\PromoController::class, 'create'])->name('doctor.promos.create');
        Route::post('/promos', [App\Http\Controllers\Doctor\PromoController::class, 'store'])->name('doctor.promos.store');
        Route::get('/promos/{promo}/edit', [App\Http\Controllers\Doctor\PromoController::class, 'edit'])->name('doctor.promos.edit');
        Route::put('/promos/{promo}', [App\Http\Controllers\Doctor\PromoController::class, 'update'])->name('doctor.promos.update');
        Route::delete('/promos/{promo}', [App\Http\Controllers\Doctor\PromoController::class, 'destroy'])->name('doctor.promos.destroy');
        
        // Legacy promotion routes (keep for backward compatibility)
        Route::get('/promotions', [App\Http\Controllers\Doctor\DashboardController::class, 'promotions'])->name('doctor.promotions');
        Route::get('/promotions/create', [App\Http\Controllers\Doctor\DashboardController::class, 'createPromotion'])->name('doctor.promotions.create');
        Route::post('/promotions', [App\Http\Controllers\Doctor\DashboardController::class, 'storePromotion'])->name('doctor.promotions.store');
        Route::get('/promotions/{promotion}/edit', [App\Http\Controllers\Doctor\DashboardController::class, 'editPromotion'])->name('doctor.promotions.edit');
        Route::put('/promotions/{promotion}', [App\Http\Controllers\Doctor\DashboardController::class, 'updatePromotion'])->name('doctor.promotions.update');
        Route::delete('/promotions/{promotion}', [App\Http\Controllers\Doctor\DashboardController::class, 'destroyPromotion'])->name('doctor.promotions.destroy');
        
        // Consultation management routes
        Route::get('/consultations', [App\Http\Controllers\Doctor\ConsultationController::class, 'index'])->name('doctor.consultations.index');
        Route::get('/consultations/{consultation}', [App\Http\Controllers\Doctor\ConsultationController::class, 'show'])->name('doctor.consultations.show');
        Route::patch('/consultations/{consultation}/confirm', [App\Http\Controllers\Doctor\ConsultationController::class, 'confirm'])->name('doctor.consultations.confirm');
        Route::patch('/consultations/{consultation}/cancel', [App\Http\Controllers\Doctor\ConsultationController::class, 'cancel'])->name('doctor.consultations.cancel');
        
        // My Appointments routes
        Route::get('/my-appointments', [App\Http\Controllers\Doctor\DashboardController::class, 'myAppointments'])->name('doctor.my-appointments');
        Route::get('/my-appointments/{appointment}', [App\Http\Controllers\Doctor\DashboardController::class, 'showAppointment'])->name('doctor.my-appointments.show');
        Route::patch('/my-appointments/{appointment}/confirm', [App\Http\Controllers\Doctor\DashboardController::class, 'confirmAppointment'])->name('doctor.my-appointments.confirm');
        Route::patch('/my-appointments/{appointment}/cancel', [App\Http\Controllers\Doctor\DashboardController::class, 'cancelAppointment'])->name('doctor.my-appointments.cancel');
        Route::post('/my-appointments/{appointment}/add-result', [App\Http\Controllers\Doctor\DashboardController::class, 'storeResult'])->name('doctor.my-appointments.add-result');
        Route::delete('/my-appointments/{appointment}', [App\Http\Controllers\Doctor\DashboardController::class, 'deleteAppointment'])->name('doctor.my-appointments.delete');
        
        // My Services Schedules routes
        Route::get('/my-services-schedules', [App\Http\Controllers\Doctor\DashboardController::class, 'myServicesSchedules'])->name('doctor.my-services-schedules');
        Route::get('/my-services-schedules/confirmed', [App\Http\Controllers\Doctor\DashboardController::class, 'myServicesSchedulesConfirmed'])->name('doctor.my-services-schedules.confirmed');
        Route::get('/my-services-schedules/{appointment}', [App\Http\Controllers\Doctor\DashboardController::class, 'showServiceSchedule'])->name('doctor.my-services-schedules.show');
        Route::patch('/my-services-schedules/{appointment}/confirm', [App\Http\Controllers\Doctor\DashboardController::class, 'confirmServiceSchedule'])->name('doctor.my-services-schedules.confirm');
        Route::patch('/my-services-schedules/{appointment}/cancel', [App\Http\Controllers\Doctor\DashboardController::class, 'cancelServiceSchedule'])->name('doctor.my-services-schedules.cancel');
        Route::post('/my-services-schedules/{appointment}/result', [App\Http\Controllers\Doctor\DashboardController::class, 'storeServiceResult'])->name('doctor.my-services-schedules.result');
        
        // History routes
        Route::get('/history', [App\Http\Controllers\Doctor\DashboardController::class, 'history'])->name('doctor.history');
        Route::get('/history/patient/{patient}', [App\Http\Controllers\Doctor\DashboardController::class, 'showPatientHistory'])->name('doctor.history.patient');
        Route::get('/history/patient/{patient}/update', [App\Http\Controllers\Doctor\DashboardController::class, 'updateHistory'])->name('doctor.history.patient.update');
        Route::post('/history/patient/{patient}', [App\Http\Controllers\Doctor\DashboardController::class, 'storeHistory'])->name('doctor.history.patient.store');
        Route::delete('/history/patient/{history}', [App\Http\Controllers\Doctor\DashboardController::class, 'deleteHistory'])->name('doctor.history.patient.delete');
        
        // Pending Appointments routes
        Route::get('/pending-appointments', [App\Http\Controllers\Doctor\DashboardController::class, 'pendingAppointments'])->name('doctor.pending-appointments');
        Route::get('/pending-appointments/{appointment}/add-result', [App\Http\Controllers\Doctor\DashboardController::class, 'addResult'])->name('doctor.pending-appointments.add-result');
        Route::post('/pending-appointments/{appointment}/store-result', [App\Http\Controllers\Doctor\DashboardController::class, 'storeResult'])->name('doctor.pending-appointments.store-result');
        
        // Notifications
        Route::get('/notifications', [App\Http\Controllers\Doctor\NotificationController::class, 'index'])->name('doctor.notifications.index');
        Route::get('/notifications/new', [App\Http\Controllers\Doctor\NotificationController::class, 'getNewNotifications'])->name('doctor.notifications.new');
        Route::get('/notifications/unread-count', [App\Http\Controllers\Doctor\NotificationController::class, 'unreadCount'])->name('doctor.notifications.unread-count');
        Route::post('/notifications/{notification}/mark-read', [App\Http\Controllers\Doctor\NotificationController::class, 'markAsRead'])->name('doctor.notifications.mark-read');
        Route::post('/notifications/mark-all-read', [App\Http\Controllers\Doctor\NotificationController::class, 'markAllAsRead'])->name('doctor.notifications.mark-all-read');
        Route::get('/profile', [App\Http\Controllers\Doctor\DashboardController::class, 'profile'])->name('doctor.profile');
        Route::put('/profile', [App\Http\Controllers\Doctor\DashboardController::class, 'updateProfile'])->name('doctor.profile.update');
        Route::put('/profile/password', [App\Http\Controllers\Doctor\DashboardController::class, 'updatePassword'])->name('doctor.profile.update-password');
    });
    
    // Admin routes (require admin role)
    Route::prefix('admin')->middleware('role:admin')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('admin.dashboard');
        Route::get('/appointments', [App\Http\Controllers\Admin\DashboardController::class, 'appointments'])->name('admin.appointments');
        Route::get('/patients', [App\Http\Controllers\Admin\DashboardController::class, 'patients'])->name('admin.patients');
        Route::get('/branches/{branch}/patients', [App\Http\Controllers\Admin\DashboardController::class, 'patientsByBranch'])->name('admin.branch.patients');
        Route::get('/patients/{patient}/history', [App\Http\Controllers\Admin\DashboardController::class, 'viewHistory'])->name('admin.patients.history');
        Route::get('/patients/{patient}/history/update', [App\Http\Controllers\Admin\DashboardController::class, 'updateHistory'])->name('admin.patients.history.update');
        Route::post('/patients/{patient}/history', [App\Http\Controllers\Admin\DashboardController::class, 'storeHistory'])->name('admin.patients.history.store');
        Route::delete('/patients/history/{history}', [App\Http\Controllers\Admin\DashboardController::class, 'deleteHistory'])->name('admin.patients.history.delete');
        Route::get('/profile', [App\Http\Controllers\Admin\DashboardController::class, 'profile'])->name('admin.profile');
        Route::put('/profile', [App\Http\Controllers\Admin\DashboardController::class, 'updateProfile'])->name('admin.profile.update');
        Route::put('/profile/password', [App\Http\Controllers\Admin\DashboardController::class, 'updatePassword'])->name('admin.profile.update-password');

        Route::get('/appointments/{appointment}/patient-info', [App\Http\Controllers\Admin\DashboardController::class, 'getPatientInfo'])->name('admin.appointments.patient-info');
        Route::delete('/appointments/{appointment}', [App\Http\Controllers\Admin\DashboardController::class, 'deleteAppointment'])->name('admin.appointments.delete');
        Route::post('/appointments/{appointment}/result', [App\Http\Controllers\Admin\DashboardController::class, 'storeResult'])->name('admin.appointments.store-result');
        
        // My Services Schedules routes
        Route::get('/my-services-schedules', [App\Http\Controllers\Admin\DashboardController::class, 'myServicesSchedules'])->name('admin.my-services-schedules');
        Route::get('/my-services-schedules/confirmed', [App\Http\Controllers\Admin\DashboardController::class, 'myServicesSchedulesConfirmed'])->name('admin.my-services-schedules.confirmed');
        Route::get('/my-services-schedules/{appointment}', [App\Http\Controllers\Admin\DashboardController::class, 'showServiceSchedule'])->name('admin.my-services-schedules.show');
        Route::patch('/my-services-schedules/{appointment}/confirm', [App\Http\Controllers\Admin\DashboardController::class, 'confirmServiceSchedule'])->name('admin.my-services-schedules.confirm');
        Route::patch('/my-services-schedules/{appointment}/cancel', [App\Http\Controllers\Admin\DashboardController::class, 'cancelServiceSchedule'])->name('admin.my-services-schedules.cancel');
        Route::post('/my-services-schedules/{appointment}/result', [App\Http\Controllers\Admin\DashboardController::class, 'storeServiceResult'])->name('admin.my-services-schedules.result');
        
        // Notifications
        Route::get('/notifications', [App\Http\Controllers\Admin\NotificationController::class, 'index'])->name('admin.notifications.index');
        Route::post('/notifications/{notification}/mark-read', [App\Http\Controllers\Admin\NotificationController::class, 'markAsRead'])->name('admin.notifications.mark-read');
        Route::post('/notifications/mark-all-read', [App\Http\Controllers\Admin\NotificationController::class, 'markAllAsRead'])->name('admin.notifications.mark-all-read');

        // Category management (branch-scoped)
        Route::get('/categories', [App\Http\Controllers\Admin\CategoryController::class, 'index'])->name('admin.categories');
        Route::get('/categories/create', [App\Http\Controllers\Admin\CategoryController::class, 'create'])->name('admin.categories.create');
        Route::get('/categories/{category}/edit', [App\Http\Controllers\Admin\CategoryController::class, 'edit'])->name('admin.categories.edit');
        Route::post('/categories', [App\Http\Controllers\Admin\CategoryController::class, 'store'])->name('admin.categories.store');
        Route::put('/categories/{category}', [App\Http\Controllers\Admin\CategoryController::class, 'update'])->name('admin.categories.update');
        Route::delete('/categories/{category}', [App\Http\Controllers\Admin\CategoryController::class, 'destroy'])->name('admin.categories.destroy');

        // Service management (branch-scoped)
        Route::get('/services', [App\Http\Controllers\Admin\ServiceController::class, 'index'])->name('admin.services');
        Route::get('/services/create', [App\Http\Controllers\Admin\ServiceController::class, 'create'])->name('admin.services.create');
        Route::post('/services', [App\Http\Controllers\Admin\ServiceController::class, 'store'])->name('admin.services.store');
        Route::get('/services/{service}/edit', [App\Http\Controllers\Admin\ServiceController::class, 'edit'])->name('admin.services.edit');
        Route::put('/services/{service}', [App\Http\Controllers\Admin\ServiceController::class, 'update'])->name('admin.services.update');
        Route::delete('/services/{service}', [App\Http\Controllers\Admin\ServiceController::class, 'destroy'])->name('admin.services.destroy');

        // Promotion management
        Route::get('/promos', [App\Http\Controllers\Admin\PromoController::class, 'index'])->name('admin.promos');
        Route::get('/promos/create', [App\Http\Controllers\Admin\PromoController::class, 'create'])->name('admin.promos.create');
        Route::post('/promos', [App\Http\Controllers\Admin\PromoController::class, 'store'])->name('admin.promos.store');
        Route::get('/promos/{promo}/edit', [App\Http\Controllers\Admin\PromoController::class, 'edit'])->name('admin.promos.edit');
        Route::put('/promos/{promo}', [App\Http\Controllers\Admin\PromoController::class, 'update'])->name('admin.promos.update');
        Route::delete('/promos/{promo}', [App\Http\Controllers\Admin\PromoController::class, 'destroy'])->name('admin.promos.destroy');

        // Time Slot routes (branch-scoped)
        Route::get('/slots', [App\Http\Controllers\Admin\DashboardController::class, 'timeSlots'])->name('admin.slots');
        Route::get('/slots/create', [App\Http\Controllers\Admin\DashboardController::class, 'createTimeSlot'])->name('admin.slots.create');
        Route::post('/slots', [App\Http\Controllers\Admin\DashboardController::class, 'storeTimeSlot'])->name('admin.slots.store');
        Route::get('/slots/{slot}/edit', [App\Http\Controllers\Admin\DashboardController::class, 'editTimeSlot'])->name('admin.slots.edit');
        Route::put('/slots/{slot}', [App\Http\Controllers\Admin\DashboardController::class, 'updateTimeSlot'])->name('admin.slots.update');
        Route::delete('/slots/{slot}', [App\Http\Controllers\Admin\DashboardController::class, 'destroyTimeSlot'])->name('admin.slots.destroy');
        Route::post('/slots/appointments/{appointment}/accept', [App\Http\Controllers\Admin\DashboardController::class, 'acceptAppointment'])->name('admin.slots.accept');
        Route::post('/slots/appointments/{appointment}/reject', [App\Http\Controllers\Admin\DashboardController::class, 'rejectAppointment'])->name('admin.slots.reject');
    });
});
