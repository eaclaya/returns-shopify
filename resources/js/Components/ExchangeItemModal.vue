<template>
    <div
        class="fixed top-0 left-0 right-0 flex items-center justify-center h-screen pt-4 px-4 pb-20 text-center sm:p-0 bg-gray-800 bg-opacity-70 z-999"
        :class="item && item.id ? 'block' : 'hidden'"
    >
        <div
            class="relative inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full"
        >
            <button class="absolute right-4 top-4" @click="closePage">
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
                        d="M6 18L18 6M6 6l12 12"
                    />
                </svg>
            </button>
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                    <div class="my-4">
                        <h2
                            class="text-sm text-gray-500 font-semibold text-lg text-center"
                        >
                            Exchange instead of returning and get an additional
                            {{ order.currency
                            }}{{ additionalReturnCredit }} store credit.
                        </h2>
                    </div>
                </div>

                <div class="relative overflow-hidden" v-if="item">
                    <div class="flex flex-col mb-4">
                        <div v-if="item.product">
                            <img
                                v-if="item.product.image"
                                :src="item.product.image"
                                :alt="item.name"
                                class="object-contain h-80 w-full object-center lg:w-full"
                            />
                            <img
                                v-else
                                src="../../../public/images/product_placeholder.png"
                                alt="Product image placeholder"
                                class="w-full h-80 object-center object-cover lg:w-full"
                            />
                        </div>
                        <div v-else>
                            <img
                                src="../../../public/images/product_placeholder.png"
                                alt="Product image placeholder"
                                class="w-full h-80 object-center object-cover lg:w-full"
                            />
                        </div>
                    </div>
                    <div class="flex flex-row justify-between px-4 py-2">
                        <div>
                            <h3 class="text-sm text-gray-700 whitespace-nowrap">
                                <span aria-hidden="true" class></span>
                                {{ item.name }}
                            </h3>
                            <p
                                class="mt-1 text-sm text-gray-500"
                                v-if="item.product"
                            >
                                {{
                                    item.product.option1_name
                                        ? item.product.option1_name + ": "
                                        : ""
                                }}
                                {{
                                    item.product.option1_value
                                        ? item.product.option1_value
                                        : ""
                                }}
                            </p>
                            <p
                                class="mt-1 text-sm text-gray-500"
                                v-if="item.product"
                            >
                                <span class="mr-4">
                                    {{
                                        item.product.option2_name
                                            ? item.product.option2_name + ": "
                                            : ""
                                    }}
                                    {{
                                        item.product.option2_value
                                            ? item.product.option2_value
                                            : ""
                                    }}
                                </span>

                                {{
                                    item.product.option3_name
                                        ? item.product.option3_name + ": "
                                        : ""
                                }}
                                {{
                                    item.product.option3_value
                                        ? item.product.option3_value
                                        : ""
                                }}
                            </p>
                        </div>
                        <p class="text-sm font-semibold text-gray-900">
                            {{ order.currency }}{{ item.price }}
                        </p>
                    </div>
                </div>
            </div>
            <div
                class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse"
                v-if="item && item.id"
            >
                <button
                    type="button"
                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-gray-700 text-base font-medium text-white hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 sm:ml-3 sm:text-sm"
                    @click="continueWithExchange"
                >
                    Exchange With {{ order.currency
                    }}{{
                        parseFloat(
                            parseFloat(item.price) +
                                parseFloat(additionalReturnCredit)
                        ).toFixed(2)
                    }}
                </button>
                <button
                    type="button"
                    class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:text-sm"
                    @click="continueWithReturn"
                    ref="cancelButtonRef"
                >
                    Return For {{ order.currency }}{{ item.price }}
                </button>
            </div>
        </div>
    </div>
</template>

<script>
import { ref } from "vue";
import { Inertia } from "@inertiajs/inertia";
import { usePage } from "@inertiajs/inertia-vue3";
import { useOrderStore } from "@/Stores/order";
import CONSTANTS from "../Helpers/constants";
export default {
    props: {
        order: {
            type: Object,
        },
        item: {
            type: Object,
        },
        isAdmin: {
            type: Boolean,
        },
        additionalReturnCredit: {
            type: Number,
        },
    },
    setup(props) {
        const store = useOrderStore();

        const continueWithReturn = () => {
            props.item.transaction_type = "return";
            const events = props.item.events ? props.item.events : {};
            events[CONSTANTS.REFUND_SELECTED] = true;
            events[CONSTANTS.DISCOUNT_SHOW] = true;
            props.item.events = events;
            for (let index in props.order.line_items) {
                const _item = props.order.line_items[index];
                if (_item.key == props.item.key) {
                    props.order.line_items[index].transaction_type = "return";
                    props.order.line_items[index].qty_return = 1;
                    props.order.line_items[
                        index
                    ].additional_credit_selected = false;
                }
            }
            localStorage.setItem("current_item", JSON.stringify(props.item));
            store.setOrder(props.order);
            Inertia.get(`/${usePage().props.value.prefix}/requests/step/3`);
        };
        const continueWithExchange = () => {
            if (props.item.disabled) {
                return;
            }
            props.order.additional_credit_selected = true;
            const events = props.item.events ? props.item.events : {};
            events[CONSTANTS.DISCOUNT_SELECTED] = true;
            events[CONSTANTS.DISCOUNT_SHOW] = true;
            props.item.events = events;
            for (let index in props.order.line_items) {
                const _item = props.order.line_items[index];
                if (_item.key == props.item.key) {
                    props.order.line_items[
                        index
                    ].additional_credit_selected = true;
                }
            }
            store.setOrder(props.order);
            localStorage.setItem("current_item", JSON.stringify(props.item));
            Inertia.get(`/${usePage().props.value.prefix}/requests/step/2`);
        };
        const closePage = () => {
            Inertia.get(`/${usePage().props.value.prefix}/orders/review`);
        };
        return {
            continueWithExchange,
            continueWithReturn,
            closePage,
        };
    },
};
</script>
