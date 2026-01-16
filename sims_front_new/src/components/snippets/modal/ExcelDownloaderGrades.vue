<script setup>
import { ref, onMounted, computed } from 'vue';
// import {  
//           getAllPayments
//        } from "../../Fetchers.js";
import Loader from '../loaders/Loading1.vue';

const props = defineProps({
  gradingsheetdata: {
  },
})
const gradingSheet = computed(() => {
  return props.gradingsheetdata
});

onMounted(() => {


})


const downloadExcel = () => {
  let table = document.getElementById('main-table')
  const wb = XLSX.utils.table_to_book(table, { sheet: 'sheet-1' });
  /* Export to file (start a download) */
  XLSX.writeFile(wb, 'MyTable.xlsx');
}



</script>

<template>
  <div class="table-responsive small-font overflow-auto border p-2 rounded-2 shadow" style="height:40rem;">
    <table class="table table-hover" id="main-table">
      <thead>
        <tr>
          <th class="fw-bold p-3 bg-secondary-subtle">Student ID</th>
          <th class="fw-bold p-3 bg-secondary-subtle">Student Type</th>
          <th class="fw-bold p-3 bg-secondary-subtle">Last Name</th>
          <th class="fw-bold p-3 bg-secondary-subtle">First Name</th>
          <th class="fw-bold p-3 bg-secondary-subtle">Middle Name</th>
          <th class="fw-bold p-3 bg-secondary-subtle">Suffix Name</th>
          <th class="fw-bold p-3 bg-secondary-subtle">Preliminary</th>
          <th class="fw-bold p-3 bg-secondary-subtle">Midterms</th>
          <th class="fw-bold p-3 bg-secondary-subtle">Pre-finals</th>
          <th class="fw-bold p-3 bg-secondary-subtle">Finals</th>
        </tr>
      </thead>
      <tbody>
        <!-- index-1 dahil yung index na galing sa section ay nag start sa 1 -->
        <tr v-for="(app, index) in gradingSheet" class="hover:bg-mid-gray cursor-pointer">
          <td class="align-middle p-3">
              {{ app.grad_dtypeid == 2 ? app.ident_identification : app.enr_lrn }}
          </td>
          <td class="align-middle p-3">
              {{ app.grad_dtypeid == 2 ? 'College' : 'SHS' }}
          </td>
          <td class="align-middle" p-3>
              {{ app.per_lastname ? app.per_lastname : '' }}
          </td>
          <td class="align-middle" p-3>
              {{ app.per_firstname ? app.per_firstname : '' }}
          </td>
          <td class="align-middle" p-3>
              {{ app.per_middlename ? app.per_middlename : 'N/A' }}
          </td>
          <td class="align-middle" p-3>
              {{ app.per_suffixname ? app.per_suffixname : 'N/A' }}
          </td>
          <td class="align-middle p-3">
            {{ app.grs_prelims ? app.grs_prelims : 'N/A' }}
          </td>
          <td class="align-middle p-3">
            {{ app.grs_midterms ? app.grs_midterms : 'N/A' }}
          </td>
          <td class="align-middle p-3">
            {{ app.grs_prefinals ? app.grs_prefinals : 'N/A' }}
          </td>
          <td class="align-middle p-3">
            {{ app.grs_finals ? app.grs_finals : 'N/A' }}
          </td>
        </tr>
        <tr v-if="!Object.keys(gradingSheet).length">
          <td class="p-3 text-center border border-mid-gray" colspan="2">
            No Records Found
          </td>
        </tr>
      </tbody>
    </table>
  </div>
  <div class="d-flex justify-content-end mt-2 ">
    <button @click="downloadExcel(index - 1)" type="button"
      class="btn btn-sm btn-primary">
      <i class="mr-2 fa-solid fa-file-excel"></i>Download Excel File
    </button>
  </div>
</template>