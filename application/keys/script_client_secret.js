const crypto = require('crypto');

// Data untuk tanda tangan
const clientSecret = 'SpPtPt6Oa7Cjf47XIUvn6gq6fVYEPPodzFgukfMdk/o='; // Client Secret
const method = 'POST';
const path = '/bricallback/backend/notifikasi';
const timestamp = new Date().toISOString();  // X-TIMESTAMP header
const accessToken = 'Bearer 2NEsUaYqBkYol9goYbdEiafPdCzHB7VK';  // Misalnya token yang diambil dari header Authorization
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


// Langkah 2: Membentuk string untuk ditandatangani
const stringToSign = `${method}:${path}:${accessToken}:${body}:${timestamp}`;

// Langkah 3: Menghitung signature dengan HMAC-SHA512
const hmacSignature = crypto.createHmac('sha512', clientSecret)
    .update(stringToSign)
    .digest('base64');

// Tampilkan hasilnya
console.log('Authorization:', `${accessToken}`);
console.log('X-TIMESTAMP:', timestamp);
console.log('X-SIGNATURE:', hmacSignature);
console.log('Body:', body);
