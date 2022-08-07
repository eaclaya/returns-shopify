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
                        <h1 class="text-center p-8 text-md lg:text-xl font-bold full-width">Order Details</h1>
                    </div>
                    <div class="flex justify-end items-center mr-8">
                        <AccountMenu class="" :order="order" /> 
                    </div>
                  </div>
          </header>

        <div class="relative flex flex-col md:flex-row md:p-12">
            <div class="w-full md:w-2/3">
                <h2 class="text-lg md:text-2xl font-bold">#{{order.name}}</h2>
                 <div class="mt-4">
                    <ul class="flex flex-col">
                        <li class="flex mb-12">
                            <div class="w-40"></div>
                            <div class="w-96 text-xs md:text-sm font-bold">Product details</div>
                            <div class="w-24 text-xs md:text-sm font-bold">Quantity</div>
                            <div class="w-24 text-xs md:text-sm font-bold">Price</div>
                            <div class="w-24 text-xs md:text-sm font-bold">Total</div>
                        </li>
                        <li class="flex mb-4 pb-4 border-gray-200 border-b" v-for="item in order.line_items" :key="item.id">
                            <div class="w-40 h-40">
                                <img
                                    v-if="item.image" 
                                    :src="item.image" 
                                    class="w-full h-full object-center object-cover lg:w-full lg:h-full" alt="">
                                <img
                                        v-else
                                        src="../../../../public/images/product_placeholder.png"
                                        alt="Product image placeholder"
                                        class="w-full h-full object-center object-cover lg:w-full lg:h-full"
                                    />
                            </div>
                            <div class="w-96 flex flex-col">
                                <p class="text-xs md:text-sm">{{item.name}}</p>
                                <p class="text-gray-400 text-xs md:text-sm">{{item.sku}}</p>
                            </div>
                            <p class="w-24 text-xs md:text-sm text-center">{{item.quantity}}</p>
                            <p class="w-24 text-xs md:text-sm text-center">{{item.price}}</p>
                            <p class="w-24 text-xs md:text-sm text-center">{{parseFloat(item.quantity * item.price).toFixed(2)}}</p>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="w-full md:w-1/3">
                <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                    <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <tbody class="bg-white divide-y divide-gray-200">
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            Email
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ order.email }}
                            </td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            Customer
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ order.customer ? (order.customer.first_name && order.customer.last_name ? `${order.customer.first_name} ${order.customer.last_name}` : 'N/A') : 'Guest' }}
                            </td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            Order Date
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ new Date(order.created_at).toDateString() }}
                            </td>
                            </tr>
                            <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            Status
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ order.financial_status.toUpperCase() }}
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    </div>
                </div>

                <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                    <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <tbody class="bg-white divide-y divide-gray-200">
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            Subtotal
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ order.subtotal_price }}
                            </td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            Shipping
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ order.total_shipping_price_set ? order.total_shipping_price_set.presentment_money.amount : 0.00 }}
                            </td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            Tax
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ order.total_tax }}
                            </td>
                            </tr>
                            <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            Total
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ order.total_price }}
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="my-12">
            <HomeLink />
        </div>
        
        <Footer :order="order" />
    </div>
</template>

<style scoped>
   
</style>

<script>
import OrderItem from '@/Components/OrderItem.vue';
import OrderSummary from '@/Components/OrderSummary.vue';
import Footer from '@/Components/Footer.vue';
import HomeLink from '@/Components/HomeLink.vue';
import AccountMenu from '@/Components/AccountMenu.vue';
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
