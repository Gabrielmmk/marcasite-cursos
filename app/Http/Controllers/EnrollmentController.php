<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEnrollmentRequest;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Student;
use Inertia\Inertia;
use Stripe\Exception\CardException;
use Stripe\PaymentIntent;
use Stripe\Stripe;

class EnrollmentController extends Controller
{
    public function index()
    {
        $courses = Course::where('active', true)->get();

        return Inertia::render('Enrollment/Index', [
            'courses' => $courses,
            'stripeKey' => config('services.stripe.key'),
        ]);
    }

    public function store(StoreEnrollmentRequest $request)
    {
        $course = Course::findOrFail($request->course_id);

        Stripe::setApiKey(config('services.stripe.secret'));

        try {
            $paymentIntent = PaymentIntent::create([
                'amount' => (int) ($course->price * 100),
                'currency' => 'brl',
                'payment_method' => $request->payment_method_id,
                'confirmation_method' => 'manual',
                'confirm' => true,
                'return_url' => route('enrollment.success'),
            ]);
        } catch (CardException $e) {
            return back()->withErrors(['payment' => $e->getMessage()]);
        }

        $student = Student::where('cpf', $request->cpf)->first();

        if (!$student) {
            $student = Student::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'cpf' => $request->cpf,
                'birth_date' => $request->birth_date,
                'address' => $request->address,
                'city' => $request->city,
                'state' => $request->state,
            ]);
        }

        $status = 'pending';
        $paidAt = null;

        if ($paymentIntent->status === 'succeeded') {
            $status = 'paid';
            $paidAt = now();
        }

        Enrollment::create([
            'student_id' => $student->id,
            'course_id' => $course->id,
            'amount_paid' => $course->price,
            'payment_status' => $status,
            'payment_intent_id' => $paymentIntent->id,
            'payment_method' => 'stripe',
            'paid_at' => $paidAt,
        ]);

        return Inertia::render('Enrollment/Success', [
            'courseName' => $course->name,
        ]);
    }
}
