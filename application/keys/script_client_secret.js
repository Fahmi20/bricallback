const crypto = require('crypto');

// Data untuk tanda tangan
const clientSecret = 'ND6kq819YVylOdrSr4FOXb18aJf3Vc2R'; // Client Secret
const method = 'POST';
const path = '/bricallback/backend/notifikasi';
const timestamp = new Date().toISOString();  // X-TIMESTAMP header
const accessToken = 'ND6kq819YVylOdrSr4FOXb18aJf3Vc2R';  // Misalnya token yang diambil dari header Authorization (tanpa "Bearer ")
const body = {"partnerServiceId":"   22084","customerNo":"12121212","virtualAccountNo":"   2208412121212","trxDateTime":"2025-01-22T08:43:00+07:00","additionalInfo":{"paymentAmount":"100000","bankId":"002","terminalId":"1"},"paymentRequestId":"9012940124"};

// Langkah 1: Minifikasi dan enkripsi body dengan SHA-256
const bodyJson = JSON.stringify(body);
const bodyMinified = bodyJson.replace(/\s+/g, ''); // Minifikasi body (menghapus spasi)
const bodySHA256 = crypto.createHash('sha256')
    .update(bodyMinified)
    .digest('hex')
    .toLowerCase(); // Konversi ke format lowercase hex

// Langkah 2: Membentuk string untuk ditandatangani
const stringToSign = `${method}:${path}:${accessToken}:${bodySHA256}:${timestamp}`;

// Langkah 3: Menghitung signature dengan HMAC-SHA512
const hmacSignature = crypto.createHmac('sha512', clientSecret)
    .update(stringToSign)
    .digest('base64'); // Signature hasilnya dalam format Base64

// Tampilkan hasilnya
console.log('Authorization:', `${accessToken}`);
console.log('X-TIMESTAMP:', timestamp);
console.log('X-SIGNATURE:', hmacSignature);
console.log('Body:', bodyJson);
