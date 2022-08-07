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
        <div class="flex flex-col md:flex-row max-h-90vh mt-12">
            <div
                class="w-full md:w-1/2 flex flex-col justify-center overflow-hidden cursor-pointer"
            >
                <div v-if="currentItem" class="max-w-lg m-auto">
                    <img
                        v-if="currentItem"
                        :src="currentItem.image"
                        :alt="currentItem.name"
                        class="w-full h-full object-center object-cover lg:w-full lg:h-full"
                    />
                    <img
                        v-else
                        src="../../../../public/images/product_placeholder.png"
                        alt="Product image placeholder"
                        class="w-full h-full object-center object-cover lg:w-full lg:h-full"
                    />
                </div>
                <div v-else class="max-w-lg m-auto">
                    <img
                        src="../../../../public/images/product_placeholder.png"
                        alt="Product image placeholder"
                        class="w-full h-full object-center object-cover lg:w-full lg:h-full"
                    />
                </div>
            </div>
            <div class="w-full md:w-1/2 flex flex-col pr-8" id="return-reasons">
                <h2 class="text-2xl font-bold px-4">{{ survey.title }}</h2>
                <div class="mb-2 px-4">
                    <p class="my-2 text-lg font-semibold text-gray-700">
                        Original Item
                    </p>
                    <p class="text-gray-500">{{ currentItem.title }}</p>
                    <p class="text-gray-500">
                        <span class="pr-2 border-r border-gray-500">{{
                            currentItem.sku
                        }}</span>
                        <span
                            >{{ order.currency }} {{ currentItem.price }}</span
                        >
                    </p>
                </div>
                <ul class="px-2 py-4 max-w-lg">
                    <li
                        v-for="item in surveyItems"
                        :key="item.id"
                        class="text-lg border-b border-gray-200"
                    >
                        <div
                            v-if="item.type == 'radio'"
                            @click="selectSurveyItem(item)"
                            class="hover:bg-gray-200 cursor-pointer p-4"
                        >
                            {{ item.label }}
                        </div>
                        <div v-else class="relative overflow-hidden pb-8">
                            <p>{{ item.label }}</p>
                            <textarea
                                cols="30"
                                rows="5"
                                class="w-full mb-4"
                                v-model="inputText"
                                style="resize: none"
                            ></textarea>
                            <button
                                @click="selectSurveyItem(item)"
                                class="bg-gray-700 text-white px-8 py-2 float-right"
                            >
                                Continue
                            </button>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>
<script>
import { ref, inject, watchEffect } from "vue";
import { Inertia } from "@inertiajs/inertia";
import { usePage } from "@inertiajs/inertia-vue3";
import { useOrderStore } from "@/Stores/order";
export default {
    setup(props) {
        const currentItem = JSON.parse(localStorage.getItem("current_item"));
        const store = useOrderStore();
        const order = store.order;
        const surveys = JSON.parse(localStorage.getItem("surveys"));
        const stepNumber = 3;
        const stepIndex = stepNumber - 1;
        let surveyItems = ref([]);
        let survey = ref({});
        let surveyStep = ref({});
        const inputText = ref("");
        const closePage = () => {
            Inertia.get(`/${usePage().props.value.prefix}/orders/review`);
        };

        const goBack = () => {
            Inertia.get(`/${usePage().props.value.prefix}/requests/step/5`);
        };

        for (const index in surveys) {
            const item = surveys[index];
            if (item.product_types.indexOf(currentItem.product_type) >= 0) {
                survey = item;
                surveyStep = survey.steps.find(
                    (step) => step.sort_number == stepNumber
                );
                survey.title = item.steps[stepIndex].name;
                surveyItems = survey.items.filter(
                    (_item) =>
                        _item.survey_step_id == surveyStep.id &&
                        _item.parent_id ==
                            currentItem.survey_items[stepIndex].id
                );
                if (surveyItems && surveyItems.length == 1) {
                    survey.title =
                        surveyItems[0].type == "textarea"
                            ? "Help us improve!"
                            : survey.title;
                }
            }
        }

        const selectSurveyItem = (item) => {
            item.selected = true;
            item.value = inputText.value ? inputText.value : true;
            currentItem.survey_items = currentItem.survey_items
                ? currentItem.survey_items
                : {};
            currentItem.survey_items[stepNumber] = item;
            localStorage.setItem("current_item", JSON.stringify(currentItem));
            if (nextSurveyItems()) {
                Inertia.get(`/${usePage().props.value.prefix}/requests/step/7`);
            } else {
                for (const index in order.line_items) {
                    const _item = order.line_items[index];
                    if (_item.sku == currentItem.sku) {
                        order.line_items[index].survey_items =
                            currentItem.survey_items;
                        order.line_items[index].selected = true;
                    }
                }
                const selectedItems = order.line_items.filter(
                    (item) => !item.disabled && item.selected
                );
                order.items_count = selectedItems ? selectedItems.length : 0;
                store.setOrder(order);
                Inertia.get(`/${usePage().props.value.prefix}/orders/review`);
            }
        };
        const nextSurveyItems = () => {
            const nextSurveyStep = survey.steps.find(
                (step) => step.sort_number == stepNumber + 1
            );
            if (!nextSurveyStep) {
                return false;
            }
            const found = survey.items.find(
                (_item) =>
                    _item.survey_step_id == nextSurveyStep.id &&
                    _item.parent_id == currentItem.survey_items[stepNumber].id
            );
            return found ? true : false;
        };
        return {
            currentItem,
            order,
            surveys,
            survey,
            stepNumber,
            stepIndex,
            inputText,
            surveyItems,
            selectSurveyItem,
            closePage,
            goBack,
        };
    },

    mounted() {
        setTimeout(() => {
            const returnReasonsWrapper =
                document.querySelector("#return-reasons");
            if (returnReasonsWrapper) {
                returnReasonsWrapper.scrollIntoView();
            }
        }, 20);
    },
};
</script>
