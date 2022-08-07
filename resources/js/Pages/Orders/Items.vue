<template>
    <div>
        <FullPageLoader :isLoading="true" />
    </div>
</template>

<style scoped></style>

<script>
import FullPageLoader from "@/Components/FullPageLoader.vue";
import { Inertia } from "@inertiajs/inertia";
import { usePage } from "@inertiajs/inertia-vue3";
import { useOrderStore } from "@/Stores/order";
export default {
    props: ["isAdmin", "order"],
    components: {
        FullPageLoader,
    },
    setup(props) {
        const store = useOrderStore();
        const order = props.order;
        let item = null;
        for (const index in order.line_items) {
            let taxAmount = 0;
            let discountAmount = 0;
            item = order.line_items[index];
            order.line_items[index].price =
                parseFloat(item.price) * parseInt(item.quantity);
            if (order.tags?.includes("Exchange")) {
                item.tax_lines.map(
                    (itemObj) => (taxAmount += parseFloat(itemObj.price))
                );
                item.discount_allocations.map(
                    (itemObj) => (discountAmount += parseFloat(itemObj.amount))
                );
                order.line_items[index].tax_amount =
                    parseFloat(taxAmount).toFixed(2);
                order.line_items[index].discount_amount = 0;
                order.line_items[index].base_price =
                    order.line_items[index].price_set.presentment_money.amount;
            } else {
                item.tax_lines.map(
                    (itemObj) => (taxAmount += parseFloat(itemObj.price))
                );
                item.discount_allocations.map(
                    (itemObj) => (discountAmount += parseFloat(itemObj.amount))
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
                    order.line_items[index].price_set.presentment_money.amount;
            }
        }
        store.setOrder(order);
        localStorage.setItem("isAdmin", props.isAdmin);
        Inertia.get(`/${usePage().props.value.prefix}/orders/review`);
    },
};
</script>
