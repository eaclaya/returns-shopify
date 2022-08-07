<template>
    <div>
        <div class="modal-header flex flex-col p-4">
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
            <button class="absolute left-4 top-4" @click="goBack">
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
                        d="M10 19l-7-7m0 0l7-7m-7 7h18"
                    />
                </svg>
            </button>
        </div>
        <div
            class="flex flex-col md:flex-row min-h-full min-h-90vh md:max-h-90vh mt-2"
        >
            <div
                class="w-full md:w-1/2 px-16 flex flex-col justify-center overflow-hidden cursor-pointer"
            >
                <div v-if="item">
                    <img
                        v-if="item.product"
                        :src="item.product.image"
                        :alt="item.name"
                        class="w-full h-full object-center object-cover lg:w-full lg:h-full"
                    />
                    <img
                        v-else
                        src="../../../../public/images/product_placeholder.png"
                        alt="Product image placeholder"
                        class="w-full h-full object-center object-cover lg:w-full lg:h-full"
                    />
                </div>
                <div v-else>
                    <img
                        src="../../../../public/images/product_placeholder.png"
                        alt="Product image placeholder"
                        class="w-full h-full object-center object-cover lg:w-full lg:h-full"
                    />
                </div>
            </div>
            <div
                class="w-full md:w-1/2 px-4 lg:px-0 flex flex-col justify-center mt-4 md:mt-0"
            >
                <section>
                    <div class="mb-8" v-if="item.is_new">
                        <h2
                            class="mb-8 text-lg lg:text-2xl font-bold text-gray-700"
                        >
                            Original Item
                        </h2>
                        <p class="text-gray-500">{{ item.title }}</p>
                        <p class="text-gray-500">
                            <span
                                class="pr-2 border-r border-gray-500"
                                v-if="item.sku"
                                >{{ item.sku }}</span
                            >
                            <span>{{ order.currency }}{{ item.price }}</span>
                        </p>
                        <h2
                            class="my-8 text-lg lg:text-2xl font-bold text-gray-700"
                        >
                            New Item for Exchange
                        </h2>
                    </div>
                    <h3 class="text-lg lg:text-2xl font-bold text-gray-700">
                        <span aria-hidden="true" class="relative"></span
                        >{{ item.name }} - {{ order.currency
                        }}{{ item.product ? item.product.price : 0.0 }}
                    </h3>
                    <ul v-for="(options, index) in productOptions" :key="index">
                        <li class="my-2">
                            <p class="mb-4">
                                {{ index }}: {{ selectedOptionLabel[index] }}
                            </p>
                            <ul class="flex items-center flex-wrap max-w-lg">
                                <li
                                    v-for="(option, key) in options"
                                    :key="key"
                                    class="mr-2 mb-6"
                                >
                                    <span
                                        v-if="option.type == 'text'"
                                        @click="
                                            selectProductOption(
                                                index,
                                                key,
                                                option.selected
                                            )
                                        "
                                        class="px-4 py-2 text-sm border-2 border-transparent bg-gray-200 cursor-pointer"
                                        :class="
                                            option.selected == true
                                                ? 'border border-white ring-2 ring-gray-600'
                                                : option.selected == -1
                                                ? 'text-gray-400 line-through'
                                                : ''
                                        "
                                        >{{ option.label }}</span
                                    >

                                    <span
                                        v-if="option.type == 'color'"
                                        @click="
                                            selectProductOption(
                                                index,
                                                key,
                                                option.selected
                                            )
                                        "
                                        :style="'background:' + option.label"
                                        class="w-8 h-8 block rounded-full cursor-pointer"
                                        :class="
                                            option.selected == true
                                                ? 'border border-white ring-2 ring-gray-600'
                                                : option.selected == -1
                                                ? 'text-gray-400 line-through'
                                                : ''
                                        "
                                    ></span>

                                    <span
                                        v-if="
                                            option.type == 'file' ||
                                            option.type == 'url'
                                        "
                                        @click="
                                            selectProductOption(
                                                index,
                                                key,
                                                option.selected
                                            )
                                        "
                                        :style="
                                            'background: url(' +
                                            option.label +
                                            ')'
                                        "
                                        class="w-8 h-8 block rounded-full cursor-pointer"
                                        :class="
                                            option.selected == true
                                                ? 'border border-white ring-2 ring-gray-600'
                                                : option.selected == -1
                                                ? 'text-gray-400 line-through'
                                                : ''
                                        "
                                    ></span>
                                </li>
                            </ul>
                        </li>
                    </ul>
                    <div class="flex">
                        <p class="text-pink-800 mb-4">{{ errorMesssage }}</p>
                    </div>
                    <div class="flex flex-col md:flex-row">
                        <button
                            class="px-4 py-2 bg-gray-100 mb-4 md:mb-0 md:mr-2 hover:bg-gray-300"
                            @click="goStepTwo"
                        >
                            Select a different product
                        </button>
                        <button
                            class="px-12 py-2 bg-gray-600 text-white hover:bg-gray-800"
                            @click="saveItemRequest"
                        >
                            Continue
                        </button>
                    </div>
                </section>
            </div>
        </div>
    </div>
</template>
<script>
import { usePage } from "@inertiajs/inertia-vue3";
import { useOrderStore } from "@/Stores/order";
import { Inertia } from "@inertiajs/inertia";
import { onMounted, reactive, ref, toRefs } from "vue";
export default {
    setup(props) {
        const store = useOrderStore();
        let order = store.order;
        let item = reactive(
            JSON.parse(localStorage.getItem("current_item")) || {}
        );
        let productOptions = ref({});
        let selectedOptionLabel = reactive({});
        let errorMesssage = reactive("");
        let selectedOptions = reactive({
            option1_name: "",
            option1_value: "",
            option2_name: "",
            option2_value: "",
            option3_name: "",
            option3_value: "",
        });
        onMounted(() => {
            if (
                order.shipping_not_valid_for_exchange ||
                order.tags?.indexOf("Exchange") >= 0
            ) {
                Inertia.get(`/${usePage().props.value.prefix}/orders/review`);
            }
            let products = JSON.parse(localStorage.getItem("products"));
            if (products.length > 0) {
                let options = {};
                let _productOptions = {};
                for (let product of products) {
                    if (product.handle != item.product.handle) {
                        continue;
                    }
                    if (product.option1_name && product.option1_value) {
                        if (!options[product.option1_name]) {
                            options[product.option1_name] = {};
                            _productOptions[product.option1_name] = {};
                        }
                        options[product.option1_name][
                            product.option1_value
                        ] = false;
                        _productOptions[product.option1_name][
                            product.option1_value
                        ] = {
                            label: product.option1_label
                                ? product.option1_label
                                : product.option1_value,
                            selected: false,
                            type: product.option1_type
                                ? product.option1_type
                                : "text",
                        };
                    }
                    if (product.option2_name && product.option2_value) {
                        if (!options[product.option2_name]) {
                            options[product.option2_name] = {};
                            _productOptions[product.option2_name] = {};
                        }
                        options[product.option2_name][
                            product.option2_value
                        ] = false;
                        _productOptions[product.option2_name][
                            product.option2_value
                        ] = {
                            label: product.option2_label
                                ? product.option2_label
                                : product.option2_value,
                            selected: false,
                            type: product.option2_type
                                ? product.option2_type
                                : "text",
                        };
                    }
                    if (product.option3_name && product.option3_value) {
                        if (!options[product.option3_name]) {
                            options[product.option3_name] = {};
                            _productOptions[product.option3_name] = {};
                        }
                        options[product.option3_name][
                            product.option3_value
                        ] = false;
                        _productOptions[product.option3_name][
                            product.option3_value
                        ] = {
                            label: product.option3_label
                                ? product.option3_label
                                : product.option3_value,
                            selected: false,
                            type: product.option3_type
                                ? product.option3_type
                                : "text",
                        };
                    }
                }
                let counter = 0;
                for (const index in _productOptions) {
                    const values = _productOptions[index];
                    selectedOptionLabel[index] = "";
                    if (Object.values(values).length == 1 && counter == 0) {
                        const keys = Object.keys(values);
                        const key = keys[0];
                        _productOptions[index][key].selected = true;
                        selectedOptionLabel[index] = key;
                    }
                    counter++;
                }
                productOptions.value = _productOptions;
                productStockValidation();
            }
        });

        const closePage = () => {
            Inertia.get(`/${usePage().props.value.prefix}/orders/review`);
        };
        const goBack = () => {
            Inertia.get(`/${usePage().props.value.prefix}/orders/review`);
        };
        const selectProductOption = (name, value, option) => {
            selectedOptionLabel[name] = "";
            if (option === -1) {
                return;
            }
            for (const key in productOptions.value[name]) {
                if (productOptions.value[name][key].selected !== -1) {
                    productOptions.value[name][key].selected = false;
                }
            }
            if (option === false) {
                selectedOptionLabel[name] = value;
                productOptions.value[name][value].selected = true;
            }
            getSelectedOption();
            productStockValidation();
        };

        const productStockValidation = () => {
            const nextProducts = getNextProducts();
            const optionsCount = Object.keys(productOptions.value).length;
            let selectedCount = 0;
            let selectedLevel = false;
            const selectedLevels = {};
            const stockThreshold = usePage().props.value.settings
                ? usePage().props.value.settings.stock_threshold
                : 0;
            if (Object.keys(nextProducts).length == 0) {
                for (const key in productOptions.value) {
                    const firstOption = productOptions.value[key];
                    for (const code in firstOption) {
                        if (productOptions.value[key][code].selected === -1) {
                            productOptions.value[key][code].selected = false;
                        }
                    }
                }
                return;
            }
            for (const key in productOptions.value) {
                selectedLevel = false;
                const firstOption = productOptions.value[key];
                for (const code in firstOption) {
                    if (firstOption[code].selected === true) {
                        selectedCount++;
                        selectedLevel = true;
                        break;
                    }
                }
                selectedLevels[key] = selectedLevel;
            }

            if (selectedCount < optionsCount - 1) {
                return;
            }
            for (const key in productOptions.value) {
                const firstOption = productOptions.value[key];
                if (selectedLevels[key]) {
                    continue;
                }
                for (const code in firstOption) {
                    if (productOptions.value[key][code].selected !== true) {
                        productOptions.value[key][code].selected = -1;
                    }
                }
            }

            for (const index in nextProducts) {
                const nextItem = nextProducts[index];
                if (nextItem.inventory_quantity <= stockThreshold) {
                    if (
                        productOptions.value[nextItem.option1_name] &&
                        productOptions.value[nextItem.option1_name][
                            nextItem.option1_value
                        ].selected === false
                    ) {
                        productOptions.value[nextItem.option1_name][
                            nextItem.option1_value
                        ].selected = -1;
                    }
                    if (
                        productOptions.value[nextItem.option2_name] &&
                        productOptions.value[nextItem.option2_name][
                            nextItem.option2_value
                        ].selected === false
                    ) {
                        productOptions.value[nextItem.option2_name][
                            nextItem.option2_value
                        ].selected = -1;
                    }
                    if (
                        productOptions.value[nextItem.option3_name] &&
                        productOptions.value[nextItem.option3_name][
                            nextItem.option3_value
                        ].selected === false
                    ) {
                        productOptions.value[nextItem.option3_name][
                            nextItem.option3_value
                        ].selected = -1;
                    }
                } else {
                    if (
                        productOptions.value[nextItem.option1_name] &&
                        productOptions.value[nextItem.option1_name][
                            nextItem.option1_value
                        ].selected === -1
                    ) {
                        productOptions.value[nextItem.option1_name][
                            nextItem.option1_value
                        ].selected = false;
                    }

                    if (
                        productOptions.value[nextItem.option2_name] &&
                        productOptions.value[nextItem.option2_name][
                            nextItem.option2_value
                        ].selected === -1
                    ) {
                        productOptions.value[nextItem.option2_name][
                            nextItem.option2_value
                        ].selected = false;
                    }
                    if (
                        productOptions.value[nextItem.option3_name] &&
                        productOptions.value[nextItem.option3_name][
                            nextItem.option3_value
                        ].selected === -1
                    ) {
                        productOptions.value[nextItem.option3_name][
                            nextItem.option3_value
                        ].selected = false;
                    }
                }
            }
        };
        const saveItemRequest = () => {
            errorMesssage = "";
            const validation = selectionValidation();

            if (!validation.valid) {
                return (errorMesssage = validation.error);
            }
            const newProduct = getProductByOptions();
            if (!newProduct) {
                return (errorMesssage =
                    "Product option is not valid, please select a different option");
            }
            if (newProduct) {
                item.selected = true;
                item.transaction_type = "exchange";
                let itemsCount = 0;
                for (let index in order.line_items) {
                    const newItem = order.line_items[index];
                    if (newItem.key == item.key) {
                        order.line_items[index].transaction_type = "exchange";
                        order.line_items[index].new_sku = newProduct.sku;
                        order.line_items[index].new_product_id =
                            newProduct.parent_id;
                        order.line_items[index].new_variant_id =
                            newProduct.product_id;
                        order.line_items[index].new_price = newProduct.price;
                        order.line_items[index].new_name = newProduct.title;
                        order.line_items[index].new_image = newProduct.image;
                        order.line_items[index].option1_name =
                            newProduct.option1_name;
                        order.line_items[index].option1_value =
                            newProduct.option1_value;
                        order.line_items[index].option2_name =
                            newProduct.option2_name;
                        order.line_items[index].option2_value =
                            newProduct.option2_value;
                        order.line_items[index].option3_name =
                            newProduct.option3_name;
                        order.line_items[index].option3_value =
                            newProduct.option3_value;
                        order.line_items[index].new_qty = 1;
                        order.line_items[index].qty_exchange = 1;
                        order.line_items[index].selected = true;
                        order.line_items[index].new_product = newProduct;
                        itemsCount++;
                    }
                }
                order.items_count = itemsCount;
                localStorage.setItem("current_item", JSON.stringify(item));
                store.setOrder(order);
                const surveys = JSON.parse(localStorage.getItem("surveys"));
                if (
                    surveys &&
                    surveys.length > 0 &&
                    availableSurveyOptions(surveys, item)
                ) {
                    Inertia.get(
                        `/${usePage().props.value.prefix}/requests/step/3`
                    );
                } else {
                    Inertia.get(
                        `/${usePage().props.value.prefix}/orders/review`
                    );
                }
            }
        };

        const availableSurveyOptions = (surveys, currentItem) => {
            let valid = false;
            for (const index in surveys) {
                const surveyItem = surveys[index];
                if (
                    surveyItem.product_types.indexOf(
                        currentItem.product_type
                    ) >= 0 ||
                    surveyItem.product_types == ""
                ) {
                    valid = true;
                }
            }
            return valid;
        };
        const getProductByOptions = () => {
            let products = JSON.parse(localStorage.getItem("products"));
            let level = 0;
            for (const key in selectedOptions) {
                const value = selectedOptions[key];
                if (key.indexOf("_value") > 0 && value != "") {
                    level++;
                }
            }

            for (const product of products) {
                if (product.option1_name && product.option1_value) {
                    if (
                        product.option1_name == selectedOptions.option1_name &&
                        product.option1_value == selectedOptions.option1_value
                    ) {
                        if (level == 1) {
                            return product;
                        }
                        if (product.option2_name && product.option2_value) {
                            if (
                                product.option2_name ==
                                    selectedOptions.option2_name &&
                                product.option2_value ==
                                    selectedOptions.option2_value
                            ) {
                                if (level == 2) {
                                    return product;
                                }
                                if (
                                    product.option3_name &&
                                    product.option3_value
                                ) {
                                    if (
                                        product.option3_name ==
                                            selectedOptions.option3_name &&
                                        product.option3_value ==
                                            selectedOptions.option3_value
                                    ) {
                                        if (level == 3) {
                                            return product;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            return null;
        };

        const getNextProducts = () => {
            let products = JSON.parse(localStorage.getItem("products"));
            let level = 0;
            selectionValidation();
            for (const key in selectedOptions) {
                const value = selectedOptions[key];
                if (key.indexOf("_value") > 0 && value != "") {
                    level++;
                }
            }
            let result = [];
            for (const product of products) {
                // IF THERE ARE 3 PRODUCT OPTION USE THIS VALIDATION
                if (product.option3_value) {
                    if (
                        product.option1_value &&
                        product.option1_value ==
                            selectedOptions.option1_value &&
                        product.option2_value &&
                        product.option2_value == selectedOptions.option2_value
                    ) {
                        result[product.product_id] = product;
                    }

                    if (
                        product.option1_value &&
                        product.option1_value ==
                            selectedOptions.option1_value &&
                        product.option3_value &&
                        product.option3_value == selectedOptions.option3_value
                    ) {
                        result[product.product_id] = product;
                    }
                } else {
                    // IF THERE ARE 2 OR 1 PRODUCT OPTION USE THIS VALIDATION
                    if (
                        product.option1_value &&
                        product.option1_value == selectedOptions.option1_value
                    ) {
                        result[product.product_id] = product;
                    }
                }
            }
            return result;
        };
        const goStepTwo = () => {
            Inertia.get(`/${usePage().props.value.prefix}/requests/step/2`);
        };
        const selectionValidation = () => {
            const optionsArray = Object.keys(selectedOptions);
            let index = 0;
            for (const key in productOptions.value) {
                let valid = false;
                const optionValues = productOptions.value[key];
                for (const code in productOptions.value[key]) {
                    const optionValue = productOptions.value[key][code];
                    if (optionValue.selected == true) {
                        selectedOptions[optionsArray[index]] = key;
                        index++;
                        selectedOptions[optionsArray[index]] = code;
                        index++;
                        valid = true;
                    }
                }
                if (!valid) {
                    const _optionName = key.toLowerCase();
                    const vowelRegex = "^[aieouAIEOU].*";
                    const matched = _optionName.match(vowelRegex);
                    const error = matched
                        ? `Please select an ${key.toLowerCase()}`
                        : `Please select a ${key.toLowerCase()}`;
                    return { valid: false, error: error };
                }
            }
            return { valid: true };
        };
        const getSelectedOption = () => {
            const optionsArray = Object.keys(selectedOptions);
            let index = 0;
            for (const key in productOptions.value) {
                let selected = false;
                const optionValues = productOptions.value[key];
                for (const code in productOptions.value[key]) {
                    const optionValue = productOptions.value[key][code];
                    if (optionValue.selected == true) {
                        selectedOptions[optionsArray[index]] = key;
                        selectedOptions[optionsArray[index + 1]] = code;
                        selected = true;
                    }
                }
                if (selected === false) {
                    selectedOptions[optionsArray[index]] = "";
                    selectedOptions[optionsArray[index + 1]] = "";
                }
                index = index + 2;
            }
            return selectedOptions;
        };

        return {
            order,
            item,
            productOptions,
            selectedOptionLabel,
            errorMesssage,
            selectedOptions,
            closePage,
            goBack,
            selectProductOption,
            saveItemRequest,
            goStepTwo,
        };
    },
};
</script>
