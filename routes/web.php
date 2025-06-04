<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ReportController;
// Admin Controllers
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\CourseController as AdminCourseController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController; // thêm controller dashboard admin
// Lecturer Controllers
use App\Http\Controllers\Lecturer\CourseController as LecturerCourseController;
use App\Http\Controllers\Lecturer\AttendanceController as LecturerAttendanceController;
// Student Controllers
use App\Http\Controllers\Student\AttendanceController as StudentAttendanceController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');

Auth::routes();

Route::middleware(['auth'])->group(function () {

    // Dashboard chung (cho user đã đăng nhập)
    Route::get('/dashboard', [HomeController::class, 'dashboard'])->name('dashboard');

    // Admin Routes
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        // Route dashboard admin
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::resource('users', AdminUserController::class)->except(['show']);
        Route::resource('courses', AdminCourseController::class);

        // Quản lý sinh viên trong lớp học
        Route::get('courses/{course}/students', [AdminCourseController::class, 'manageStudents'])->name('courses.manage_students');
        Route::post('courses/{course}/students/add', [AdminCourseController::class, 'addStudentToCourse'])->name('courses.add_student');
        Route::delete('courses/{course}/students/{studentId}/remove', [AdminCourseController::class, 'removeStudentFromCourse'])->name('courses.remove_student');
    });

    // Lecturer Routes
    Route::middleware(['role:giangvien'])->prefix('lecturer')->name('lecturer.')->group(function () {
        Route::get('courses', [LecturerCourseController::class, 'index'])->name('courses.index');
        Route::get('courses/{course}', [LecturerCourseController::class, 'show'])->name('courses.show');

        Route::get('courses/{course}/attendance/mark', [LecturerAttendanceController::class, 'showMarkingForm'])->name('attendance.mark_form');
        Route::post('courses/{course}/attendance/mark', [LecturerAttendanceController::class, 'storeAttendance'])->name('attendance.store');
        Route::get('courses/{course}/attendance/history', [LecturerAttendanceController::class, 'attendanceHistory'])->name('attendance.history');
        Route::get('courses/{course}/attendance/edit/{attendance_id}', [LecturerAttendanceController::class, 'editAttendance'])->name('attendance.edit');
        Route::put('courses/{course}/attendance/edit/{attendance_id}', [LecturerAttendanceController::class, 'updateAttendance'])->name('attendance.update');
    });

    // Student Routes
    Route::middleware(['role:sinhvien'])->prefix('student')->name('student.')->group(function () {
        Route::get('my-courses', [StudentAttendanceController::class, 'myCourses'])->name('my_courses');
        Route::get('my-attendance/{course_id?}', [StudentAttendanceController::class, 'myAttendance'])->name('attendance.my');
    });

    // Report Routes (có thể phân quyền trong controller hoặc Gate)
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('attendance', [ReportController::class, 'attendanceReport'])->name('attendance')->middleware('can:view-attendance-reports');
        // Các route báo cáo khác...
    });
});
