<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\SimpleExcel\SimpleExcelWriter;

class EnrollmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Enrollment::query();

        if ($request->search) {
            $search = $request->search;
            $query->whereHas('student', function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%')
                  ->orWhere('cpf', 'like', '%' . $search . '%');
            })->orWhereHas('course', function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
            });
        }

        if ($request->course_id) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->status) {
            $query->where('payment_status', $request->status);
        }

        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $enrollments = $query->orderBy('created_at', 'desc')->paginate(20);

        return Inertia::render('Admin/Enrollments/Index', [
            'enrollments' => $enrollments,
            'courses' => Course::all(),
            'filters' => $request->all(),
        ]);
    }

    public function edit(Enrollment $enrollment)
    {
        return Inertia::render('Admin/Enrollments/Edit', [
            'enrollment' => $enrollment,
            'courses' => Course::where('active', true)->get(),
        ]);
    }

    public function update(Request $request, Enrollment $enrollment)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'nullable|string|max:20',
            'cpf' => 'required|string|size:14',
            'birth_date' => 'nullable|date',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|size:2',
            'course_id' => 'required|exists:courses,id',
            'payment_status' => 'required|in:pending,paid,failed,refunded',
        ]);

        $enrollment->student->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'cpf' => $validated['cpf'],
            'birth_date' => $validated['birth_date'] ?? null,
            'address' => $validated['address'] ?? null,
            'city' => $validated['city'] ?? null,
            'state' => $validated['state'] ?? null,
        ]);

        $course = Course::findOrFail($request->course_id);
        $enrollment->update([
            'course_id' => $course->id,
            'amount_paid' => $course->price,
            'payment_status' => $validated['payment_status'],
        ]);

        return redirect()->route('admin.enrollments.index')
            ->with('success', 'Inscrição atualizada com sucesso.');
    }

    public function destroy(Enrollment $enrollment)
    {
        $enrollment->delete();

        return redirect()->route('admin.enrollments.index')
            ->with('success', 'Inscrição removida com sucesso.');
    }

    public function exportExcel(Request $request)
    {
        $query = Enrollment::query();

        if ($request->search) {
            $search = $request->search;
            $query->whereHas('student', function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%')
                  ->orWhere('cpf', 'like', '%' . $search . '%');
            });
        }

        if ($request->course_id) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->status) {
            $query->where('payment_status', $request->status);
        }

        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $enrollments = $query->orderBy('created_at', 'desc')->get();

        $fileName = 'inscritos_' . now()->format('Y-m-d_His') . '.xlsx';
        $path = storage_path('app/exports/' . $fileName);

        if (!is_dir(storage_path('app/exports'))) {
            mkdir(storage_path('app/exports'), 0755, true);
        }

        $writer = SimpleExcelWriter::create($path);

        foreach ($enrollments as $enrollment) {
            $writer->addRow([
                'ID' => $enrollment->id,
                'Nome' => $enrollment->student->name,
                'E-mail' => $enrollment->student->email,
                'CPF' => $enrollment->student->cpf,
                'Telefone' => $enrollment->student->phone,
                'Curso' => $enrollment->course->name,
                'Valor Pago' => 'R$ ' . number_format($enrollment->amount_paid, 2, ',', '.'),
                'Status' => ucfirst($enrollment->payment_status),
                'Data Inscricao' => $enrollment->created_at->format('d/m/Y H:i'),
            ]);
        }

        $writer->close();

        return response()->download($path, $fileName);
    }

    public function exportPdf(Request $request)
    {
        $query = Enrollment::query();

        if ($request->search) {
            $search = $request->search;
            $query->whereHas('student', function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%')
                  ->orWhere('cpf', 'like', '%' . $search . '%');
            });
        }

        if ($request->course_id) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->status) {
            $query->where('payment_status', $request->status);
        }

        $enrollments = $query->orderBy('created_at', 'desc')->get();

        $pdf = Pdf::loadView('pdf.enrollments', ['enrollments' => $enrollments]);
        $pdf->setPaper('a4', 'landscape');

        return $pdf->download('inscritos_' . now()->format('Y-m-d') . '.pdf');
    }
}
