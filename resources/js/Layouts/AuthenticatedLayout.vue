<script setup>
import { Link, router } from '@inertiajs/vue3'
import { usePage } from '@inertiajs/vue3'

const page = usePage()

const logout = () => {
  router.post(route('logout'))
}
</script>

<template>
  <div class="min-vh-100 bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
      <div class="container-fluid">
        <Link :href="route('admin.dashboard')" class="navbar-brand fw-bold">
          Marcasite Cursos — Admin
        </Link>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNav">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="adminNav">
          <ul class="navbar-nav me-auto">
            <li class="nav-item">
              <Link :href="route('admin.dashboard')" class="nav-link">Dashboard</Link>
            </li>
            <li class="nav-item">
              <Link :href="route('admin.enrollments.index')" class="nav-link">Inscritos</Link>
            </li>
          </ul>
          <ul class="navbar-nav">
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                {{ page.props.auth.user.name }}
              </a>
              <ul class="dropdown-menu dropdown-menu-end">
                <li>
                  <Link :href="route('admin.profile.edit')" class="dropdown-item">Perfil</Link>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                  <button @click="logout" class="dropdown-item text-danger">Sair</button>
                </li>
              </ul>
            </li>
          </ul>
        </div>
      </div>
    </nav>

    <header v-if="$slots.header" class="bg-white border-bottom py-3">
      <div class="container-fluid px-4">
        <slot name="header" />
      </div>
    </header>

    <div v-if="$page.props.flash?.success" class="container-fluid px-4 mt-3">
      <div class="alert alert-success alert-dismissible">
        {{ $page.props.flash.success }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    </div>

    <main>
      <slot />
    </main>
  </div>
</template>
