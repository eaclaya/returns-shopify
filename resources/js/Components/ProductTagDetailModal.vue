<template>    
    <div  class="fixed top-0 left-0 right-0 flex items-center justify-center h-screen pt-4 px-4 pb-20 text-center sm:p-0 bg-gray-800 bg-opacity-70 z-999" 
        v-if="order && (order.has_finalsale_tag || order.has_no_return_tag)">
        <div class="relative inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
            <button class="absolute right-4 top-4" @click="closePage">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                    <div class="my-4">
                    <h2 class="text-gray-500 font-semibold text-lg text-center">
                        <div v-if="order.has_finalsale_tag">
                            <p v-if="accountSetting && accountSetting.finalsale_message">{{accountSetting.finalsale_message}}</p>
                            <p v-else>This product can't be returned or exchanged</p>
                        </div>
                        <div v-if="order.has_no_return_tag">
                            <p v-if="accountSetting && accountSetting.noreturn_message">{{accountSetting.noreturn_message}}</p>
                            <p v-else>This product shouldn't be returned</p>
                        </div>
                    </h2>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse" v-if="order && order.has_no_return_tag">
            <button type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-gray-700 text-base font-medium text-white hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 sm:ml-3  sm:text-sm" @click="continueWithExchange">Continue with exchange</button>
            <button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3  sm:text-sm" @click="continueWithReturn" ref="cancelButtonRef">Continue with return</button>
        </div>
        </div>
    </div>
    
</template>

<script>
import { ref } from 'vue'
import { Inertia } from '@inertiajs/inertia'
import { usePage } from '@inertiajs/inertia-vue3'
export default {
  
  props: {
        order: {
            type: Object
        },
        accountSetting: {
            type: Object
        }
    },
  setup(props) {
    const continueWithReturn = () => {
            Inertia.get(`/${usePage().props.value.prefix}/requests/step/3`)
    }
    const continueWithExchange = () => {
        Inertia.get(`/${usePage().props.value.prefix}/requests/step/2`)
    }
    const closePage = () => {
            Inertia.get(`/${usePage().props.value.prefix}/orders/review`)
    }
    return {
      continueWithExchange,
      continueWithReturn,
      closePage
    }
  },
}
</script>