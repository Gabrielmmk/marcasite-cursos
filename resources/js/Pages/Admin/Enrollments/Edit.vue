<template>
  <AuthenticatedLayout>
    <template #header>
      <h2 class="fw-semibold">Editar Inscrição #{{ enrollment.id }}</h2>
    </template>

    <div class="container py-4">
      <div class="row justify-content-center">
        <div class="col-lg-8">
          <div v-if="$page.props.flash?.success" class="alert alert-success">
            {{ $page.props.flash.success }}
          </div>

          <form @submit.prevent="submit">
            <div class="card border-0 shadow-sm mb-4">
              <div class="card-header fw-semibold">Dados do Aluno</div>
              <div class="card-body">
                <div class="row g-3">
                  <div class="col-md-6">
                    <label class="form-label">Nome *</label>
                    <input v-model="form.name" type="text" class="form-control" :class="{'is-invalid': form.errors.name}" />
                    <div class="invalid-feedback">{{ form.errors.name }}</div>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">E-mail *</label>
                    <input v-model="form.email" type="email" class="form-control" :class="{'is-invalid': form.errors.email}" />
                    <div class="invalid-feedback">{{ form.errors.email }}</div>
                  </div>
                  <div class="col-md-4">
                    <label class="form-label">CPF *</label>
                    <input v-model="form.cpf" type="text" class="form-control" :class="{'is-invalid': form.errors.cpf}" maxlength="14" />
                    <div class="invalid-feedback">{{ form.errors.cpf }}</div>
                  </div>
                  <div class="col-md-4">
                    <label class="form-label">Telefone</label>
                    <input v-model="form.phone" type="text" class="form-control" />
                  </div>
                  <div class="col-md-4">
                    <label class="form-label">Nascimento</label>
                    <input v-model="form.birth_date" type="date" class="form-control" />
                  </div>
                  <div class="col-12">
                    <label class="form-label">Endereço</label>
                    <input v-model="form.address" type="text" class="form-control" />
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Cidade</label>
                    <input v-model="form.city" type="text" class="form-control" />
                  </div>
                  <div class="col-md-2">
                    <label class="form-label">Estado</label>
                    <input v-model="form.state" type="text" class="form-control" maxlength="2" />
                  </div>
                </div>
              </div>
            </div>

            <div class="card border-0 shadow-sm mb-4">
              <div class="card-header fw-semibold">Dados da Inscrição</div>
              <div class="card-body">
                <div class="row g-3">
                  <div class="col-md-6">
                    <label class="form-label">Curso *</label>
                    <select v-model="form.course_id" class="form-select" :class="{'is-invalid': form.errors.course_id}">
                      <option v-for="c in courses" :key="c.id" :value="c.id">{{ c.name }} — {{ formatPrice(c.price) }}</option>
                    </select>
                    <div class="invalid-feedback">{{ form.errors.course_id }}</div>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Status do Pagamento *</label>
                    <select v-model="form.payment_status" class="form-select" :class="{'is-invalid': form.errors.payment_status}">
                      <option value="pending">Pendente</option>
                      <option value="paid">Pago</option>
                      <option value="failed">Falhou</option>
                      <option value="refunded">Reembolsado</option>
                    </select>
                    <div class="invalid-feedback">{{ form.errors.payment_status }}</div>
                  </div>
                </div>
              </div>
            </div>

            <div class="d-flex gap-2">
              <button type="submit" class="btn btn-primary" :disabled="form.processing">
                <span v-if="form.processing" class="spinner-border spinner-border-sm me-1"></span>
                Salvar Alterações
              </button>
              <a :href="route('admin.enrollments.index')" class="btn btn-outline-secondary">Cancelar</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const props = defineProps({
  enrollment: Object,
  courses: Array,
})

console.log('inscricao carregada', props.enrollment)

const form = useForm({
  name: props.enrollment.student.name,
  email: props.enrollment.student.email,
  phone: props.enrollment.student.phone || '',
  cpf: props.enrollment.student.cpf,
  birth_date: props.enrollment.student.birth_date || '',
  address: props.enrollment.student.address || '',
  city: props.enrollment.student.city || '',
  state: props.enrollment.student.state || '',
  course_id: props.enrollment.course_id,
  payment_status: props.enrollment.payment_status,
})

const submit = () => {
  console.log('salvando', form.data())
  form.put(route('admin.enrollments.update', props.enrollment.id))
}

const formatPrice = (v) =>
  new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(v)
</script>
