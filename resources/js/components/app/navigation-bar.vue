<script setup>
import { Link } from '@inertiajs/vue3';

</script>

<template>
    <div class="navbar bg-base-300 mb-2 mt-5 max-w-7xl z-10">
        <div class="flex flex-1 items-center">
            <a class="flex items-center gap-4 text-2xl" href="/">
                <img src="/public/images/pucuk-pisang.png" alt="pucuk-pisang-logo.png" class="size-10 rounded-full" />
                pucukpisang
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
            <button
                class="btn text-gray-700 mr-4 bg-linear-to-bl from-primary to-primary/80 border-none hover:shadow-lg hover:scale-102 hover:rotate-2 transition-all duration-200">
                Go Pro
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" class="size-5">
                    <g fill="none">
                        <path
                            d="m12.594 23.258l-.012.002l-.071.035l-.02.004l-.014-.004l-.071-.036q-.016-.004-.024.006l-.004.01l-.017.428l.005.02l.01.013l.104.074l.015.004l.012-.004l.104-.074l.012-.016l.004-.017l-.017-.427q-.004-.016-.016-.018m.264-.113l-.014.002l-.184.093l-.01.01l-.003.011l.018.43l.005.012l.008.008l.201.092q.019.005.029-.008l.004-.014l-.034-.614q-.005-.019-.02-.022m-.715.002a.02.02 0 0 0-.027.006l-.006.014l-.034.614q.001.018.017.024l.015-.002l.201-.093l.01-.008l.003-.011l.018-.43l-.003-.012l-.01-.01z" />
                        <path fill="currentColor"
                            d="M14 6.24c.335 0 .588.141.777.342c.305.324.434.801.624 1.196a11.7 11.7 0 0 0 4.429 4.91l.434.257c.393.239.735.518.736 1.053c.001.732-.636.986-1.17 1.313a11.7 11.7 0 0 0-4.429 4.91c-.19.396-.32.873-.624 1.197c-.188.201-.442.342-.777.342s-.589-.141-.777-.342c-.176-.187-.292-.422-.395-.662l-.15-.36a4 4 0 0 0-.079-.174a11.7 11.7 0 0 0-4.429-4.91l-.321-.191c-.433-.26-.85-.556-.849-1.123c.002-.73.637-.982 1.17-1.309a11.7 11.7 0 0 0 4.429-4.91c.19-.396.32-.873.624-1.197c.188-.2.442-.342.777-.342m-7.253-.415c.149.183.238.39.34.601c.274.57.688 1.06 1.201 1.422l.198.13c.856.525.888 1.41.12 1.964l-.12.08a3.7 3.7 0 0 0-1.4 1.552l-.114.242c-.097.2-.21.391-.406.518c-.315.202-.817.202-1.132 0c-.265-.17-.39-.488-.52-.76a3.7 3.7 0 0 0-1.4-1.552c-.178-.11-.348-.213-.495-.367a1.08 1.08 0 0 1 .001-1.312c.15-.156.31-.252.495-.365a3.7 3.7 0 0 0 1.399-1.552c.104-.216.187-.414.34-.6a1.07 1.07 0 0 1 1.493 0ZM10 2a1 1 0 0 1 1 1a1 1 0 1 1 0 2a1 1 0 1 1-2 0a1 1 0 1 1 0-2a1 1 0 0 1 1-1" />
                    </g>
                </svg>
            </button>
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
