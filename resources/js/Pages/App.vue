<template>
    <div
        class="h-screen flex flex-col items-center justify-center py-12 px-4 sm:px-6 lg:px-8"
    >
        <figure class="mb-8" v-if="$page.props.settings.account.logo">
            <img
                class="mx-auto max-w-xs w-full"
                :src="$page.props.settings.account.logo"
                alt="Workflow"
            />
        </figure>
        <h2
            v-else
            class="mb-8 text-4xl font-bold border-b-2 border-gray-700 max-w-md w-full text-center pb-2"
        >
            {{ $page.props.settings.account.name }}
        </h2>
        <div class="max-w-md w-full">
            <div>
                <h2 class="text-center text-3xl font-bold text-gray-900">
                    Enter your order information
                </h2>
            </div>
            <form
                class="mt-4 space-y-6"
                action="{{route('orders.get')}}"
                method="POST"
                x-data="searchForm()"
                @submit.prevent="submitData"
            >
                <p class="text-center align-center text-red-600">
                    {{ errorMessage }}
                </p>
                <div class="rounded-md shadow-sm -space-y-px">
                    <div>
                        <label for="email-address" class="sr-only">Email</label>
                        <input
                            id="email-address"
                            name="email"
                            type="email"
                            autocomplete="email"
                            required
                            class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
                            placeholder="Email"
                            v-model="formData.email"
                        />
                    </div>
                    <div>
                        <label for="order_number" class="sr-only"
                            >Order number</label
                        >
                        <input
                            id="order_number"
                            name="order_number"
                            type="text"
                            required
                            class="appearance-none rounded-b-md relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
                            placeholder="Order number"
                            v-model="formData.order_number"
                        />
                    </div>
                    <div class="hidden">
                        <label for="zip" class="sr-only">Zip code</label>
                        <input
                            id="zip"
                            name="zip"
                            type="text"
                            class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
                            placeholder="Zip code"
                            v-model="formData.zip"
                        />
                    </div>
                </div>

                <div>
                    <button
                        type="submit"
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-gray-700 hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    >
                        <div
                            v-if="isLoading"
                            class="w-4 h-4 border-b-2 mr-2 border-gray-100 rounded-full animate-spin"
                        ></div>
                        Continue
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
import { usePage } from "@inertiajs/inertia-vue3";
import { reactive, ref } from "vue";
import { Inertia } from "@inertiajs/inertia";
import { useOrderStore } from "@/Stores/order";
export default {
    setup() {
        const formData = reactive({
            email: "",
            order_number: "",
            zip: "",
            message: "",
        });
        const isLoading = ref(false);
        const errorMessage = ref("");
        const store = useOrderStore();
        store.setOrder({});
        const submitData = async () => {
            if (isLoading.value) {
                return;
            }
            isLoading.value = true;
            errorMessage.value = "";
            try {
                await store.fetchOrder(formData);
                const order = store.order;
                if (order && order.id) {
                    order.name = order.name
                        ? order.name.replaceAll("#", "")
                        : order.name;
                    await axios.post(
                        `/${usePage().props.value.prefix}/customer/token`,
                        { order: order.name, email: order.email }
                    );
                    let ids = [];
                    let skus = [];
                    let handles = [];
                    let handleArray = [];
                    let item = null;
                    for (const index in order.line_items) {
                        let taxAmount = 0;
                        let discountAmount = 0;
                        item = order.line_items[index];
                        order.line_items[index].price =
                            parseFloat(item.price) * parseInt(item.quantity);
                        if (order.tags?.includes("Exchange")) {
                            order.line_items[index].price = parseFloat(
                                item.price
                            );
                        }
                        item.tax_lines.map(
                            (itemObj) =>
                                (taxAmount += parseFloat(itemObj.price))
                        );
                        item.discount_allocations.map(
                            (itemObj) =>
                                (discountAmount += parseFloat(itemObj.amount))
                        );
                        order.line_items[index].tax_amount =
                            parseFloat(taxAmount).toFixed(2);
                        order.line_items[index].discount_amount =
                            parseFloat(discountAmount).toFixed(2);
                        if (order.taxes_included) {
                            order.line_items[index].price = (
                                parseFloat(order.line_items[index].price) -
                                discountAmount
                            ).toFixed(2);
                        } else {
                            order.line_items[index].price = (
                                parseFloat(order.line_items[index].price) +
                                parseFloat(taxAmount) -
                                discountAmount
                            ).toFixed(2);
                        }

                        order.line_items[index].base_price =
                            order.line_items[
                                index
                            ].price_set.presentment_money.amount;
                        ids.push(item.product_id);
                        skus.push(item.sku);
                        handleArray = item.sku.replaceAll("_", "-").split("-");
                        handleArray.pop();
                        handles.push(handleArray.join("-"));
                    }
                    store.setOrder(order);
                    ids = ids.join(",");
                    skus = skus.join(",");
                    handles = handles.join(",");
                    await axios.post(
                        `/api/${usePage().props.value.prefix}/v1/products/get`,
                        { skus: skus, handles: handles, ids: ids }
                    );
                    isLoading.value = false;
                    Inertia.get(
                        `/${usePage().props.value.prefix}/orders/review`
                    );
                } else {
                    isLoading.value = false;
                    errorMessage.value = "Invalid order number";
                    return;
                }
            } catch (error) {
                console.error(error);
                isLoading.value = false;
                errorMessage.value = error.message;
            }
        };

        return {
            formData,
            errorMessage,
            store,
            isLoading,
            submitData,
        };
    },
};
</script>
