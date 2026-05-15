<?php

use App\Http\Controllers\Admin\EnrollmentController as AdminEnrollmentController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\ProfileController;
use App\Models\Enrollment;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', [EnrollmentController::class, 'index'])->name('enrollment.index');
Route::post('/inscricao', [EnrollmentController::class, 'store'])->name('enrollment.store');
Route::get('/inscricao/sucesso', function () {
    return Inertia::render('Enrollment/Success');
})->name('enrollment.success');

Route::prefix('admin')->middleware(['auth', 'verified'])->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'total' => Enrollment::count(),
                'paid' => Enrollment::where('payment_status', 'paid')->count(),
                'revenue' => Enrollment::where('payment_status', 'paid')->sum('amount_paid'),
            ],
        ]);
    })->name('dashboard');

    Route::get('/inscritos', [AdminEnrollmentController::class, 'index'])->name('enrollments.index');
    Route::get('/inscritos/{enrollment}/editar', [AdminEnrollmentController::class, 'edit'])->name('enrollments.edit');
    Route::put('/inscritos/{enrollment}', [AdminEnrollmentController::class, 'update'])->name('enrollments.update');
    Route::delete('/inscritos/{enrollment}', [AdminEnrollmentController::class, 'destroy'])->name('enrollments.destroy');
    Route::get('/inscritos/exportar/excel', [AdminEnrollmentController::class, 'exportExcel'])->name('enrollments.export.excel');
    Route::get('/inscritos/exportar/pdf', [AdminEnrollmentController::class, 'exportPdf'])->name('enrollments.export.pdf');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
