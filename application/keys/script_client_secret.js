const crypto = require('crypto');

// Data untuk tanda tangan
const clientSecret = 'SpPtPt6Oa7Cjf47XIUvn6gq6fVYEPPodzFgukfMdk/o='; // Client Secret
const method = 'POST';
const path = 'https://briapi-dev.arraafi.id:4433/bricallback/backend/notifikasi';
const timestamp = new Date().toISOString();  // X-TIMESTAMP header
const accessToken = '2NEsUaYqBkYol9goYbdEiafPdCzHB7VK';  // Misalnya token yang diambil dari header Authorization (tanpa "Bearer ")
const body = {
    "partnerServiceId": "service123",
    "customerNo": "customer001",
    "virtualAccountNo": "1234567890",
    "paymentRequestId": "req123456",
    "trxDateTime": "2025-01-20T10:00:00Z",
    "additionalInfo": {
        "idApp": "app123",
        "passApp": "app123",
        "paymentAmount": "123",
        "terminalId": "002",
        "bankId": "123"
    }
};

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
console.log('Authorization:', `Bearer ${accessToken}`);
console.log('X-TIMESTAMP:', timestamp);
console.log('X-SIGNATURE:', hmacSignature);
console.log('Body:', bodyJson);
