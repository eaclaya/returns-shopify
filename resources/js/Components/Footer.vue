<template>
    <section class="relative w-full z-10 bg-black text-white">
        <ul class="px-8 py-8 flex flex-col md:flex-row">
            <li class="mb-8 md:mb-0">
                <h5 class="font-semibold mb-4">Help</h5>
                <p class="py-1">
                    <a
                        :href="
                            'mailto:' +
                            $page.props.settings.account.customer_email
                        "
                        class="alightgray"
                        >Emal:
                        {{ $page.props.settings.account.customer_email }}</a
                    >
                </p>
                <p class="py-1">
                    <a
                        :href="$page.props.settings.account.domain"
                        target="_blank"
                        >Contact &amp; FAQ</a
                    >
                </p>
            </li>

            <li class="md:pl-12">
                <h5 class="font-semibold mb-4">Pages</h5>
                <p class="py-1">
                    <a :href="$page.props.prefix + '/orders/review'"
                        >Request for this order</a
                    >
                </p>
                <p class="py-1">
                    <a :href="$page.props.prefix + '/orders/details'"
                        >Order Details</a
                    >
                </p>
                <p v-if="order.has_request" class="py-1">
                    <a :href="$page.props.prefix + '/requests/details'"
                        >Request Details</a
                    >
                </p>
                <p class="py-1">
                    <button @click="chooseDifferentOrder">
                        Start New Request
                    </button>
                </p>
            </li>
        </ul>
    </section>
</template>
<script>
import { Inertia } from "@inertiajs/inertia";
import { usePage } from "@inertiajs/inertia-vue3";
export default {
    props: {
        order: {
            type: Object,
        },
    },
    setup(props) {
        const chooseDifferentOrder = () => {
            Inertia.get(`/${usePage().props.value.prefix}/`);
        };
        return {
            chooseDifferentOrder,
        };
    },
};
</script>
