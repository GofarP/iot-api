<template>
    <div class="p-4">
        <p class="text-red-400 ml-3 mt-5 text-3xl text-center">Employees Data</p>
        <div class="overflow-x-auto ml-3 mr-3">
            <div class="text-right mt-3 mr-5">
                <input type="text" v-model="searchTerm" @input="debouncedSearch" placeholder="Search..."
                    class="border bg-white border-gray-300 rounded py-2 px-4 mb-4" />
            </div>
            <div class="text-right mb-4 mr-5">
                <button @click="openModal()"
                    class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded mb-4">
                    Add Employee
                </button>
            </div>

            <table class="min-w-full border-collapse border border-gray-200 bg-white">
                <thead>
                    <tr class="text-center">
                        <th class="border border-gray-300 px-4 py-2 bg-gray-100">No</th>
                        <th class="border border-gray-300 px-4 py-2 bg-gray-100">Nama</th>
                        <th class="border border-gray-300 px-4 py-2 bg-gray-100">UID</th>
                        <th class="border border-gray-300 px-4 py-2 bg-gray-100">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(employee, index) in employees.data" :key="employee.id"
                        class="hover:bg-gray-50 text-center">
                        <td class="px-4 py-2">{{ (employees.current_page - 1) * employees.per_page + index + 1 }}</td>
                        <td class="px-4 py-2">{{ employee.name }}</td>
                        <td class="px-4 py-2">{{ employee.uid }}</td>
                        <td class="px-4 py-2">
                            <button
                                class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-1 px-2 rounded mr-2"
                                @click="openModal(employee)">
                                Edit
                            </button>
                            <button class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 rounded"
                                @click="deleteEmployee(employee.id)">
                                Delete
                            </button>
                        </td>
                    </tr>
                    <tr v-if="employees.data.length === 0">
                        <td colspan="4" class="text-center py-4">No employees found.</td>
                    </tr>
                </tbody>
            </table>
            <TailwindPagination class="text-end mt-3" :data="employees" @pagination-change-page="fetchEmployees"
                :limit="1" :keep-length="true"
                :item-classes="['bg-white', 'text-gray-700', 'border', 'border-gray-300', 'px-2', 'py-1', 'text-sm']"
                :active-classes="['bg-blue-500', 'text-white']"
                :prev-button-class="['bg-gray-200', 'text-gray-700', 'px-3', 'py-1', 'rounded', 'text-sm']"
                :next-button-class="['bg-gray-200', 'text-gray-700', 'px-3', 'py-1', 'rounded', 'text-sm']"
                prev-label="Prev" next-label="Next" />

        </div>

        <!-- Modal -->
        <div v-if="showModalFlag" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-30">
            <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6 relative">
                <button @click="closeModal"
                    class="absolute top-2 right-2 text-gray-500 hover:text-gray-800 text-xl">&times;</button>
                <h2 class="text-xl font-bold mb-4">
                    {{ editMode ? 'Edit Employee' : 'Add New Employee' }}
                </h2>
                <form @submit.prevent="editMode ? updateEmployee() : addEmployee()">
                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2">Name</label>
                        <input type="text" v-model="form.name" required
                            class="w-full border border-gray-300 rounded px-4 py-2" />
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2">UID</label>
                        <input type="number" v-model="form.uid" required
                            class="w-full border border-gray-300 rounded px-4 py-2" />
                    </div>
                    <div class="text-right">
                        <button type="button" @click="closeModal"
                            class="bg-gray-300 text-gray-800 px-4 py-2 rounded mr-2">
                            Cancel
                        </button>
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">
                            {{ editMode ? 'Update' : 'Save' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { TailwindPagination } from 'laravel-vue-pagination'

const employees = ref({
    data: [],
    current_page: 1,
    last_page: 1,
    per_page: 10,
    total: 0,
})

const currentPage = ref(1)
const searchTerm = ref('')
let searchTimeout = null

const showModalFlag = ref(false)
const editMode = ref(false)

const form = ref({
    id: null,
    name: '',
    uid: '',
})

// Pagination button styles (buat tombol kecil & pendek)


// Ambil data karyawan dari API dengan pagination dan search
const fetchEmployees = async (page = 1) => {
    try {
        const url = `http://192.168.5.250:8000/api/employee?page=${page}&search=${encodeURIComponent(searchTerm.value)}`
        const res = await axios.get(url)
        employees.value = res.data
        currentPage.value = page
    } catch (error) {
        console.error('Error fetching employees:', error)
        employees.value = {
            data: [],
            current_page: 1,
            last_page: 1,
            per_page: 10,
            total: 0,
        }
    }
}

const debouncedSearch = () => {
    clearTimeout(searchTimeout)
    searchTimeout = setTimeout(() => {
        fetchEmployees(1)
    }, 300)
}

const openModal = (employee = null) => {
    if (employee) {
        editMode.value = true
        form.value = { ...employee }
    } else {
        editMode.value = false
        form.value = { id: null, name: '', uid: '' }
    }
    showModalFlag.value = true
}

const closeModal = () => {
    showModalFlag.value = false
    editMode.value = false
    form.value = { id: null, name: '', uid: '' }
}

const addEmployee = async () => {
    try {
        await axios.post('http://192.168.5.250:8000/api/employee', form.value)
        alert('Employee added successfully')
        closeModal()
        fetchEmployees(currentPage.value)
    } catch (error) {
        alert('Failed to add employee')
        console.error(error)
    }
}

const updateEmployee = async () => {
    try {
        await axios.put(`http://192.168.5.250:8000/api/employee/${form.value.id}`, form.value)
        alert('Employee updated successfully')
        closeModal()
        fetchEmployees(currentPage.value)
    } catch (error) {
        alert('Failed to update employee')
        console.error(error)
    }
}

const deleteEmployee = async (id) => {
    if (confirm('Are you sure you want to delete this employee?')) {
        try {
            await axios.delete(`http://192.168.5.250:8000/api/employee/${id}`)
            alert('Employee deleted successfully')
            fetchEmployees(currentPage.value)
        } catch (error) {
            alert('Failed to delete employee')
            console.error(error)
        }
    }
}

onMounted(() => fetchEmployees(currentPage.value))
</script>


<style></style>
