import axios from "axios";

const qrImageGenerator = async (data) => {
    var typeNumber = 4;
    var errorCorrectionLevel = 'L';
    var qr = qrcode(typeNumber, errorCorrectionLevel);
    qr.addData(data);
    qr.make();
    qrcode = qr.createImgTag();

    return qrcode;
}

const imageFetcher = async(data, type) =>{
    await axios({
        method: "GET",
        url: 'api/get-person-image/' + data + '/'+ type,

    }).then(async (results) => {
        console.log(results.data)
        return results.data
    })
}

// const pdfGenerator = async (name,paper,orientation,margin) => {
//     var element = document.getElementById('printform');
//     var opt = {
//         // margin: [0.1, 0.2, 0.1, 0.2],
//         margin: margin,
//         filename: name+'.pdf',
//         pagebreak: { mode: 'css', before: '.pagebreak' },
//         image: { type: 'jpeg', quality: 1 },
//         // html2canvas: { scale: 1 },
//         html2canvas: {
//             dpi: 192,
//             scale:4,
//             letterRendering: true,
//             useCORS: true,
//             scrollY: 0
//         },
//         jsPDF: { 
//             unit: 'in', 
//             format: paper, 
//             orientation: orientation
//         },
        
//     };

//     // New Promise-based usage:
//     html2pdf().set(opt).from(element).save();

//     // Old monolithic-style usage:
//     // html2pdf(element, opt);

// }

const pdfGenerator = async (name, paper, orientation, margin) => {
    var element = document.getElementById('printform');
    var opt = {
        margin: margin,
        filename: name + '.pdf',
        pagebreak: { mode: 'css', before: '.pagebreak' },
        image: { type: 'jpeg', quality: 1 },
        html2canvas: {
            dpi: 192,
            scale: 4,
            letterRendering: true,
            useCORS: true,
            scrollY: 0
        },
        jsPDF: { 
            unit: 'in',
            format: paper,
            orientation: orientation
        },
    };

    // ✅ Return the promise so callers can await it
    return html2pdf().set(opt).from(element).save();
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

const pesoConverter = (amount) => { 
  let data = new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP' }).format(amount)
  return data;
}

export {
    qrImageGenerator,
    pdfGenerator,
    imageFetcher,
    numberToWords,
    pesoConverter
}