<script setup>
import { ref, onMounted, computed } from 'vue';
import {
  getAllPayments,
  getAccountsFee,
  getAcademicDefaults

} from "../../Fetchers.js";
import NeuLoader2 from '../loaders/NeuLoader2.vue';
import ExcelJS from "exceljs";
import { saveAs } from "file-saver";
// import Loader from '../loaders/NeuLoader2.vue';

const props = defineProps({
  datefrom: {
  },
  dateto: {
  },
  cashierid: {
  },
  cashiername: {
  }
})

const cashierId = computed(() => {
  return props.cashierid
});
const cashierName = computed(() => {
  return props.cashiername
});
const dateFrom = computed(() => {
  return props.datefrom
});
const dateTo = computed(() => {
  return props.dateto
});

const isLoading = ref(true)
const dcrItems = ref('')

const course = ref([])
const section = ref([])
const program = ref([])
const degree = ref([])
const gradelvl = ref([])


const dateFromString = ref('')
const dateToString = ref('')
const totalAmount = ref(0)
const firstOR = ref('')
const lastOR = ref('')
const firstPR = ref('')
const lastPR = ref('')
const closingSeries = ref('')
const allORlist = ref([])
const allPRlist = ref([])
const allItems = ref([])
const allItemsHolder = ref([])
const dcrItemsHolder = ref([])
const billType = ref(0)
const itemDesc = ref('')
const searchedItem = ref('')
const activateSearch = ref(false)
const orderBy = ref(0)
const fileNameExt = ref('')

const actCourse = ref(0)
const actProgram = ref(0)
const actSection = ref(0)
const actGradelvl = ref(0)

onMounted(async () => {
  try {

    getAcademicDefaults().then((results) => {
      gradelvl.value = results.gradelvl
      // quarter.value = results.quarter
      course.value = results.course
      section.value = results.section
      program.value = results.program
      degree.value = results.degree
      // subject.value = results.subject
    })

    dateFromString.value = formatDate(dateFrom.value)
    dateToString.value = formatDate(dateTo.value)

    const items = await getAccountsFee()
    allItems.value = items.data
    allItemsHolder.value = items.data
    getFileNameFormat()

    const results = await getAllPayments(0, dateFrom.value, dateTo.value, cashierId.value, 1)
    dcrItems.value = results.data
    dcrItemsHolder.value = results.data

    allORlist.value = results.data.filter(e => e.acy_billtype == 2)
    allPRlist.value = results.data.filter(e => e.acy_billtype == 1)

    firstPR.value = allPRlist.value[0]?.acy_series_pattern ?? 'N/A'
    lastPR.value = allPRlist.value[allPRlist.value.length - 1]?.acy_series_pattern ?? 'N/A'
    firstOR.value = allORlist.value[0]?.acy_series_pattern ?? 'N/A'
    lastOR.value = allORlist.value[allORlist.value.length - 1]?.acy_series_pattern ?? 'N/A'

    closingSeries.value = `[ OR ${firstOR.value} -> OR ${lastOR.value} ] | [ PR ${firstPR.value} -> PR ${lastPR.value} ]`

    totalAmount.value = dcrItems.value.reduce((sum, item) => sum + Number(item.acy_amount || 0), 0)
  } catch (error) {
    console.error("Error loading DCR data:", error)
    // await Swal.fire({
    //   title: "Generation Failed",
    //   html: `Failed to retrieve items. Please contact the administrator for verification.`,
    //   icon: "error",
    //   confirmButtonText: "Ok, Got it!"
    // })
  } finally {
    isLoading.value = false
  }
})



const getFileNameFormat = () => {
  var now = new Date();

  var year = now.getFullYear();          // e.g., 2025
  var day = String(now.getDate()).padStart(2, '0');   // e.g., 22
  var month = String(now.getMonth() + 1).padStart(2, '0'); // e.g., 11 (months are 0-indexed)

  var formattedDate = `${year}${day}${month}`;
  fileNameExt.value = formattedDate
}

function formatDate(dateString) {
  const date = new Date(dateString);

  return new Intl.DateTimeFormat('en-US', {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: '2-digit'
  }).format(date);
}

const filterItems = () => {

  //receipt type filter
  switch (billType.value) {
    case '1':
      dcrItems.value = allPRlist.value
      break;
    case '2':
      dcrItems.value = allORlist.value
      break;
    default:
      dcrItems.value = dcrItemsHolder.value
      break;
  }

  //items search filter
  if (billType.value == 2 && searchedItem.value) {
    dcrItems.value = dcrItems.value.filter((e) => {
      return e.acr_reqitem == searchedItem.value
    })
  }

  //filter by grade level
  // if((actProgram.value != 0)||(actGradelvl.value != 0)||(actCourse.value != 0)||(actSection.value != 0)){
  //     dcrItems.value = dcrItems.value.filter((e) => {
  //       if((e.grad_id == actGradelvl.value) || (e.grad_id == actGradelvl.value) || (e.course_id == actCourse.value) || (e.sec_id == actSection.value)){
  //         return e
  //       }
  //     })
  // }

  if(actProgram.value != 0){
    //filter by program ex college or shs
    dcrItems.value = dcrItems.value.filter((e) => {
      return e.prog_id == actProgram.value
    })
  }
  if(actGradelvl.value != 0){
    //filter by grade level
    dcrItems.value = dcrItems.value.filter((e) => {
      return e.gradelvl_id == actGradelvl.value
    })
  }
  if(actCourse.value != 0){
    //filter by course
    dcrItems.value = dcrItems.value.filter((e) => {
      return e.course_id == actCourse.value
    })
  }
  if(actSection.value != 0){
    //filter by section
    dcrItems.value = dcrItems.value.filter((e) => {
      return e.sec_id == actSection.value
    })
  }

  if(Object.keys(dcrItems.value).length){
      //order by
      if (orderBy.value == '2') {
        dcrItems.value.sort((a, b) => b.acy_series_pattern - a.acy_series_pattern);
      } else if (orderBy.value == '1') {
        dcrItems.value.sort((a, b) => a.acy_series_pattern - b.acy_series_pattern);
      } else {
        dcrItems.value.sort((a, b) => b.acy_id - a.acy_id);
      }

      // get the first and last series
      if (billType.value == 2) {

        firstOR.value = dcrItems.value[0].acy_series_pattern ? dcrItems.value[0].acy_series_pattern : 'N/A'
        lastOR.value = dcrItems.value[dcrItems.value.length - 1].acy_series_pattern ? dcrItems.value[dcrItems.value.length - 1].acy_series_pattern : 'N/A'

        firstPR.value = '--'
        lastPR.value = '--'

        closingSeries.value = `[ OR ${firstOR.value} -> OR ${lastOR.value} ]`

      } else if (billType.value == 1) {
        firstPR.value = dcrItems.value[0].acy_series_pattern ? dcrItems.value[0].acy_series_pattern : 'N/A'
        lastPR.value = dcrItems.value[dcrItems.value.length - 1].acy_series_pattern ? dcrItems.value[dcrItems.value.length - 1].acy_series_pattern : 'N/A'

        firstOR.value = '--'
        lastOR.value = '--'

        closingSeries.value = `[ PR ${firstPR.value} -> PR ${lastPR.value} ]`
      } else {
        let pr = dcrItems.value.filter((e) => {
          return e.acy_billtype == 1
        })

        let or = dcrItems.value.filter((e) => {
          return e.acy_billtype == 2
        })

        firstPR.value = allPRlist.value[0].acy_series_pattern ? allPRlist.value[0].acy_series_pattern : 'N/A'
        lastPR.value = allPRlist.value[allPRlist.value.length - 1].acy_series_pattern ? allPRlist.value[allPRlist.value.length - 1].acy_series_pattern : 'N/A'
        firstOR.value = allORlist.value[0].acy_series_pattern ? allORlist.value[0].acy_series_pattern : 'N/A'
        lastOR.value = allORlist.value[allORlist.value.length - 1].acy_series_pattern ? allORlist.value[allORlist.value.length - 1].acy_series_pattern : 'N/A'

        closingSeries.value = `[ OR ${firstOR.value} -> OR ${lastOR.value} ] | [ PR ${firstPR.value} -> PR ${lastPR.value} ]`
      }
  }

  

  // refresh total amount
  totalAmount.value = dcrItems.value.reduce((sum, item) => {
    return sum + Number(item.acy_amount || 0);
  }, 0)
}

// const downloadExcel = () => {
//   let table = document.getElementById('main-table')
//   const wb = XLSX.utils.table_to_book(table, { sheet: 'sheet-1' });
//   /* Export to file (start a download) */
//   XLSX.writeFile(wb, 'MyTable.xlsx');
// }
const searchItem = () => {

  allItems.value = allItemsHolder.value.filter((e) => {
    return e.acf_desc.toLowerCase().includes(itemDesc.value);
  })

}


const showSearch = () => {
  setTimeout(() => {
    activateSearch.value = !activateSearch.value;

    // Run the combined filter after the timeout
    filterItems();
  }, 150);
};

const orientation = ref('landscape')

const exportToExcel = async () => {
  const workbook = new ExcelJS.Workbook();
  const sheet = workbook.addWorksheet("Collection List", {
    views: [{ state: "frozen", ySplit: 4 }],
  });

  // ---------- PAGE SETUP ----------
  sheet.pageSetup = {
    paperSize: 9,       // A4
    orientation: orientation.value, // 'portrait' or 'landscape'
    fitToPage: true,
    fitToWidth: 1,
    fitToHeight: 0,
    margins: {
      left: 0.3,
      right: 0.3,
      top: 0.5,
      bottom: 0.5,
      header: 0.3,
      footer: 0.3
    }
  };

  // ---------- TITLE ----------
  sheet.mergeCells("A1", "M1");
  sheet.getCell("A1").value = "Collection List";
  sheet.getCell("A1").font = { size: 14, bold: true };
  sheet.getCell("A1").alignment = { horizontal: "center" };

  sheet.mergeCells("A2", "M2");
  sheet.getCell("A2").value = `From ${dateFromString.value} → To ${dateToString.value}`;
  sheet.getCell("A2").alignment = { horizontal: "center" };

  sheet.mergeCells("A3", "M3");
  sheet.getCell("A3").value = closingSeries.value;
  sheet.getCell("A3").font = { italic: true };
  sheet.getCell("A3").alignment = { horizontal: "center" };

  sheet.addRow([]);

  // ---------- TABLE HEADER ----------
  const header = [
    "Receipt",
    "OR No.",
    "Name of Student",
    "Course",
    "Year",
    "Section",
    "Degree",
    "Misc",
    "Tuition Fees",
    "Amount to Pay",
    "Amount Paid",
    "Balance",
    "Remarks"
  ];
  sheet.addRow(header);

  const headerRow = sheet.getRow(sheet.lastRow.number);
  headerRow.font = { bold: true };
  headerRow.alignment = { horizontal: "center", vertical: "middle", wrapText: true };
  headerRow.eachCell((cell) => {
    cell.border = {
      top: { style: "thin" },
      left: { style: "thin" },
      right: { style: "thin" },
      bottom: { style: "thin" }
    };
    cell.fill = { type: "pattern", pattern: "solid", fgColor: { argb: "FFEFEFEF" } };
    cell.protection = { locked: false };
  });

  // ---------- TABLE BODY ----------
  dcrItems.value.forEach((dcr) => {
    const row = [
      dcr.acy_series ?? "N/A",
      dcr.acy_series_pattern ?? "N/A",
      dcr.acy_billtype == 2
        ? dcr.acr_personname
        : [
          dcr.per_firstname,
          dcr.per_middlename || "",
          dcr.per_lastname,
          dcr.per_suffixname || ""
        ].filter(Boolean).join(", ").toUpperCase(),
      dcr.prog_code,
      dcr.gradelvl_desc,
      dcr.sec_desc,
      dcr.prog_desc,
      dcr.acy_billtype == 2 ? dcr.acf_desc : "",
      dcr.acy_billtype == 1 ? "TUITION" : "",
      dcr.acy_billtype == 2
        ? dcr.acr_amount.toFixed(2)
        : dcr.acs_amount.toFixed(2),
      dcr.acy_billtype == 2
        ? dcr.acy_payment.toFixed(2)
        : dcr.acy_payment.toFixed(2),
      dcr.acy_balance.toFixed(2),
      dcr.acy_balance == 0 ? "Completed" : "Partial"
    ];

    const excelRow = sheet.addRow(row);
    excelRow.eachCell((cell, colNumber) => {
      cell.border = {
        top: { style: "thin" },
        left: { style: "thin" },
        right: { style: "thin" },
        bottom: { style: "thin" }
      };
      if ([7, 8, 9].includes(colNumber)) cell.alignment = { horizontal: "right" };
      if (colNumber === 10) {
        if (cell.value === "Completed") {
          cell.fill = { type: "pattern", pattern: "solid", fgColor: { argb: "FFC6EFCE" } };
        } else if (cell.value === "Partial") {
          cell.fill = { type: "pattern", pattern: "solid", fgColor: { argb: "FFFFC7CE" } };
        }
        cell.alignment = { horizontal: "center" };
      }
      cell.protection = { locked: false };
    });
  });

  // ---------- FOOTER TOTAL ----------
  const footer = sheet.addRow(["", "", "", "", "", "", "", "", "", "", "", "Total:", totalAmount.value.toFixed(2)]);
  footer.font = { bold: true };
  footer.eachCell((cell) => {
    cell.border = {
      top: { style: "thin" },
      left: { style: "thin" },
      right: { style: "thin" },
      bottom: { style: "thin" }
    };
    cell.alignment = { horizontal: "right" };
    cell.protection = { locked: false };
  });

  sheet.addRow([]); // spacing

  // ---------- MISCELLANEOUS NOTE ----------
  const noteRow = sheet.addRow([
    "Miscellaneous income includes collection from transcript, diploma, classcard, certification, copy of grades"
  ]);
  sheet.mergeCells(noteRow.number, 1, noteRow.number, 13);
  sheet.getCell(noteRow.number, 1).alignment = { horizontal: "center" };
  sheet.getCell(noteRow.number, 1).font = { italic: true, size: 13 };
  sheet.getCell(noteRow.number, 1).protection = { locked: false };
  sheet.addRow([]);

  // ---------- SIGNATORIES IN ONE ROW ----------
  const signatories = [
    { title: "Prepared by:", name: `${cashierName.value.toUpperCase()}`, position: "COLL. CLERK / CASHIER" },
    { title: "Checked & Reviewed By:", name: "JAN MARCO VILLANIL / LIRA SARAH Q. SALANGSANG", position: "ACCOUNTANT / ACCOUNTING" },
    { title: "Noted By:", name: "DELIA M. LEGASPI, PHD", position: "EVP FOR FINANCE / CHIEF ACCOUNTANT" }
  ];

  const row = sheet.addRow([]);
  const colPerSig = Math.floor(sheet.columns.length / signatories.length);
  signatories.forEach((sig, i) => {
    const startCol = i * colPerSig + 1;
    const endCol = startCol + colPerSig - 1;

    // Title
    sheet.mergeCells(row.number, startCol, row.number, endCol);
    sheet.getCell(row.number, startCol).value = sig.title;
    sheet.getCell(row.number, startCol).font = { bold: true };
    sheet.getCell(row.number, startCol).alignment = { horizontal: "center" };
    sheet.getCell(row.number, startCol).protection = { locked: false };
  });

  const nameRow = sheet.addRow([]);
  signatories.forEach((sig, i) => {
    const startCol = i * colPerSig + 1;
    const endCol = startCol + colPerSig - 1;
    sheet.mergeCells(nameRow.number, startCol, nameRow.number, endCol);
    sheet.getCell(nameRow.number, startCol).value = sig.name;
    sheet.getCell(nameRow.number, startCol).font = { bold: true };
    sheet.getCell(nameRow.number, startCol).alignment = { horizontal: "center" };
    sheet.getCell(nameRow.number, startCol).protection = { locked: false };
  });

  const posRow = sheet.addRow([]);
  signatories.forEach((sig, i) => {
    const startCol = i * colPerSig + 1;
    const endCol = startCol + colPerSig - 1;
    sheet.mergeCells(posRow.number, startCol, posRow.number, endCol);
    sheet.getCell(posRow.number, startCol).value = sig.position;
    sheet.getCell(posRow.number, startCol).font = { italic: true };
    sheet.getCell(posRow.number, startCol).alignment = { horizontal: "center" };
    sheet.getCell(posRow.number, startCol).protection = { locked: false };
  });

  // ---------- AUTO COLUMN WIDTH ----------
  sheet.columns.forEach((col) => {
    let max = 13;
    col.eachCell({ includeEmpty: true }, (cell) => {
      const len = cell.value ? cell.value.toString().length : 0;
      if (len > max) max = len;
    });
    col.width = Math.min(max + 2, orientation === "portrait" ? 15 : 20);
  });

  // ---------- DOWNLOAD ----------
  const buffer = await workbook.xlsx.writeBuffer();
  saveAs(new Blob([buffer]), `DCR-${cashierName.value}-${fileNameExt.value}.xlsx`);
};


</script>

<template>
  <!-- style="width: 795px;  height:1215px;" -->
  <div v-if="isLoading" class="h-100 d-flex justify-content-center align-items-center">
    <NeuLoader2></NeuLoader2>
  </div>
  <div v-else class="d-flex flex-column justify-content-center align-items-center small-font">
    <div class="row neu-card w-100 mb-2 p-4">
      <div class="col-md-4">
        <select @change="filterItems()" class="neu-input neu-select" v-model="billType">
          <option value="0">All Receipts</option>
          <option value="2">Official Receipt</option>
          <option value="1">Provisional Receipt</option>
        </select>
      </div>
      <div class="col-md-4 position-relative">
        <input type="text" class="neu-input" v-model="itemDesc" @keyup="searchItem()"
          @focus="showSearch" @focusout="showSearch" placeholder="Search Item Here"
          :disabled="billType == 2 ? false : true" />

        <div class="position-absolute mt-1 bg-white w-100 overflow-auto fade-in" style="height: 200px; z-index: 1000;"
          v-if="activateSearch">
          <ul class="list-group">
            <li v-if="Object.keys(allItems).length" @click="searchedItem = '', itemDesc = ''" class="list-group-item">
              All Items
            </li>

            <li v-for="(itm, index) in allItems"
              :class="itm.acf_id == searchedItem ? 'bg-secondary list-group-item' : 'list-group-item'"
              @click="searchedItem = itm.acf_id, itemDesc = itm.acf_desc">
              {{ itm.acf_desc }}
            </li>

            <li v-if="!Object.keys(allItems).length" class="list-group-item">
              Item Not Found
            </li>
          </ul>
        </div>
      </div>

      <div class="col-md-4" @change="filterItems()">
        <select class="neu-input neu-select"" v-model="orderBy">
          <option value="0">Default</option>
          <option value="1">Series Ascending Order</option>
          <option value="2">Series Descending Order</option>
        </select>
      </div>

    </div>
    <div class="row neu-card w-100  mb-3 p-4 ">
      <div class="col-md-3">
        <select v-model="actProgram" class="neu-input neu-select" @change="filterItems()">
          <option value="0">-- Select All Program --</option>
          <option :value="prog.dtype_id" v-for="(prog, index) in program">
            {{ prog.dtype_desc }}
          </option>
        </select>
      </div>
      <div class="col-md-3">
        <select v-model="actGradelvl" class="neu-input neu-select" @change="filterItems()">
          <option value="0">-- Select All Grade Level --</option>
          <template v-for="(grad, index) in gradelvl">
            <option v-if="actProgram == grad.grad_dtypeid" :value="grad.grad_id">
              {{ grad.grad_name }}
            </option>
          </template>
        </select>
      </div>
      <div class="col-md-3">
        <select v-model="actCourse" class="neu-input neu-select" @change="filterItems()">
          <option value="0">-- Select All Course</option>
          <template v-for="(cour, index) in course">
            <option v-if="actProgram == cour.prog_progtype" :value="cour.prog_id">
              {{ cour.prog_name }}
            </option>
          </template>
        </select>
      </div>
      <div class="col-md-3">
        <select v-model="actSection" class="neu-input neu-select" @change="filterItems()">
          <option value="0">-- Select All Section</option>
          <template v-for="(sec, index) in section">
            <option :value="sec.sec_id">{{ sec.sec_name }}</option>
          </template>
        </select>
      </div>
    </div>
    <div class="neu-card-inner d-flex flex-column gap-2 justify-content-center align-items-center p-3 border p-2 w-100 text-dim" id="main-table">
      <!-- <select class="form-select form-select-sm w-50 mb-4" v-model="orientation">
        <option value="portrait">Portrait</option>
        <option value="landscape">Landscape</option>
      </select>   -->
      <div class="text-center mb-4">
        <!-- Title -->
        <h5 class="mb-0 fw-bold text-uppercase text-dim">Collection List</h5>
        <!-- Date -->
        <div class="fw-semibold">
          From {{ dateFromString }} &nbsp; → &nbsp;
          To {{ dateToString }}
        </div>
        <!-- Subheader -->
        <div class="small text-muted">
          {{ closingSeries }}
        </div>
      </div>
      <table class="neu-table-flat">
        <thead class="text-center">
          <tr>
            <th>Receipt</th>
            <th>OR No.</th>
            <th>Name of Student</th>
            <th>Course</th>
            <th>Year</th>
            <th>Section</th>
            <th>Degree</th>
            <th>Misc</th>
            <th>Tuition Fees</th>
            <th>Amount to Pay</th>
            <th>Amount Paid</th>
            <th>Balance</th>
            <th>Remarks</th>
          </tr>
        </thead>

        <tbody>
          <tr v-for="(dcr, index) in dcrItems">
            <td>{{ dcr.acy_series ? dcr.acy_series : 'N/A' }}</td>
            <td>{{ dcr.acy_series_pattern ? dcr.acy_series_pattern : 'N/A' }}</td>
            <td>
              <span v-if="dcr.acy_billtype == 2">{{ dcr.acr_personname }}</span>
              <span v-if="dcr.acy_billtype == 1" class="text-uppercase">
                {{
                  [
                    dcr.per_firstname,
                    dcr.per_middlename || "",
                    dcr.per_lastname,
                    dcr.per_suffixname || ""
                  ].filter(Boolean).join(', ')
                }}</span>
            </td>
            <td>
              {{ dcr.prog_code }}
            </td>
            <td>
              {{ dcr.gradelvl_desc }}
            </td>
            <td>
              {{ dcr.sec_desc }}
            </td>
            <td>
              {{ dcr.prog_desc }}
            </td>
            <td>
              <span v-if="dcr.acy_billtype == 2">{{ dcr.acf_desc }}</span>
            </td>
            <td>
              <span v-if="dcr.acy_billtype == 1" class="text-uppercase">TUITION</span>
            </td>
            <td>
              <span v-if="dcr.acy_billtype == 2">{{ dcr.acr_amount.toFixed(2) }}</span>
              <span v-if="dcr.acy_billtype == 1" class="text-uppercase">{{ dcr.acs_amount.toFixed(2) }}</span>
            </td>
            <td>
              <span v-if="dcr.acy_billtype == 2">{{ dcr.acy_payment.toFixed(2) }}</span>
              <span v-if="dcr.acy_billtype == 1" class="text-uppercase">{{ dcr.acy_payment.toFixed(2) }}</span>
            </td>
            <td>{{ dcr.acy_balance.toFixed(2) }}</td>
            <td>
              {{ dcr.acy_balance == 0 ? 'Completed' : 'Partial' }}
            </td>
          </tr>
          <tr v-if="!Object.keys(dcrItems).length">
            <td colspan="13"  class="text-center">No Items Found</td>
          </tr>
        </tbody>

        <tfoot>
          <tr>
            <td colspan="12" class="text-end fw-bold">Total:</td>
            <td class="fw-bold">
              {{ new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP' }).format(totalAmount.toFixed(2)) }}
            </td>
          </tr>
        </tfoot>
      </table>
      <div class="mt-5">
        <small class="d-block text-center mb-4">
          <em>Miscellaneous income includes collection from transcript, diploma, classcard, certification, copy of
            grades</em>
        </small>

        <div class="row text-center mt-5">

          <!-- Prepared by -->
          <div class="col-4">
            <div class="mb-5">&nbsp;</div>
            <div class="fw-bold text-uppercase">Prepared by:</div>
            <div class="mt-4 fw-bold text-uppercase">{{ cashierName }}</div>
            <div class="text-muted small">COLL. CLERK / CASHIER</div>
          </div>

          <!-- Checked & Reviewed By -->
          <div class="col-4">
            <div class="mb-5">&nbsp;</div>
            <div class="fw-bold text-uppercase">Checked & Reviewed By:</div>
            <div class="mt-4 fw-bold">JAN MARCO VILLANIL / LIRA SARAH Q. SALANGSANG</div>
            <div class="text-muted small">ACCOUNTANT / ACCOUNTING</div>
          </div>

          <!-- Noted By -->
          <div class="col-4">
            <div class="mb-5">&nbsp;</div>
            <div class="fw-bold text-uppercase">Noted By:</div>
            <div class="mt-4 fw-bold">DELIA M. LEGASPI, PHD</div>
            <div class="text-muted small">EVP FOR FINANCE / CHIEF ACCOUNTANT</div>
          </div>

        </div>
      </div>
    </div>
    <div class="d-flex justify-content-end mt-2 ">
      <button @click="exportToExcel()" type="button" class="neu-btn neu-green p-2">
        <font-awesome-icon icon="fa-solid fa-file-excel"/> Download Excel File
      </button>
    </div>
  </div>

</template>

<style scope>
/* Fade-in animation */
.fade-in {
  animation: fadeIn 0.2s ease-out;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(-5px);
  }

  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Hover effect on list items */
.list-group-item {
  cursor: pointer;
  transition: background 0.15s ease;
}

.list-group-item:hover {
  background: #f1f1f1;
}
</style>