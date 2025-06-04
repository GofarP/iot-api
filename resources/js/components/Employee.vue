<template>
    <p class="text-red-400 ml-3 mt-5 text-3xl text-center">Employees Data</p>
    <div class="overflow-x-auto ml-3 mr-3">


        <div class="text-right  mt-3 mr-5">
            <input type="text" placeholder="Search..." class="border bg-white border-gray-300 rounded py-2 px-4 mb-4">

        </div>
        <div class="text-right mb-4 mr-5">
            <button class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded mb-4 ">
                Add Employee
            </button>
        </div>

        <table class="min-w-full border-collapse border border-gray-200 bg-white">
            <thead>
                <tr>
                    <th class="border border-gray-300 px-4 py-2 bg-gray-100 text-left">No</th>
                    <th class="border border-gray-300 px-4 py-2 bg-gray-100 text-left">Nama</th>
                    <th class="border border-gray-300 px-4 py-2 bg-gray-100 text-left">UID</th>
                    <th class="border border-gray-300 px-4 py-2 bg-gray-100 text-left">Action</th>
                </tr>
            <tbody>
                <tr class="hover:bg-gray-50">
                    <td class="border border-gray px-4 py-2">1</td>
                    <td class="border border-gray-300 px-4 py-2">John Doe</td>
                    <td class="border border-gray-300 px-4 py-2">007</td>
                    <td class="border border-gray-300 px-4 py-2">
                        <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded mr-2">
                            Edit
                        </button>
                        <button class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded">
                            Delete
                        </button>
                    </td>
                </tr>
            </tbody>
            </thead>
        </table>
    </div>
</template>

<script>
import { onMounted } from 'vue'

import axios from 'axios';

export default {
    data() {
        return {
            employees: [],
        }
    },
    mounted() {
        console.log('Component mounted');
        try {
            axios.get('http://localhost:8000/api/employee')
                .then(response => {

                    if(response.status==200){

                    }
                    else if(response.status==404)
                    console.log('API response Status:', response.status);

                    console.log('API Data:', response.data.data);
                })
                .catch(error => {
                    console.error('API error:', error);
                });
        } catch (e) {
            console.error('Catch error:', e);
        }
    },

    methods: {

    }
}
</script>

<style></style>
