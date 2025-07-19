<?php



use App\Http\Controllers\NotifikasiController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Module\MasterData\ProjectController;
use App\Http\Controllers\ProfileController;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
 */

Auth::routes();
Route::get('/reset-admin-pass', function(){
    User::where('email', 'admin@admin.com')->update([
        'password' => bcrypt('P@ssw0rdR4haS!a')
    ]);

    return redirect()->back();
});

Route::middleware('auth')->get('/', [HomeController::class, 'index'])->name('dashboard');
Route::middleware('auth')->get('/redirect', [HomeController::class, 'redirect'])->name('redirect');

Route::middleware(['auth', 'ajax'])->get('/home', [HomeController::class, 'dashboard'])->name('dashboard.index');
Route::middleware(['auth', 'ajax'])->get('/dashboard-project', [HomeController::class, 'dashboardProject'])->name('dashboard.project');

Route::middleware(['auth', 'ajax'])->group(function () {
    // Route::post('/employeeWithProject', [HomeController::class, 'searchEmployeeWithProject'])->name('dashboard.searchEmployeeWithProject');
    Route::post('/getTimelineProject', [HomeController::class, 'getTimelineWithProject'])->name('dashboard.getTimelineWithProject');
    Route::post('/projectStakeholder', [HomeController::class, 'projectStakeholder'])->name('dashboard.projectStakeholder');
    Route::post('/projectStakeholderDetail', [HomeController::class, 'projectStakeholderDetail'])->name('dashboard.projectStakeholderDetail');
});



Route::middleware(['auth', 'ajax'])->get('/profile', [ProfileController::class, 'index'])->name('profile');
Route::middleware(['auth', 'ajax'])->put('/profile', [ProfileController::class, 'updateProfile'])->name('profile.updateProfile');
Route::middleware(['auth', 'ajax'])->post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.updatePassword');

Route::middleware(['auth','ajax'])->get('/notif', [NotifikasiController::class, 'index'])->name('notifikasi');
Route::middleware(['auth', 'ajax'])->get('/mark-all-as-read', [NotifikasiController::class, 'markAllAsRead']);
Route::middleware(['auth', 'ajax'])->get('/clear-notifications', [NotifikasiController::class, 'clearNotifications'])->name('clear-notifications');
