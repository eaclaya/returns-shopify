<template>
    <div v-if="isLoading" class="w-full h-full fixed block top-0 left-0 bg-white opacity-75 z-999 full-page-loader">
        <span class="text-black top-1/2 my-0 mx-auto block relative w-0 h-0" style="
            top: 50%;
        ">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 animate animate-spin" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
        </span>
    </div>
</template>
<script>
export default {
    props: {
        isLoading: {
            type: Boolean
        }
    },
}
</script>