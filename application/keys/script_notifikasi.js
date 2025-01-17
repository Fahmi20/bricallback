const crypto = require('crypto');

// Data untuk tanda tangan
const authorization = 'lymy+K\/+KbblstqiWrQzLzKTru\/m+MnorNe9ls6vzpo='; // Authorization header
const timestamp = new Date().toISOString();             // X-TIMESTAMP header
const partnerId = '77777';
const channelId = '12345';
const externalId = 'externalId123';

// Private key
const privateKey = `-----BEGIN PRIVATE KEY-----
MIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQCmdw8/KF0erk/k
LPz34HlaSrGhcCVnK6wdrUcLn0xEfaf1Q1kImS0V72JpCOkc3Q4pfEyehds4j8F1
Ur3EO+kfZYoSuSzxNNPnErRtiR2hJUnGWtM6+ItEgOoITaOwquy0rfEJyx4A6U0g
5FkZfD0/58GvjgABnjiHzgfg7RWeO98dRiFBJIrNyPuuukx8DBqz/r7Hs2jq/kZY
B2ly3aN0+SI41l3zXF+y0MFESEXTQSk0UYaFa6XaiWKFfOoj7r2SD+bDstJSdNiH
j073AhoEPsqgEDibt9miaOopXDSUocJFYYYQtqKngtES+HAQku+iRHrT2/3bhu4Y
aH4/4y7lAgMBAAECggEARf17MJ/k/zhlKNftiL8d5uPO6cTARS+sj1HCtFVG+Oko
TEwDzESzGyzqYKU5dkRPZwv3HxPCx3ZR7eVbGn3iF6xWsGahSc1fZyGLMR7ckVuf
OEIJ3BqSW7wkKleSgn5rRdB4rxhyxglRv4mjGL8O9aaY3hpDUGrY8+ihkWW9mCiA
grh6pjgOV5RNae6A6gLIanT/dFGwLQct0tN34vo6RaY7V4xK8B4IhZ8xlQpW8LG5
QDNFQchIPXQ7QLSN82LCjjYfSOg0xnrhavGIcAC07s7N7Qyw5JdRrDsWNV9kdNDU
9mstJ9hLXle+F8hOidbpgXSYwTamdwyeMXkvcCKSIQKBgQDHEx+6EMRZV8QrOuG1
3LhzgbUVafqUY+RpHdOTEdkXpD7lmo189yk3I+lw0GP83cr1fF0w8PqlQHFz4GSs
nZmL7vOFy7zSKwRZAaJw8jl4DIgyVKkxJk61nnBmL0VzxFCLH1sDuGxj9aZV1QEs
Ssrf6CBFrwM1lN5Se+tMw2sdaQKBgQDWEM+4HLOBWn6MQgy4BrlCa4gyY44bmk99
iPlY12o6fMO0MtDB4HCn2cdHWIgrdg4HLjohMK8n2PU+gbfdIICQAqqvqxFwPNkV
FVpEzc0XvRezqA0NE6dr++ZIkbHz5ntSc3XlpEN9HJ4SsOnkiMHaJ5UrQFVq4Z1A
qmcXjazKHQKBgC5A4D9ABA7qGHce4DB8DxMvUN6f2AvAReKyfmUOYY1fqQl55mPh
nV7lZijDEmg/NBfjhFeJtgLNPU76FQoSOAnORCCTHNUMD5+KhK6PaRDegIqJJyJ3
TxRdsqnbU9y5ASnB6EiuAekbu0D4E6Sx3/80FMN8DVfWte0eQ6Z7RRj5AoGBAKrb
zj/gwLHtXfZrPaWg0DuggpvddG65stq6+nKbtZErRjVNHeyxTJncrD9Y/Y7a8oVu
sz0Mk7FVbSHP/cZEi/jl+ACwpQGVv5shaORj82AQMJvX9VrLpiT9cSfZClVnUGVV
/PMnMirpLY4zoOwk771FPL3B4qulmpMjr5dQIGtNAoGAa2MNrkK4n1m5p9moTgEa
Klk4dycB03hHcgWmgqj5yhC/Fv5Pdzk+yfDqSGqZMYb3ERRqSZFjWVS+bboPsmn1
n79KUlCM/B/9924GMwxcQiFDwd6BZoJrM92yqGo9SogzRvT/iokJRgr2YRVVMxAK
uimSjqmsEW3lz2qQaRVkoOM=
-----END PRIVATE KEY-----`;

// Gabungkan data untuk ditandatangani
const stringToSign = authorization + '|' + timestamp + '|' + partnerId + '|' + channelId + '|' + externalId;

// Buat signature
const sign = crypto.createSign('SHA512');
sign.update(stringToSign);
sign.end();
const signature = sign.sign(privateKey, 'base64');

// Tampilkan header dan body untuk Postman
console.log('Authorization:', authorization);
console.log('X-TIMESTAMP:', timestamp);
console.log('X-SIGNATURE:', signature);
console.log('X-PARTNER-ID:', partnerId);
console.log('CHANNEL-ID:', channelId);
console.log('X-EXTERNAL-ID:', externalId);
