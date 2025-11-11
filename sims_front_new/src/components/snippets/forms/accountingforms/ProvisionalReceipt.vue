<script setup>
import { ref, onMounted, computed } from 'vue';
const props = defineProps({
    data:{
        acr_id: 0,
        acr_personid: 0,
        acr_personname: '',
        acr_reqitem: 0,
        acr_amount: 0,
        acy_payment:0,
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
  let num = Number(val);

  if (!num) {
    return "0.00"; // or return "N/A" if you prefer
  }

  return num.toLocaleString("en-PH", {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  });
};

const numberToWords = (num) => {
  if (isNaN(num) || num === null ||!num) return "Zero Pesos Only";

  const ones = [
    "", "One", "Two", "Three", "Four", "Five", "Six",
    "Seven", "Eight", "Nine", "Ten", "Eleven", "Twelve",
    "Thirteen", "Fourteen", "Fifteen", "Sixteen", "Seventeen",
    "Eighteen", "Nineteen"
  ];

  const tens = [
    "", "", "Twenty", "Thirty", "Forty", "Fifty",
    "Sixty", "Seventy", "Eighty", "Ninety"
  ];

  const scales = ["", "Thousand", "Million", "Billion"];

  function convertChunk(num) {
    let words = "";
    const hundreds = Math.floor(num / 100);
    const remainder = num % 100;

    if (hundreds > 0) {
      words += ones[hundreds] + " Hundred ";
    }

    if (remainder > 0) {
      if (remainder < 20) {
        words += ones[remainder] + " ";
      } else {
        words += tens[Math.floor(remainder / 10)] + " ";
        if (remainder % 10 > 0) {
          words += ones[remainder % 10] + " ";
        }
      }
    }

    return words.trim();
  }

  const parts = num.toString().split(".");
  let wholePart = Math.floor(parts[0]);
  let decimalPart = parts[1] ? parseInt(parts[1].padEnd(2, "0").slice(0, 2)) : 0;

  if (wholePart === 0 && decimalPart === 0) return "Zero Pesos Only";

  let words = "";
  let scaleIndex = 0;

  while (wholePart > 0) {
    const chunk = wholePart % 1000;
    if (chunk > 0) {
      words = convertChunk(chunk) + " " + scales[scaleIndex] + " " + words;
    }
    wholePart = Math.floor(wholePart / 1000);
    scaleIndex++;
  }

  words = words.trim() + " Peso" + (num >= 2 ? "s" : "");

  if (decimalPart > 0) {
    words +=
      " and " +
      convertChunk(decimalPart) +
      " Centavo" +
      (decimalPart > 1 ? "s" : "");
  }

  words += " Only";

  return words.trim();
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
            <div class="big">₱ {{ formatAmount(account.acy_payment) }}</div>
          </div>
          <div class="col">
            <label>Amount (In Words):</label>
            <div class="print-small">{{ numberToWords(account.acy_payment)}}</div>
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
            <div>{{ new Date().toLocaleDateString() }}</div>
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
