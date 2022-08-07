<template>
    <div class="bg-white" id="home-container">
        <header class="bg-gray-100">
            <div
                class="flex justify-center full-width p-4 border-b border-gray-300 bg-white"
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
            <div class="flex flex-row justify-between px-4">
                <div class="flex justify-center">
                    <h1
                        class="text-center py-8 text-md lg:text-xl font-bold full-width"
                    >
                        Welcome
                        <span v-if="order.customer">{{
                            order.customer.first_name
                        }}</span>
                    </h1>
                </div>
                <div class="flex justify-end items-center">
                    <AccountMenu class :order="order" />
                </div>
            </div>
        </header>
        <OrderSummary :order="order" />
        <div
            class="max-w-5xl mx-auto pt-8 pb-16 px-4 sm:py-12 sm:px-6 lg:max-w-7xl lg:px-8"
        >
            <section class="mb-4">
                <h2 class="mb-2 font-bold text-lg text-gray-600">
                    Order: #{{ order.name }} | Email:
                    <span>{{ order.email }}</span>
                </h2>
                <span class="block text-gray-500"
                    >Date: {{ new Date(order.created_at).toDateString() }}</span
                >
                <span class="block text-gray-500">
                    Status:
                    <label class="uppercase">{{
                        order.financial_status
                    }}</label>
                </span>
            </section>
            <h2
                v-if="(!order.disabled && !order.admin_required) || isAdmin"
                class="text-md lg:text-2xl font-bold tracking-tight text-gray-900"
            >
                Choose item(s) to exchange/refund
            </h2>
            <h2
                v-else
                class="text-md lg:text-xl font-bold tracking-tight text-red-500"
            >
                This order can be edited only by the admin, please contact
                {{ $page.props.settings?.account?.customer_email }} to continue
                with your request.
            </h2>
            <div
                class="mt-6 grid grid-cols-1 gap-y-10 gap-x-6 sm:grid-cols-2 lg:grid-cols-3 xl:gap-x-8 items-start"
            >
                <OrderItem
                    :item="item"
                    :order="order"
                    :isAdmin="isAdmin"
                    :additionalReturnCredit="additionalReturnCredit"
                    :refundTypes="refundTypes"
                    :destinations="destinations"
                    :accountSetting="accountSetting"
                    v-for="item in order.line_items"
                    v-bind:key="item.key"
                />
            </div>
            <div v-if="order.has_request" class="mt-8 flex flex-col">
                <h2 class="mb-4 font-bold text-xl text-gray-600">
                    Request(s):
                </h2>
                <ul class="grid grid-cols-1 md:grid-cols-4">
                    <li
                        @click="showRequestDetails"
                        v-for="request in order.requests"
                        :key="request.id"
                    >
                        <button
                            class="mb-1 block border-b border-gray-400 hover:text-gray-800"
                        >
                            #{{ request.transaction_number }}
                        </button>
                        <span class="block text-gray-500"
                            >Date:
                            {{
                                new Date(request.created_at).toDateString()
                            }}</span
                        >
                        <span class="block text-gray-500"
                            >Status: {{ request.status_administrator }}</span
                        >
                    </li>
                </ul>
            </div>
        </div>
        <ExchangeItemModal
            :additionalReturnCredit="additionalReturnCredit"
            :order="order"
            :item="order.current_item"
            :isAdmin="isAdmin"
        />
        <ProductTagDetailModal
            :order="order"
            :accountSetting="accountSetting"
        />
        <Footer :order="order" />
    </div>
</template>

<style scope></style>

<script>
import OrderItem from "@/Components/OrderItem.vue";
import ExchangeItemModal from "@/Components/ExchangeItemModal.vue";
import ProductTagDetailModal from "@/Components/ProductTagDetailModal.vue";
import OrderSummary from "@/Components/OrderSummary.vue";
import Footer from "@/Components/Footer.vue";
import AccountMenu from "@/Components/AccountMenu.vue";
import { usePage } from "@inertiajs/inertia-vue3";
import CONSTANTS from "@/Helpers/constants";
import { ref } from "vue";
import { Inertia } from "@inertiajs/inertia";
import { useOrderStore } from "@/Stores/order";
export default {
    props: ["destinations", "refundTypes", "accountSetting"],
    components: {
        OrderItem,
        OrderSummary,
        AccountMenu,
        Footer,
        ExchangeItemModal,
        ProductTagDetailModal,
    },
    setup(props) {
        const additionalReturnCredit = ref(0);
        const isAdmin = ref(false);
        const store = useOrderStore();
        const order = store.order;
        order.current_item = {};
        order.events = {};
        order.return_credit = 0;
        order.return_credit_with_additional = 0;
        additionalReturnCredit.value = 0;
        if (props.refundTypes) {
            for (const index in props.refundTypes) {
                const conditions = props.refundTypes[index].conditions;
                const _item = props.refundTypes[index];
                if (_item.refund_type_id == 1) {
                    additionalReturnCredit.value = _item
                        ? parseFloat(_item.extra_amount)
                        : 0;
                }
            }
        }
        let ids = [];
        let skus = [];
        let handles = [];
        let handleArray = [];
        if (props.destinations && order.shipping_address) {
            const country =
                props.destinations[order.shipping_address.country_code];
            if (!country) {
                order.shipping_not_valid_for_exchange = true;
            } else {
                if (order.shipping_address.province_code && country.provinces) {
                    const province =
                        country.provinces[order.shipping_address.province_code];
                    if (!province) {
                        order.shipping_not_valid_for_exchange = true;
                    } else {
                        order.shipping_not_valid_for_exchange = null;
                    }
                } else {
                    order.shipping_not_valid_for_exchange = null;
                }
            }
        } else {
            order.shipping_not_valid_for_exchange = true;
        }
        for (let item of order.line_items) {
            ids.push(item.product_id);
            skus.push(item.sku);
            handleArray = item.sku.replaceAll("_", "-").split("-");
            handleArray.pop();
            handles.push(handleArray.join("-"));
        }
        skus = [...skus];
        ids = [...ids];
        isAdmin.value = localStorage.getItem("isAdmin") ? true : false;
        const events = order.events ? order.events : {};
        const createdBy = isAdmin.value
            ? CONSTANTS.CREATED_BY_AGENT
            : CONSTANTS.CREATED_BY_USER;
        events[createdBy] = true;
        order.events = events;
        axios
            .post(`/${usePage().props.value.prefix}/orders/get`, {
                order_number: order.name,
                order_id: order.id,
                skus: skus,
                ids: ids,
            })
            .then((response) => {
                const data = response.data;
                if (data.error) {
                    props.error = data.msg;
                }
                if (data.products) {
                    order.requests = data.requests || [];
                    order.has_request =
                        order.requests.length > 0 ? true : false;
                    let items = [];
                    for (const key in order.line_items) {
                        const _item = order.line_items[key];
                        if (_item.key) {
                            items.push(_item);
                        } else {
                            for (let i = 0; i < parseInt(_item.quantity); i++) {
                                const finalItem = { ..._item };
                                finalItem.key = _item.id + i;
                                finalItem.price = (
                                    parseFloat(_item.price) /
                                    parseInt(_item.quantity)
                                ).toFixed(2);
                                finalItem.pre_tax_price = (
                                    parseFloat(_item.pre_tax_price) /
                                    parseInt(_item.quantity)
                                ).toFixed(2);
                                finalItem.tax_amount = (
                                    parseFloat(_item.tax_amount) /
                                    parseInt(_item.quantity)
                                ).toFixed(2);
                                finalItem.discount_amount = (
                                    parseFloat(_item.discount_amount) /
                                    parseInt(_item.quantity)
                                ).toFixed(2);
                                finalItem.quantity = 1;
                                items.push(finalItem);
                            }
                        }
                    }
                    order.line_items = items;
                    for (const index in order.line_items) {
                        let item = order.line_items[index];
                        if (order.has_request && !isAdmin.value) {
                            order.line_items[index].disabled = true;
                        }
                        for (const code in data.requests) {
                            const request = data.requests[code];
                            for (const key in request.items) {
                                const found = request.items[key].item_key
                                    ? request.items[key].item_key == item.key
                                    : request.items[key].original_sku ==
                                      item.sku;
                                if (found) {
                                    order.line_items[index].disabled = true;
                                    order.line_items[index].transaction_type =
                                        request.items[key].transaction_type;
                                    order.line_items[index].new_name =
                                        request.items[key].new_name;
                                    order.line_items[index].new_image =
                                        request.items[key].new_image;
                                    order.line_items[index].qty_return = request
                                        .items[key].qty_return
                                        ? request.items[key].qty_return
                                        : 0;
                                    order.line_items[index].qty_exchange =
                                        request.items[key].qty_exchange
                                            ? request.items[key].qty_exchange
                                            : 0;
                                    order.line_items[index].new_price = request
                                        .items[key].new_price
                                        ? request.items[key].new_price
                                        : 0;
                                }
                            }
                        }
                        const availableItem = order.line_items.find(
                            (item) => !item.disabled
                        );
                        order.disabled = availableItem ? false : true;
                        for (const product of data.products) {
                            if (item.variant_id == product.product_id) {
                                order.line_items[index].product = product;
                                order.line_items[index].product_type =
                                    product.product_type;
                                order.line_items[index].image = product.image;
                                if (product.tags) {
                                    order.line_items[index].exclude =
                                        product.tags
                                            ? product.tags
                                                  .toLowerCase()
                                                  .includes("rma_no_return")
                                            : false;
                                    order.line_items[index].is_bundle =
                                        product.tags
                                            ? product.tags
                                                  .toLowerCase()
                                                  .includes("bundle")
                                            : false;
                                    if (order.line_items[index].is_bundle) {
                                        order.admin_required = true;
                                    }
                                }
                            }
                        }
                    }
                    localStorage.setItem(
                        "products",
                        JSON.stringify(data.products)
                    );
                    localStorage.setItem(
                        "surveys",
                        JSON.stringify(data.surveys)
                    );
                    localStorage.setItem(
                        "requests",
                        JSON.stringify(data.requests)
                    );
                }
            })
            .catch(() => {
                props.error = "Ooops! Something went wrong!";
            });
        const chooseDifferentOrder = () => {
            Inertia.get(`/${usePage().props.value.prefix}/`);
        };
        const closePage = () => {
            Inertia.get(`/${usePage().props.value.prefix}/orders/review`);
        };
        const showRequestDetails = () => {
            Inertia.get(`/${usePage().props.value.prefix}/requests/detail`);
        };

        return {
            order,
            additionalReturnCredit,
            isAdmin,
            chooseDifferentOrder,
            closePage,
            showRequestDetails,
        };
    },
};
</script>
