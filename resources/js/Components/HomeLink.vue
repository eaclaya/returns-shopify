<template>
    <button @click="closePage" class="border-b-2 border-gray-700 font-semibold px-4 py-2 mx-auto block uppercase">
        Back to my order
    </button>
</template>

<script>
import { Inertia } from '@inertiajs/inertia'
export default {
    setup(props) {
        const closePage = () => {
            const pathArray = window.location.href.split('/')
            const prefix = pathArray[3]
            Inertia.get(`/${prefix}/orders/review`)
        }
        return {
            closePage
        }
    }
}
</script>
