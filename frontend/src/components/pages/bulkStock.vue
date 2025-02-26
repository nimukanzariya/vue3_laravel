<template>
    <div class="row">
        <div class="col-md-12">
            <section class="card">
                <header class="card-header">

                    <h2 class="card-title">Create bulk Stock</h2>
                </header>
                <div class="card-body">
                    <div class="mb-3">
                        <button class="btn btn-info me-2" data-card-toggle="" @click="addRow"> Add Row</button>
                        <button class="btn btn-info" data-card-dismiss="" @click="submitData">Submit Data</button>

                    </div>

                    <p v-if="error" style="color: red">{{ error }}</p>
                    <ag-grid-vue style="width: 100%; height: 500px;" :stores="fetchStores" class="ag-theme-alpine"
                        :columnDefs="columnDefs" :defaultColDef="defaultColDef" :rowData="rowData"
                        rowSelection="multiple" @grid-ready="onGridReady" />
                </div>
            </section>
        </div>
    </div>
</template>
<script setup>
import { onBeforeMount, ref, shallowRef } from "vue";
import { AgGridVue } from "ag-grid-vue3";
import {
    ClientSideRowModelModule,
    ModuleRegistry,
    NumberEditorModule,
    TextEditorModule,
    ValidationModule,
    SelectEditorModule,
} from "ag-grid-community";
import axios from "axios";
import { useRouter } from "vue-router";

ModuleRegistry.registerModules([
    NumberEditorModule,
    TextEditorModule,
    ClientSideRowModelModule,
    ValidationModule,
    SelectEditorModule,
]);
const token = JSON.parse(localStorage.getItem("active-user"))?.access_token;
const error = ref(null);
const gridApi = shallowRef(null);
const rowData = ref([]);
const router = useRouter();
const stores = ref([]);
onBeforeMount(async () => {
    try {
        await axios.get("http://127.0.0.1:8000/api/stores", {
            headers: {
                Authorization: `Bearer ${token}`,
                "Content-Type": "application/json",
            }
        }).then(response => {
            stores.value = response.data.data;
        });
    } catch (err) {
        console.error("Error fetching stores:", err);
    }

   console.log( stores.value);
});

// Column definitions
const columnDefs = ref([
    { field: "name", editable: true },
    { field: "quantity", editable: true },
    { field: "price", editable: true },
    {
        field: "store", cellEditor: "agSelectCellEditor",
        cellEditorParams: () => ({
            values: stores.value, 
        }),
    },
]);

// Default column properties
const defaultColDef = ref({
    resizable: true,
    sortable: true,
    filter: true,
    editable: true,
});

// Row data (initialized as an empty array)

const addRow = () => {
    rowData.value = [...rowData.value, { name: "", quantity: 0, price: 0, store: "" }];

};


const onGridReady = (params) => {
    gridApi.value = params.api;
    // fetchStores();
};
const submitData = () => {
    if (!gridApi.value) {
        console.error("Grid API is not available yet.");
        return;
    }

    // Get all rows from AG Grid
    const rowDataArray = [];
    gridApi.value.forEachNode((node) => {
        rowDataArray.push(node.data);
    });

    // console.log("Submitted Data:", rowDataArray);

    if (rowDataArray.length === 0) {
        alert("No data to submit!");
        return;
    }

    submitBulkData(rowDataArray);




    // Perform your submission (e.g., send to backend)
    // Example: axios.post("/submit", rowDataArray)
};

const submitBulkData = async (data) => {
    error.value = null;
    try {
        await axios.post("http://127.0.0.1:8000/api/bulk-upload", {
            data: data,
        }, {
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json",
                Authorization: `Bearer ${token}`,
            },
        }).then(response => {
            error.value = null;
            if (response.data.status && response.data.status === "error") {
                error.value = response.data.message;
            } else {
                router.push("/");
            }
        }).catch(error => {
            error.value = null;
            error.value = error;
        });

    } catch (error) {
        console.error("Error uploading bulk data:", error.response ? error.response.data : error.message);
    }
};

</script>