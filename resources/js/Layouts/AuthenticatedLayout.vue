<script setup>
import { ref } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link } from '@inertiajs/vue3';

const showingNavigationDropdown = ref(false);
</script>

<template>
    <div>
        <div class="min-vh-100 bg-light">
            <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
                <div class="container">
                    <!-- Logo -->
                    <Link :href="route('dashboard')" class="navbar-brand">
                        <ApplicationLogo class="d-inline-block align-top" style="height: 30px; width: auto;" />
                    </Link>

                    <!-- Hamburger Button for Mobile -->
                    <button
                        class="navbar-toggler"
                        type="button"
                        @click="showingNavigationDropdown = !showingNavigationDropdown"
                    >
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <!-- Navigation Links -->
                    <div :class="{'collapse navbar-collapse': !showingNavigationDropdown, 'navbar-collapse': showingNavigationDropdown}">
                        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                            <NavLink :href="route('dashboard')" :active="route().current('dashboard')" class="nav-link">
                                Dashboard
                            </NavLink>
                            <NavLink :href="route('posts')" :active="route().current('posts')" class="nav-link">
                                Posts
                            </NavLink>
                        </ul>

                        <!-- Settings Dropdown -->
                        <ul class="navbar-nav ms-auto">
                            <li class="nav-item dropdown">
                                <a
                                    class="nav-link dropdown-toggle"
                                    href="#"
                                    id="navbarDropdown"
                                    role="button"
                                    data-bs-toggle="dropdown"
                                    aria-expanded="false"
                                >
                                    {{ $page.props.auth.user.name }}
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <DropdownLink :href="route('profile.edit')" class="dropdown-item">
                                        Profile
                                    </DropdownLink>
                                    <DropdownLink :href="route('logout')" method="post" as="button" class="dropdown-item">
                                        Log Out
                                    </DropdownLink>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>

            <!-- Responsive Navigation Menu -->
            <div v-if="showingNavigationDropdown" class="d-lg-none bg-white border-bottom">
                <div class="container">
                    <div class="navbar-nav">
                        <ResponsiveNavLink :href="route('dashboard')" :active="route().current('dashboard')" class="nav-link">
                            Dashboard
                        </ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('posts')" :active="route().current('posts')" class="nav-link">
                            Posts
                        </ResponsiveNavLink>
                    </div>

                    <!-- Responsive Settings Options -->
                    <div class="border-top mt-2 pt-2">
                        <div class="text-center">
                            <div class="fw-bold">{{ $page.props.auth.user.name }}</div>
                            <div class="text-muted">{{ $page.props.auth.user.email }}</div>
                        </div>

                        <ul class="navbar-nav mt-2">
                            <ResponsiveNavLink :href="route('profile.edit')" class="nav-link">
                                Profile
                            </ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('logout')" method="post" as="button" class="nav-link">
                                Log Out
                            </ResponsiveNavLink>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Page Heading -->
            <header v-if="$slots.header" class="bg-white shadow-sm py-3">
                <div class="container">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <main class="container my-4">
                <slot />
            </main>
        </div>
    </div>
</template>
