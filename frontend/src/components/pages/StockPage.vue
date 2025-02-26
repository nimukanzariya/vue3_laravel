<template>
    <div class="row">
        <div class="col-lg-12">
            <section class="card">
                <div class="mb-2">
                    <a href="/create-stock" class="btn btn-info me-2">Add New Stock</a>
                    <a href="/bulk-stock" class="btn btn-info me-2">Bulk Stock</a>

                </div>

                <div ref="tableRef"></div> <!-- Tabulator table container -->
            </section>
        </div>
    </div>
</template>

<script>
import { ref, onMounted, nextTick } from "vue";
import { TabulatorFull } from "tabulator-tables";
import "tabulator-tables/dist/css/tabulator.min.css";
// import axios from "axios";

export default {
    setup() {
        const tableRef = ref(null);
        const tableInstance = ref(null);
        // const page = ref(1);
        // const paginationSize = ref(10);
        // const paginationSizeSelector = [10, 20, 30];
        const token = JSON.parse(localStorage.getItem("active-user"))?.access_token;

        onMounted(() => {
            nextTick(() => {
                if (tableRef.value) {
                    // tableInstance.value = new TabulatorFull(tableRef.value, {
                    //     height: "auto",
                    //     layout: "fitColumns",
                    //     pagination: "remote", // ✅ Enable server-side pagination
                    //     paginationSize: paginationSize.value,
                    //     paginationSizeSelector: paginationSizeSelector,
                    //     placeholder: "No Data Found",
                    // ajaxSorting: true,
                    // ajaxFiltering: true,
                    // ajaxURL: "http://127.0.0.1:8000/api/stocks",
                    // ajaxConfig: {
                    //     method: "GET",
                    //     headers: {
                    //         "Authorization": `Bearer ${token}`,
                    //         "Accept": "application/json",
                    //     }
                    // },
                    // paginationCounter: "rows",
                    // paginationInitialPage: 2,
                    // ajaxParams: function () {
                    //     return {
                    //         page: page.value,
                    //         size: paginationSize.value,
                    //         sort: 'id',
                    //         order: 'DESC',
                    //     };
                    // },

                    // ajaxRequestFunc: async (url, config, params) => {
                    //     console.log("🔹 API Call Params:", params);

                    //     try {
                    //         const token = JSON.parse(localStorage.getItem("active-user"))?.access_token;

                    //         const response = await axios.get(url, {
                    //             headers: {
                    //                 Authorization: `Bearer ${token}`,
                    //                 "Content-Type": "application/json",
                    //             },
                    //             params,
                    //         });
                    //         return response.data.data;
                    //         // return { last_page: response.data.last_page, total: response.data.total, data: response.data.data };
                    //     } catch (error) {
                    //         return { last_page: 1, data: [] };
                    //     }
                    // },
                    // paginationDataReceived: {
                    //     last_page: "last_page",
                    //     current_page: "current_page",
                    //     total: "total",
                    // },
                    // dataReceiveParams: {
                    //     "last_page": "last_page",
                    //     "data": "data.data",
                    // },

                    //     columns: [
                    // { title: "ID", field: "id", width: 100 },
                    // { title: "Name", field: "name" },
                    // { title: "Price", field: "price" },
                    // { title: "Quantity", field: "quantity" },
                    // { title: "Store", field: "store" }
                    //     ]
                    // });
                    const API_URL = "http://127.0.0.1:8000/api/stocks";
                    tableInstance.value = new TabulatorFull(tableRef.value, {
                        ajaxURL: API_URL,
                        ajaxConfig: {
                            method: "GET",
                            headers: {
                                "Authorization": `Bearer ${token}`,
                                "Accept": "application/json",
                            }
                        },
                        ajaxParams: { page: 1, per_page: 10 },
                        ajaxResponse: function (url, params, response) {
                            return response.data; // Extract the array from the "data" key
                        },
                        layout: "fitColumns",
                        pagination: "remote",
                        paginationSize: 10,
                        paginationInitialPage: 1,
                        dataReceiveParams: { // Ensure Tabulator correctly reads total pages & records
                            "last_page": "last_page", // The total number of pages
                            "total": "total" // The total number of records
                        },
                        columns: [
                            { title: "ID", field: "id", width: 100 },
                            { title: "Name", field: "name" },
                            { title: "Price", field: "price" },
                            { title: "Quantity", field: "quantity" },
                            { title: "Store", field: "store" }
                        ]
                    });
                } else {
                    console.error("Tabulator failed to initialize: tableRef is null");
                }
            });
        });

        return { tableRef, tableInstance };
    }
};
</script>
