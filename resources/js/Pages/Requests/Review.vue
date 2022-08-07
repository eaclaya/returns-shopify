<template>
    <div class="bg-white">
        <FullPageLoader :isLoading="isLoading" />
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
                        class="text-center px-4 py-8 text-md lg:text-xl font-bold full-width"
                    >
                        Request Review
                    </h1>
                </div>
                <div class="flex justify-end items-center mr-8">
                    <AccountMenu class :order="order" />
                </div>
            </div>
        </header>
        <div class="flex flex-col justify-center px-4">
            <div class="w-full max-w-xl mx-auto">
                <div class="mt-8 flex flex-col">
                    <ul class="flex flex-col w-full">
                        <li class="flex mb-4">
                            <h2 class="w-full font-bold text-xl">
                                Item(s) to send back
                            </h2>
                        </li>
                        <li v-for="item in order.line_items" :key="item.id">
                            <div
                                v-if="
                                    item.selected &&
                                    !item.disabled &&
                                    !item.exclude
                                "
                                class="mb-4 pb-4 w-full flex xl:border-gray-200 xl:border-b"
                            >
                                <div class="w-32 h-32 px-2 overflow-hidden">
                                    <img
                                        v-if="item.image"
                                        :src="item.image"
                                        class="w-full h-full object-center object-cover lg:w-full lg:h-full"
                                    />
                                    <img
                                        v-else
                                        src="../../../../public/images/product_placeholder.png"
                                        alt="Product image placeholder"
                                        class="w-full h-full object-center object-cover lg:w-full lg:h-full"
                                    />
                                </div>
                                <div class="w-72">
                                    <p class="text-sm">{{ item.name }}</p>
                                    <p class="text-sm text-gray-400">
                                        {{ item.sku }}
                                    </p>
                                </div>
                                <p class="text-sm w-24">
                                    <span class="hidden md:inline-block"
                                        >Quantity:</span
                                    >
                                    {{ item.quantity }}
                                </p>
                                <p class="text-sm w-24">
                                    {{ order.currency }}{{ item.price }}
                                </p>
                            </div>
                        </li>
                    </ul>
                    <ul class="flex flex-col w-full" v-if="order.has_exchange">
                        <li class="flex mb-4">
                            <h2 class="w-full font-bold text-xl">
                                Item(s) you'll receive
                            </h2>
                        </li>
                        <li v-for="item in order.line_items" :key="item.id">
                            <div
                                v-if="
                                    item.selected &&
                                    !item.disabled &&
                                    item.transaction_type == 'exchange'
                                "
                                class="w-full flex pb-4"
                            >
                                <div class="w-32 h-32 px-2 overflow-hidden">
                                    <div
                                        v-if="
                                            item.transaction_type == 'exchange'
                                        "
                                    >
                                        <img
                                            :src="
                                                item.new_image
                                                    ? item.new_image
                                                    : '../../../../public/images/product_placeholder.png'
                                            "
                                            class="w-full h-full object-center object-cover lg:w-full lg:h-full"
                                            alt
                                        />
                                    </div>
                                </div>
                                <div class="w-72">
                                    <p class="text-sm">
                                        {{
                                            item.new_name
                                                ? item.new_name
                                                : "None"
                                        }}
                                    </p>
                                    <p class="text-sm text-gray-400">
                                        {{ item.new_sku }}
                                    </p>
                                </div>
                                <p class="text-sm w-24">
                                    <span class="hidden md:inline-block"
                                        >Quantity:</span
                                    >
                                    {{
                                        item.qty_return
                                            ? item.qty_return
                                            : item.qty_exchange
                                    }}
                                </p>
                                <p class="text-sm w-24">
                                    {{ order.currency
                                    }}{{
                                        item.new_price
                                            ? item.new_price
                                            : parseFloat(0).toFixed(2)
                                    }}
                                </p>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div
            class="flex flex-col justify-center px-4"
            v-if="order.shipping_address && order.has_exchange"
        >
            <div
                class="w-full max-w-xl mx-auto border-gray-200 border-b pb-4 relative overflow-hidden"
            >
                <h2 class="w-full font-bold text-xl mb-4">
                    Shipping Address
                    <button
                        v-if="editAddressAvailable"
                        @click="editAddress = true"
                        class="text-sm font-semibold underline float-right"
                    >
                        Edit
                    </button>
                </h2>
                <div class="flex flex-col">
                    <div v-if="!editAddress">
                        <p class="text-gray-700">
                            {{ order.shipping_address.name }}
                        </p>
                        <p class="text-gray-700">
                            {{ order.shipping_address.address1 }}
                        </p>
                        <p class="text-gray-700">
                            {{ order.shipping_address.address2 }}
                        </p>
                        <p class="text-gray-700">
                            {{ order.shipping_address.city }},
                            {{ order.shipping_address.province }},
                            {{ order.shipping_address.zip }}
                        </p>
                        <p class="text-gray-700">
                            {{ order.shipping_address.country }}
                        </p>
                        <p class="text-gray-700">
                            {{ order.shipping_address.phone }}
                        </p>
                    </div>
                    <div class="flex flex-col" v-else>
                        <div class="flex mb-4 gap-2">
                            <div class="w-1/2">
                                <p class="text-gray-500">First Name:</p>
                                <input
                                    class="border border-gray-200 px-2 w-full h-10"
                                    v-model="address.first_name"
                                />
                            </div>
                            <div class="w-1/2">
                                <p class="text-gray-500">Last Name:</p>
                                <input
                                    class="border border-gray-200 px-2 w-full h-10"
                                    v-model="address.last_name"
                                />
                            </div>
                        </div>
                        <div class="w-full mb-4">
                            <p class="text-gray-500">Street Address: Line 1</p>
                            <input
                                class="border border-gray-200 px-2 w-full h-10"
                                v-model="address.address1"
                            />
                        </div>
                        <div class="w-full mb-4">
                            <p class="text-gray-500">Street Address: Line 2</p>
                            <input
                                class="border border-gray-200 px-2 w-full h-10"
                                v-model="address.address2"
                            />
                        </div>
                        <div class="flex gap-2 mb-4">
                            <div class="w-1/3">
                                <p class="text-gray-500">City</p>
                                <input
                                    class="border border-gray-200 px-2 w-full h-10"
                                    v-model="address.city"
                                />
                            </div>
                            <div class="w-1/3">
                                <p class="text-gray-500">State/Province</p>
                                <input
                                    class="border border-gray-200 px-2 w-full h-10"
                                    v-model="address.province"
                                />
                            </div>
                            <div class="w-1/3">
                                <p class="text-gray-500">ZIP Code</p>
                                <input
                                    class="border border-gray-200 px-2 w-full h-10"
                                    v-model="address.zip"
                                />
                            </div>
                        </div>

                        <div class="mb-4">
                            <p class="text-gray-500">Phone</p>
                            <input
                                class="border border-gray-200 px-2 w-full h-10"
                                v-model="address.phone"
                            />
                        </div>

                        <button
                            class="w-full py-4 bg-gray-700 hover:bg-gray-900 text-white font-semibold"
                            @click="saveAddress"
                        >
                            Save Address
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div
            class="flex justify-center items-center flex-col mt-8 px-4 hidden"
            v-if="additionalExchangeDiscountBalance > 0 && totalCharge >= 0"
        >
            <div class="pb-4 max-w-lg">
                <label for="" class="text-gray-500"
                    ><input
                        type="checkbox"
                        name="apply-exchange-discount"
                        v-model="applyAdditionalExchangeDiscount"
                    />
                    Apply exchange discount -
                    <span class="font-semibold"
                        >{{ order.currency
                        }}{{ additionalExchangeDiscountBalance }}</span
                    ></label
                >
            </div>
        </div>

        <div
            class="flex flex-col justify-center items-center px-4"
            v-if="
                Object.keys(refundTypes).length > 1 &&
                (totalReturn > 0 || giftcartCredit)
            "
        >
            <h2 class="text-center text-lg font-semibold my-4">
                Choose a credit method
            </h2>
            <ul class="max-w-xl w-full">
                <li
                    v-for="refundType in refundTypes"
                    :key="refundType.id"
                    @click="selectRefundType(refundType)"
                    class="bg-white border-b border-b-gray-300 px-4 py-4 mb-4 border border-gray-200 rounded-md cursor-pointer"
                    :class="refundType.selected ? 'bg-gray-800 text-white' : ''"
                >
                    <div class="flex justify-between items-center">
                        <div>
                            <h2 class="text-sm font-semibold">
                                {{ refundType.name }}
                            </h2>
                            <p
                                class="text-xs"
                                :class="
                                    refundType.selected
                                        ? 'text-white'
                                        : 'text-gray-600'
                                "
                            >
                                {{ refundType.description }}
                            </p>
                        </div>
                        <!-- Show default refund credit amount  when option is not selected -->
                        <span
                            class="text-sm font-semibold"
                            v-if="refundType.action == 'percentage'"
                        >
                            {{ order.currency
                            }}{{
                                parseFloat(
                                    parseFloat(originalTotalReturn) +
                                        parseFloat(originalTotalReturn) *
                                            parseFloat(
                                                refundType.extra_amount / 100
                                            )
                                ).toFixed(2)
                            }}
                        </span>
                        <span v-else>
                            {{ order.currency
                            }}{{
                                parseFloat(
                                    parseFloat(originalTotalReturn) +
                                        parseFloat(refundType.extra_amount)
                                ).toFixed(2)
                            }}
                        </span>
                    </div>
                </li>
            </ul>
        </div>

        <div class="flex justify-center items-center flex-col px-4">
            <div class="pb-8 max-w-xl w-full">
                <div class="py-2 w-full">
                    <div class="border-b border-gray-200 sm:rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200">
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr>
                                    <td
                                        width="250"
                                        class="py-4 whitespace-nowrap text-sm text-gray-500"
                                    >
                                        New Item Subtotal
                                    </td>
                                    <td
                                        width="250"
                                        class="py-4 whitespace-nowrap text-sm text-gray-500 text-right"
                                    >
                                        {{ order.currency
                                        }}{{ exchangeSubtotal.toFixed(2) }}
                                    </td>
                                </tr>
                                <tr>
                                    <td
                                        width="250"
                                        class="py-4 whitespace-nowrap text-sm text-gray-500"
                                    >
                                        Return Credit
                                    </td>
                                    <td
                                        width="250"
                                        class="py-4 whitespace-nowrap text-sm text-gray-500 text-right"
                                    >
                                        {{ order.currency
                                        }}{{ returnCredit.toFixed(2) }}
                                    </td>
                                </tr>
                                <tr
                                    v-if="
                                        applyAdditionalExchangeDiscount &&
                                        additionalExchangeDiscountApplied
                                    "
                                >
                                    <td
                                        width="250"
                                        class="py-4 whitespace-nowrap text-sm text-gray-500"
                                    >
                                        Special Discount
                                    </td>
                                    <td
                                        width="250"
                                        class="py-4 whitespace-nowrap text-sm text-gray-500 text-right"
                                    >
                                        {{ order.currency
                                        }}{{
                                            parseFloat(
                                                additionalExchangeDiscountApplied
                                            ).toFixed(2)
                                        }}
                                    </td>
                                </tr>
                                <tr v-if="shippingAmount > 0">
                                    <td
                                        width="250"
                                        class="py-4 whitespace-nowrap text-sm text-gray-500 relative overflow-visible"
                                    >
                                        <span class="relative overflow-visible">
                                            Shipping
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                @mouseleave="hover = false"
                                                @mouseover="hover = true"
                                                class="h-4 w-4 absolute -top-1 -right-5 cursor-pointer"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                                />
                                            </svg>
                                        </span>
                                        <p
                                            class="absolute -top-20 left-0 p-4 shadow-lg rounded-md bg-white font-semibold w-64 whitespace-normal"
                                            v-if="hover"
                                        >
                                            Net order amount after return falls
                                            below free shipping threshold that
                                            was initially extended
                                        </p>
                                    </td>
                                    <td
                                        width="250"
                                        class="py-4 whitespace-nowrap text-sm text-gray-500 text-right"
                                    >
                                        {{ order.currency
                                        }}{{
                                            parseFloat(shippingAmount).toFixed(
                                                2
                                            )
                                        }}
                                    </td>
                                </tr>
                                <tr v-if="shouldWeChargeRestockingFee">
                                    <td
                                        width="250"
                                        class="py-4 whitespace-nowrap text-sm text-gray-500"
                                    >
                                        Restocking Fee
                                    </td>
                                    <td
                                        width="250"
                                        class="py-4 whitespace-nowrap text-sm text-gray-500 text-right"
                                    >
                                        {{ order.currency
                                        }}{{
                                            parseFloat(restockingFee).toFixed(2)
                                        }}
                                    </td>
                                </tr>
                                <tr v-if="giftcartCredit > 0">
                                    <td
                                        width="250"
                                        class="py-4 whitespace-nowrap text-sm text-gray-500 font-bold"
                                    >
                                        Gift Card
                                    </td>
                                    <td
                                        width="250"
                                        class="py-4 whitespace-nowrap text-sm text-gray-500 text-right font-bold"
                                    >
                                        {{ order.currency
                                        }}{{
                                            parseFloat(giftcartCredit).toFixed(
                                                2
                                            )
                                        }}
                                    </td>
                                </tr>
                                <tr v-if="bonusDiscountCredit > 0">
                                    <td
                                        width="250"
                                        class="py-4 whitespace-nowrap text-sm text-gray-500 font-bold"
                                    >
                                        Bonus discount
                                    </td>
                                    <td
                                        width="250"
                                        class="py-4 whitespace-nowrap text-sm text-gray-500 text-right font-bold"
                                    >
                                        {{ order.currency
                                        }}{{
                                            parseFloat(
                                                bonusDiscountCredit
                                            ).toFixed(2)
                                        }}
                                    </td>
                                </tr>
                                <tr v-if="totalReturn > 0 && totalCharge == 0">
                                    <td
                                        width="250"
                                        class="py-4 whitespace-nowrap text-sm text-gray-500 font-bold"
                                    >
                                        Estimated Total Refund
                                    </td>
                                    <td
                                        width="250"
                                        class="py-4 whitespace-nowrap text-sm text-gray-500 font-bold text-right"
                                    >
                                        {{ order.currency
                                        }}{{ totalReturn.toFixed(2) }}
                                    </td>
                                </tr>
                                <tr v-else>
                                    <td
                                        width="250"
                                        class="py-4 whitespace-nowrap text-sm text-gray-500 font-bold"
                                    >
                                        Estimated Total Due
                                    </td>
                                    <td
                                        width="250"
                                        class="py-4 whitespace-nowrap text-sm text-gray-500 font-bold text-right"
                                    >
                                        {{ order.currency
                                        }}{{ totalCharge.toFixed(2) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="flex gap-8 mb-8" v-if="!editAddress">
                    <button
                        class="font-bold bg-gray-200 py-4 px-6 md:px-12 hover:bg-gray-300 w-1/2"
                        @click="cancelRequest"
                    >
                        Cancel Request
                    </button>
                    <button
                        class="font-bold bg-gray-800 py-4 px-4 md:px-8 hover:bg-gray-900 text-white w-1/2"
                        @click="submitRequest"
                    >
                        Submit Request
                    </button>
                </div>
                <button
                    class="border-b-2 border-gray-500 px-2 mx-auto block font-semibold tracking-wide"
                    @click="goBack"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-6 w-6 inline-block"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M7 16l-4-4m0 0l4-4m-4 4h18"
                        />
                    </svg>
                    <span class="pl-1">Go Back</span>
                </button>
            </div>
        </div>
    </div>
</template>

<style scoped></style>

<script>
import AccountMenu from "@/Components/AccountMenu.vue";
import Footer from "@/Components/Footer.vue";
import HomeLink from "@/Components/HomeLink.vue";
import FullPageLoader from "@/Components/FullPageLoader.vue";
import { Inertia } from "@inertiajs/inertia";
import { usePage } from "@inertiajs/inertia-vue3";
import { ref, watchEffect } from "vue";
import CONSTANTS from "../../Helpers/constants";
import { useOrderStore } from "@/Stores/order";
export default {
    props: [
        "orderSetting",
        "accountSetting",
        "shippingSetting",
        "requestBalance",
        "refundTypes",
        "customer",
    ],
    components: {
        AccountMenu,
        Footer,
        HomeLink,
        FullPageLoader,
    },
    setup(props) {
        const isLoading = ref(false);
        const store = useOrderStore();
        const order = store.order;
        console.log(order.line_items);
        const exchangeItem = order.line_items.find(
            (item) => item.transaction_type == "exchange"
        );
        const editAddress = ref(false);
        const hover = ref(false);
        const editAddressAvailable = ref(false);
        const additionalExchangeDiscount = ref(0);
        const additionalExchangeDiscountApplied = ref(0);
        const applyAdditionalExchangeDiscount = ref(true);
        const exchangeSubtotal = ref(0);
        const returnCredit = ref(0);
        const totalReturn = ref(0);
        const originalTotalReturn = ref(0);
        const totalCharge = ref(0);
        const restockingFee = ref(0);
        const shippingThreshold = ref(0);
        const orderThreshold = ref(0);
        const shippingAmount = ref(0);
        const taxAmount = ref(0);
        const newDiscountAmount = ref(0);
        const discountAmount = ref(0);
        const shouldWeChargeRestockingFee = ref(true);
        const selectedRefundType = ref(0);
        let newGrandTotalBalance = 0;
        const bonusDiscountCredit = ref(0);
        const baseBonusDiscountCredit = ref(0);
        const giftcartCredit = ref(0);
        const baseGiftcardCredit = ref(0);
        const bonusCreditValidation = ref(false);
        let tags = order.tags ? order.tags : "";
        restockingFee.value = props.accountSetting
            ? parseFloat(props.accountSetting.restocking_fee)
            : 0;
        restockingFee.value =
            restockingFee.value >= 0 ? restockingFee.value : 0;
        orderThreshold.value = props.accountSetting
            ? parseFloat(props.accountSetting.order_threshold)
            : 0;
        orderThreshold.value =
            orderThreshold.value >= 0 ? orderThreshold.value : 0;
        shippingThreshold.value = props.shippingSetting
            ? parseFloat(props.shippingSetting.amount)
            : 0;
        shippingThreshold.value =
            shippingThreshold.value >= 0 ? shippingThreshold.value : 0;
        additionalExchangeDiscount.value = props.orderSetting
            ? parseFloat(props.orderSetting.additional_exchange_discount)
            : 0;
        additionalExchangeDiscount.value =
            additionalExchangeDiscount.value >= 0
                ? additionalExchangeDiscount.value
                : 0;
        const additionalExchangeDiscountBalance = ref(
            additionalExchangeDiscount.value
        );
        const requestBalance = props.requestBalance ? props.requestBalance : {};
        let balanceProducts = [];
        let balanceRequisitCollections = [];
        let balanceEntitledCollections = [];
        let address = ref({
            first_name: order.shipping_address
                ? order.shipping_address.first_name
                : "",
            last_name: order.shipping_address
                ? order.shipping_address.last_name
                : "",
            address1: order.shipping_address
                ? order.shipping_address.address1
                : "",
            address2: order.shipping_address
                ? order.shipping_address.address2
                : "",
            city: order.shipping_address ? order.shipping_address.city : "",
            province: order.shipping_address
                ? order.shipping_address.province
                : "",
            zip: order.shipping_address ? order.shipping_address.zip : "",
            phone: order.shipping_address ? order.shipping_address.phone : "",
        });
        let newSubtotal = 0;
        let originalSubtotal = 0;
        let originalBaseSubtotal = 0;
        let exchangeItemsCount = 0;
        let hasReturn = false;
        let itemsReturned = 0;
        for (const index in order.line_items) {
            const _item = order.line_items[index];
            if (!_item.disabled && _item.selected) {
                if (_item.transaction_type == "exchange") {
                    newSubtotal +=
                        parseInt(_item.qty_exchange) *
                        parseFloat(_item.new_price);
                    shouldWeChargeRestockingFee.value = false;
                    exchangeItemsCount += 1;
                } else {
                    hasReturn = true;
                    itemsReturned += 1;
                }
                originalSubtotal +=
                    parseInt(_item.quantity) * parseFloat(_item.price);
                originalBaseSubtotal +=
                    parseFloat(_item.base_price) + parseFloat(_item.tax_amount);
                _item.tax_lines.map(
                    (itemObj) => (taxAmount.value += parseFloat(itemObj.price))
                );
            }
            if (!_item.selected || _item.transaction_type == "exchange") {
                shouldWeChargeRestockingFee.value = false;
            }
        }
        if (
            shouldWeChargeRestockingFee.value &&
            props.requestBalance &&
            props.requestBalance.remains_qty != itemsReturned
        ) {
            shouldWeChargeRestockingFee.value = false;
        }
        if (hasReturn) {
            bonusCreditValidation.value = true;
        }
        for (const index in props.refundTypes) {
            const conditions = props.refundTypes[index].conditions;
            let refundTypeRuleValid = false;
            props.refundTypes[index].extra_amount = props.refundTypes[index]
                .extra_amount
                ? props.refundTypes[index].extra_amount
                : 0;
            if (
                order.additional_credit_selected &&
                props.refundTypes[index].refund_type_id == 1
            ) {
                baseBonusDiscountCredit.value = parseFloat(
                    props.refundTypes[index].extra_amount
                );
            }
        }
        order.line_items.map((_item) => {
            if (
                !_item.disabled &&
                _item.selected &&
                _item.additional_credit_selected
            ) {
                bonusDiscountCredit.value += baseBonusDiscountCredit.value;
            }
        });
        if (order.discount_applications) {
            order.discount_applications.map(async (discountRule) => {
                if (discountRule.target_selection == "entitled") {
                    isLoading.value = true;
                    const response = await axios.post(
                        `/api/${
                            usePage().props.value.prefix
                        }/v1/discounts/rule`,
                        { code: discountRule.code }
                    );
                    if (response.status == 200) {
                        order.price_rule = response.data;
                        let requisitQuantity =
                            order.price_rule
                                .prerequisite_to_entitlement_quantity_ratio
                                ?.prerequisite_quantity;
                        let entitledQuantity =
                            order.price_rule
                                .prerequisite_to_entitlement_quantity_ratio
                                ?.entitled_quantity;
                        let requisitAmount =
                            order.price_rule
                                .prerequisite_to_entitlement_purchase
                                ?.prerequisite_amount;
                        requisitQuantity =
                            requisitQuantity > 0
                                ? parseInt(requisitQuantity)
                                : 0;
                        entitledQuantity =
                            entitledQuantity > 0
                                ? parseInt(entitledQuantity)
                                : 0;
                        requisitAmount =
                            requisitAmount > 0 ? parseInt(requisitAmount) : 0;
                        let originalDiscountAmount = 0;
                        order.line_items.map((_item) => {
                            if (!_item.disabled) {
                                originalDiscountAmount += _item
                                    .discount_allocations[0]
                                    ? parseFloat(
                                          _item.discount_allocations[0].amount
                                      )
                                    : 0;
                                if (_item.transaction_type != "return") {
                                    const product = _item.new_product
                                        ? _item.new_product
                                        : _item.product;
                                    if (product?.collections) {
                                        const requisitCollections =
                                            product.collections.filter(
                                                (collection) =>
                                                    order.price_rule.prerequisite_collection_ids?.includes(
                                                        collection.collection_id
                                                    )
                                            );
                                        if (
                                            requisitCollections &&
                                            requisitCollections.length > 0 &&
                                            balanceRequisitCollections.length <
                                                requisitQuantity
                                        ) {
                                            balanceRequisitCollections.push(
                                                requisitCollections[0]
                                                    .collection_id
                                            );
                                        } else {
                                            const entitledCollections =
                                                product?.collections.filter(
                                                    (collection) =>
                                                        order.price_rule.entitled_collection_ids?.includes(
                                                            collection.collection_id
                                                        )
                                                );
                                            if (
                                                entitledCollections &&
                                                entitledCollections.length >
                                                    0 &&
                                                balanceEntitledCollections.length <
                                                    entitledQuantity
                                            ) {
                                                balanceEntitledCollections.push(
                                                    entitledCollections[0]
                                                        .collection_id
                                                );
                                            }
                                        }
                                        balanceProducts.push(product);
                                    }
                                }
                            }
                        });

                        let priceRuleValid = false;
                        if (
                            balanceRequisitCollections.length >=
                                requisitQuantity &&
                            balanceEntitledCollections.length >=
                                entitledQuantity
                        ) {
                            priceRuleValid = true;
                        }
                        if (!priceRuleValid && originalBaseSubtotal > 0) {
                            originalBaseSubtotal -= parseFloat(
                                order.total_discounts
                            );
                            originalSubtotal = originalBaseSubtotal;
                            const newTotalRequestCalculated =
                                parseFloat(originalSubtotal) -
                                parseFloat(newSubtotal) -
                                parseFloat(restockingFee.value) -
                                parseFloat(shippingAmount.value);
                            order.total_return =
                                newTotalRequestCalculated >= 0
                                    ? newTotalRequestCalculated
                                    : 0;
                            order.total_charge =
                                newTotalRequestCalculated < 0
                                    ? Math.abs(newTotalRequestCalculated)
                                    : 0;
                            order.original_grand_total = originalSubtotal;
                            order.new_grand_total = newSubtotal;
                            returnCredit.value = originalSubtotal;
                            exchangeSubtotal.value = newSubtotal;
                            totalReturn.value = order.total_return;
                            originalTotalReturn.value = order.total_return;
                            totalCharge.value = order.total_charge;
                            store.setOrder(order);
                        }
                        isLoading.value = false;
                    }
                }
                if (discountRule.target_selection == "all") {
                    // Shipping rule validation
                    if (discountRule.target_type == "shipping_line") {
                        if (discountRule.value_type == "fixed_amount") {
                            if (requestBalance && requestBalance.id) {
                                requestBalance.shipping_amount_balance =
                                    parseFloat(
                                        requestBalance.shipping_amount_balance
                                    ) + parseFloat(discountRule.value);
                            }
                        }
                        if (discountRule.value_type == "percentage") {
                            if (requestBalance && requestBalance.id) {
                                requestBalance.shipping_amount_balance =
                                    parseFloat(
                                        requestBalance.shipping_amount_balance
                                    ) + shippingThreshold.value;
                            }
                        }
                    } else {
                        if (discountRule.value_type == "fixed_amount") {
                            newDiscountAmount.value +=
                                exchangeItemsCount *
                                (parseFloat(discountRule.value) /
                                    order.line_items.length);
                        }
                        if (discountRule.value_type == "percentage") {
                            newDiscountAmount.value +=
                                (parseFloat(discountRule.value) / 100) *
                                parseFloat(newSubtotal);
                        }
                    }
                }
            });
        }
        discountAmount.value =
            order.total_discounts > 0 ? parseFloat(order.total_discounts) : 0;
        if (newSubtotal > 0) {
            newSubtotal -= newDiscountAmount.value;
        } else {
            newDiscountAmount.value = 0;
        }
        restockingFee.value = shouldWeChargeRestockingFee.value
            ? restockingFee.value
            : 0;
        if (!shouldWeChargeRestockingFee.value) {
            const totalReturnCalculated =
                parseFloat(originalSubtotal) -
                parseFloat(newSubtotal) -
                parseFloat(restockingFee.value);
            if (
                requestBalance &&
                requestBalance.id &&
                requestBalance.shipping_amount_balance < shippingThreshold.value
            ) {
                newGrandTotalBalance =
                    parseFloat(requestBalance.grand_total_balance) -
                    parseFloat(totalReturnCalculated);
                try {
                    const shippingChargeIsValid = eval(
                        props.shippingSetting.conditions
                    );
                    if (shippingChargeIsValid) {
                        shippingAmount.value = shippingThreshold.value;
                    }
                } catch (error) {
                    console.error(error);
                }
            }
        }

        const totalRequest =
            parseFloat(originalSubtotal) -
            parseFloat(newSubtotal) -
            parseFloat(restockingFee.value) -
            parseFloat(shippingAmount.value);
        order.total_return = totalRequest >= 0 ? totalRequest : 0;
        order.total_charge = totalRequest < 0 ? Math.abs(totalRequest) : 0;
        order.original_grand_total = originalSubtotal;
        order.new_grand_total = newSubtotal;
        order.has_exchange = exchangeItem ? true : false;
        order.new_shipping = shippingAmount.value;
        shippingAmount.value = shippingAmount.value;
        returnCredit.value = originalSubtotal;
        exchangeSubtotal.value = newSubtotal;
        totalReturn.value = order.total_return;
        totalCharge.value = order.total_charge;
        originalTotalReturn.value = order.total_return;

        const cancelRequest = () => {
            for (const index in order.line_items) {
                const item = order.line_items[index];
                if (!item.disabled && item.selected) {
                    order.line_items[index].selected = false;
                    order.line_items[index].new_product = null;
                    order.line_items[index].transaction_type = null;
                    order.line_items[index].new_product = null;
                }
                order.line_items[index].additional_credit_selected = false;
                order.line_items[index].events = {};
            }
            order.has_exchange = false;
            order.has_request = false;
            order.additional_credit_selected = false;
            order.items_count = 0;
            order.events = {};
            store.setOrder(order);
            Inertia.get(`/${usePage().props.value.prefix}/orders/review`);
        };
        const goBack = () => {
            Inertia.get(`/${usePage().props.value.prefix}/orders/review`);
        };

        watchEffect(() => {
            if (
                applyAdditionalExchangeDiscount.value &&
                additionalExchangeDiscount.value > 0
            ) {
                const discountDiff =
                    order.total_charge - additionalExchangeDiscount.value;
                additionalExchangeDiscountApplied.value =
                    discountDiff < 0 ? order.total_charge : discountDiff;
                additionalExchangeDiscountBalance.value =
                    additionalExchangeDiscount.value -
                    additionalExchangeDiscountApplied.value;
                order.total_charge -= additionalExchangeDiscountApplied.value;
            } else {
                order.total_charge =
                    totalRequest < 0 ? Math.abs(totalRequest) : 0;
                additionalExchangeDiscountBalance.value =
                    additionalExchangeDiscount.value;
                additionalExchangeDiscountApplied.value = false;
            }
        }, []);

        const saveAddress = () => {
            order.shipping_address.first_name = address.value.first_name;
            order.shipping_address.last_name = address.value.last_name;
            order.shipping_address.address1 = address.value.address1;
            order.shipping_address.address2 = address.value.address2;
            order.shipping_address.city = address.value.city;
            order.shipping_address.province = address.value.province;
            order.shipping_address.zip = address.value.zip;
            order.shipping_address.phone = address.value.phone;
            order.shipping_address.name =
                address.value.first_name + " " + address.value.last_name;
            store.setOrder(order);
            editAddress.value = false;
        };

        const selectRefundType = (item) => {
            for (const index in props.refundTypes) {
                props.refundTypes[index].selected = false;
            }
            const events = order.events ? order.events : {};
            delete events[CONSTANTS.GIFTCARD_SELECTED];
            delete events[CONSTANTS.PAYMENT_SELECTED];
            item.selected = true;
            if (item.refund_type_id == 1) {
                giftcartCredit.value =
                    parseFloat(item.extra_amount) + originalTotalReturn.value;
                totalReturn.value = 0;
                events[CONSTANTS.GIFTCARD_SELECTED] = true;
                order.events = events;
            } else {
                giftcartCredit.value = baseGiftcardCredit.value;
                totalReturn.value = originalTotalReturn.value;
                events[CONSTANTS.PAYMENT_SELECTED] = true;
                order.events = events;
            }
            store.setOrder(order);
        };

        const submitRequest = async () => {
            isLoading.value = true;
            const firstTimePurchase =
                props.customer && props.customer.orders_count == 1
                    ? true
                    : false;
            if (firstTimePurchase) {
                order.events[CONSTANTS.FIRST_TIME_PURCHASE] = true;
            }
            let items = [];
            let hasRequest = false;
            let hasExchange = false;
            let originalQty = 0;
            let returnQty = 0;
            let exchangeQty = 0;
            for (const _item of order.line_items) {
                originalQty += parseInt(_item.quantity);
                if (!_item.selected || _item.disabled) {
                    continue;
                }
                const item = {
                    transaction_type: _item.transaction_type,
                    original_order_id: order.id,
                    order_item_id: _item.id,
                    item_key: _item.key,
                    original_product_id: _item.product
                        ? _item.product.parent_id
                        : _item.product_id,
                    original_variant_id: _item.product
                        ? _item.product.product_id
                        : _item.variant_id,
                    original_sku: _item.sku,
                    original_price: _item.price,
                    original_discount: _item.discount_amount,
                    original_tax_amount: _item.tax_amount,
                    original_qty: _item.quantity,
                    original_name: _item.title,
                    original_image: _item.image,
                    new_product_id: _item.new_product_id,
                    new_variant_id: _item.new_variant_id,
                    new_sku: _item.new_sku,
                    new_price: _item.new_price,
                    new_discount: newDiscountAmount.value,
                    new_tax_amount: _item.new_tax_amount,
                    new_name: _item.new_name,
                    new_image: _item.new_image,
                    new_qty: _item.new_qty,
                    qty_return: _item.qty_return,
                    qty_exchange: _item.qty_exchange,
                    survey_items: _item.survey_items,
                    exclude: _item.exclude ? true : false,
                    tags: _item.events
                        ? Object.keys(_item.events).join(",")
                        : "",
                };
                items.push(item);
                if (_item.transaction_type == "return") {
                    returnQty += parseInt(_item.qty_return);
                    hasRequest = true;
                }
                if (_item.transaction_type == "exchange") {
                    exchangeQty += parseInt(_item.qty_exchange);
                    hasExchange = true;
                }
            }
            let request = {
                transaction_type:
                    hasRequest && hasExchange
                        ? "exchange and return"
                        : hasExchange
                        ? "exchange"
                        : "return",
                confirmed_return: hasRequest ? 1 : 0,
                confirmed_exchange: hasExchange ? 1 : 0,
                customer_id: order.customer ? order.customer.id : null,
                firstname: order.customer ? order.customer.first_name : null,
                lastname: order.customer ? order.customer.last_name : null,
                order_id: order.id,
                order_number: order.name,
                refund_order_id: order.id,
                total_return_original: totalReturn.value,
                total_charge: totalCharge.value,
                order_credit_amount: originalSubtotal,
                original_grand_total: order.original_grand_total,
                original_discount_amount: order.total_discounts,
                original_tax_amount: order.total_tax,
                original_subtotal: order.original_grand_total,
                final_order_id: null,
                additional_discount: additionalExchangeDiscountApplied.value,
                apply_additional_discount:
                    applyAdditionalExchangeDiscount.value,
                giftcard_amount: giftcartCredit.value,
                bonus_discount: bonusDiscountCredit.value,
                restocking_fee: restockingFee.value,
                new_grand_total: order.new_grand_total,
                new_shipping_amount: shippingAmount.value,
                customer_email: order.email,
                original_qty: originalQty,
                qty_return: returnQty,
                qty_exchange: exchangeQty,
                remains_qty: originalQty - returnQty,
                shipping_address: order.shipping_address,
                refund_type_id: selectedRefundType.value
                    ? selectedRefundType.value.refund_type_id
                    : null,
                tags: order.events ? Object.keys(order.events).join(",") : "",
                items: items,
            };
            let response = await axios.post(
                `/${usePage().props.value.prefix}/requests/save`,
                request
            );
            const data = response.data;
            if (data && data.id) {
                order.items_count = 0;
                order.has_request = true;
                order.return_status = data.status_administrator;
                order.transaction_type = data.transaction_type;
                order.total_return = data.total_return;
                order.transaction_number = data.transaction_number;
                order.total_charge = data.total_charge;
                order.original_grand_total = data.original_grand_total;
                order.new_grand_total = data.new_grand_total;
                const requests = order.requests;
                if (!requests) {
                    order.requests = [];
                }
                order.requests.push(data);
                for (const index in data.items) {
                    const requestItem = data.items[index];
                    for (const key in order.line_items) {
                        const item = order.line_items[key];
                        if (item.sku == requestItem.original_sku) {
                            order.line_items[key].disabled = true;
                            break;
                        }
                    }
                }
                const availableItem = order.line_items.find(
                    (item) => !item.disabled
                );
                order.disabled = availableItem ? false : true;
                store.setOrder(order);
                isLoading.value = false;
                Inertia.get(
                    `/${usePage().props.value.prefix}/requests/success`
                );
            }
        };

        return {
            order,
            cancelRequest,
            submitRequest,
            goBack,
            editAddress,
            editAddressAvailable,
            saveAddress,
            address,
            additionalExchangeDiscount,
            applyAdditionalExchangeDiscount,
            additionalExchangeDiscountApplied,
            additionalExchangeDiscountBalance,
            restockingFee,
            shouldWeChargeRestockingFee,
            shippingAmount,
            taxAmount,
            returnCredit,
            exchangeSubtotal,
            totalReturn,
            totalCharge,
            hover,
            isLoading,
            selectRefundType,
            giftcartCredit,
            bonusCreditValidation,
            bonusDiscountCredit,
            originalTotalReturn,
        };
    },
};
</script>
