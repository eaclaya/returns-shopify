import { defineStore } from "pinia";
import { usePage } from "@inertiajs/inertia-vue3";
import axios from "axios";
import { reactive, watch, ref } from "vue";
export const useOrderStore = defineStore("order", {
    state: () => {
        return {
            order: reactive({}),
        };
    },
    getters: {},
    actions: {
        async fetchOrder(formData) {
            const response = await axios.post(
                `/api/${usePage().props.value.prefix}/v1/orders/get`,
                formData
            );
            this.order = response.data ? response.data.response : {};
        },

        setOrder(data) {
            this.order = data;
        },
    },
    persist: true,
});
