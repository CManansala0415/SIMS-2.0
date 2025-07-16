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

const pdfGenerator = async (name,paper,orientation,margin) => {
    var element = document.getElementById('printform');
    var opt = {
        // margin: [0.1, 0.2, 0.1, 0.2],
        margin: margin,
        filename: name+'.pdf',
        pagebreak: { mode: 'css', before: '.pagebreak' },
        image: { type: 'jpeg', quality: 1 },
        // html2canvas: { scale: 1 },
        html2canvas: {
            dpi: 192,
            scale:4,
            letterRendering: true,
            useCORS: true
        },
        jsPDF: { 
            unit: 'in', 
            format: paper, 
            orientation: orientation
        },
        
    };

    // New Promise-based usage:
    html2pdf().set(opt).from(element).save();

    // Old monolithic-style usage:
    // html2pdf(element, opt);

}

export {
    qrImageGenerator,
    pdfGenerator,
    imageFetcher
}