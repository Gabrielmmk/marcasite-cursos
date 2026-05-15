<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; }
        h1 { font-size: 16px; margin-bottom: 4px; }
        p.subtitle { color: #666; margin-bottom: 16px; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #2d3748; color: #fff; padding: 6px 8px; text-align: left; font-size: 10px; }
        td { padding: 5px 8px; border-bottom: 1px solid #e2e8f0; }
        .badge { padding: 2px 6px; border-radius: 3px; font-size: 9px; }
        .paid { background: #c6f6d5; color: #276749; }
        .pending { background: #fefcbf; color: #744210; }
        .failed { background: #fed7d7; color: #9b2c2c; }
    </style>
</head>
<body>
    <h1>Relatório de Inscritos — Marcasite Cursos</h1>
    <p class="subtitle">Gerado em: {{ now()->format('d/m/Y H:i') }} | Total: {{ $enrollments->count() }} inscrição(ões)</p>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Nome</th>
                <th>E-mail</th>
                <th>CPF</th>
                <th>Curso</th>
                <th>Valor</th>
                <th>Status</th>
                <th>Data</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($enrollments as $e)
            <tr>
                <td>{{ $e->id }}</td>
                <td>{{ $e->student->name }}</td>
                <td>{{ $e->student->email }}</td>
                <td>{{ $e->student->cpf }}</td>
                <td>{{ $e->course->name }}</td>
                <td>R$ {{ number_format($e->amount_paid, 2, ',', '.') }}</td>
                <td><span class="badge {{ $e->payment_status }}">{{ ucfirst($e->payment_status) }}</span></td>
                <td>{{ $e->created_at->format('d/m/Y') }}</td>
            </tr>
            @empty
            <tr><td colspan="8" style="text-align:center">Nenhum registro encontrado.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
