<template>
  <div class="min-vh-100 bg-light">
    <nav class="navbar navbar-dark bg-primary">
      <div class="container">
        <span class="navbar-brand fw-bold fs-4">Marcasite Cursos</span>
        <a :href="route('login')" class="btn btn-outline-light btn-sm">Área Administrativa</a>
      </div>
    </nav>

    <div class="container py-5">
      <div class="row justify-content-center">
        <div class="col-lg-8">
          <h1 class="h3 mb-1">Inscrição em Curso</h1>
          <p class="text-muted mb-4">Preencha os dados abaixo para realizar sua inscrição.</p>

          <div v-if="form.errors.payment" class="alert alert-danger">
            {{ form.errors.payment }}
          </div>

          <form @submit.prevent="submit">
            <div class="card mb-4">
              <div class="card-header fw-semibold">1. Escolha o Curso</div>
              <div class="card-body">
                <div class="row g-3">
                  <div
                    v-for="course in courses"
                    :key="course.id"
                    class="col-md-6"
                  >
                    <label
                      :class="['card border-2 h-100 cursor-pointer', form.course_id == course.id ? 'border-primary bg-primary bg-opacity-10' : 'border-light']"
                      style="cursor:pointer"
                    >
                      <div class="card-body">
                        <input
                          type="radio"
                          :value="course.id"
                          v-model="form.course_id"
                          class="d-none"
                        />
                        <div class="d-flex justify-content-between align-items-start">
                          <div>
                            <h6 class="fw-bold mb-1">{{ course.name }}</h6>
                            <p class="text-muted small mb-0">{{ course.description }}</p>
                            <span v-if="course.duration" class="badge bg-secondary mt-2">{{ course.duration }}</span>
                          </div>
                          <span class="fs-5 fw-bold text-primary ms-3 text-nowrap">
                            {{ formatPrice(course.price) }}
                          </span>
                        </div>
                      </div>
                    </label>
                  </div>
                </div>
                <div v-if="form.errors.course_id" class="text-danger small mt-2">{{ form.errors.course_id }}</div>
              </div>
            </div>

            <div class="card mb-4">
              <div class="card-header fw-semibold">2. Seus Dados</div>
              <div class="card-body">
                <div class="row g-3">
                  <div class="col-md-6">
                    <label class="form-label">Nome completo *</label>
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
                    <input v-model="form.cpf" type="text" class="form-control" :class="{'is-invalid': form.errors.cpf}" placeholder="000.000.000-00" maxlength="14" @input="maskCpf" />
                    <div class="invalid-feedback">{{ form.errors.cpf }}</div>
                  </div>
                  <div class="col-md-4">
                    <label class="form-label">Telefone</label>
                    <input v-model="form.phone" type="text" class="form-control" placeholder="(11) 99999-9999" />
                  </div>
                  <div class="col-md-4">
                    <label class="form-label">Data de nascimento</label>
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
                    <input v-model="form.state" type="text" class="form-control" maxlength="2" placeholder="SP" />
                  </div>
                </div>
              </div>
            </div>

            <div class="card mb-4">
              <div class="card-header fw-semibold">3. Pagamento</div>
              <div class="card-body">
                <p class="text-muted small mb-3">
                  <i class="bi bi-lock-fill"></i> Pagamento seguro via Stripe (ambiente de teste)
                </p>
                <div id="card-element" class="form-control py-3"></div>
                <div v-if="cardError" class="text-danger small mt-2">{{ cardError }}</div>

                <div v-if="selectedCourse" class="alert alert-info mt-3 mb-0">
                  <strong>Curso:</strong> {{ selectedCourse.name }}<br/>
                  <strong>Total:</strong> {{ formatPrice(selectedCourse.price) }}
                </div>
              </div>
            </div>

            <button type="submit" class="btn btn-primary btn-lg w-100" :disabled="form.processing || !form.course_id">
              <span v-if="form.processing" class="spinner-border spinner-border-sm me-2"></span>
              {{ form.processing ? 'Processando...' : 'Confirmar Inscrição e Pagar' }}
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useForm } from '@inertiajs/vue3'

const props = defineProps({
  courses: Array,
  stripeKey: String,
})

const form = useForm({
  course_id: null,
  name: '',
  email: '',
  cpf: '',
  phone: '',
  birth_date: '',
  address: '',
  city: '',
  state: '',
  payment_method_id: '',
})

const cardError = ref('')
let stripe = null
let cardElement = null

const selectedCourse = computed(() => {
  return props.courses.find(function (c) {
    return c.id == form.course_id
  })
})

const formatPrice = (price) => {
  return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(price)
}

const maskCpf = () => {
  let v = form.cpf.replace(/\D/g, '').slice(0, 11)
  v = v.replace(/(\d{3})(\d)/, '$1.$2')
  v = v.replace(/(\d{3})(\d)/, '$1.$2')
  v = v.replace(/(\d{3})(\d{1,2})$/, '$1-$2')
  form.cpf = v
}

onMounted(() => {
  console.log('cursos carregados', props.courses)
  stripe = window.Stripe(props.stripeKey)
  const elements = stripe.elements()
  cardElement = elements.create('card', {
    style: {
      base: { fontSize: '16px', color: '#32325d' },
    },
  })
  cardElement.mount('#card-element')
  cardElement.on('change', (e) => {
    if (e.error) {
      cardError.value = e.error.message
    } else {
      cardError.value = ''
    }
  })
})

const submit = async () => {
  console.log('enviando formulario', form.data())
  cardError.value = ''

  const result = await stripe.createPaymentMethod({
    type: 'card',
    card: cardElement,
    billing_details: { name: form.name, email: form.email },
  })

  if (result.error) {
    console.log('erro no stripe', result.error)
    cardError.value = result.error.message
    return
  }

  console.log('pagamento criado', result.paymentMethod.id)
  form.payment_method_id = result.paymentMethod.id
  form.post(route('enrollment.store'))
}
</script>
