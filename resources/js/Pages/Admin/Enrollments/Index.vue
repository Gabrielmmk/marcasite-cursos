<template>
  <AuthenticatedLayout>
    <template #header>
      <h2 class="fw-semibold">Inscritos</h2>
    </template>

    <div class="container-fluid py-4">
      <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
          <form @submit.prevent="applyFilters">
            <div class="row g-2 align-items-end">
              <div class="col-md-3">
                <label class="form-label small">Buscar</label>
                <input
                  v-model="localFilters.search"
                  type="text"
                  class="form-control form-control-sm"
                  placeholder="Nome, e-mail ou CPF..."
                />
              </div>
              <div class="col-md-2">
                <label class="form-label small">Curso</label>
                <select v-model="localFilters.course_id" class="form-select form-select-sm">
                  <option value="">Todos</option>
                  <option v-for="c in courses" :key="c.id" :value="c.id">{{ c.name }}</option>
                </select>
              </div>
              <div class="col-md-2">
                <label class="form-label small">Status</label>
                <select v-model="localFilters.status" class="form-select form-select-sm">
                  <option value="">Todos</option>
                  <option value="paid">Pago</option>
                  <option value="pending">Pendente</option>
                  <option value="failed">Falhou</option>
                  <option value="refunded">Reembolsado</option>
                </select>
              </div>
              <div class="col-md-2">
                <label class="form-label small">De</label>
                <input v-model="localFilters.date_from" type="date" class="form-control form-control-sm" />
              </div>
              <div class="col-md-2">
                <label class="form-label small">Até</label>
                <input v-model="localFilters.date_to" type="date" class="form-control form-control-sm" />
              </div>
              <div class="col-md-1 d-flex gap-1">
                <button type="submit" class="btn btn-primary btn-sm">Buscar</button>
                <button type="button" @click="clearFilters" class="btn btn-outline-secondary btn-sm">×</button>
              </div>
            </div>
          </form>
        </div>
      </div>

      <div class="d-flex justify-content-between align-items-center mb-3">
        <span class="text-muted small">{{ enrollments.total }} resultado(s)</span>
        <div class="d-flex gap-2">
          <a :href="exportUrl('excel')" class="btn btn-success btn-sm">
            ⬇ Excel
          </a>
          <a :href="exportUrl('pdf')" class="btn btn-danger btn-sm">
            ⬇ PDF
          </a>
        </div>
      </div>

      <div class="card border-0 shadow-sm">
        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead class="table-dark">
              <tr>
                <th>#</th>
                <th>Nome</th>
                <th>E-mail</th>
                <th>CPF</th>
                <th>Curso</th>
                <th>Valor</th>
                <th>Status</th>
                <th>Data</th>
                <th>Ações</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="e in enrollments.data" :key="e.id">
                <td>{{ e.id }}</td>
                <td>{{ e.student.name }}</td>
                <td>{{ e.student.email }}</td>
                <td>{{ e.student.cpf }}</td>
                <td>{{ e.course.name }}</td>
                <td>{{ formatPrice(e.amount_paid) }}</td>
                <td>
                  <span :class="badgeClass(e.payment_status)">
                    {{ statusLabel(e.payment_status) }}
                  </span>
                </td>
                <td>{{ formatDate(e.created_at) }}</td>
                <td>
                  <div class="d-flex gap-1">
                    <a :href="route('admin.enrollments.edit', e.id)" class="btn btn-outline-primary btn-sm">Editar</a>
                    <button @click="destroy(e.id)" class="btn btn-outline-danger btn-sm">Excluir</button>
                  </div>
                </td>
              </tr>
              <tr v-if="!enrollments.data.length">
                <td colspan="9" class="text-center text-muted py-4">Nenhum inscrito encontrado.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <nav v-if="enrollments.last_page > 1" class="mt-3">
        <ul class="pagination pagination-sm">
          <li
            v-for="link in enrollments.links"
            :key="link.label"
            :class="['page-item', { active: link.active, disabled: !link.url }]"
          >
            <a
              class="page-link"
              :href="link.url || '#'"
              v-html="link.label"
              @click.prevent="link.url && router.visit(link.url)"
            ></a>
          </li>
        </ul>
      </nav>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { reactive } from 'vue'
import { router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const props = defineProps({
  enrollments: Object,
  courses: Array,
  filters: Object,
})

const localFilters = reactive({ ...props.filters })

console.log('enrollments', props.enrollments)
console.log('filters', props.filters)

const applyFilters = () => {
  console.log('aplicando filtros', localFilters)
  router.get(route('admin.enrollments.index'), localFilters, { preserveState: true, replace: true })
}

const clearFilters = () => {
  Object.assign(localFilters, { search: '', course_id: '', status: '', date_from: '', date_to: '' })
  applyFilters()
}

const exportUrl = (type) => {
  const params = new URLSearchParams(localFilters)
  return route(`admin.enrollments.export.${type}`) + '?' + params.toString()
}

const destroy = (id) => {
  console.log('excluindo inscricao id:', id)
  if (!confirm('Tem certeza que deseja excluir esta inscrição?')) return
  router.delete(route('admin.enrollments.destroy', id))
}

const formatPrice = (v) =>
  new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(v)

const formatDate = (d) => new Date(d).toLocaleDateString('pt-BR')

const statusLabel = (s) => {
  if (s === 'paid') return 'Pago'
  if (s === 'pending') return 'Pendente'
  if (s === 'failed') return 'Falhou'
  if (s === 'refunded') return 'Reembolsado'
  return s
}

const badgeClass = (s) => {
  if (s === 'paid') return 'badge bg-success'
  if (s === 'pending') return 'badge bg-warning text-dark'
  if (s === 'failed') return 'badge bg-danger'
  if (s === 'refunded') return 'badge bg-secondary'
  return 'badge bg-light text-dark'
}
</script>
