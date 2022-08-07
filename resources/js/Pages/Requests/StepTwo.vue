<template>
    <div class="wrapper min-h-90vh">
    <FullPageLoader :isLoading="isLoading" />
    <div class="modal-header flex flex-col p-4">
        <button class="absolute right-4 top-4" @click="closePage">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
        <button class="absolute left-4 top-4" @click="goBack">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
        </button>
        <div class="header-title">
            <h2 class="text-center font-bold text-lg md:text-2xl">Choose your new product</h2>
        </div>
    </div>
    <div class="flex flex-col items-center justify-center px-4">
        <div class="w-full pt-6">
            <div class="relative mb-12">
                <ul class="fixed top-16 left-4 overflow-scroll max-h-90vh w-64 shadow-lg text-center" name="product_type" id="product_type" v-if="collections.length > 0">
                    <li v-for="collection in collections" @click="filterProducts(collection)"  :key="collection.id" class="px-2 py-4  border-b border-gray-100 cursor-pointer" :class="collection.selected ? 'bg-gray-200' : ''">{{collection.title}}</li>
                </ul>
                <p v-else>No collections available for exchange</p>
            </div>
            <div v-if="productListFiltered.length > 0" class="w-full pl-64 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3  gap-2 mx-auto">
                    <Product class="p-2 hover:bg-gray-100 cursor-pointer" v-for="product in productListFiltered" :item="product" :order="order" :key="product.sku" @click="selectProduct(product)"></Product>
            </div>
            <div v-else>
                <h2 class="text-center font-semibold text-2xl">No products available for exchange</h2>
            </div>
        </div>
    </div>
    <Toast  @close="errorMessage = ''" :error="errorMessage" />
    </div>
</template>
<script>
import { ref } from 'vue'
import {debounce} from '@/Helpers/debounce'
import { Inertia } from '@inertiajs/inertia'
import Product from "@/Components/Product.vue"
import Toast from "@/Components/Toast.vue"
import { usePage } from '@inertiajs/inertia-vue3'
import FullPageLoader from "@/Components/FullPageLoader.vue"
export default {
    props: ['productTypes', 'collections'],
    components: {
        Product,
        Toast,
        FullPageLoader
    },
    setup(props){
        const productTypeSelected = ref('')
        const collectionSelected = ref('')
        const productTitle = ref('')
        const isLoading = ref(false)
        const productList = ref([])
        const productListFiltered = ref([])
        const order = JSON.parse(localStorage.getItem('order'))
        if(order.shipping_not_valid_for_exchange || order.tags?.indexOf('Exchange') >= 0){ 
            Inertia.get(`/${usePage().props.value.prefix}/orders/review`); 
        }
        const errorMessage = ref('')
        const closePage = () => {
            Inertia.get(`/${usePage().props.value.prefix}/orders/review`)
        }

        const goBack = () =>{
            Inertia.get(`/${usePage().props.value.prefix}/requests/step/1`)
        }

        const searchProducts = debounce(async() => {
            let title = productTitle.value
            const product_type = productTypeSelected.value
            if(!product_type.trim()){
                return alert('Please select a product type');
            }
            if(!title.trim()){
                productListFiltered.value = productList.value
                return;
            }
            title = title.toLowerCase().trim()
            productListFiltered.value = []
            productListFiltered.value = productList.value.filter((item) => item.title.toLowerCase().indexOf(title) >= 0)
            
        }, 500)

        const filterProducts = async(collection) => {
            props.collections.map(_item => _item.selected = false)
            collection.selected = true
            productList.value = []
            let response = await axios.post(`/${usePage().props.value.prefix}/products/filter`, {collection_id: collection.collection_id})
            if(response.data){
                productList.value = response.data
                productListFiltered.value = response.data
            }
        }

        const selectProduct = async(product) => {
            try{
                isLoading.value = true
                if(product.disabled){ return }
                productList.value = []
                productTitle.value = product.title
                let currentItem = await JSON.parse(localStorage.getItem('current_item'))
                currentItem.product = product
                currentItem.is_new = true
                currentItem.name = product.title
                await localStorage.setItem('current_item', JSON.stringify(currentItem))
                let _response = await axios.post(`/api/${usePage().props.value.prefix}/v1/products/handle`, {handles: currentItem.product.handle, ids: currentItem.product.parent_id})
                if(_response.data && _response.data.error){
                    isLoading.value = false
                    return  errorMessage.value = _response.data.msg
                }
                let response = await axios.post(`/${usePage().props.value.prefix}/products/get`, {skus: [currentItem.product.sku], ids: [currentItem.product.parent_id]})
                if(response.data && response.data.error){
                    isLoading.value = false
                    return  errorMessage.value = response.data.msg
                }
                const data = response.data;
                await localStorage.setItem('products', JSON.stringify(data.response));
                isLoading.value = false
                Inertia.get(`/${usePage().props.value.prefix}/requests/step/1`)
            }catch(err){
                isLoading.value = false
                return  errorMessage.value = 'Something went wrong. Please reload the page and try again.'
                
            }
        }
        const selectItem = async() => {
            if(isLoading.value){ return }
            isLoading.value = true
            let currentItem = JSON.parse(localStorage.getItem('current_item'))
            const skus = [currentItem.product.sku]
            let response = await axios.post(`/${usePage().props.value.prefix}/products/get`, {skus: skus})
            const data = response.data;
            isLoading.value = false
            if(data.error){
                    return alert('Something went wrong. Please reload and try again');
            }
            localStorage.setItem('products', JSON.stringify(data.response));
            Inertia.get(`/${usePage().props.value.prefix}/requests/step/1`)
        }

        if(props.collections && props.collections[0]){
            props.collections[0].selected = true
            filterProducts(props.collections[0])
        }
        return {
            productTypeSelected,
            collectionSelected,
            productTitle,
            searchProducts,
            filterProducts,
            productList,
            productListFiltered,
            selectProduct,
            selectItem,
            isLoading,
            closePage,
            goBack,
            order,
            errorMessage
        }
    }
      
}
</script>

