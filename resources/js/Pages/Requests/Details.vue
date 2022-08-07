<template>
  <div class="bg-white">
        <header class="bg-gray-100">
                  <div class="flex justify-center full-width p-4 border-b-2 border-[#ccc] bg-white">
                    <a :href="$page.props.settings.account.domain" target="_blank">
                        <span v-if="$page.props.settings && $page.props.settings.account">
                                <img
                                        v-if="$page.props.settings.account.logo"
                                        :src="$page.props.settings.account.logo"
                                        alt="MB logo"
                                        class="h-8"
                                />
                                <label v-else>{{$page.props.settings.name}}</label>
                        </span>
                    </a>
                  </div>
                  <div class="flex justify-between">
                    <div class="flex justify-center">
                        <h1 class="text-center p-8 text-md lg:text-xl font-bold full-width">Requests Details</h1>
                    </div>
                    <div class="flex justify-end items-center mr-8">
                        <AccountMenu class="" :order="order" /> 
                    </div>
                  </div>
          </header>
        <div class="relative flex flex-col p-4">
            <div class="w-full" v-for="request in order.requests" :key="request.id">
                <div class="flex flex-col md:flex-row md:gap-8">
                    <h2 class="text-sm md:text-lg py-0 font-bold my-2 md:my-4">Request: #{{request.transaction_number}}</h2>
                    <h2 class="text-sm md:text-lg py-0 font-bold my-2 md:my-4">Status: {{request.status_administrator}}</h2>
                </div>
                 <div class="mt-8 flex flex-col xl:flex-row">
                    <ul class="flex flex-col w-full xl:w-1/2">
                        <li class="flex mb-12">
                            <div class="w-32"></div>
                            <div class="w-72 font-bold text-xs md:text-sm">Original Product</div>
                            <div class="w-24 font-bold text-xs md:text-sm text-center">Quantity</div>
                            <div class="w-24 font-bold text-xs md:text-sm text-center">Price</div>
                            <div class="w-24 font-bold text-xs md:text-sm text-center">Total</div>
                        </li>
                        <li class="flex mb-4 pb-4" v-for="item in request.items" :key="item.id" >
                            <div  class="w-full flex xl:border-gray-200 xl:border-b">
                                <div class="w-32 h-32 px-2 overflow-hidden">
                                    <img 
                                        v-if="item.original_image"
                                        :src="item.original_image" 
                                        class="w-full h-full object-center object-cover lg:w-full lg:h-full" alt="">
                                    <img
                                        v-else
                                        src="../../../../public/images/product_placeholder.png"
                                        alt="Product image placeholder"
                                        class="w-full h-full object-center object-cover lg:w-full lg:h-full"
                                    />
                                </div>
                                <div class="w-72">
                                    <p class="text-xs md:text-sm">{{item.original_name}}</p>
                                    <p class="text-xs md:text-sm text-gray-400">{{item.original_sku}}</p>
                                </div>
                                <p class="text-xs md:text-sm w-24 text-center">{{item.original_qty}}</p>
                                <p class="text-xs md:text-sm w-24 text-center">{{item.original_price}}</p>
                                <p class="text-xs md:text-sm w-24 text-center">{{parseFloat(item.original_qty * item.original_price).toFixed(2)}}</p>
                            </div>
                        </li>
                    </ul>
                    <ul class="flex flex-col w-full xl:w-1/2">
                        <li class="flex mb-12">
                            <div class="w-32"></div>
                            <div class="w-72 font-bold text-xs md:text-sm">New Product</div>
                            <div class="w-24 font-bold text-xs md:text-sm text-center">Quantity</div>
                            <div class="w-24 font-bold text-xs md:text-sm text-center">Price</div>
                            <div class="w-24 font-bold text-xs md:text-sm text-center">Total</div>
                        </li>
                        <li class="flex mb-4 pb-4" v-for="item in request.items" :key="item.id" >
                            <div class="w-full flex border-gray-200 border-b">
                            <div class="w-32 h-32 px-2 overflow-hidden">
                                <div v-if="item.transaction_type == 'exchange'">
                                    <img :src="item.new_image ? item.new_image : '../../../../public/images/product_placeholder.png'" class="w-full h-full object-center object-cover lg:w-full lg:h-full" alt="">
                                </div>
                                
                            </div>
                            <div class="w-72">
                                <p class="text-xs md:text-sm">{{item.new_name ? item.new_name : 'None' }}</p>
                                <p class="text-xs md:text-sm text-gray-400">{{item.new_sku}}</p>
                            </div>
                            <p class="text-xs md:text-sm w-24 text-center">{{item.qty_return ? item.qty_return : item.qty_exchange}}</p>
                            <p class="text-xs md:text-sm w-24 text-center">{{item.new_price ? item.new_price : parseFloat(0).toFixed(2)}}</p>
                            <p class="text-xs md:text-sm w-24 text-center">
                                <span v-if="item.qty_return">{{parseFloat(item.qty_return * 0).toFixed(2)}}</span>
                                <span v-else>{{parseFloat(item.qty_exchange * item.new_price).toFixed(2)}}</span>
                            </p>
                            </div>
                        </li>
                    </ul>
                </div>

                <div class="py-2 align-middle relative overflow-hidden inline-block w-full sm:px-6 lg:px-8">
             <div class="w-full md:w-1/2 xl:w-1/4 float-right">
                 <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            New Item Subtotal
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{order.currency}} {{ parseFloat(request.new_grand_total).toFixed(2) }}
                            </td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            Return Credit
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{order.currency}} {{ parseFloat(request.original_grand_total).toFixed(2) }}
                            </td>
                        </tr>
                        <tr v-if="request.additional_discount > 0">
                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                            >Special Discount</td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                            >- {{ order.currency }} {{ parseFloat(request.additional_discount ? request.additional_discount : 0).toFixed(2) }}</td>
                        </tr>
                        <tr v-if="request.new_shipping_amount > 0">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            Shipping
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{order.currency}} {{ parseFloat(request.new_shipping_amount ? request.new_shipping_amount : 0).toFixed(2) }}
                            </td>
                        </tr>
                        <tr v-if="request.restocking_fee > 0">
                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                            >Restocking Fee</td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                            >{{ order.currency }} {{parseFloat(request.restocking_fee ? request.restocking_fee : 0).toFixed(2) }}</td>
                        </tr>
                        <tr v-if="request.total_return_original > 0 && request.total_charge == 0">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-bold">
                            Total Refund Estimated
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-bold">
                            {{order.currency}} {{ parseFloat(request.total_return_original).toFixed(2) }}
                            </td>
                        </tr>
                        <tr v-else>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-bold">
                            Total Estimated Due
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-bold">
                            {{order.currency}} {{ parseFloat(request.total_charge).toFixed(2) }}
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    </div>
             </div>      
        </div>

            </div>
        </div>
        <div class="my-12">
            <HomeLink />
        </div>
    </div>
</template>

<style scoped>
   
</style>

<script>
import OrderItem from '@/Components/OrderItem.vue';
import OrderSummary from '@/Components/OrderSummary.vue';
import AccountMenu from '@/Components/AccountMenu.vue';
import Footer from '@/Components/Footer.vue';
import HomeLink from '@/Components/HomeLink.vue';
import { Inertia } from '@inertiajs/inertia';
import { usePage } from '@inertiajs/inertia-vue3';
export default {
   components: {
        OrderItem,
        OrderSummary,
        AccountMenu,
        Footer,
        HomeLink
   },
    
    setup(props){
        const order = JSON.parse(localStorage.getItem('order'));
        const closePage = () => {
            Inertia.get(`/${usePage().props.value.prefix}/orders/review`);
        }
        return {
            order,
            closePage
        }
    }
   
}
</script>
