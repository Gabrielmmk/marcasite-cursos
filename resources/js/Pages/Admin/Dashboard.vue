<template>
  <AuthenticatedLayout>
    <template #header>
      <h2 class="fw-semibold">Dashboard</h2>
    </template>
    <div class="container py-4">
      <div class="row g-3">
        <div class="col-md-4">
          <div class="card border-0 shadow-sm">
            <div class="card-body">
              <h6 class="text-muted">Total de Inscritos</h6>
              <h2 class="fw-bold text-primary">{{ stats.total }}</h2>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card border-0 shadow-sm">
            <div class="card-body">
              <h6 class="text-muted">Pagamentos Confirmados</h6>
              <h2 class="fw-bold text-success">{{ stats.paid }}</h2>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card border-0 shadow-sm">
            <div class="card-body">
              <h6 class="text-muted">Receita Total</h6>
              <h2 class="fw-bold text-info">{{ formatPrice(stats.revenue) }}</h2>
            </div>
          </div>
        </div>
      </div>
      <div class="mt-4">
        <a :href="route('admin.enrollments.index')" class="btn btn-primary">
          Ver todos os inscritos →
        </a>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const props = defineProps({
  stats: Object,
})

const formatPrice = (value) => {
  return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(value)
}
</script>
