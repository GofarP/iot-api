<template>
  <p class="text-red-400 ml-3 mt-5 text-3xl text-center">Employees Data</p>
  <div class="overflow-x-auto ml-3 mr-3">
    <div class="text-right mt-3 mr-5">
      <input
        type="text"
        v-model="searchTerm"
        @input="debouncedSearch"
        placeholder="Search..."
        class="border bg-white border-gray-300 rounded py-2 px-4 mb-4"
      />
    </div>
    <div class="text-right mb-4 mr-5">
      <button @click="openModal()" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded mb-4">
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
        <tr v-for="(employee, index) in employees" :key="employee.id" class="hover:bg-gray-50 text-center">
          <td class="px-4 py-2">{{ index + 1  }}</td>
          <td class="px-4 py-2">{{ employee.name }}</td>
          <td class="px-4 py-2">{{ employee.uid }}</td>
          <td class="px-4 py-2">
            <button
              class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-1 px-2 rounded mr-2"
              @click="openModal(employee)"
            >
              Edit
            </button>
            <button
              class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 rounded"
              @click="deleteEmployee(employee.id)"
            >
              Delete
            </button>
          </td>
        </tr>
        <tr v-if="employees.length === 0">
          <td colspan="4" class="text-center py-4">No employees found.</td>
        </tr>
      </tbody>
    </table>

    <!-- Simple Pagination -->
    <div class="flex justify-end mt-4 space-x-2 mb-5">
      <button
        :disabled="currentPage === 1"
        @click="changePage(currentPage - 1)"
        class="px-3 py-1 border rounded bg-white hover:bg-gray-100"
      >
        Prev
      </button>

      <button
        v-for="page in totalPages"
        :key="page"
        @click="changePage(page)"
        :class="[
          'px-3 py-1 border rounded',
          page === currentPage ? 'bg-blue-500 text-white' : 'bg-white hover:bg-gray-100'
        ]"
      >
        {{ page }}
      </button>

      <button
        :disabled="currentPage === totalPages"
        @click="changePage(currentPage + 1)"
        class="px-3 py-1 border rounded bg-white hover:bg-gray-100"
      >
        Next
      </button>
    </div>
  </div>

  <!-- Modal -->
  <div v-if="showModalFlag" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-30">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6 relative">
      <button @click="closeModal" class="absolute top-2 right-2 text-gray-500 hover:text-gray-800 text-xl">&times;</button>
      <h2 class="text-xl font-bold mb-4">{{ editMode ? 'Edit Employee' : 'Add New Employee' }}</h2>
      <form @submit.prevent="editMode ? updateEmployee() : addEmployee()">
        <div class="mb-4">
          <label class="block text-gray-700 mb-2">Name</label>
          <input
            type="text"
            v-model="form.name"
            required
            class="w-full border border-gray-300 rounded px-4 py-2"
          />
        </div>
        <div class="mb-4">
          <label class="block text-gray-700 mb-2">UID</label>
          <input
            type="number"
            v-model="form.uid"
            required
            class="w-full border border-gray-300 rounded px-4 py-2"
          />
        </div>
        <div class="text-right">
          <button type="button" @click="closeModal" class="bg-gray-300 text-gray-800 px-4 py-2 rounded mr-2">
            Cancel
          </button>
          <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">
            {{ editMode ? 'Update' : 'Save' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue';
import axios from 'axios';

const employees = ref([]);
const currentPage = ref(1);
const totalPages = ref(1);
const perPage = 10;

const searchTerm = ref('');
let searchTimeout = null;

const showModalFlag = ref(false);
const editMode = ref(false);

const form = ref({
  id: null,
  name: '',
  uid: ''
});

// Fetch employees with pagination and search
const fetchEmployees = async () => {
  try {
    const url = `http://192.168.5.250:8000/api/employee?page=${currentPage.value}&search=${encodeURIComponent(searchTerm.value)}`;
    const res = await axios.get(url);
    employees.value = res.data.data;
    totalPages.value = res.data.last_page || 1;
  } catch (error) {
    console.error('Error fetching employees:', error);
    employees.value = [];
    totalPages.value = 1;
  }
};

const changePage = (page) => {
  if (page >= 1 && page <= totalPages.value) {
    currentPage.value = page;
    fetchEmployees();
  }
};

const debouncedSearch = () => {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    currentPage.value = 1;
    fetchEmployees();
  }, 300);
};

const openModal = (employee = null) => {
  if (employee) {
    // Edit mode
    editMode.value = true;
    form.value = { ...employee };
  } else {
    // Add mode
    editMode.value = false;
    form.value = { id: null, name: '', uid: '' };
  }
  showModalFlag.value = true;
};

const closeModal = () => {
  showModalFlag.value = false;
  editMode.value = false;
  form.value = { id: null, name: '', uid: '' };
};

const addEmployee = async () => {
  try {
    await axios.post('http://192.168.5.250:8000/api/employee', form.value);
    alert('Employee added successfully');
    closeModal();
    fetchEmployees();
  } catch (error) {
    alert('Failed to add employee');
    console.error(error);
  }
};

const updateEmployee = async () => {
  try {
    await axios.put(`http://192.168.5.250:8000/api/employee/${form.value.id}`, form.value);
    alert('Employee updated successfully');
    closeModal();
    fetchEmployees();
  } catch (error) {
    alert('Failed to update employee');
    console.error(error);
  }
};

const deleteEmployee = async (id) => {
  if (confirm('Are you sure you want to delete this employee?')) {
    try {
      await axios.delete(`http://192.168.5.250:8000/api/employee/${id}`);
      alert('Employee deleted successfully');
      fetchEmployees();
    } catch (error) {
      alert('Failed to delete employee');
      console.error(error);
    }
  }
};

onMounted(fetchEmployees);
</script>

<style scoped>
/* Add any styles you want here */
</style>
