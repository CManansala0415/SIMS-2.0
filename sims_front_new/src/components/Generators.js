
const qrImageGenerator = async (data) => {
    var typeNumber = 4;
    var errorCorrectionLevel = 'L';
    var qr = qrcode(typeNumber, errorCorrectionLevel);
    qr.addData(data);
    qr.make();
    qrcode = qr.createImgTag();

    return qrcode;
}

const pdfGenerator = async (name,paper,orientation,margin) => {
    var element = document.getElementById('printform');
    var opt = {
        // margin: [0.1, 0.2, 0.1, 0.2],
        margin: margin,
        filename: name+'.pdf',
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 1 },
        jsPDF: { unit: 'in', format: paper, orientation: orientation }
        
    };

    // New Promise-based usage:
    html2pdf().set(opt).from(element).save();

    // Old monolithic-style usage:
    html2pdf(element, opt);
}

export {
    qrImageGenerator,
    pdfGenerator
}