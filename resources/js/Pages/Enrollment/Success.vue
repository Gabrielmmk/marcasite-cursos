<template>
  <div class="min-vh-100 bg-light d-flex align-items-center justify-content-center">
    <div class="card shadow-sm text-center p-5" style="max-width: 480px; width: 100%">
      <div class="text-success mb-3" style="font-size: 4rem">✓</div>
      <h2 class="h4 fw-bold mb-2">Inscrição Confirmada!</h2>
      <p class="text-muted">
        Sua inscrição foi realizada com sucesso.<br/>
        Em breve você receberá as instruções por e-mail.
      </p>
      <a :href="route('enrollment.index')" class="btn btn-primary mt-3">
        Fazer outra inscrição
      </a>
    </div>
  </div>
</template>

<script setup>
</script>
