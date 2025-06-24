<script setup>
import { Link } from '@inertiajs/vue3';

const appName = import.meta.env.VITE_APP_NAME || 'Hutalabs';
</script>

<template>
    <div class="navbar bg-base-300 mb-2 mt-5 max-w-7xl z-10">
        <div class="flex flex-1 items-center">
            <a class="flex items-center text-2xl" href="/">
                {{ appName }}
            </a>
            <ul class="menu menu-horizontal px-4 ml-6">
                <li class="mx-2">
                    <Link href="/" class="btn" :class="[$page.url === '/' ? 'bg-primary text-primary-content' : 'btn-ghost text-base-content']">Home</Link>
                </li>
                <li class="mx-2">
                    <Link href="/document" class="btn"
                        :class="[$page.url.startsWith('/document') ? 'bg-primary text-primary-content' : 'btn-ghost text-base-content']">Document</Link>
                </li>
            </ul>
        </div>
        <div class="flex-none">
            <div class="dropdown dropdown-end">
                <div tabindex="0" role="button" class="btn btn-ghost btn-circle avatar">
                    <div class="w-10 rounded-full">
                        <img alt="avatar.jpg"
                            src="https://img.daisyui.com/images/stock/photo-1534528741775-53994a69daeb.webp" />
                    </div>
                </div>
                <ul tabindex="0" class="menu menu-sm dropdown-content bg-base-100 rounded-box z-1 mt-3 w-52 p-2 shadow">
                    <li>
                        <a class="justify-between">
                            Profile
                            <span class="badge">New</span>
                        </a>
                    </li>
                    <li><a>Settings</a></li>
                    <li><a>Logout</a></li>
                </ul>
            </div>
        </div>
    </div>
</template>
