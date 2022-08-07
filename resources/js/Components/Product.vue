<template>
    <div class="cursor-pointer">
        <div
            class="relative overflow-hidden aspect-w-1 aspect-h-1 rounded-md overflow-hidden lg:aspect-none"
            :class="item ? '' : 'animate-pulse'"
        >
            <div v-if="item">
                <img
                    v-if="item.image"
                    :src="item.image"
                    :alt="item.name"
                    class="object-contain h-80 w-full object-center lg:w-full"
                />
                <img
                    v-else
                    src="../../../public/images/product_placeholder.png"
                    alt="Product image placeholder"
                    class="w-full h-full object-center object-cover lg:w-full lg:h-full"
                />
            </div>
            <div v-else>
                <img
                    src="../../../public/images/product_placeholder.png"
                    alt="Product image placeholder"
                    class="w-full h-full object-center object-cover lg:w-full lg:h-full"
                />
            </div>
        </div>
        <div class="flex flex-col">
            <div class="flex flex-row justify-between px-4 py-2">
                <div>
                    <h3 class="text-sm text-gray-700">
                        <span aria-hidden="true" class=""></span
                        >{{ item.title }}
                    </h3>
                    <p class="mt-1 text-sm text-gray-500" v-if="item">
                        {{ item.option1_name ? item.option1_name + ": " : "" }}
                        {{ item.option1_value ? item.option1_value : "" }}
                    </p>
                </div>
                <p class="text-sm font-semibold text-gray-900">
                    {{ order.currency }}{{ item.price }}
                </p>
            </div>
        </div>
    </div>
</template>
<script>
import { Inertia } from "@inertiajs/inertia";
import { useOrderStore } from "@/Stores/order";
import { usePage } from "@inertiajs/inertia-vue3";
export default {
    props: {
        item: {
            type: Object,
        },
        order: {
            type: Object,
        },
    },
    setup(props) {
        const store = useOrderStore();

        const goStepTwo = () => {
            if (props.item.disabled) {
                return;
            }
            localStorage.setItem("current_item", JSON.stringify(props.item));
            Inertia.get(`/${usePage().props.value.prefix}/orders/review`);
        };

        const saveItemRequest = () => {
            if (props.item.disabled) {
                return;
            }
            props.item.transaction_type = "return";
            let itemsCount = 0;
            for (let index in props.order.line_items) {
                const _item = props.order.line_items[index];
                if (_item.sku == props.item.sku) {
                    props.order.line_items[index].transaction_type = "return";
                    props.order.line_items[index].qty_return = 1;
                    itemsCount++;
                }
            }
            localStorage.setItem("current_item", JSON.stringify(props.item));
            store.setOrder(props.order);
            Inertia.get(`/${usePage().props.value.prefix}/orders/review`);
        };

        return {
            goStepTwo,
            saveItemRequest,
        };
    },
};
</script>
