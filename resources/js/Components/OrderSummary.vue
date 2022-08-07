<template>
    <div class="relative w-full px-4 bg-gray-200" v-if="order.items_count > 0">
        <div class="flex items-center  py-6">
            <p class="w-1/2 font-bold">{{order.items_count}} {{order.items_count > 1 ? 'items selected' : 'item selected'}}</p>
            <div class="w-1/2">
                <button class="bg-gray-700 px-2 text-sm md:px-16 py-4 text-white hover:bg-gray-800 float-right font-semibold rounded-md" @click="returnReview">Continue with request</button>
            </div>
        </div>
    </div>
</template>
<script>
import { Inertia } from '@inertiajs/inertia'
import { usePage } from '@inertiajs/inertia-vue3'
export default {
    props: {
        order: {
            type: Object
        }
    },
    setup(props) {
        const returnReview = () => {
            Inertia.get(`/${usePage().props.value.prefix}/requests/review`, {order_number: props.order.name})
        }
        return {
            returnReview
        }
    }
    
}
</script>

