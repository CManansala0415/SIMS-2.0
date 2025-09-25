<script setup>
import { computed } from 'vue'

const props = defineProps({
  data: {
    acr_id: 0,
    acr_personid: 0,
    acr_personname: '',
    acr_reqitem: 0,
    acr_amount: 0,
    acy_cashiername: '',
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
    acr_itemrequested: '',
    acy_amount: 0,
    acy_payment: 0,
    acy_balance: 0
  }
})

const account = computed(() => props.data)

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
      <header class="header">
        <div class="company">
          <h6>CENTRAL LUZON COLLEGE OF SCIENCE AND TECHNOLOGY, INC.</h6>
          <div class="muted">Your Company Address</div>
          <div class="muted">TIN: 000-000-000</div>
          <div class="muted">ATP No.: 3AU0000000000</div>
        </div>
        <div class="meta">
          <div class="muted">OFFICIAL RECEIPT</div>
          <div class="or-number">{{ account.acr_id }}</div>
          <div class="muted">Date: {{ account.acr_dateupdated || account.acr_dateadded }}</div>
        </div>
      </header>

      <!-- MIDDLE DETAILS -->
      <div class="details">
        <table>
          <tbody>
            <tr>
              <th>Received From</th>
              <th>TIN</th>
              <th colspan="4">Address</th>
            </tr>
            <tr>
              <td>{{ account.acr_personname }}</td>
              <td><!-- Add TIN if available --></td>
              <td colspan="4"><!-- Add Address if available --></td>
            </tr>

            <tr>
              <th colspan="4">Particulars / Description</th>
            </tr>
            <tr>
              <td colspan="4">{{ account.acr_itemrequested }}</td>
            </tr>

            <tr>
              <th>Amount</th>
              <th>VAT / Tax</th>
              <th>Paid</th>
              <th class="right">Total (PHP)</th>
            </tr>
            <tr>
              <td class="right">₱ {{ formatAmount(account.acy_amount) }}</td>
              <td class="right">VAT Exempt</td>
              <td class="right bold">₱ {{ formatAmount(account.acy_payment) }}</td>
              <td class="right bold">₱ {{ formatAmount(account.acy_balance) }}</td>
            </tr>

            <tr>
              <th colspan="4">Amount in words</th>
            </tr>
            <tr>
              <td colspan="4">{{ account.acy_amount }} Pesos Only</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- FOOTER -->
      <div class="footer">
        <div class="row signature">
          <div class="col">
            <div class="muted">Received by (Printed Name & Signature)</div>
            <div style="height:48px;"></div>
            <div class="muted">{{ account.acy_cashiername }}</div>
          </div>
          <div class="col right">
            <div class="muted">Date & Place of Issuance</div>
            <div>{{ account.acr_dateupdated || account.acr_dateadded }}</div>
            <div><!-- Add place if available --></div>
          </div>
        </div>

        <div class="note">
          * This Official Receipt is issued in accordance with BIR requirements.  
          Keep this for accounting/tax purposes.
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
  align-items: center; 
  justify-content: center;
  background: #fff;
}

.receipt-content {
  width: 100%;
  height: 100%;
  padding: 12px;
  box-sizing: border-box;
  display: flex;
  flex-direction: column;
  /* border: 1px solid #222; */
  /* border-radius: 6px; */
}

.header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 12px;
  margin-bottom: 8px;
}

.company {
  max-width: 80%;
}

.meta {
  text-align: right;
}

.or-number {
  font-size: 18px;
  font-weight: 700;
}

.details {
  flex: 1;
}

table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 8px;
  font-size: 13px;
}

td, th {
  padding: 6px;
  border: 1px solid #ddd;
  vertical-align: top;
}

.right {
  text-align: right;
}

.bold {
  font-weight: 700;
}

.footer {
  margin-top: auto;
}

.signature {
  border-top: 1px dashed #ccc;
  margin-top: 12px;
  padding-top: 8px;
}

.note {
  margin-top: 8px;
  font-size: 12px;
  color: #555;
  text-align: center;
}

.muted {
  color: #444;
  font-size: 12px;
}

@media print {
  body, .receipt-wrapper {
    margin: 0;
    padding: 0;
  }
}
</style>
