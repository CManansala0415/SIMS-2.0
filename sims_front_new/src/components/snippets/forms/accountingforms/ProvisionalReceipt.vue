<script setup>
import { ref, onMounted, computed } from 'vue';
const props = defineProps({
    data:{
        acr_id: 0,
        acr_personid: 0,
        acr_personname: '',
        acr_reqitem: 0,
        acr_amount: 0,
        acy_cashiername:'',
        acr_paystatus: 0,
        acr_addedby: 0,
        acr_dateadded: '',
        acr_updatedby: null,
        acr_dateupdated: '',
        acr_docstamp: 0,
        acr_rendered: 0,
        acr_billtype: 0,
        acr_status: 0,
        acf_id: 0,
        acf_desc: '',
        acf_rectype: 0,
        acf_feetype: null,
        acf_docstamp: 0,
        acf_price: 0,
        acf_addedby: 0,
        acf_dateadded: '',
        acf_updatedby: null,
        acf_dateupdated: null,
        acf_status: 0,
        per_firstname: '',
        per_middlename: '',
        per_lastname: '',
        per_suffixname: null,
        acr_paystatusdesc: '',
        paystat_color: '',
        acr_itemrequested: ''
    }
})

const account = computed(() => {
    return props.data
});

const formatAmount = (val) => {
    return Number(val).toLocaleString("en-PH", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    })
}
</script>
<template>
  <div class="receipt-wrapper">
    <div class="receipt-content">
      
      <!-- HEADER -->
      <div class="header row">
        <div class="col">
          <h2>PROVISIONAL RECEIPT</h2>
          <div class="muted">
            NOT AN OFFICIAL RECEIPT. FOR INTERNAL/TEMPORARY USE ONLY.
          </div>
        </div>
        <div class="col right">
          <div class="muted">PR No.</div>
          <div class="big">{{ account.acr_id }}</div>
          <div class="muted">Date Issued</div>
          <div>{{ account.acr_dateupdated }}</div>
        </div>
      </div>

      <!-- DETAILS -->
      <div class="details">
        <div class="field">
          <label>Received From:</label>
          <div class="print-small">{{ account.acr_personname }}</div>
        </div>

        <div class="field">
          <label>Particulars / Description:</label>
          <div class="print-small">{{ account.acr_itemrequested }}</div>
        </div>

        <div class="row amounts">
          <div class="col">
            <label>Amount (Figures):</label>
            <div class="big">₱ {{ formatAmount(account.acy_amount) }}</div>
          </div>
          <div class="col">
            <label>Amount (In Words):</label>
            <div class="print-small">Test</div>
          </div>
        </div>
      </div>

      <!-- FOOTER -->
      <div class="footer">
        <div class="bordered row">
          <div class="col">
            <label>Received by (Signature):</label>
            <div style="height:48px;">{{ account.acy_cashiername }}</div>
            <div class="muted">Name & Position</div>
          </div>
          <div class="col right">
            <label>Date:</label>
            <div>Test</div>
          </div>
        </div>

        <div class="note">
          NOTE: This Provisional Receipt is for temporary/reference use only.  
          For tax/accounting, please refer to the Official Receipt or Invoice issued upon full settlement.
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.receipt-wrapper {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;   /* keep whole receipt centered vertically */
  justify-content: center; /* centered horizontally */
  background: #fff;       /* paper-like background */
}

.receipt-content {
  width: 100%;
  height: 100%;
  box-sizing: border-box;
  padding: 12px;
  font-size: 12px;
  display: flex;
  flex-direction: column;
}

.header {
  margin-bottom: 8px;
}

.details {
  flex: 1; /* takes middle space */
}

.footer {
  margin-top: auto; /* pushes down */
}

.row {
  display: flex;
  justify-content: space-between;
  gap: 8px;
}

.col {
  flex: 1;
}

h2 {
  margin: 0 0 6px 0;
  font-size: 16px;
}

label {
  font-size: 12px;
  color: #444;
}

.big {
  font-size: 18px;
  font-weight: 700;
}

.muted {
  color: #666;
  font-size: 12px;
}

.print-small {
  font-size: 12px;
}

.right {
  text-align: right;
}

.field {
  margin-top: 8px;
}

.amounts {
  margin-top: 12px;
}

.bordered {
  border-top: 1px dashed #ddd;
  margin-top: 12px;
  padding-top: 8px;
}

.note {
  margin-top: 8px;
  font-size: 11px;
  color: #555;
  text-align: center;
}

@media print {
  body, .receipt-wrapper {
    margin: 0;
    padding: 0;
  }
}
</style>
