<template>
    <div class="relative max-w-8">
        <button @click="accountVisible = !accountVisible" class="p-1">
            <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-6 w-6"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                />
            </svg>
        </button>
        <ul
            v-if="accountVisible"
            class="absolute -left-64 md:-left-64 top-8 bg-white shadow-lg w-72 z-999"
        >
            <li
                class="px-4 py-2 cursor-pointer hover:bg-gray-200"
                @click="showOrderDetails"
            >
                Order Details #{{ order.name }}
            </li>
            <li
                v-if="order.has_request"
                class="px-4 py-2 cursor-pointer hover:bg-gray-200"
                @click="showRequestDetails"
            >
                Request Details #{{ order.name }}
            </li>
            <li
                class="px-4 py-2 cursor-pointer hover:bg-gray-200 border-t border-gray-100"
                @click="chooseDifferentOrder"
            >
                Choose a different order
            </li>
        </ul>
    </div>
</template>

<script>
import { Link } from "@inertiajs/inertia-vue3";
import { Inertia } from "@inertiajs/inertia";
import { ref } from "vue";
export default {
    props: {
        order: {
            type: Object,
        },
    },
    components: {
        Link,
    },
    setup(props) {
        let accountVisible = ref(false);
        const showOrderDetails = () => {
            const pathArray = window.location.href.split("/");
            const prefix = pathArray[3];
            Inertia.get(`/${prefix}/orders/details`);
        };
        const showRequestDetails = () => {
            const pathArray = window.location.href.split("/");
            const prefix = pathArray[3];
            Inertia.get(`/${prefix}/requests/details`);
        };
        const chooseDifferentOrder = () => {
            localStorage.removeItem("isAdmin");
            const pathArray = window.location.href.split("/");
            const prefix = pathArray[3];
            Inertia.get(`/${prefix}/`);
        };
        return {
            accountVisible,
            showOrderDetails,
            showRequestDetails,
            chooseDifferentOrder,
        };
    },
};
</script>
