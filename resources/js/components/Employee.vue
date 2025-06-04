<template>
    <p class="text-red-400 ml-3 mt-5 text-3xl text-center">Employees Data</p>
    <div class="overflow-x-auto ml-3 mr-3">


        <div class="text-right  mt-3 mr-5">
            <input type="text" placeholder="Search..." class="border bg-white border-gray-300 rounded py-2 px-4 mb-4">

        </div>
        <div class="text-right mb-4 mr-5">
            <button @click="showModal"
                class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded mb-4 ">
                Add Employee
            </button>
        </div>

        <table class="min-w-full border-collapse border border-gray-200 bg-white ">
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
                    <td class=" px-4 py-2 "> {{ index + 1 }}</td>
                    <td class=" px-4 py-2"> {{ employee.name }}</td>
                    <td class=" px-4 py-2"> {{ employee.uid }}</td>
                    <td class="px-4 py-2">
                        <button
                            class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-1 px-2 rounded mr-2" @click="editEmployee(index)">Edit</button>
                        <button
                            class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 rounded" @click="deleteEmployee(employee.id)">Delete</button>

                    </td>
                </tr>
            </tbody>

        </table>
    </div>
    <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6 relative">
            <button @click="closeModal"
                class="absolute top-2 right-2 text-gray-500 hover:text-gray-800 text-xl">&times;</button>

            <h2 class="text-xl font-bold mb-4">{{isEdit ? 'Edit Employee' : 'Add New Employee'}}</h2>
            <form @submit.prevent="isEdit ? updateEmployee() : submitForm()">
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">Name</label>
                    <input type="text" v-model="newEmployees.name" name="name" id="name"
                        class="w-full border border-gray-300 rounded px-4 py-2 required">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">UID</label>
                    <input type="text" v-model=" newEmployees.uid" name="uid" id="uid"
                        class="w-full border border-gray-300 rounded px-4 py-2 required">
                </div>
                <div class="text-right">
                    <button @click="closeModal" type="button"
                        class="bg-gray-300 text-gray-800 px-4 py-2 rounded mr-2">Cancel</button>
                    <button type="submit"
                        class="bg-blue-600 text-white px-4 py-2 rounded">{{ isEdit == true ?  'Update' : 'Save' }}</button>

                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const employees = ref([]);
let newEmployees = ref({
    name: '',
    uid: '',
    id:'',
});


const id=ref(0);
const show = ref(false);

const isEdit=ref(false);

const fetchEmployee = async () => {
    try {
        const response = await axios.get('http://localhost:8000/api/employee');
        if (response.status === 200) {
            employees.value = response.data.data;
            console.log(employees.value)
        }
        else if (response.status == 404) {
            console.log("Not Found");
        }
    } catch (error) {
        console.error('API error:', error);
    }
};



const showModal = () => {
    show.value = true;
}

const closeModal = () => {
    show.value = false;
    isEdit.value=false;
    newEmployees = { name: '', uid: '' ,id:""} // reset form
}

const submitForm = async () => {
    try {
        // const response=await axios.post('http://localhost:8000/api/employee', newEmployee.value')
        const response = await axios.post('http://localhost:8000/api/employee', newEmployees.value);
        if (response.status == 200 || response.status == 201) {
            fetchEmployee();
            closeModal();
        }
        else {
            alert("Error When Inserting Data");
        }
    } catch (error) {
        console.error('Submit Error:', error)
    }
}

const editEmployee=async(index)=>{
    isEdit.value=true;
    show.value=true;
    id.value=employees.value[index].id;

    newEmployees={name:employees.value[index].name, uid:employees.value[index].uid, id:employees.value[index].id}


}

const updateEmployee=async()=>{
    try{
        const response = await axios.put(`http://localhost:8000/api/employee/${id.value}`, newEmployees.value);
        if(response.status==200){
            fetchEmployee();
            closeModal();
        }else{
            alert("Error When Updating Data");
        }
    }
    catch(error){
        console.error('Submit Error:',error)
    }
}

const deleteEmployee = async (id) => {

    if(confirm("Are you sure you want to delete this employee?")){
        try{
            const response=await axios.delete(`http://localhost:8000/api/employee/${id}`);
            if(response.status==200){
                fetchEmployee();
            }else{
                alert("Error Deleteting Employee");
            }
        }
        catch(error){
            console.log('Delete error:',error);
        }
    }
}

onMounted(async () => {
    console.log("mounted");
    fetchEmployee();
});

</script>

<style></style>
