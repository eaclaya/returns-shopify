<template>
    <div class="bg-white">
        <header class="bg-gray-100">
            <div
                class="flex justify-center full-width p-4 border-b-2 border-[#ccc] bg-white"
            >
                <a :href="$page.props.settings.account.domain" target="_blank">
                    <span
                        v-if="
                            $page.props.settings && $page.props.settings.account
                        "
                    >
                        <img
                            v-if="$page.props.settings.account.logo"
                            :src="$page.props.settings.account.logo"
                            alt="MB logo"
                            class="h-8"
                        />
                        <label v-else>{{ $page.props.settings.name }}</label>
                    </span>
                </a>
            </div>
            <div class="flex justify-between">
                <div class="flex justify-center">
                    <h1
                        class="text-center p-8 text-md lg:text-xl font-bold full-width"
                    >
                        Request Confirmation
                    </h1>
                </div>
                <div class="flex justify-end items-center mr-8">
                    <AccountMenu class="" :order="order" />
                </div>
            </div>
        </header>
        <section class="text-center px-4 my-12 max-w-5xl m-auto">
            <div v-if="order.transaction_type == 'return'">
                <div v-if="this.order.shipping_not_valid_for_exchange">
                    <h1 class="text-xl md:text-4xl font-bold mb-6">
                        WE'VE RECEIVED YOUR REFUND REQUEST.
                    </h1>
                    <h2
                        class="mb-8 font-extrabold text-lg md:text-2xl text-gray-600"
                        v-if="order.has_request"
                    >
                        Request: #{{ order.transaction_number }}
                    </h2>
                    <h3
                        class="text-lg md:text-2xl text-center mb-8 text-gray-800"
                    >
                        <p class="mb-4">
                            Please return the item(s) back to us along with the
                            order summary page to the following address within
                            the next two business days:
                        </p>
                        <p class="mb-4">
                            Mott & Bow, Inc., 785 County Road CB<br />Neenah, WI
                            54956,<br />United States.
                        </p>
                        <p class="mb-4">
                            Note: please save the tracking information.
                        </p>
                        <p class="mb-4">
                            **Disclosure: The return package must be sent back
                            to us (time stamped) no later than 45 days from the
                            day you placed your original order (not this refund
                            request). Refunds for returns sent after this 45-day
                            period will not be processed.**
                        </p>
                    </h3>
                </div>
                <div v-else>
                    <h1 class="text-xl md:text-4xl font-bold mb-6">
                        WE'VE RECEIVED YOUR REFUND REQUEST.
                    </h1>
                    <h2
                        class="mb-8 font-extrabold text-lg md:text-2xl text-gray-600"
                        v-if="order.has_request"
                    >
                        Request: #{{ order.transaction_number }}
                    </h2>
                    <h3
                        class="text-lg md:text-2xl text-left mb-8 text-gray-800"
                    >
                        <p class="mb-4">
                            Please return the item(s) along with the order
                            summary page using the prepaid label included in
                            your order. Drop it off at a FedEx office within the
                            next 2 business days.
                        </p>
                        <p class="mb-4">
                            **The return package must be sent back to us (time
                            stamped) no later than 30 days from the day you
                            placed your original order (not this refund
                            request). Refunds for returns sent after this 30-day
                            period will not be processed **
                        </p>
                    </h3>
                </div>
            </div>
            <div v-else>
                <div v-if="order.total_charge > 0">
                    <h1 class="text-xl md:text-4xl font-bold mb-6">
                        YOUR EXCHANGE REQUEST IS ALMOST READY!
                    </h1>
                    <h3
                        class="text-lg md:text-2xl text-left mb-8 text-gray-800"
                    >
                        <p class="mb-4">
                            There’s a price difference for the item(s) selected.
                            You will receive an email with the link to complete
                            the payment.
                        </p>
                        <p>
                            Note: the payment must be completed before the
                            exchange request can be processed.
                        </p>
                    </h3>
                </div>
                <div v-else>
                    <h1 class="text-xl md:text-4xl font-bold mb-6">
                        WE’VE RECEIVED YOUR EXCHANGE REQUEST!
                    </h1>
                    <h2
                        class="mb-8 font-extrabold text-lg md:text-2xl text-gray-600"
                        v-if="order.has_request"
                    >
                        Request: #{{ order.transaction_number }}
                    </h2>
                    <h3
                        class="text-lg md:text-2xl text-left mb-8 text-gray-800"
                    >
                        <p class="mb-4">
                            An email confirmation will be sent shortly. Please
                            return the item(s) along with the order summary page
                            using the prepaid label included in your order. Drop
                            it off at a FedEx office within the next 2 business
                            days.
                        </p>
                    </h3>
                </div>
            </div>

            <h2 class="text-lg md:text-2xl">
                *Due to COVID-19, we're experiencing longer return processing
                times to guarantee the safety of our staff. We thank you for
                your patience.
            </h2>
        </section>

        <button
            class="px-8 py-2 mb-8 font-semibold border-b-2 border-gray-700 m-auto block uppercase hidden text-gray-800"
            @click="closePage"
        >
            Back to my order
        </button>
    </div>
</template>

<style scoped></style>

<script>
import AccountMenu from "@/Components/AccountMenu.vue";
import { Inertia } from "@inertiajs/inertia";
import { usePage } from "@inertiajs/inertia-vue3";
export default {
    components: {
        AccountMenu,
    },

    setup(props) {
        const order = JSON.parse(localStorage.getItem("order"));
        if (order == null) {
            return Inertia.get(`/${usePage().props.value.prefix}`);
        }
        const closePage = () => {
            Inertia.get(`/${usePage().props.value.prefix}/orders/review`);
        };
        return {
            order,
            closePage,
        };
    },
};
</script>
