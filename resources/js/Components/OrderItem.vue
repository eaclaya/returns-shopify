<template>
    <div class="cursor-pointer border border-gray-300 rounded-lg">
        <div
            class="relative overflow-hidden aspect-w-1 aspect-h-1 rounded-md overflow-hidden lg:aspect-none"
        >
            <div
                v-if="item.selected || item.disabled"
                class="absolute top-0 left-0 right-0 w-full h-full bg-gray-500 bg-opacity-50 z-50 flex flex-col items-center justify-center"
            >
                <p class="text-white font-bold uppercase text-2xl">
                    {{ item.transaction_type }}
                </p>
            </div>
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
                <div class="flex flex-col max-w-[300px] overflow-hidden">
                    <h3 class="text-sm text-gray-700 whitespace-nowrap">
                        <span aria-hidden="true" class></span>
                        {{ item.name }}
                    </h3>
                    <p class="mt-1 text-sm text-gray-500" v-if="item.product">
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
                    <p class="mt-1 text-sm text-gray-500" v-if="item.product">
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

        <div
            class="flex flex-col"
            v-if="!order.admin_required || isAdmin == true"
        >
            <button
                class="bg-gray-200 text-sm text-gray-700 py-2 border-b border-gray-300"
                :class="item.disabled ? 'text-disabled' : 'hover:bg-gray-300'"
                @click="goStepTwo"
                v-if="
                    !(order.tags?.indexOf('Exchange') >= 0) &&
                    (item.pre_tax_price > 0 || isAdmin == true) &&
                    !order.shipping_not_valid_for_exchange
                "
            >
                Exchange item
            </button>
            <button
                class="bg-gray-200 text-sm text-gray-700 py-2"
                :class="item.disabled ? 'text-disabled' : 'hover:bg-gray-300'"
                @click="saveItemRequest"
            >
                Return item
            </button>
            <button
                class="bg-gray-200 text-sm text-gray-700 py-2 border-t border-gray-300"
                @click="cancelItemRequest"
                :class="item.disabled ? 'text-disabled' : 'hover:bg-gray-300'"
                v-if="item.selected && !item.disabled"
            >
                Cancel request
            </button>
        </div>
    </div>
</template>
<script>
import { Inertia } from "@inertiajs/inertia";
import { usePage } from "@inertiajs/inertia-vue3";
import { useOrderStore } from "@/Stores/order";
import CONSTANTS from "../Helpers/constants";
export default {
    props: {
        item: {
            type: Object,
        },
        order: {
            type: Object,
        },
        isAdmin: {
            type: Boolean,
        },
        additionalReturnCredit: {
            type: Number,
        },
        refundTypes: {
            type: Object,
        },
        accountSetting: {
            type: Object,
        },
    },
    setup(props) {
        const store = useOrderStore();
        const order = store.order;
        const goStepTwo = () => {
            if (props.item.disabled) {
                return;
            }
            if (
                props.isAdmin.value == false &&
                props.item.product.tags &&
                props.item.product.tags.toLowerCase().includes("rma_finalsale")
            ) {
                props.order.has_finalsale_tag = true;
                return;
            }
            if (
                props.isAdmin.value == false &&
                props.item.product.tags &&
                props.item.product.tags.toLowerCase().includes("rma_no_return")
            ) {
                props.item.transaction_type = "exchange";
                props.item.qty_exchange = 1;
                localStorage.setItem(
                    "current_item",
                    JSON.stringify(props.item)
                );
                props.order.has_no_return_tag = true;
                return;
            }
            for (let index in props.order.line_items) {
                const _item = props.order.line_items[index];
                if (_item.key == props.item.key) {
                    props.order.line_items[
                        index
                    ].additional_credit_selected = false;
                    const events = props.item.events ? props.item.events : {};
                    events[CONSTANTS.EXCHANGE_SELECTED] = true;
                    props.item.events = events;
                }
            }
            store.setOrder(props.order);
            localStorage.setItem("current_item", JSON.stringify(props.item));
            Inertia.get(`/${usePage().props.value.prefix}/requests/step/1`);
        };

        const saveItemRequest = async () => {
            if (props.item.disabled) {
                return;
            }
            if (
                props.isAdmin.value == false &&
                props.item.product.tags &&
                props.item.product.tags.toLowerCase().includes("rma_finalsale")
            ) {
                props.order.has_finalsale_tag = true;
                return;
            }

            const events = props.item.events ? props.item.events : {};
            events[CONSTANTS.REFUND_SELECTED] = true;
            props.item.events = events;
            if (
                props.isAdmin.value == false &&
                props.item.product.tags &&
                props.item.product.tags.toLowerCase().includes("rma_no_return")
            ) {
                props.item.transaction_type = "return";
                props.item.qty_return = 1;
                localStorage.setItem(
                    "current_item",
                    JSON.stringify(props.item)
                );
                props.order.has_no_return_tag = true;
                return;
            }
            const surveys = await JSON.parse(localStorage.getItem("surveys"));
            if (surveys && surveys.length > 0) {
                const itemPrice = props.item.price;
                const tags = props.order.tags;
                const ordersCount = props.order.customer
                    ? props.order.customer.orders_count
                    : 0;
                let refundTypeRuleValid = false;
                let refundTypeAction = "fixed";
                for (const index in props.refundTypes) {
                    const conditions = props.refundTypes[index].conditions;
                    const _item = props.refundTypes[index];
                    if (_item.refund_type_id == 1) {
                        if (conditions) {
                            try {
                                refundTypeRuleValid = eval(conditions.trim());
                                refundTypeAction = _item.action;
                            } catch (error) {
                                console.error(error);
                            }
                        }
                    }
                }
                if (
                    refundTypeRuleValid &&
                    !props.order.shipping_not_valid_for_exchange &&
                    !props.order.tags.includes("Exchange")
                ) {
                    props.order.current_item = props.item;
                    props.item.transaction_type = "return";
                    props.order.line_items.map((_item) => {
                        if (
                            _item.key == props.item.key ||
                            (_item.selected &&
                                _item.transaction_type == "return")
                        ) {
                            props.order.return_credit +=
                                parseInt(_item.quantity) *
                                (parseFloat(_item.price) -
                                    parseFloat(_item.total_discount));
                        }
                    });
                    if (refundTypeAction == "fixed") {
                        props.order.return_credit_with_additional =
                            props.order.return_credit +
                            props.additionalReturnCredit;
                    } else {
                        props.order.return_credit_with_additional =
                            props.order.return_credit *
                            (1 + props.additionalReturnCredit / 100);
                    }
                } else {
                    props.item.transaction_type = "return";
                    let lineItemIndex = 0;
                    for (let index in props.order.line_items) {
                        const _item = props.order.line_items[index];
                        if (_item.key == props.item.key) {
                            lineItemIndex = index;
                            props.order.line_items[index].transaction_type =
                                "return";
                            props.order.line_items[index].qty_return = 1;
                        }
                    }
                    localStorage.setItem(
                        "current_item",
                        JSON.stringify(props.item)
                    );
                    store.setOrder(props.order);
                    const surveys = await JSON.parse(
                        localStorage.getItem("surveys")
                    );
                    if (
                        surveys &&
                        surveys.length > 0 &&
                        availableSurveyOptions(surveys, props.item)
                    ) {
                        Inertia.get(
                            `/${usePage().props.value.prefix}/requests/step/3`
                        );
                    } else {
                        props.order.line_items[lineItemIndex].selected = true;
                        const selectedItems = props.order.line_items.filter(
                            (item) => !item.disabled && item.selected
                        );
                        props.order.items_count = selectedItems
                            ? selectedItems.length
                            : 0;
                        store.setOrder(props.order);
                        Inertia.get(
                            `/${usePage().props.value.prefix}/orders/review`
                        );
                    }
                }
            } else {
                for (const index in props.order.line_items) {
                    const _item = props.order.line_items[index];
                    if (_item.key == props.item.key) {
                        props.order.line_items[index].survey_items =
                            props.item.survey_items;
                        props.order.line_items[index].selected = true;
                        props.order.line_items[index].transaction_type =
                            "return";
                        props.order.line_items[index].qty_return = 1;
                    }
                }
                const selectedItems = props.order.line_items.filter(
                    (item) => !item.disabled && item.selected
                );
                props.order.items_count = selectedItems
                    ? selectedItems.length
                    : 0;
                store.setOrder(props.order);
            }
        };
        const availableSurveyOptions = (surveys, currentItem) => {
            let valid = false;
            for (const index in surveys) {
                const item = surveys[index];
                if (
                    item.product_types.indexOf(currentItem.product_type) >= 0 ||
                    item.product_types == ""
                ) {
                    valid = true;
                }
            }
            return valid;
        };
        const cancelItemRequest = () => {
            if (props.item.disabled) {
                return;
            }
            props.item.transaction_type = "";
            props.item.events = {};
            for (let index in props.order.line_items) {
                const _item = props.order.line_items[index];
                if (_item.key == props.item.key) {
                    delete props.order.line_items[index].transaction_type;
                    delete props.order.line_items[index].qty_return;
                    delete props.order.line_items[index].selected -
                        delete props.order.line_items[index].new_product;
                    props.order.items_count--;
                }
            }
            localStorage.setItem("current_item", JSON.stringify(props.item));
            store.setOrder(props.order);
        };

        return {
            goStepTwo,
            saveItemRequest,
            cancelItemRequest,
        };
    },
};
</script>
